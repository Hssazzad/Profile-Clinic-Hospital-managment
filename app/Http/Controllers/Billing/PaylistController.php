<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaylistController extends Controller
{
    // ============================================================
    // GET /Billing/Paylist
    // ============================================================
    public function index()
    {
        return view('billing.paylist.index');
    }

    // ============================================================
    // AJAX: Payment List (Date grouped, paginated)
    // POST /Billing/Paylist/list
    // ============================================================
    public function list(Request $request)
    {
        $perPage    = (int) ($request->input('per_page', 20));
        $q          = trim((string) $request->input('q', ''));
        $patientId  = $request->input('patient_id');
        $dateMode   = $request->input('date_mode', 'all'); // default: 'all' (no date filter)
        $dateColumn = $request->input('date_column', 'PaymentDate');
        $singleDate = $request->input('single_date', now()->toDateString());
        $dateFrom   = $request->input('date_from', now()->toDateString());
        $dateTo     = $request->input('date_to', now()->toDateString());

        // Validate date_column
        if (!in_array($dateColumn, ['PaymentDate', 'created_at'])) {
            $dateColumn = 'PaymentDate';
        }

        $query = DB::table('investigation_payments')
            ->select(
                'ID',
                'BillNo',
                'PatientId',
                'PatientCode',
                'PatientName',
                'MobileNo',
                'TotalBill',
                'Discount',
                'PaidAmount',
                'DueAmount',
                'PaymentDate',
                'created_at',
                DB::raw("DATE({$dateColumn}) as GroupDate")
            );

        // -- Date Filter ------------------------------------------
        // 'all' = no date filter (show all time)
        if ($dateMode === 'range') {
            $query->whereDate($dateColumn, '>=', $dateFrom)
                  ->whereDate($dateColumn, '<=', $dateTo);
        } elseif ($dateMode === 'single') {
            $query->whereDate($dateColumn, $singleDate);
        }
        // 'all' => no date where clause — show everything

        // -- Patient Filter ---------------------------------------
        if ($patientId) {
            $query->where('PatientId', $patientId);
        }

        // -- Text Search ------------------------------------------
        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(function ($q2) use ($like) {
                $q2->where('PatientName', 'like', $like)
                   ->orWhere('PatientCode', 'like', $like)
                   ->orWhere('MobileNo',    'like', $like)
                   ->orWhere('BillNo',      'like', $like);
            });
        }

        $query->orderByDesc($dateColumn)->orderByDesc('ID');

        $paginated = $query->paginate($perPage);

        // -- Group by Date ----------------------------------------
        $grouped = [];
        foreach ($paginated->items() as $row) {
            $grouped[$row->GroupDate][] = $row;
        }

        return response()->json([
            'data'    => $grouped,
            'flat'    => $paginated->items(),
            'meta'    => [
                'total'        => $paginated->total(),
                'per_page'     => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'from'         => $paginated->firstItem() ?? 0,
                'to'           => $paginated->lastItem()  ?? 0,
            ],
        ]);
    }

    // ============================================================
    // AJAX: Summary Strip
    // POST /Billing/Paylist/summary
    // ============================================================
    public function summary(Request $request)
    {
        $patientId  = $request->input('patient_id');
        $dateMode   = $request->input('date_mode', 'all'); // default: 'all'
        $dateColumn = $request->input('date_column', 'PaymentDate');
        $singleDate = $request->input('single_date', now()->toDateString());
        $dateFrom   = $request->input('date_from', now()->toDateString());
        $dateTo     = $request->input('date_to', now()->toDateString());

        if (!in_array($dateColumn, ['PaymentDate', 'created_at'])) {
            $dateColumn = 'PaymentDate';
        }

        $query = DB::table('investigation_payments');

        // -- Date Filter ------------------------------------------
        if ($dateMode === 'range') {
            $query->whereDate($dateColumn, '>=', $dateFrom)
                  ->whereDate($dateColumn, '<=', $dateTo);
        } elseif ($dateMode === 'single') {
            $query->whereDate($dateColumn, $singleDate);
        }
        // 'all' => no date filter

        // -- Patient Filter ---------------------------------------
        if ($patientId) {
            $query->where('PatientId', $patientId);
        }

        $summary = $query->selectRaw('
            COUNT(*) as total_invoices,
            COUNT(DISTINCT PatientId) as total_patients,
            SUM(TotalBill)  as total_bill,
            SUM(PaidAmount) as total_paid,
            SUM(DueAmount)  as total_due,
            SUM(Discount)   as total_discount
        ')->first();

        return response()->json([
            'total_invoices'  => (int)   ($summary->total_invoices  ?? 0),
            'total_patients'  => (int)   ($summary->total_patients  ?? 0),
            'total_bill'      => (float) ($summary->total_bill      ?? 0),
            'total_paid'      => (float) ($summary->total_paid      ?? 0),
            'total_due'       => (float) ($summary->total_due       ?? 0),
            'total_discount'  => (float) ($summary->total_discount  ?? 0),
        ]);
    }
}