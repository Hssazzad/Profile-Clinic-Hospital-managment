<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DuelistFinalController extends Controller
{
    public function index()
    {
        return view('billing.duelist_final.index');
    }

    public function list(Request $request)
    {
        $perPage = (int) ($request->input('per_page', 20));
        $page    = (int) ($request->input('page', 1));
        $q       = trim((string) $request->input('q', ''));
        $showAll = $request->input('show_all', 0);

        $query = DB::table('investigation_payments as ip')
            ->join('patients as p', 'p.id', '=', 'ip.PatientId')
            ->select(
                'p.id                                                                        as patient_id',
                'p.patientname                                                               as PatientName',
                'p.patientcode                                                               as PatientCode',
                'p.mobile_no                                                                 as MobileNo',
                DB::raw('GROUP_CONCAT(ip.BillNo ORDER BY ip.ID SEPARATOR ", ")              as BillNo'),
                DB::raw('MAX(ip.PaymentDate)                                                 as PaymentDate'),
                DB::raw('SUM(ip.TotalBill)                                                   as TotalBill'),
                DB::raw('SUM(IFNULL(ip.Discount,0))                                         as Discount'),
                DB::raw('SUM(IFNULL(ip.PaidAmount,0))                                       as PaidAmount'),
                DB::raw('SUM(IFNULL(ip.DueAmount,0))                                        as DueAmount'),
                DB::raw('CASE
                    WHEN SUM(IFNULL(ip.DueAmount,0)) <= 0 THEN "paid"
                    WHEN SUM(IFNULL(ip.PaidAmount,0)) > 0 THEN "partial"
                    ELSE "due"
                 END                                                                         as Status')
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

        $total  = (clone $query)->selectRaw('COUNT(DISTINCT p.id) as cnt')->value('cnt') ?? 0;
        $offset = ($page - 1) * $perPage;
        $rows   = $query->offset($offset)->limit($perPage)->get();

        return response()->json([
            'data' => $rows,
            'meta' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => $perPage > 0 ? (int) ceil($total / $perPage) : 1,
                'from'         => $offset + 1,
                'to'           => min($offset + $perPage, $total),
            ],
        ]);
    }
}