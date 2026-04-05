<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommonComplain;

class ComplainController extends Controller
{
    public function index()
    {
        $complains = CommonComplain::orderByDesc('id')->paginate(20);

        return view('settings.complain.index', compact('complains'));
    }

    public function create()
    {
        return view('settings.complain.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'code'   => 'nullable|integer',
            'name'   => 'required|string|max:100',
            'active' => 'required|boolean',
        ]);

        CommonComplain::create([
            'code'            => $r->code,
            'name'            => $r->name,
            'name_normalized' => strtolower(trim($r->name)),
            'active'          => $r->active,
        ]);

        return redirect()
            ->route('settings.complain.index')
            ->with('success', 'Complain added successfully!');
    }

    public function edit($id)
    {
        $c = CommonComplain::findOrFail($id);

        return view('settings.complain.edit', compact('c'));
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'code'   => 'nullable|integer',
            'name'   => 'required|string|max:100',
            'active' => 'required|boolean',
        ]);

        $c = CommonComplain::findOrFail($id);

        $c->update([
            'code'            => $r->code,
            'name'            => $r->name,
            'name_normalized' => strtolower(trim($r->name)),
            'active'          => $r->active,
        ]);

        return redirect()
            ->route('settings.complain.index')
            ->with('success', 'Complain updated successfully!');
    }

    public function destroy($id)
    {
        CommonComplain::findOrFail($id)->delete();

        return redirect()
            ->route('settings.complain.index')
            ->with('success', 'Complain deleted successfully!');
    }
}
