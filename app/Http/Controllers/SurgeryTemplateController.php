<?php

namespace App\Http\Controllers;

use App\Models\SurgeryTemplate;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SurgeryTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of surgery templates.
     */
    public function index()
    {
        $templates = SurgeryTemplate::with('creator')
            ->active()
            ->orderBy('template_name')
            ->paginate(20);

        return view('surgery-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new surgery template.
     */
    public function create()
    {
        $medicineTypes = Medicine::select('type')->distinct()->pluck('type');
        $medicines = Medicine::active()->orderBy('name')->get();

        return view('surgery-templates.create', compact('medicineTypes', 'medicines'));
    }

    /**
     * Store a newly created surgery template.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:255|unique:surgery_templates,template_name',
            'rx_admission' => 'nullable|array',
            'pre_op_orders' => 'nullable|array',
            'post_op_orders' => 'nullable|array',
            'investigations' => 'nullable|array',
            'advices' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        $template = SurgeryTemplate::create([
            'template_name' => $validated['template_name'],
            'rx_admission' => $validated['rx_admission'] ?? [],
            'pre_op_orders' => $validated['pre_op_orders'] ?? [],
            'post_op_orders' => $validated['post_op_orders'] ?? [],
            'investigations' => $validated['investigations'] ?? [],
            'advices' => $validated['advices'] ?? [],
            'notes' => $validated['notes'] ?? null,
            'created_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Surgery template created successfully!',
            'template' => $template
        ]);
    }

    /**
     * Show the form for editing the specified surgery template.
     */
    public function edit(SurgeryTemplate $surgeryTemplate)
    {
        $medicineTypes = Medicine::select('type')->distinct()->pluck('type');
        $medicines = Medicine::active()->orderBy('name')->get();

        return view('surgery-templates.edit', compact('surgeryTemplate', 'medicineTypes', 'medicines'));
    }

    /**
     * Update the specified surgery template.
     */
    public function update(Request $request, SurgeryTemplate $surgeryTemplate): JsonResponse
    {
        $validated = $request->validate([
            'template_name' => 'required|string|max:255|unique:surgery_templates,template_name,' . $surgeryTemplate->id,
            'rx_admission' => 'nullable|array',
            'pre_op_orders' => 'nullable|array',
            'post_op_orders' => 'nullable|array',
            'investigations' => 'nullable|array',
            'advices' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);

        $surgeryTemplate->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Surgery template updated successfully!',
            'template' => $surgeryTemplate
        ]);
    }

    /**
     * Remove the specified surgery template.
     */
    public function destroy(SurgeryTemplate $surgeryTemplate): JsonResponse
    {
        $surgeryTemplate->delete();

        return response()->json([
            'success' => true,
            'message' => 'Surgery template deleted successfully!'
        ]);
    }

    /**
     * Get medicines by type for AJAX requests.
     */
    public function getMedicinesByType(Request $request): JsonResponse
    {
        $type = $request->get('type');

        if (!$type) {
            return response()->json(['error' => 'Type is required'], 400);
        }

        $medicines = Medicine::active()
            ->byType($type)
            ->orderBy('name')
            ->get(['id', 'name', 'company_name', 'strength']);

        return response()->json($medicines);
    }

    /**
     * Get template data for AJAX requests (for prescription auto-fill).
     */
    public function getTemplateData(SurgeryTemplate $surgeryTemplate): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $surgeryTemplate->getFormattedData()
        ]);
    }

    /**
     * Get all active templates for dropdown.
     */
    public function getTemplates(): JsonResponse
    {
        $templates = SurgeryTemplate::active()
            ->orderBy('template_name')
            ->get(['id', 'template_name']);

        return response()->json($templates);
    }

    /**
     * Print view for surgery template.
     */
    public function print(SurgeryTemplate $surgeryTemplate)
    {
        return view('surgery-templates.print', compact('surgeryTemplate'));
    }
}
