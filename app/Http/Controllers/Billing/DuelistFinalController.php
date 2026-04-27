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
        // ?? patient ?? due bill — total_bill - total_paid > 0
        $query = DB::table('invoices as i')
            ->join('patients as p', 'p.id', '=', 'i.patient_id')
            ->select(
                'p.id as patient_id',
                'p.name as patient_name',
                'p.phone',
                DB::raw('SUM(i.total_amount) as total_bill'),
                DB::raw('SUM(i.paid_amount)  as total_paid'),
                DB::raw('SUM(i.total_amount - i.paid_amount) as due_amount')
            )
            ->groupBy('p.id', 'p.name', 'p.phone')
            ->having('due_amount', '>', 0)
            ->orderByDesc('due_amount');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('p.name',  'like', "%$s%")
                  ->orWhere('p.phone','like', "%$s%");
            });
        }

        $data = $query->get();

        return response()->json($data);
    }
}