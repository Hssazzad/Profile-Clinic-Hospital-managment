<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    // ১. সকল অ্যাপয়েন্টমেন্ট দেখার জন্য (Approve Page)
    public function appointmentapprove()
    {
        // ডাটাবেস থেকে সব অ্যাপয়েন্টমেন্ট নিয়ে আসা (Join Query)
        $appointments = DB::table('appointments')
            ->join('patients', 'appointments.patient_id', '=', 'patients.id')
            ->select('appointments.*', 'patients.patientname')
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('appointments.approve', compact('appointments'));
    }

    // ২. নতুন যুক্ত করা মেথড: অ্যাপয়েন্টমেন্ট স্ট্যাটাস আপডেট/অ্যাপ্রুভ করা
    public function updateStatus(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|integer|exists:appointments,id',
        ]);

        $id = $request->input('appointment_id');

        // স্ট্যাটাস আপডেট
        $updated = DB::table('appointments')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'updated_at' => now()
            ]);

        if ($updated) {
            return back()->with('success', 'Appointment approved successfully!');
        }

        return back()->with('error', 'Something went wrong. Please try again.');
    }

    // ৩. AJAX: নির্দিষ্ট তারিখের পরবর্তী সিরিয়াল নাম্বার বের করা
    public function nextSerial(Request $r)
    {
        $r->validate([
            'date' => ['required', 'date'],
        ]);

        $date = $r->get('date');
        $doctorId = $r->get('doctor_id');

        $query = DB::table('appointments')->whereDate('appointment_date', $date);
        if ($doctorId) $query->where('doctor_id', $doctorId);

        $count = (int) $query->count();
        $next  = $count + 1;

        return response()->json(['ok' => true, 'next' => $next]);
    }

    // ৪. অ্যাপয়েন্টমেন্ট তৈরি করা (Store Method)
    public function store(Request $r)
    {
        $data = $r->validate([
            'patient_id'       => ['required', 'integer', 'exists:patients,id'],
            'appointment_date' => ['required', 'date'],
            'serial_no'        => ['required', 'integer', 'min:1', 'max:50'],
            'remarks'          => ['nullable', 'string', 'max:500'],
        ]);

        // একই তারিখে একই সিরিয়াল চেক করা
        $exists = DB::table('appointments')
            ->whereDate('appointment_date', $data['appointment_date'])
            ->where('serial', $data['serial_no'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['serial_no' => 'This serial is already booked for the selected date.'])
                ->withInput();
        }

        // ডুপ্লিকেট অ্যাপয়েন্টমেন্ট চেক
        $already = DB::table('appointments')
            ->where('patient_id', $data['patient_id'])
            ->whereDate('appointment_date', $data['appointment_date'])
            ->exists();

        if ($already) {
            return back()->withErrors(['serial_no' => 'This patient already has an appointment on this date.'])
                         ->withInput();
        }

        DB::table('appointments')->insert([
            'patient_id'       => $data['patient_id'],
            'appointment_date' => $data['appointment_date'],
            'serial'           => $data['serial_no'],
            'remarks'          => $data['remarks'] ?? null,
            'status'           => 'pending',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return back()->with('success', 'Appointment created successfully!');
    }

    // ৫. এভেইলেবল সিরিয়াল চেক করা (AJAX)
    public function availableSerials(Request $r)
    {
        $r->validate([
            'date'  => ['required', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);
        $date  = $r->get('date');
        $limit = (int)($r->get('limit', 50));

        $taken = DB::table('appointments')
            ->whereDate('appointment_date', $date)
            ->pluck('serial')
            ->map(fn($v) => (int)$v)
            ->toArray();

        $all        = range(1, $limit);
        $available  = array_values(array_diff($all, $taken));

        return response()->json(['ok' => true, 'available' => $available]);
    }

    // ৬. রোগীর অ্যাপয়েন্টমেন্ট চেক করা (AJAX)
    public function checkPatientDate(Request $r)
    {
        $r->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'date'       => 'required|date',
        ]);

        $exists = DB::table('appointments')
            ->where('patient_id', $r->patient_id)
            ->whereDate('appointment_date', $r->date)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    // ৭. ক্রিয়েট ভিউ দেখানো
    public function create()
    {
        $patients = DB::table('patients')->orderBy('patientname')->get();
        return view('appointments.create', compact('patients'));
    }
}