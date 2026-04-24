<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestigationManageController extends Controller
{
    /**
     * Display all investigations
     */
    public function index()
    {
        $investigations = DB::table('configSub')
            ->join('configMain', 'configSub.MainCode', '=', 'configMain.Code')
            ->select('configSub.*', 'configMain.Name as category')
            ->orderBy('configMain.Name')
            ->orderBy('configSub.Name')
            ->get();

        // 🟢 pluck এর বদলে get() — ID, Code সব পাওয়া যাবে
        $categories = DB::table('configMain')
            ->orderBy('Name')
            ->get();

        return view('investigations.index', compact('investigations', 'categories'));
    }

    /**
     * Get all categories with Code and Name (API)
     */
    public function getCategories()
    {
        try {
            $categories = DB::table('configMain')
                ->select('ID', 'Code', 'Name')
                ->orderBy('Name')
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $categories,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store new category in configMain
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        try {
            $exists = DB::table('configMain')
                ->where('Name', trim($request->name))
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This category already exists.',
                ], 422);
            }

            $maxCode  = DB::table('configMain')->max('Code') ?? 99;
            $nextCode = $maxCode + 1;

            $id = DB::table('configMain')->insertGetId([
                'Name' => trim($request->name),
                'Code' => $nextCode,
            ]);

            $record = DB::table('configMain')->where('ID', $id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Category added successfully.',
                'data'    => $record,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update category in configMain
     */
    public function updateCategory(Request $request)
    {
        $request->validate([
            'id'   => 'required|integer',
            'name' => 'required|string|max:100',
        ]);

        try {
            $exists = DB::table('configMain')
                ->where('Name', trim($request->name))
                ->where('ID', '!=', $request->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category name already exists.',
                ], 422);
            }

            DB::table('configMain')
                ->where('ID', $request->id)
                ->update(['Name' => trim($request->name)]);

            $record = DB::table('configMain')->where('ID', $request->id)->first();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data'    => $record,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete category from configMain
     */
    public function destroyCategory($id)
    {
        try {
            $category = DB::table('configMain')->where('ID', $id)->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found.',
                ], 404);
            }

            // এই category-তে কোনো test আছে কিনা চেক করো
            $hasTests = DB::table('configSub')
                ->where('MainCode', $category->Code)
                ->exists();

            if ($hasTests) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete this category because it contains investigations/tests. Delete them first.',
                ], 422);
            }

            DB::table('configMain')->where('ID', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store new investigation in configSub
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'category'        => 'required|string|max:100',
            'custom_category' => 'nullable|string|max:100',
            'price'           => 'required|numeric|min:0',
            'status'          => 'required|in:active,inactive',
        ]);

        try {
            $category = $request->category === 'custom'
                ? trim($request->input('custom_category', ''))
                : trim($request->category);

            if (empty($category)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category is required.',
                ], 422);
            }

            $mainCode = DB::table('configMain')
                ->where('Name', $category)
                ->value('Code');

            if (!$mainCode) {
                $maxMainCode = DB::table('configMain')->max('Code') ?? 99;
                $mainCode    = $maxMainCode + 1;

                DB::table('configMain')->insert([
                    'Name' => $category,
                    'Code' => $mainCode,
                ]);
            }

            $exists = DB::table('configSub')
                ->where('Name', trim($request->name))
                ->where('MainCode', $mainCode)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This test already exists in this category.',
                ], 422);
            }

            $maxCode = DB::table('configSub')->max('Code') ?? 0;
            $newCode = $maxCode > 0 ? $maxCode + 1 : (int)($mainCode . '01');

            $id = DB::table('configSub')->insertGetId([
                'MainCode' => $mainCode,
                'Code'     => $newCode,
                'Name'     => trim($request->name),
                'Amount'   => (float) $request->price,
            ]);

            $record = DB::table('configSub')
                ->join('configMain', 'configSub.MainCode', '=', 'configMain.Code')
                ->select('configSub.*', 'configMain.Name as category')
                ->where('configSub.ID', $id)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Investigation added successfully.',
                'data'    => $record,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update investigation in configSub
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'              => 'required|integer|exists:configSub,ID',
            'name'            => 'required|string|max:255',
            'category'        => 'required|string|max:100',
            'custom_category' => 'nullable|string|max:100',
            'price'           => 'required|numeric|min:0',
            'status'          => 'required|in:active,inactive',
        ]);

        try {
            $category = $request->category === 'custom'
                ? trim($request->input('custom_category', ''))
                : trim($request->category);

            if (empty($category)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category is required.',
                ], 422);
            }

            $mainCode = DB::table('configMain')
                ->where('Name', $category)
                ->value('Code');

            if (!$mainCode) {
                $maxMainCode = DB::table('configMain')->max('Code') ?? 99;
                $mainCode    = $maxMainCode + 1;

                DB::table('configMain')->insert([
                    'Name' => $category,
                    'Code' => $mainCode,
                ]);
            }

            $exists = DB::table('configSub')
                ->where('Name', trim($request->name))
                ->where('MainCode', $mainCode)
                ->where('ID', '!=', $request->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This test already exists in this category.',
                ], 422);
            }

            DB::table('configSub')
                ->where('ID', $request->id)
                ->update([
                    'MainCode' => $mainCode,
                    'Name'     => trim($request->name),
                    'Amount'   => (float) $request->price,
                ]);

            $record = DB::table('configSub')
                ->join('configMain', 'configSub.MainCode', '=', 'configMain.Code')
                ->select('configSub.*', 'configMain.Name as category')
                ->where('configSub.ID', $request->id)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Investigation updated successfully.',
                'data'    => $record,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete investigation from configSub
     */
    public function destroy($id)
    {
        try {
            $record = DB::table('configSub')->where('ID', $id)->first();

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found.',
                ], 404);
            }

            DB::table('configSub')->where('ID', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Investigation deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}