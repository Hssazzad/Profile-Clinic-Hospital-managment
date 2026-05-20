<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DuelistFinalController extends Controller
{
    // Summary cache key — একটাই জায়গায় রাখা হয়েছে
    private const SUMMARY_CACHE_KEY = 'duelist_summary';
    private const SUMMARY_CACHE_TTL = 300;

    public function index()
    {
        return view('billing.duelist_final.index');
    }

    // -------------------------------------------------------
    // Cache clear — PaynowController থেকে call হয়
    // -------------------------------------------------------
    public static function clearSummaryCache(): void
    {
        Cache::forget(self::SUMMARY_CACHE_KEY);
    }

    // -------------------------------------------------------
    // Due List (Paginated)
    // -------------------------------------------------------
    public function list(Request $request)
    {
        $perPage = (int) ($request->input('per_page', 20));
        $q       = trim((string) $request->input('q', ''));
        $showAll = $request->boolean('show_all');

        $query = DB::table('investigation_payments as ip')
            ->join('patients as p', 'p.id', '=', 'ip.PatientId')
            ->select(
                'p.id as patient_id',
                'p.patientname as PatientName',
                'p.patientcode as PatientCode',
                'p.mobile_no as MobileNo',
                DB::raw('GROUP_CONCAT(ip.BillNo ORDER BY ip.ID SEPARATOR ", ") as BillNo'),
                DB::raw('MAX(ip.PaymentDate) as PaymentDate'),
                DB::raw('SUM(ip.TotalBill) as TotalBill'),
                DB::raw('SUM(IFNULL(ip.Discount,0)) as Discount'),
                DB::raw('SUM(IFNULL(ip.PaidAmount,0)) as PaidAmount'),
                DB::raw('SUM(IFNULL(ip.DueAmount,0)) as DueAmount'),
                DB::raw('SUM(ip.TotalBill) - SUM(IFNULL(ip.Discount,0)) as NetBill'),
                DB::raw('CASE
                    WHEN SUM(IFNULL(ip.DueAmount,0)) <= 0 THEN "paid"
                    WHEN SUM(IFNULL(ip.PaidAmount,0)) > 0 THEN "partial"
                    ELSE "due"
                 END as Status')
            )
            ->groupBy('p.id', 'p.patientname', 'p.patientcode', 'p.mobile_no');

        if (!$showAll) {
            $query->having('DueAmount', '>', 0);
        }

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($qb) use ($like) {
                $qb->where('p.patientname',  'like', $like)
                   ->orWhere('p.patientcode', 'like', $like)
                   ->orWhere('p.mobile_no',   'like', $like)
                   ->orWhere('ip.BillNo',     'like', $like);
            });
        }

        $query->orderByDesc('DueAmount');

        $paginated = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $paginated->items(),
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

    // -------------------------------------------------------
    // Summary Cards
    // -------------------------------------------------------
    public function summary()
    {
        $data = Cache::remember(self::SUMMARY_CACHE_KEY, self::SUMMARY_CACHE_TTL, function () {
            return DB::table('investigation_payments as ip')
                ->join('patients as p', 'p.id', '=', 'ip.PatientId')
                ->selectRaw('
                    COUNT(DISTINCT p.id)         AS total_patients,
                    SUM(IFNULL(ip.DueAmount, 0)) AS total_due
                ')
                ->whereRaw('EXISTS (
                    SELECT 1 FROM investigation_payments ip2
                    WHERE ip2.PatientId = p.id
                    AND IFNULL(ip2.DueAmount, 0) > 0
                )')
                ->first();
        });

        return response()->json([
            'success'        => true,
            'total_patients' => (int)   ($data->total_patients ?? 0),
            'total_due'      => (float) ($data->total_due      ?? 0),
        ]);
    }

    // -------------------------------------------------------
    // Patient Details Modal
    // -------------------------------------------------------
    public function patientDetails($patientId)
    {
        $patientId = (int) $patientId;

        if ($patientId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid patient ID',
            ], 422);
        }

        $patient = DB::table('patients')->where('id', $patientId)->first();

        if (!$patient) {
            return response()->json([
                'success' => false,
                'message' => 'Patient not found',
            ], 404);
        }

        $rows = DB::table('investigation_payments')
            ->select(
                'ID',
                'BillNo',
                'TotalBill',
                'Discount',
                'PaidAmount',
                'DueAmount',
                'PaymentDate',
                'CollectedBy'
            )
            ->where('PatientId', $patientId)
            ->orderByDesc('ID')
            ->get();

        $bills = $rows->map(function ($r) {
            return [
                'ID'         => $r->ID,
                'BillNo'     => $r->BillNo,
                'TotalBill'  => (float) $r->TotalBill,
                'Discount'   => (float) ($r->Discount ?? 0),
                'NetBill'    => (float) $r->TotalBill - (float) ($r->Discount ?? 0),
                'PaidAmount' => (float) ($r->PaidAmount ?? 0),
                'DueAmount'  => (float) ($r->DueAmount  ?? 0),
            ];
        });

        $payments = $rows->filter(fn($r) => (float) $r->PaidAmount > 0)
            ->map(function ($r) {
                return [
                    'ID'          => $r->ID,
                    'BillNo'      => $r->BillNo,
                    'PaymentDate' => $r->PaymentDate,
                    'amount'      => (float) $r->PaidAmount,
                    'CollectedBy' => $r->CollectedBy,
                ];
            })
            ->values();

        return response()->json([
            'success'       => true,
            'patient'       => [
                'name' => $patient->patientname,
            ],
            'bills'         => $bills,
            'payments'      => $payments,
            'total_due'     => (float) $rows->sum('DueAmount'),
            'total_payment' => (float) $rows->sum('PaidAmount'),
        ]);
    }
}