<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateInvoiceController extends Controller
{
    // ============================================================
    // GET /Billing/CreateInvoice
    // ============================================================
    public function index()
    {
        $invTypes = DB::table('configMain')
            ->select('Code', 'Name')
            ->orderBy('Name')
            ->get();

        return view('billing.create_invoice_index', compact('invTypes'));
    }

    // ============================================================
    // GET /Billing/CreateInvoice/ajax/bill-types
    // ============================================================
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
            \Log::warning('bill_types table not found, using fallback: ' . $e->getMessage());
            return response()->json($fallback);
        }
    }

    // ============================================================
    // GET /Billing/CreateInvoice/ajax/main-categories
    // ============================================================
    public function getMain()
    {
        $data = DB::table('configMain')
            ->select('Code', 'Name')
            ->orderBy('Name')
            ->get();

        return response()->json($data);
    }

    // ============================================================
    // GET /Billing/CreateInvoice/ajax/sub-categories?main_code=X
    // ============================================================
    public function getSub(Request $request)
    {
        $data = DB::table('configSub')
            ->select('Code', 'MainCode', 'Name', 'Amount')
            ->where('MainCode', $request->input('main_code'))
            ->orderBy('Name')
            ->get();

        return response()->json($data);
    }

    // ============================================================
    // GET /Billing/CreateInvoice/ajax/get-doctors
    // ============================================================
    public function getDoctors()
    {
        $data = DB::table('doctors')
            ->select('id', 'doctor_name as name', 'speciality', 'contact')
            ->where('active', 1)
            ->orderBy('doctor_name')
            ->get();

        return response()->json($data);
    }

    // ============================================================
    // GET /Billing/CreateInvoice/ajax/search-patient?q=X
    // ============================================================
    public function searchPatient(Request $request)
    {
        $q = '%' . $request->input('q', '') . '%';

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
                      ->orWhere('p.mobile_no',   'like', $q);
            })
            ->orderByDesc('p.id')
            ->limit(20)
            ->get();

        return response()->json($data);
    }

    // ============================================================
    // POST /Billing/CreateInvoice/ajax/add-tmp
    // ============================================================
    public function addTmp(Request $request)
    {
        try {
            $id = DB::table('tbl_bill_tmp')->insertGetId([
                'PatientCode' => (int) $request->input('patient_code', 0),
                'MainCode'    => (int) $request->input('main_code'),
                'SubCode'     => (int) $request->input('sub_code'),
                'Name'        => (int) $request->input('sub_code'),
                'Amount'      => (int) $request->input('amount', 0),
                'BillNo'      => 0,
                'InputerID'   => (int) (auth()->id() ?? 0),
                'Status'      => 1,
            ]);

            return response()->json(['success' => true, 'tmp_id' => $id]);
        } catch (\Throwable $e) {
            \Log::error('addTmp: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============================================================
    // DELETE /Billing/CreateInvoice/ajax/remove-tmp
    // ============================================================
    public function removeTmp(Request $request)
    {
        DB::table('tbl_bill_tmp')->where('ID', $request->input('tmp_id'))->delete();
        return response()->json(['success' => true]);
    }

    // ============================================================
    // POST /Billing/CreateInvoice/ajax/clear-tmp
    // ============================================================
    public function clearTmp(Request $request)
    {
        DB::table('tbl_bill_tmp')
            ->where('PatientCode', $request->input('patient_code', 0))
            ->delete();

        return response()->json(['success' => true]);
    }

    // ============================================================
    // GET /Billing/CreateInvoice/ajax/get-collectors
    // ============================================================
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
            \Log::error('getCollectors: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    // ============================================================
    // GET /Billing/CreateInvoice/ajax/get-tmp?patient_code=X
    // ============================================================
    public function getTmp(Request $request)
    {
        $data = DB::table('tbl_bill_tmp')
            ->where('PatientCode', $request->input('patient_code', 0))
            ->get();

        return response()->json($data);
    }

    // ============================================================
    // GET /Billing/CreateInvoice/ajax/list
    // ✅ Patient-grouped: একই patient এর সব invoice merge করে একটা row দেখায়
    // ============================================================
    public function list(Request $request)
    {
        $query = DB::table('investigation_payments')
            ->select(
                'PatientId',
                'PatientCode',
                'PatientName',
                'MobileNo',
                DB::raw('SUM(TotalBill)   AS TotalBill'),
                DB::raw('SUM(Discount)    AS Discount'),
                DB::raw('SUM(PaidAmount)  AS PaidAmount'),
                DB::raw('SUM(DueAmount)   AS DueAmount'),
                DB::raw('MAX(PaymentDate) AS PaymentDate'),
                DB::raw('MAX(ID)          AS ID'),
                DB::raw('GROUP_CONCAT(BillNo ORDER BY ID SEPARATOR ", ") AS BillNo'),
                DB::raw('COUNT(*)         AS invoice_count'),
                DB::raw("
                    CASE
                        WHEN SUM(DueAmount) <= 0             THEN 'paid'
                        WHEN SUM(PaidAmount) > 0             THEN 'partial'
                        ELSE 'due'
                    END AS Status
                ")
            )
            ->groupBy('PatientId', 'PatientCode', 'PatientName', 'MobileNo')
            ->when($request->q, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('PatientName', 'like', '%' . $request->q . '%')
                       ->orWhere('PatientCode', 'like', '%' . $request->q . '%')
                       ->orWhere('MobileNo',    'like', '%' . $request->q . '%')
                       ->orWhere('BillNo',      'like', '%' . $request->q . '%');
                });
            })
            ->when(!$request->show_all, function ($q) {
                // GROUP BY এর পরে filter → having ব্যবহার করতে হবে, where না
                $q->having('DueAmount', '>', 0);
            })
            ->orderByDesc('ID');

        $paginated = $query->paginate($request->per_page ?? 20);

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

    // ============================================================
    // POST /Billing/CreateInvoice/store
    // ============================================================
    public function store(Request $request)
    {
        $request->validate([
            'patient_id'     => 'required|integer|exists:patients,id',
            'total_bill'     => 'required|numeric|min:1',
            'paid_amount'    => 'required|numeric|min:0',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:cash,mobile_banking,card,bank_transfer,cheque',
        ]);

        $totalBill   = (float) $request->total_bill;
        $paidAmount  = (float) $request->paid_amount;
        $discount    = (float) $request->input('discount', 0);
        $netAmount   = max(0, $totalBill - $discount);
        $minRequired = ceil($netAmount * 0.25);

        if ($paidAmount < $minRequired) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum 25% required. Please pay at least ৳ ' . number_format($minRequired, 0) . '.',
            ], 422);
        }

        $dueAmount = max(0, $netAmount - $paidAmount);
        $status    = 'due';
        if ($paidAmount >= $netAmount) $status = 'paid';
        elseif ($paidAmount > 0)       $status = 'partial';

        $last   = DB::table('investigation_payments')->orderByDesc('ID')->value('BillNo');
        $num    = $last ? (intval(substr($last, 4)) + 1) : 1;
        $billNo = 'INV-' . str_pad($num, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $paymentId = DB::table('investigation_payments')->insertGetId([
                'BillNo'        => $billNo,
                'PatientId'     => $request->patient_id,
                'PatientCode'   => $request->patient_code,
                'PatientName'   => $request->patient_name,
                'PatientAge'    => $request->patient_age,
                'MobileNo'      => $request->mobile_no,
                'AdmissionId'   => $request->input('admission_id') ?: null,
                'TotalBill'     => $totalBill,
                'Discount'      => $discount,
                'PaidAmount'    => $paidAmount,
                'DueAmount'     => $dueAmount,
                'PaymentDate'   => $request->payment_date,
                'PaymentMethod' => $request->payment_method,
                'CollectedBy'   => $request->input('collected_by'),
                'Status'        => $status,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            foreach ($request->input('items', []) as $item) {
                if (empty(trim($item['service_name'] ?? ''))) continue;
                DB::table('investigation_payment_items')->insert([
                    'PaymentId'    => $paymentId,
                    'CategoryCode' => $item['category']      ?? null,
                    'CategoryName' => $item['category_name'] ?? null,
                    'ServiceName'  => $item['service_name'],
                    'UnitPrice'    => $item['unit_price']    ?? 0,
                    'Quantity'     => $item['quantity']      ?? 1,
                    'Amount'       => $item['amount']        ?? 0,
                    'Remarks'      => $item['remarks']       ?? null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }

            DB::table('tbl_bill_tmp')
                ->where('PatientCode', $request->patient_code)
                ->delete();

            DB::commit();

            return response()->json([
                'success'    => true,
                'payment_id' => $paymentId,
                'bill_no'    => $billNo,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('CreateInvoice store: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save. Please try again.'], 500);
        }
    }

    // ============================================================
    // GET /Billing/CreateInvoice/{id}/print
    // ============================================================
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

    // ============================================================
    // GET /Billing/CreateInvoice/{patientId}
    // ============================================================
    public function show($patientId)
    {
        $patient = DB::table('patients')->where('id', $patientId)->first();
        if (!$patient) abort(404);

        $admission = DB::table('nursing_admissions')
            ->where('patient_id', $patientId)
            ->orderByDesc('id')
            ->first();

        return view('billing.create_invoice_show', compact('patient', 'admission'));
    }
}