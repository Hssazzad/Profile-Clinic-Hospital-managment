<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommonInvestigation;

class InvestigationController extends Controller
{
    public function index()
    {
        $investigations = CommonInvestigation::orderBy('id', 'desc')
            ->paginate(20);

        return view('settings.investigation.index', compact('investigations'));
    }

    public function create()
    {
        return view('settings.investigation.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'description' => 'required|string',
            'active'      => 'required|boolean',
        ]);

        CommonInvestigation::create([
            'name'        => $r->name,
            'category'    => $r->category,
            'description' => $r->description,
            'active'      => $r->active,
        ]);

        return redirect()
            ->route('settings.investigation.index')
            ->with('success', 'Investigation added successfully!');
    }

    public function edit($id)
    {
        $inv = CommonInvestigation::findOrFail($id);

        return view('settings.investigation.edit', compact('inv'));
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'description' => 'required|string',
            'active'      => 'required|boolean',
        ]);

        $inv = CommonInvestigation::findOrFail($id);

        $inv->update([
            'name'        => $r->name,
            'category'    => $r->category,
            'description' => $r->description,
            'active'      => $r->active,
        ]);

        return redirect()
            ->route('settings.investigation.index')
            ->with('success', 'Investigation updated successfully!');
    }

    public function destroy($id)
    {
        CommonInvestigation::findOrFail($id)->delete();

        return redirect()
            ->route('settings.investigation.index')
            ->with('success', 'Investigation deleted successfully!');
    }
}
