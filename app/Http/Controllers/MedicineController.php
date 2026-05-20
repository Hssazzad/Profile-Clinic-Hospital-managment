<?php
// app/Http/Controllers/MedicineController.php
namespace App\Http\Controllers;
use App\Models\CommonMedicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $query = CommonMedicine::query()->select('id', 'name')->where('active', 1)->orderBy('name');

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }
        return response()->json($query->limit(200)->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:medicines,name',
        ]);

        $row = CommonMedicine::create([
            'name'   => trim($data['name']),
            'active' => 1,
        ]);

        return response()->json([
            'ok'   => true,
            'item' => ['id' => $row->id, 'name' => $row->name],
        ]);
    }
}
?>