<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateInvoiceController extends Controller
{
    private const MIN_ADVANCE_RATIO = 0.25;

    // GET /Billing/CreateInvoice
    public function index()
    {
        $invTypes = DB::table('configMain')
            ->select('Code', 'Name')
            ->orderBy('Name')
            ->get();

        return view('billing.create_invoice_index', compact('invTypes'));
    }

    // GET /Billing/CreateInvoice/ajax/bill-types
    public function getBillTypes()
    {
        $fallback = [
            [
                'value'             => 'doctor_visit',
                'label'             => 'Doctor Visit',
                'icon'              => 'fa-user-md',
                'color'             => '#2563eb',
                'main_code'         => null,
                'source'            => 'fallback',
                'requires_doctor'   => true,
                'requires_category' => false,
                'free_text_items'   => false,
            ],
            [
                'value'             => 'investigation',
                'label'             => 'Investigation',
                'icon'              => 'fa-microscope',
                'color'             => '#059669',
                'main_code'         => null,
                'source'            => 'fallback',
                'requires_doctor'   => false,
                'requires_category' => true,
                'free_text_items'   => false,
            ],
            [
                'value'             => 'full_bill',
                'label'             => 'Full Bill',
                'icon'              => 'fa-file-invoice',
                'color'             => '#dc2626',
                'main_code'         => null,
                'source'            => 'fallback',
                'requires_doctor'   => false,
                'requires_category' => false,
                'free_text_items'   => true,
            ],
        ];

        try {
            $rows = DB::table('bill_types')
                ->where('is_active', 1)
                ->orderBy('sort_order')
                ->get();

            if ($rows->isEmpty()) {
                return response()->json($fallback);
            }

            $types = $rows->map(function ($row) {
                return [
                    'value'             => $row->slug,
                    'label'             => $row->name,
                    'icon'              => $row->icon,
                    'color'             => $row->color,
                    'main_code'         => $row->main_code,
                    'source'            => 'bill_types',
                    'requires_doctor'   => (bool) $row->requires_doctor,
                    'requires_category' => (bool) $row->requires_category,
                    'free_text_items'   => (bool) $row->free_text_items,
                ];
            });

            return response()->json($types);
        } catch (\Throwable $e) {
            Log::warning('bill_types lookup failed, using fallback: ' . $e->getMessage());
            return response()->json($fallback);
        }
    }

    // GET /Billing/CreateInvoice/ajax/main-categories
    public function getMain()
    {
        $data = DB::table('configMain')
            ->select('Code', 'Name')
            ->orderBy('Name')
            ->get();

        return response()->json($data);
    }

    // GET /Billing/CreateInvoice/ajax/sub-categories?main_code=X
    public function getSub(Request $request)
    {
        $validated = $request->validate([
            'main_code' => 'required|integer|min:1',
        ]);

        $data = DB::table('configSub')
            ->select('Code', 'MainCode', 'Name', 'Amount')
            ->where('MainCode', (int) $validated['main_code'])
            ->orderBy('Name')
            ->get();

        return response()->json($data);
    }

    // GET /Billing/CreateInvoice/ajax/get-doctors
    public function getDoctors()
    {
        $data = DB::table('doctors')
            ->select('id', 'doctor_name as name', 'speciality', 'contact')
            ->where('active', 1)
            ->orderBy('doctor_name')
            ->get();

        return response()->json($data);
    }

    // GET /Billing/CreateInvoice/ajax/search-patient?q=X
    public function searchPatient(Request $request)
    {
        $q = '%' . trim((string) $request->input('q', '')) . '%';

        $data = DB::table('patients as p')
            ->leftJoin('nursing_admissions as na', function ($join) {
                $join->on('na.patient_id', '=', 'p.id')
                    ->whereRaw('na.id = (SELECT MAX(id) FROM nursing_admissions WHERE patient_id = p.id)');
            })
            ->select(
                'p.id',
                'p.patientcode',
                'p.patientname',
                'p.age',
                'p.gender',
                'p.mobile_no',
                'na.id as admission_id'
            )
            ->where(function ($query) use ($q) {
                $query->where('p.patientname', 'like', $q)
                    ->orWhere('p.patientcode', 'like', $q)
                    ->orWhere('p.mobile_no', 'like', $q);
            })
            ->orderByDesc('p.id')
            ->limit(20)
            ->get();

        return response()->json($data);
    }

    // POST /Billing/CreateInvoice/ajax/add-tmp
    public function addTmp(Request $request)
    {
        $validated = $request->validate([
            'patient_code' => 'required|string|max:60',
            'main_code'    => 'required|integer|min:1',
            'sub_code'     => 'required|integer|min:1',
            'name'         => 'required|string|max:255',
            'amount'       => 'required|numeric|gt:0',
        ]);

        try {
            $id = DB::table('tbl_bill_tmp')->insertGetId([
                'PatientCode' => $validated['patient_code'],
                'MainCode'    => (int) $validated['main_code'],
                'SubCode'     => (int) $validated['sub_code'],
                'Name'        => trim($validated['name']),
                'Amount'      => round((float) $validated['amount'], 2),
                'BillNo'      => 0,
                'InputerID'   => (int) (auth()->id() ?? 0),
                'Status'      => 1,
            ]);

            return response()->json(['success' => true, 'tmp_id' => $id]);
        } catch (\Throwable $e) {
            Log::error('addTmp failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to add item.'], 500);
        }
    }

    // DELETE /Billing/CreateInvoice/ajax/remove-tmp
    public function removeTmp(Request $request)
    {
        $validated = $request->validate([
            'tmp_id' => 'required|integer|min:1',
        ]);

        DB::table('tbl_bill_tmp')
            ->where('ID', (int) $validated['tmp_id'])
            ->where('InputerID', (int) (auth()->id() ?? 0))
            ->delete();

        return response()->json(['success' => true]);
    }

    // POST /Billing/CreateInvoice/ajax/clear-tmp
    public function clearTmp(Request $request)
    {
        $validated = $request->validate([
            'patient_code' => 'required|string|max:60',
        ]);

        DB::table('tbl_bill_tmp')
            ->where('PatientCode', $validated['patient_code'])
            ->where('InputerID', (int) (auth()->id() ?? 0))
            ->delete();

        return response()->json(['success' => true]);
    }

    // GET /Billing/CreateInvoice/ajax/get-collectors
    public function getCollectors()
    {
        try {
            $data = DB::table('patient_ref')
                ->select('Name')
                ->where('ref_type', 'OfficeEmployee')
                ->where('active', 1)
                ->orderBy('Name')
                ->get();

            return response()->json($data);
        } catch (\Throwable $e) {
            Log::error('getCollectors failed: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    // GET /Billing/CreateInvoice/ajax/get-tmp?patient_code=X
    public function getTmp(Request $request)
    {
        $validated = $request->validate([
            'patient_code' => 'required|string|max:60',
        ]);

        $data = DB::table('tbl_bill_tmp')
            ->where('PatientCode', $validated['patient_code'])
            ->where('InputerID', (int) (auth()->id() ?? 0))
            ->get();

        return response()->json($data);
    }

    // GET /Billing/CreateInvoice/ajax/list
    public function list(Request $request)
    {
        $query = DB::table('investigation_payments')
            ->select(
                'PatientId',
                'PatientCode',
                'PatientName',
                'MobileNo',
                DB::raw('SUM(TotalBill) AS TotalBill'),
                DB::raw('SUM(Discount) AS Discount'),
                DB::raw('SUM(PaidAmount) AS PaidAmount'),
                DB::raw('SUM(DueAmount) AS DueAmount'),
                DB::raw('MAX(PaymentDate) AS PaymentDate'),
                DB::raw('MAX(ID) AS ID'),
                DB::raw('GROUP_CONCAT(BillNo ORDER BY ID SEPARATOR ", ") AS BillNo'),
                DB::raw('COUNT(*) AS invoice_count'),
                DB::raw("
                    CASE
                        WHEN SUM(DueAmount) <= 0 THEN 'paid'
                        WHEN SUM(PaidAmount) > 0 THEN 'partial'
                        ELSE 'due'
                    END AS Status
                ")
            )
            ->groupBy('PatientId', 'PatientCode', 'PatientName', 'MobileNo')
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = trim((string) $request->q);
                $q->where(function ($q2) use ($search) {
                    $q2->where('PatientName', 'like', '%' . $search . '%')
                        ->orWhere('PatientCode', 'like', '%' . $search . '%')
                        ->orWhere('MobileNo', 'like', '%' . $search . '%')
                        ->orWhere('BillNo', 'like', '%' . $search . '%');
                });
            })
            ->when(!$request->boolean('show_all'), function ($q) {
                $q->having('DueAmount', '>', 0);
            })
            ->orderByDesc('ID');

        $paginated = $query->paginate($request->input('per_page', 20));

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'total'        => $paginated->total(),
                'per_page'     => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ],
        ]);
    }

    // POST /Billing/CreateInvoice/store
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'            => 'required|integer|exists:patients,id',
            'patient_code'          => 'required|string|max:60',
            'patient_name'          => 'required|string|max:200',
            'patient_age'           => 'nullable|string|max:30',
            'mobile_no'             => 'nullable|string|max:30',
            'admission_id'          => 'nullable|integer|min:1',
            'total_bill'            => 'required|numeric|gt:0',
            'discount'              => 'nullable|numeric|min:0',
            'paid_amount'           => 'required|numeric|min:0',
            'payment_date'          => 'required|date',
            'payment_method'        => 'required|in:cash,mobile_banking,card,bank_transfer,cheque',
            'collected_by'          => 'nullable|string|max:100',
            'items'                 => 'required|array|min:1',
            'items.*.category'      => 'nullable|integer',
            'items.*.category_name' => 'nullable|string|max:255',
            'items.*.service_name'  => 'required|string|max:255',
            'items.*.unit_price'    => 'required|numeric|min:0',
            'items.*.quantity'      => 'required|numeric|gte:1',
            'items.*.amount'        => 'required|numeric|gt:0',
            'items.*.remarks'       => 'nullable|string|max:500',
        ]);

        $totalBill = round((float) $request->input('total_bill'), 2);
        $discount = round((float) $request->input('discount', 0), 2);
        $paidAmount = round((float) $request->input('paid_amount', 0), 2);

        if ($discount > $totalBill) {
            return response()->json([
                'success' => false,
                'message' => 'Discount cannot be greater than total bill.',
            ], 422);
        }

        $netAmount = round($totalBill - $discount, 2);
        if ($netAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Net payable must be greater than zero.',
            ], 422);
        }

        if ($paidAmount > $netAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Paid amount cannot exceed net payable.',
            ], 422);
        }

        $minRequired = (float) ceil($netAmount * self::MIN_ADVANCE_RATIO);
        if ($paidAmount < $minRequired) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum 25% required. Please pay at least BDT ' . number_format($minRequired, 0) . '.',
            ], 422);
        }

        $cleanItems = [];
        $itemsTotal = 0.0;

        foreach ((array) $request->input('items', []) as $item) {
            $serviceName = trim((string) ($item['service_name'] ?? ''));
            if ($serviceName === '') {
                continue;
            }

            $unitPrice = round((float) ($item['unit_price'] ?? 0), 2);
            $quantity = round((float) ($item['quantity'] ?? 1), 2);
            $amount = round((float) ($item['amount'] ?? 0), 2);
            $calculated = round($unitPrice * $quantity, 2);

            if (abs($calculated - $amount) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => "Item amount mismatch for '{$serviceName}'.",
                ], 422);
            }

            $itemsTotal += $amount;
            $cleanItems[] = [
                'category'      => isset($item['category']) ? (int) $item['category'] : null,
                'category_name' => trim((string) ($item['category_name'] ?? '')),
                'service_name'  => $serviceName,
                'unit_price'    => $unitPrice,
                'quantity'      => $quantity,
                'amount'        => $amount,
                'remarks'       => trim((string) ($item['remarks'] ?? '')),
            ];
        }

        if (empty($cleanItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Please add at least one valid item.',
            ], 422);
        }

        if (abs(round($itemsTotal, 2) - $totalBill) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Item total does not match invoice total.',
            ], 422);
        }

        $dueAmount = round(max(0, $netAmount - $paidAmount), 2);
        $status = 'due';
        if ($dueAmount <= 0) {
            $status = 'paid';
        } elseif ($paidAmount > 0) {
            $status = 'partial';
        }

        DB::beginTransaction();
        try {
            $lastBillNo = DB::table('investigation_payments')
                ->lockForUpdate()
                ->orderByDesc('ID')
                ->value('BillNo');

            $billNo = $this->nextBillNo((string) $lastBillNo);

            $paymentId = DB::table('investigation_payments')->insertGetId([
                'BillNo'        => $billNo,
                'PatientId'     => (int) $request->input('patient_id'),
                'PatientCode'   => $request->input('patient_code'),
                'PatientName'   => $request->input('patient_name'),
                'PatientAge'    => $request->input('patient_age'),
                'MobileNo'      => $request->input('mobile_no'),
                'AdmissionId'   => $request->filled('admission_id') ? (int) $request->input('admission_id') : null,
                'TotalBill'     => $totalBill,
                'Discount'      => $discount,
                'PaidAmount'    => $paidAmount,
                'DueAmount'     => $dueAmount,
                'PaymentDate'   => $request->input('payment_date'),
                'PaymentMethod' => $request->input('payment_method'),
                'CollectedBy'   => $request->input('collected_by'),
                'Status'        => $status,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            foreach ($cleanItems as $item) {
                DB::table('investigation_payment_items')->insert([
                    'PaymentId'    => $paymentId,
                    'CategoryCode' => $item['category'],
                    'CategoryName' => $item['category_name'] !== '' ? $item['category_name'] : null,
                    'ServiceName'  => $item['service_name'],
                    'UnitPrice'    => $item['unit_price'],
                    'Quantity'     => $item['quantity'],
                    'Amount'       => $item['amount'],
                    'Remarks'      => $item['remarks'] !== '' ? $item['remarks'] : null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            DB::table('tbl_bill_tmp')
                ->where('PatientCode', $request->input('patient_code'))
                ->where('InputerID', (int) (auth()->id() ?? 0))
                ->delete();

            DB::commit();

            return response()->json([
                'success'    => true,
                'payment_id' => $paymentId,
                'bill_no'    => $billNo,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CreateInvoice store failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save. Please try again.',
            ], 500);
        }
    }

    // GET /Billing/CreateInvoice/{id}/print
    public function printInvoice($id)
    {
        $payment = DB::table('investigation_payments')->where('ID', $id)->first();

        if (!$payment) {
            abort(404, 'Invoice not found.');
        }

        $items = DB::table('investigation_payment_items')
            ->where('PaymentId', $id)
            ->get();

        return view('billing.invoice_print', compact('payment', 'items'));
    }

    // GET /Billing/CreateInvoice/{patientId}
    public function show($patientId)
    {
        $patient = DB::table('patients')->where('id', $patientId)->first();
        if (!$patient) {
            abort(404);
        }

        $admission = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->orderByDesc('id')
            ->first();

        return view('billing.create_invoice_show', compact('patient', 'admission'));
    }

    private function nextBillNo(string $lastBillNo): string
    {
        $next = 1;
        if ($lastBillNo !== '' && preg_match('/(\d+)$/', $lastBillNo, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'INV-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
