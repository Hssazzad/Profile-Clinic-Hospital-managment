<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{

    public function index()
    {
        // Latest first - active ডাক্তার দেখাবে
        $doctors = Doctor::orderByDesc('id')->paginate(20);

        return view('settings.doctors.index', compact('doctors'));
    }

    public function create()
    {
         $specialities = DB::table('configspeciality')
        ->orderBy('name')
        ->get();   
        return view('settings.doctors.create',compact('specialities'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'reg_no'      => 'required|string|max:12',
            'doctor_name' => 'required|string|max:50',
            'speciality'  => 'required|string|max:25',
            'contact'     => 'required|string|max:12',
            'Posting'     => 'required|string|max:25',
            'RateCode'    => 'required|string|max:12',
        ]);

        Doctor::create([
            'reg_no'      => $r->reg_no,
            'doctor_name' => $r->doctor_name,
            'speciality'  => $r->speciality,
            'contact'     => $r->contact,
            'Posting'     => $r->Posting,
            'RateCode'    => $r->RateCode,
            'active'      => 1, // ✅ active field added with default 1
        ]);

        return redirect()
            ->route('settings.doctors.index')
            ->with('success', 'Doctor added successfully!');
    }

    public function edit($id)
    {
        $doctor = Doctor::findOrFail($id);

        return view('settings.doctors.edit', compact('doctor'));
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'reg_no'      => 'required|string|max:12',
            'doctor_name' => 'required|string|max:50',
            'speciality'  => 'required|string|max:25',
            'contact'     => 'required|string|max:12',
            'Posting'     => 'required|string|max:25',
            'RateCode'    => 'required|string|max:12',
        ]);

        $doctor = Doctor::findOrFail($id);

        $doctor->update([
            'reg_no'      => $r->reg_no,
            'doctor_name' => $r->doctor_name,
            'speciality'  => $r->speciality,
            'contact'     => $r->contact,
            'Posting'     => $r->Posting,
            'RateCode'    => $r->RateCode,
            'active'      => $r->has('active') ? $r->active : 1, // ✅ active field added
        ]);

        return redirect()
            ->route('settings.doctors.index')
            ->with('success', 'Doctor updated successfully!');
    }

    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();

        return redirect()
            ->route('settings.doctors.index')
            ->with('success', 'Doctor deleted successfully!');
    }
    
    // ✅ নতুন মেথড: active/inactive টগল করার জন্য
    public function toggleActive($id)
    {
        $doctor = Doctor::findOrFail($id);
        $doctor->active = !$doctor->active;
        $doctor->save();
        
        return redirect()
            ->route('settings.doctors.index')
            ->with('success', 'Doctor status updated successfully!');
    }
}