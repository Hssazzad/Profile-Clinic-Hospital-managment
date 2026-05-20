<?php

namespace App\Http\Controllers;

use App\Models\ReferencePerson;
use Illuminate\Http\Request;

class ReferencePersonController extends Controller
{
    public function index()
    {
        $refs = ReferencePerson::orderBy('ID', 'desc')->get();
        return view('settings.ReferencePerson', compact('refs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Code'     => 'required|string|max:25|unique:patient_ref,Code',
            'ref_type' => 'required',
            'Name'     => 'required|string|max:25',
            'Mobile'   => 'required|string|max:15',
        ]);

        ReferencePerson::create([
            'Code'     => $request->Code,
            'ref_type' => $request->ref_type,
            'Name'     => $request->Name,
            'Mobile'   => $request->Mobile,
            'active'   => 1,
        ]);

        return redirect()->route('settings.referenceperson.index')
            ->with('success', 'Reference person added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Code'     => 'required|string|max:25|unique:patient_ref,Code,' . $id . ',ID',
            'ref_type' => 'required',
            'Name'     => 'required|string|max:25',
            'Mobile'   => 'required|string|max:15',
        ]);

        $ref = ReferencePerson::findOrFail($id);
        $ref->update([
            'Code'     => $request->Code,
            'ref_type' => $request->ref_type,
            'Name'     => $request->Name,
            'Mobile'   => $request->Mobile,
            'active'   => $request->has('active') ? 1 : 0,
        ]);

        return redirect()->route('settings.referenceperson.index')
            ->with('success', 'Reference person updated successfully.');
    }

    public function destroy($id)
    {
        ReferencePerson::findOrFail($id)->delete();
        return redirect()->route('settings.referenceperson.index')
            ->with('success', 'Reference person deleted.');
    }
}