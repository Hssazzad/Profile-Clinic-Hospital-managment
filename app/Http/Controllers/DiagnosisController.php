<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommonDiagnosis;

class DiagnosisController extends Controller
{
    public function index()
    {
        $diagnosis = CommonDiagnosis::orderBy('id', 'desc')->paginate(20);

        return view('settings.diagnosis.index', compact('diagnosis'));
    }

    public function create()
    {
        return view('settings.diagnosis.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'code'  => 'required|string|max:50',
            'name'  => 'required|string|max:255',
            'active' => 'required|boolean',
        ]);

        CommonDiagnosis::create([
            'code'  => $r->code,
            'name'  => $r->name,
            'name_normalized' => strtolower(trim($r->name)),
            'active' => $r->active,
        ]);

        return redirect()
            ->route('settings.diagnosis.index')
            ->with('success', 'Diagnosis added successfully!');
    }

    public function edit($id)
    {
        $d = CommonDiagnosis::findOrFail($id);
        return view('settings.diagnosis.edit', compact('d'));
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'code'  => 'required|string|max:50',
            'name'  => 'required|string|max:255',
            'active' => 'required|boolean',
        ]);

        $d = CommonDiagnosis::findOrFail($id);

        $d->update([
            'code'  => $r->code,
            'name'  => $r->name,
            'name_normalized' => strtolower(trim($r->name)),
            'active' => $r->active,
        ]);

        return redirect()
            ->route('settings.diagnosis.index')
            ->with('success', 'Diagnosis updated successfully!');
    }

    public function destroy($id)
    {
        CommonDiagnosis::findOrFail($id)->delete();

        return redirect()
            ->route('settings.diagnosis.index')
            ->with('success', 'Diagnosis deleted successfully!');
    }
}
