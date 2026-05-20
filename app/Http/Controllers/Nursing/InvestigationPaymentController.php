<?php

namespace App\Http\Controllers\Nursing;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientAdmission;
use App\Models\Investigation;
use App\Models\InvestigationPayment;
use App\Models\NursingInvestigationPayment;
use App\Models\NursingInvestigationPaymentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestigationPaymentController extends Controller
{
    // ════════════════════════════════════════════════════════════
    // SECTION 1 — MAIN PAGE  (index)
    // ════════════════════════════════════════════════════════════

    /**
     * GET /nursing/InvestigationPayment
     * Main page — admitted patients list + recent payments + preset services
     */
    public function index(Request $request)
    {
        // ── Admitted patients ──────────────────────────────────
        $admittedPatients = DB::table('nursing_admissions as na')
            ->join('patients as p', 'p.id', '=', 'na.patient_id')
            ->select(
                'na.id          as admission_id',
                'na.patient_id',
                'na.admission_date',
                'p.patientcode  as patient_code',
                'p.patientname  as patient_name',
                'p.age          as patient_age',
                'p.gender',
                'p.mobile_no',
                'p.blood_group',
                'p.address',
                'p.upozila'
            )
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('p.patientname', 'like', '%' . $request->search . '%')
                       ->orWhere('p.patientcode', 'like', '%' . $request->search . '%')
                       ->orWhere('p.mobile_no',   'like', '%' . $request->search . '%');
                });
            })
            ->orderByDesc('na.admission_date')
            ->paginate(20)
            ->withQueryString();

        // ── Past payments ──────────────────────────────────────
        $pastPayments = NursingInvestigationPayment::with('items')
            ->orderByDesc('created_at')
            ->paginate(20);

        // ── Preset services ────────────────────────────────────
        $presetServices = $this->getServicesFromDatabase();

        $categories = collect($presetServices)
            ->pluck('category')
            ->unique()
            ->values()
            ->toArray();

        return view('nursing.investigation_payment', compact(
            'admittedPatients',
            'pastPayments',
            'categories',
            'presetServices'
        ));
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 2 — STORE  (save payment + items)
    // ════════════════════════════════════════════════════════════

    /**
     * POST /nursing/InvestigationPayment/store
     *
     * Blade JS sends:
     *   patient_id, admission_id, patient_name, patient_code, patient_age,
     *   mobile_no, payment_date, payment_method, collected_by, notes,
     *   discount, paid_amount,
     *   items: [ { category, service_name, unit_price, quantity, item_discount, amount, remarks } ]
     */
    public function store(Request $request)
    {
        // Debug: Log incoming request
        \Log::info('InvestigationPayment store request: ' . json_encode($request->all()));

        $request->validate([
            'patient_id'           => 'required|integer',
            'items'                => 'required|array|min:1',
            'items.*.service_name' => 'required|string',
            'items.*.amount'       => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $items       = $request->input('items', []);
            $grossAmount = collect($items)->sum('amount');          // sum of (unit_price * qty)
            $discount    = (float) $request->input('discount', 0);
            $netAmount   = max(0, $grossAmount - $discount);
            $paidAmount  = (float) $request->input('paid_amount', $netAmount);
            $dueAmount   = max(0, $netAmount - $paidAmount);

            // Determine payment status
            $status = 'due';
            if ($paidAmount >= $netAmount) {
                $status = 'paid';
            } elseif ($paidAmount > 0) {
                $status = 'partial';
            }

            // Generate receipt number: INV-YYYYMMDD-XXXX
            $receiptNo = 'INV-' . date('Ymd') . '-' . str_pad(
                (NursingInvestigationPayment::whereDate('created_at', today())->count() + 1),
                4, '0', STR_PAD_LEFT
            );

            $payment = NursingInvestigationPayment::create([
                'receipt_no'      => $receiptNo,
                'admission_id'    => $request->input('admission_id'),
                'patient_id'      => $request->input('patient_id'),
                'patient_name'    => $request->input('patient_name'),
                'patient_code'    => $request->input('patient_code'),
                'patient_age'     => $request->input('patient_age'),
                'mobile_no'       => $request->input('mobile_no'),
                'payment_type'    => 'partial',
                'payment_date'    => $request->input('payment_date', now()->toDateString()),
                'collected_by'    => $request->input('collected_by'),
                'total_amount'    => $netAmount,
                'gross_amount'    => $grossAmount,
                'discount'        => $discount,
                'paid_amount'     => $paidAmount,
                'due_amount'      => $dueAmount,
                'status'          => $status,
                'payment_method'  => $request->input('payment_method', 'cash'),
                'transaction_ref' => $request->input('transaction_ref'),
                'notes'           => $request->input('notes'),
            ]);

            foreach ($items as $item) {
                if (empty(trim($item['service_name'] ?? ''))) continue;

                NursingInvestigationPaymentItem::create([
                    'payment_id'   => $payment->id,
                    'category'     => $item['category']       ?? 'Other',
                    'service_name' => $item['service_name'],
                    'unit_price'   => $item['unit_price']      ?? $item['amount'],
                    'quantity'     => $item['quantity']        ?? 1,
                    'discount'     => $item['item_discount']   ?? 0,
                    'amount'       => $item['amount'],
                    'remarks'      => $item['remarks']         ?? null,
                ]);
            }

            DB::commit();

            // Reload with items so the receipt can be built from the response
            $payment->load('items');

            return response()->json([
                'success'    => true,
                'message'    => 'Payment saved successfully.',
                'payment_id' => $payment->id,
                'data'       => $this->formatPayment($payment),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('InvestigationPayment store error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 3 — DETAIL / RECEIPT  (single payment)
    // ════════════════════════════════════════════════════════════

    /**
     * GET /nursing/InvestigationPayment/detail/{id}
     * Returns formatted payment + items for receipt rendering in JS
     */
    public function detail($id)
    {
        $payment = NursingInvestigationPayment::with('items')->find($id);

        if (! $payment) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatPayment($payment),
        ]);
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 4 — PATIENT PAYMENT HISTORY  (for a single admission)
    // ════════════════════════════════════════════════════════════

    /**
     * GET /nursing/InvestigationPayment/patient-payments/{patientId}
     * Used by Step 2 to show previous payments for the selected admission
     * Also used by release bill summary
     *
     * Accepts optional ?admission_id=X query param to filter by admission
     */
    public function patientPayments(Request $request, $patientId)
    {
        $query = NursingInvestigationPayment::with('items')
            ->where('patient_id', $patientId)
            ->where('payment_type', 'partial')
            ->orderBy('payment_date');

        // Filter by admission if provided
        if ($request->filled('admission_id')) {
            $query->where('admission_id', $request->admission_id);
        }

        $payments = $query->get();

        return response()->json([
            'success'    => true,
            'payments'   => $payments->map(fn($p) => $this->formatPayment($p)),
            'total_paid' => $payments->sum('paid_amount'),
            'total_due'  => $payments->sum('due_amount'),
        ]);
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 5 — LIST BY ADMISSION  (used by Blade JS loadPreviousPayments)
    // ════════════════════════════════════════════════════════════

    /**
     * GET /nursing/InvestigationPayment/by-admission/{admissionId}
     * Returns all payments for a specific admission_id
     * Called by loadPreviousPayments() in the Blade JS
     */
    public function byAdmission($admissionId)
    {
        $payments = NursingInvestigationPayment::with('items')
            ->where('admission_id', $admissionId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(
            $payments->map(fn($p) => $this->formatPayment($p))
        );
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 6 — DELETE
    // ════════════════════════════════════════════════════════════

    /**
     * DELETE /nursing/InvestigationPayment/delete/{id}
     */
    public function destroy($id)
    {
        $payment = NursingInvestigationPayment::find($id);

        if (! $payment) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found.',
            ], 404);
        }

        $payment->delete();  // cascade deletes items via DB foreign key or model boot

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 7 — OLD METHODS  (kept exactly, used by other modules)
    // ════════════════════════════════════════════════════════════

    /**
     * GET /nursing/InvestigationPayment/patient-data/{patientId}
     */
    public function getPatientData($patientId)
    {
        $patient = Patient::with('admissions')->find($patientId);

        if (! $patient) {
            return response()->json(['success' => false, 'message' => 'Patient not found']);
        }

        $admission = PatientAdmission::where('patient_id', $patientId)
            ->where('status', 'admitted')
            ->first();

        return response()->json([
            'success'   => true,
            'patient'   => $patient,
            'admission' => $admission,
        ]);
    }

    /**
     * GET /nursing/InvestigationPayment/investigations/{patientId}
     */
    public function getInvestigations($patientId)
    {
        $investigations = Investigation::where('patient_id', $patientId)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($inv) {
                $paid = $inv->payments()->sum('amount');
                return [
                    'id'             => $inv->id,
                    'type'           => $inv->type,
                    'type_label'     => $this->getInvestigationLabel($inv->type),
                    'date'           => $inv->date,
                    'charge'         => $inv->charge,
                    'paid_amount'    => $paid,
                    'due_amount'     => $inv->charge - $paid,
                    'payment_status' => $this->getPaymentStatus($inv->charge, $paid),
                    'status'         => $inv->status,
                    'notes'          => $inv->notes,
                ];
            });

        return response()->json(['success' => true, 'investigations' => $investigations]);
    }

    /**
     * GET /nursing/InvestigationPayment/payment-history/{patientId}
     */
    public function getPaymentHistory($patientId)
    {
        $payments = InvestigationPayment::where('patient_id', $patientId)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($pay) {
                return [
                    'id'                 => $pay->id,
                    'investigation_id'   => $pay->investigation_id,
                    'date'               => $pay->date,
                    'amount'             => $pay->amount,
                    'method'             => $pay->method,
                    'reference'          => $pay->reference,
                    'notes'              => $pay->notes,
                    'investigation_type' => $pay->investigation
                        ? $this->getInvestigationLabel($pay->investigation->type)
                        : null,
                ];
            });

        return response()->json(['success' => true, 'payments' => $payments]);
    }

    /**
     * POST /nursing/InvestigationPayment/record-payment
     */
    public function recordPayment(Request $request)
    {
        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'investigation_id' => 'required|exists:investigations,id',
            'amount'           => 'required|numeric|min:0.01',
            'date'             => 'required|date',
            'method'           => 'required|in:cash,cheque,bank_transfer,mobile_banking,card',
            'reference'        => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $investigation = Investigation::find($validated['investigation_id']);
        $paid          = $investigation->payments()->sum('amount');
        $totalPaid     = $paid + $validated['amount'];

        if ($totalPaid > $investigation->charge) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount exceeds investigation charge!',
            ], 422);
        }

        $payment = InvestigationPayment::create([
            'patient_id'       => $validated['patient_id'],
            'investigation_id' => $validated['investigation_id'],
            'amount'           => $validated['amount'],
            'date'             => $validated['date'],
            'method'           => $validated['method'],
            'reference'        => $validated['reference'],
            'notes'            => $validated['notes'],
            'recorded_by'      => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully!',
            'payment' => $payment,
        ]);
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 8 — FORMAT HELPER  (standardised JSON shape for JS)
    // ════════════════════════════════════════════════════════════

    /**
     * Returns a consistent array that the Blade JS receipt renderer expects.
     * Keys match what the JS functions (showReceipt, renderModalReceipt) read.
     */
    private function formatPayment(NursingInvestigationPayment $payment): array
    {
        return [
            'id'              => $payment->id,
            'receipt_no'      => $payment->receipt_no,
            'admission_id'    => $payment->admission_id,
            'patient_id'      => $payment->patient_id,
            'patient_name'    => $payment->patient_name,
            'patient_age'     => $payment->patient_age,
            'patient_code'    => $payment->patient_code,
            'mobile_no'       => $payment->mobile_no,
            'payment_date'    => $payment->payment_date,
            'payment_method'  => $payment->payment_method,
            'total_amount'    => (float) $payment->total_amount,   // net payable
            'gross_amount'    => (float) ($payment->gross_amount ?? $payment->total_amount),
            'discount_amount' => (float) ($payment->discount ?? 0),
            'paid_amount'     => (float) $payment->paid_amount,
            'due_amount'      => (float) $payment->due_amount,
            'status'          => $payment->status ?? 'due',
            'received_by'     => $payment->collected_by,           // blade uses received_by
            'notes'           => $payment->notes,
            'created_at'      => $payment->created_at,
            // items — each item maps to what JS receipt table expects
            'tests'           => ($payment->relationLoaded('items') ? $payment->items : collect())
                ->map(fn($i) => [
                    'category'  => $i->category,
                    'test_name' => $i->service_name,        // JS reads test_name
                    'price'     => (float) $i->unit_price,
                    'discount'  => (float) ($i->discount ?? 0),
                    'qty'       => (int)   $i->quantity,
                    'subtotal'  => (float) $i->amount,
                    'remarks'   => $i->remarks,
                ])->toArray(),
        ];
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 9 — PRESET SERVICES LIST
    // ════════════════════════════════════════════════════════════

    private function presetServices(): array
    {
        return $this->getServicesFromDatabase();
    }

    /**
     * Fetch investigation services and prices from database
     * Returns array of services with category, name, and price from investigations table
     */
    private function getServicesFromDatabase(): array
    {
        try {
            $investigations = DB::table('investigations')
                ->select('name', 'category', 'price')
                ->where('status', 'active')
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->toArray();

            $services = [];
            foreach ($investigations as $investigation) {
                $services[] = [
                    'category' => $investigation->category ?? 'Other',
                    'name'     => $investigation->name,
                    'price'    => (float) $investigation->price,
                ];
            }

            if (empty($services)) {
                return $this->getFallbackServices();
            }

            return $services;

        } catch (\Exception $e) {
            return $this->getFallbackServices();
        }
    }

    /**
     * GET /nursing/InvestigationPayment/get-investigations
     * Returns all active investigations from database for the frontend dropdown
     *
     * ✅ FIX: response()->json() sets Content-Type: application/json automatically.
     *         response()->header() does NOT exist on ResponseFactory — it only
     *         exists on a response object (JsonResponse). The old chaining
     *         response()->header()->json() caused the 500 error.
     */
    public function getInvestigationsFromDatabase()
    {
        try {
            // Clean any previous output
            if (ob_get_level()) {
                ob_clean();
            }

            \Log::info('getInvestigations: Attempting to fetch investigations');

            $investigations = DB::table('investigations')
                ->select('name', 'category', 'price')
                ->where('status', 'active')
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->toArray();

            \Log::info('getInvestigations: Found ' . count($investigations) . ' investigations');
            \Log::info('getInvestigations: Results: ' . json_encode($investigations));

            return response()->json($investigations);

        } catch (\Exception $e) {
            \Log::error('getInvestigations error: ' . $e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fallback hardcoded services if database query fails
     */
    private function getFallbackServices(): array
    {
        return [
            // X-Ray
            ['category' => 'X-Ray',     'name' => 'X-Ray Chest PA View',              'price' => 350],
            ['category' => 'X-Ray',     'name' => 'X-Ray Abdomen',                    'price' => 300],
            ['category' => 'X-Ray',     'name' => 'X-Ray Spine (LS)',                 'price' => 450],
            ['category' => 'X-Ray',     'name' => 'X-Ray KUB',                        'price' => 350],
            ['category' => 'X-Ray',     'name' => 'X-Ray Pelvis',                     'price' => 350],
            ['category' => 'X-Ray',     'name' => 'X-Ray Skull',                      'price' => 400],
            ['category' => 'X-Ray',     'name' => 'X-Ray Hand / Foot',                'price' => 300],
            // ECG
            ['category' => 'ECG',       'name' => 'ECG (12-Lead)',                    'price' => 250],
            ['category' => 'ECG',       'name' => 'Echo / 2D Echo',                  'price' => 1500],
            ['category' => 'ECG',       'name' => 'Holter Monitoring (24hr)',         'price' => 3000],
            // Pathology
            ['category' => 'Pathology', 'name' => 'CBC / Complete Blood Count',       'price' => 350],
            ['category' => 'Pathology', 'name' => 'Blood Glucose (FBS/RBS)',           'price' => 100],
            ['category' => 'Pathology', 'name' => 'Urine R/M/E',                      'price' => 150],
            ['category' => 'Pathology', 'name' => 'Serum Creatinine',                 'price' => 250],
            ['category' => 'Pathology', 'name' => 'Blood Urea',                       'price' => 200],
            ['category' => 'Pathology', 'name' => 'SGPT / ALT',                       'price' => 250],
            ['category' => 'Pathology', 'name' => 'SGOT / AST',                       'price' => 250],
            ['category' => 'Pathology', 'name' => 'HBsAg',                            'price' => 300],
            ['category' => 'Pathology', 'name' => 'VDRL',                             'price' => 200],
            ['category' => 'Pathology', 'name' => 'Blood Grouping',                   'price' => 150],
            ['category' => 'Pathology', 'name' => 'Serum Bilirubin (T/D)',            'price' => 300],
            ['category' => 'Pathology', 'name' => 'Thyroid Profile (TSH/T3/T4)',      'price' => 600],
            ['category' => 'Pathology', 'name' => 'Widal Test',                       'price' => 200],
            ['category' => 'Pathology', 'name' => 'Dengue NS1 / IgM/IgG',            'price' => 800],
            ['category' => 'Pathology', 'name' => 'Pregnancy Test (Urine)',           'price' => 100],
            ['category' => 'Pathology', 'name' => 'Coagulation Profile (PT/APTT)',    'price' => 500],
            // USG
            ['category' => 'USG',       'name' => 'USG Whole Abdomen',                'price' => 800],
            ['category' => 'USG',       'name' => 'USG Lower Abdomen',                'price' => 600],
            ['category' => 'USG',       'name' => 'USG Obstetric',                    'price' => 700],
            // Other
            ['category' => 'Other',     'name' => 'Consultation Fee',                 'price' => 500],
            ['category' => 'Other',     'name' => 'Procedure Charge',                 'price' => 0],
        ];
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 10 — PRIVATE HELPERS
    // ════════════════════════════════════════════════════════════

    private function getInvestigationLabel($type): string
    {
        $labels = [
            'xray'       => 'X-Ray (এক্স-রে)',
            'ecg'        => 'ECG (ই.সি.জি)',
            'ultrasound' => 'Ultrasound (আলট্রাসনোগ্রাফি)',
            'blood_test' => 'Blood Test (রক্ত পরীক্ষা)',
            'urine_test' => 'Urine Test (প্রস্রাব পরীক্ষা)',
            'ekg'        => 'EKG (ইকেজি)',
            'ct_scan'    => 'CT Scan (সিটি স্ক্যান)',
            'mri'        => 'MRI (এমআরআই)',
        ];

        return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    private function getPaymentStatus($charge, $paid): string
    {
        if ($paid == 0) return 'unpaid';
        if ($paid >= $charge) return 'paid';
        return 'partial';
    }
}