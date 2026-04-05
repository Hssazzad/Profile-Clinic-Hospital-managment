<?php

namespace App\Http\Controllers\Account;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    // First page with user dropdown + Show button
    public function assign()
    {
        $users = DB::table('users')->select('id', 'name', 'email')
            ->where('accounttype', '1')
            ->orderBy('name')->get();
        return view('usermenu.assign', compact('users'));
    }

    // Second page: display Parent + Submenus with assignment flags
    public function show(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer'
        ]);

        $userpin = (int) $request->query('user_id');

        $parents = DB::table('parentmenu as p')
            ->leftJoin('usermenu as um', function ($join) use ($userpin) {
                $join->on('um.menuparentcode', '=', 'p.parentcode')
                     ->where('um.userpin', '=', $userpin);
            })
            ->select(
                'p.parentcode',
                'p.parentname',
                'p.controllername',
                'p.mainroute',
                DB::raw('CASE WHEN um.menuparentcode IS NULL THEN 0 ELSE 1 END AS has_parent')
            )
            ->orderBy('p.parentname')
            ->get();

        $submenusByParent = DB::table('submenu as s')
            ->leftJoin('user_sub_menu as usm', function ($join) use ($userpin) {
                $join->on('usm.submenucode', '=', 's.submenucode')
                     ->where('usm.userpin', '=', $userpin);
            })
            ->select(
                's.menuparentcode',
                's.submenucode',
                's.submenuname',
                's.method',
                's.position',
                DB::raw('CASE WHEN usm.submenucode IS NULL THEN 0 ELSE 1 END AS has_sub')
            )
            ->orderBy('s.menuparentcode')
            ->orderBy('s.position')
            ->get()
            ->groupBy('menuparentcode');

        $user = DB::table('users')->select('id', 'name', 'email')->where('id', $userpin)->first();

        return view('usermenu.show', [
            'user'             => $user,
            'parents'          => $parents,
            'submenusByParent' => $submenusByParent,
            'userpin'          => $userpin,
        ]);
    }

    public function toggleParent(Request $request)
    {
        $validated = $request->validate([
            'userpin'        => 'required|integer',
            'menuparentcode' => 'required|string|max:50',
            'on'             => 'required|boolean',
        ]);

        $userpin        = (int) $validated['userpin'];
        $menuparentcode = $validated['menuparentcode'];
        $on             = (bool) $validated['on'];

        try {
            if ($on) {
                $exists = DB::table('usermenu')
                    ->where('userpin', $userpin)
                    ->where('menuparentcode', $menuparentcode)
                    ->exists();

                if (!$exists) {
                    DB::table('usermenu')->insert([
                        'userpin'        => $userpin,
                        'menuparentcode' => $menuparentcode,
                        'position'       => 0,
                    ]);
                }
            } else {
                DB::table('usermenu')
                    ->where('userpin', $userpin)
                    ->where('menuparentcode', $menuparentcode)
                    ->delete();
            }

            return response()->json(['ok' => true, 'on' => $on]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleSub(Request $request)
    {
        $validated = $request->validate([
            'userpin'        => 'required|integer',
            'menuparentcode' => 'required|string|max:50',
            'submenucode'    => 'required|string|max:50',
            'on'             => 'required|boolean',
        ]);

        $userpin        = (int) $validated['userpin'];
        $menuparentcode = $validated['menuparentcode'];
        $submenucode    = $validated['submenucode'];
        $on             = (bool) $validated['on'];

        try {
            if ($on) {
                // Ensure parent row exists in usermenu
                $existsParent = DB::table('usermenu')
                    ->where('userpin', $userpin)
                    ->where('menuparentcode', $menuparentcode)
                    ->exists();

                if (!$existsParent) {
                    DB::table('usermenu')->insert([
                        'userpin'        => $userpin,
                        'menuparentcode' => $menuparentcode,
                        'position'       => 0,
                    ]);
                }

                // Upsert submenu row
                $exists = DB::table('user_sub_menu')
                    ->where('userpin', $userpin)
                    ->where('menuparentcode', $menuparentcode)
                    ->where('submenucode', $submenucode)
                    ->exists();

                if (!$exists) {
                    DB::table('user_sub_menu')->insert([
                        'userpin'        => $userpin,
                        'menuparentcode' => $menuparentcode,
                        'submenucode'    => $submenucode,
                        'position'       => 0,
                    ]);
                }
            } else {
                DB::table('user_sub_menu')
                    ->where('userpin', $userpin)
                    ->where('menuparentcode', $menuparentcode)
                    ->where('submenucode', $submenucode)
                    ->delete();
            }

            $subCount = DB::table('user_sub_menu')
                ->where('userpin', $userpin)
                ->where('menuparentcode', $menuparentcode)
                ->count();

            return response()->json([
                'ok'              => true,
                'on'              => $on,
                'parentHasAnySub' => $subCount > 0,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Return parent menus + submenus as JSON for dynamic dropdowns / UI options
     */
    public function options(Request $request)
    {
        try {
            $parents = DB::table('parentmenu')
                ->select('parentcode as code', 'parentname as name')
                ->orderBy('parentname')
                ->get();

            $submenus = DB::table('submenu')
                ->select('menuparentcode', 'submenucode', 'submenuname', 'method', 'position')
                ->orderBy('menuparentcode')
                ->orderBy('position')
                ->get()
                ->groupBy('menuparentcode');

            return response()->json([
                'ok'      => true,
                'parents' => $parents,
                'submenus'=> $submenus,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Bulk save all parent + submenu assignments for a user
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'userpin'      => 'required|integer',
            'parents'      => 'nullable|array',
            'parents.*'    => 'integer',
            'submenus'     => 'nullable|array',
            'submenus.*'   => 'string|max:50',
        ]);

        $userpin  = (int) $validated['userpin'];
        $parents  = $validated['parents']  ?? [];
        $submenus = $validated['submenus'] ?? [];

        try {
            DB::transaction(function () use ($userpin, $parents, $submenus) {
                // Replace all parent assignments
                DB::table('usermenu')->where('userpin', $userpin)->delete();
                foreach ($parents as $parentcode) {
                    DB::table('usermenu')->insert([
                        'userpin'        => $userpin,
                        'menuparentcode' => $parentcode,
                        'position'       => 0,
                    ]);
                }

                // Replace all submenu assignments
                DB::table('user_sub_menu')->where('userpin', $userpin)->delete();
                foreach ($submenus as $entry) {
                    // entry format: "parentcode|submenucode"
                    [$menuparentcode, $submenucode] = explode('|', $entry);
                    DB::table('user_sub_menu')->insert([
                        'userpin'        => $userpin,
                        'menuparentcode' => $menuparentcode,
                        'submenucode'    => $submenucode,
                        'position'       => 0,
                    ]);
                }
            });

            return response()->json(['ok' => true, 'message' => 'Saved successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
        }
    }
}