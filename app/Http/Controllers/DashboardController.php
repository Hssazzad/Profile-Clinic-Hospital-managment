<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class DashboardController extends Controller
{
    /**
     * Show the Admin Dashboard.
     */
    public function index()
    {
        $today = Carbon::today();

        // ===== Counts =====
        $totalPatients = DB::table('patients')->count();

        $todayPatients = DB::table('prescriptions')
            ->whereDate('created_at', $today)
            ->distinct('patient_id')
            ->count('patient_id');

        $totalDoctors = DB::table('doctors')->count();

        $todayAppointments = DB::table('appointments')
            ->whereDate('appointment_date', $today)
            ->count();

        $pendingAppointments = DB::table('appointments')
            ->where('status', 'pending')
            ->count();

        $todayRevenue = DB::table('payments')
		->whereDate('payment_date', $today)
		->sum('paid_amount');

        $todayPrescription = DB::table('prescriptions')
            ->whereDate('created_at', $today)
            ->count();

        $todayVitals = DB::table('preconassessment')
            ->whereDate('created_at', $today)
            ->count();

        $followupToday = DB::table('prescriptions')
            ->whereDate('next_appointment', $today)
            ->count();

        // ===== Chart: Last 7 days patient =====
        $last7days = DB::table('prescriptions')
            ->selectRaw("DATE(created_at) as date, COUNT(*) as total")
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ===== Monthly revenue =====
        $monthlyRevenue = DB::table('payments')
            ->selectRaw("MONTH(payment_date) as month, SUM(paid_amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard', compact(
            'totalPatients','todayPatients','totalDoctors',
            'todayAppointments','pendingAppointments','todayRevenue',
            'todayPrescription','todayVitals','followupToday',
            'last7days','monthlyRevenue'
        ));
    }
}
