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
    // SECTION 1 — DIAGNOSTIC PAYMENT (X-Ray, ECG, Pathology)
    // ════════════════════════════════════════════════════════════

    /**
     * Main page — admitted patients list + recent payments
     */
    public function index(Request $request)
    {
        // ── Admitted patients ──────────────────────────────────
        // Blade expects: $admittedPatients
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
        // Blade expects: $pastPayments
        $pastPayments = NursingInvestigationPayment::with('items')
            ->orderByDesc('created_at')
            ->paginate(20);

        // ── Preset services ────────────────────────────────────
        // Blade expects: $categories (array of category name strings)
        // and $presetServices for JS
        $presetServices = $this->presetServices();

        // Extract unique category labels for the Quick-Add pills
        $categories = collect($presetServices)
            ->pluck('category')
            ->unique()
            ->values()
            ->toArray();

        return view('nursing.investigation_payment', compact(
            'admittedPatients',   // ← Blade uses $admittedPatients
            'pastPayments',       // ← Blade uses $pastPayments
            'categories',         // ← Blade uses $categories  (Quick-Add pills)
            'presetServices'      // ← available for JS inline data if needed
        ));
    }

    /**
     * Store diagnostic payment + items
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'           => 'required|integer',
            'items'                => 'required|array|min:1',
            'items.*.service_name' => 'required|string',
            'items.*.amount'       => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $items       = $request->input('items', []);
            $totalAmount = collect($items)->sum('amount');
            $discount    = (float) $request->input('discount', 0);
            $paidAmount  = (float) $request->input('paid_amount', $totalAmount - $discount);
            $dueAmount   = ($totalAmount - $discount) - $paidAmount;

            $payment = NursingInvestigationPayment::create([
                'admission_id'    => $request->input('admission_id'),
                'patient_id'      => $request->input('patient_id'),
                'patient_name'    => $request->input('patient_name'),
                'patient_code'    => $request->input('patient_code'),
                'patient_age'     => $request->input('patient_age'),
                'mobile_no'       => $request->input('mobile_no'),
                'payment_type'    => 'partial',
                'payment_date'    => $request->input('payment_date', now()->toDateString()),
                'collected_by'    => $request->input('collected_by'),
                'total_amount'    => $totalAmount,
                'discount'        => $discount,
                'paid_amount'     => $paidAmount,
                'due_amount'      => max(0, $dueAmount),
                'payment_method'  => $request->input('payment_method', 'cash'),
                'transaction_ref' => $request->input('transaction_ref'),
                'notes'           => $request->input('notes'),
            ]);

            foreach ($items as $item) {
                if (empty(trim($item['service_name'] ?? ''))) continue;

                NursingInvestigationPaymentItem::create([
                    'payment_id'   => $payment->id,
                    'category'     => $item['category']   ?? 'other',
                    'service_name' => $item['service_name'],
                    'unit_price'   => $item['unit_price']  ?? $item['amount'],
                    'quantity'     => $item['quantity']    ?? 1,
                    'amount'       => $item['amount'],
                    'remarks'      => $item['remarks']     ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'payment_id' => $payment->id,
                'message'    => 'Payment saved successfully.',
                'data'       => $payment->load('items'),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Single payment detail — receipt (AJAX)
     */
    public function detail($id)
    {
        $payment = NursingInvestigationPayment::with('items')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $payment,
        ]);
    }

    /**
     * All partial payments for a patient — used by release bill
     */
    public function patientPayments($patientId)
    {
        $payments = NursingInvestigationPayment::with('items')
            ->where('patient_id', $patientId)
            ->where('payment_type', 'partial')
            ->orderBy('payment_date')
            ->get();

        return response()->json([
            'success'    => true,
            'payments'   => $payments,
            'total_paid' => $payments->sum('paid_amount'),
            'total_due'  => $payments->sum('due_amount'),
        ]);
    }

    /**
     * Delete payment (cascade deletes items)
     */
    public function destroy($id)
    {
        NursingInvestigationPayment::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted.',
        ]);
    }

    // ════════════════════════════════════════════════════════════
    // SECTION 2 — OLD METHODS (পুরানো methods হুবহু রাখা হয়েছে)
    // ════════════════════════════════════════════════════════════

    public function getPatientData($patientId)
    {
        $patient = Patient::with('admissions')->find($patientId);

        if (!$patient) {
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
    // PRESET SERVICES LIST
    // ════════════════════════════════════════════════════════════

    private function presetServices(): array
    {
        return [
            // X-Ray
            ['category' => 'X-Ray',     'name' => 'X-Ray Chest PA View',         'price' => 350],
            ['category' => 'X-Ray',     'name' => 'X-Ray Abdomen',                'price' => 300],
            ['category' => 'X-Ray',     'name' => 'X-Ray Spine (LS)',             'price' => 450],
            ['category' => 'X-Ray',     'name' => 'X-Ray KUB',                    'price' => 350],
            ['category' => 'X-Ray',     'name' => 'X-Ray Pelvis',                 'price' => 350],
            ['category' => 'X-Ray',     'name' => 'X-Ray Skull',                  'price' => 400],
            ['category' => 'X-Ray',     'name' => 'X-Ray Hand / Foot',            'price' => 300],
            // ECG
            ['category' => 'ECG',       'name' => 'ECG (12-Lead)',                'price' => 200],
            ['category' => 'ECG',       'name' => 'Echo / 2D Echo',              'price' => 1500],
            ['category' => 'ECG',       'name' => 'Holter Monitoring (24hr)',    'price' => 3000],
            // Pathology
            ['category' => 'Pathology', 'name' => 'CBC / Complete Blood Count',  'price' => 350],
            ['category' => 'Pathology', 'name' => 'Blood Glucose (FBS/RBS)',      'price' => 100],
            ['category' => 'Pathology', 'name' => 'Urine R/M/E',                 'price' => 150],
            ['category' => 'Pathology', 'name' => 'Serum Creatinine',            'price' => 250],
            ['category' => 'Pathology', 'name' => 'Blood Urea',                  'price' => 200],
            ['category' => 'Pathology', 'name' => 'SGPT / ALT',                  'price' => 250],
            ['category' => 'Pathology', 'name' => 'SGOT / AST',                  'price' => 250],
            ['category' => 'Pathology', 'name' => 'HBsAg',                       'price' => 300],
            ['category' => 'Pathology', 'name' => 'VDRL',                        'price' => 200],
            ['category' => 'Pathology', 'name' => 'Blood Grouping',              'price' => 150],
            ['category' => 'Pathology', 'name' => 'Serum Bilirubin (T/D)',       'price' => 300],
            ['category' => 'Pathology', 'name' => 'Thyroid Profile (TSH/T3/T4)', 'price' => 600],
            ['category' => 'Pathology', 'name' => 'Widal Test',                  'price' => 200],
            ['category' => 'Pathology', 'name' => 'Dengue NS1 / IgM/IgG',       'price' => 800],
            ['category' => 'Pathology', 'name' => 'Pregnancy Test (Urine)',      'price' => 100],
            ['category' => 'Pathology', 'name' => 'Coagulation Profile (PT/APTT)','price'=> 500],
            // USG
            ['category' => 'USG',       'name' => 'USG Whole Abdomen',           'price' => 800],
            ['category' => 'USG',       'name' => 'USG Lower Abdomen',           'price' => 600],
            ['category' => 'USG',       'name' => 'USG Obstetric',               'price' => 700],
            // Other
            ['category' => 'Other',     'name' => 'Consultation Fee',            'price' => 500],
            ['category' => 'Other',     'name' => 'Procedure Charge',            'price' => 0],
        ];
    }

    // ════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
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