<?php

namespace App\Http\Controllers;

use App\Models\ConfigSpeciality;
use Illuminate\Http\Request;

class ConfigSpecialityController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->get('q', ''));

        $rows = ConfigSpeciality::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('code', 'like', "%{$q}%")
                   ->orWhere('name', 'like', "%{$q}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('configspeciality.index', compact('rows', 'q'));
    }

    public function create()
    {
        return view('configspeciality.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'code' => 'required|string|max:25|unique:configspeciality,code',
            'name' => 'required|string|max:50',
        ]);

        ConfigSpeciality::create($data);

        return redirect()
            ->route('configspeciality.index')
            ->with('success', 'Speciality created successfully.');
    }

    public function edit(ConfigSpeciality $configspeciality)
    {
        return view('configspeciality.edit', ['row' => $configspeciality]);
    }

    public function update(Request $r, ConfigSpeciality $configspeciality)
    {
        $data = $r->validate([
            'code' => 'required|string|max:25|unique:configspeciality,code,' . $configspeciality->id,
            'name' => 'required|string|max:50',
        ]);

        $configspeciality->update($data);

        return redirect()
            ->route('configspeciality.index')
            ->with('success', 'Speciality updated successfully.');
    }

    public function destroy(ConfigSpeciality $configspeciality)
    {
        $configspeciality->delete();

        return redirect()
            ->route('configspeciality.index')
            ->with('success', 'Speciality deleted successfully.');
    }
}
