<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AccountController extends Controller
{
    // First page with user dropdown + Show button
    public function assign()
    {
        // If your userpin equals users.id, this is fine.
        $users = DB::table('users')->select('id', 'name', 'email')
        ->where('accounttype','1')
        ->orderBy('name')->get();
        return view('usermenu.assign', compact('users'));
    }

    // Second page: display Parent + Submenus with assignment flags
    public function show(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer'
        ]);

        $userpin = (int) $request->query('user_id'); // treat selected user ID as userpin

        // All parents + whether user has parent in usermenu
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

        // All submenus grouped by parent (menuparentcode) + whether user has submenu in user_sub_menu
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

        // Optional: bring selected user for header display
        $user = DB::table('users')->select('id', 'name', 'email')->where('id', $userpin)->first();

        return view('usermenu.show', [
            'user'            => $user,
            'parents'         => $parents,
            'submenusByParent'=> $submenusByParent,
            'userpin'         => $userpin,
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
            // upsert parent row in `usermenu`
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

            // Optional: also remove all submenus of this parent for this user
            // DB::table('user_sub_menu')
            //   ->where('userpin', $userpin)
            //   ->where('menuparentcode', $menuparentcode)
            //   ->delete();
        }
   Cache::increment('menu_version');
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
            // ensure parent row exists in usermenu
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

            // upsert submenu row
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
       Cache::increment('menu_version');
        // Return counts to help UI update parent badge if needed
        $subCount = DB::table('user_sub_menu')
            ->where('userpin', $userpin)
            ->where('menuparentcode', $menuparentcode)
            ->count();

        return response()->json([
            'ok' => true,
            'on' => $on,
            'parentHasAnySub' => $subCount > 0,
        ]);
    } catch (\Throwable $e) {
        return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
    }
}

private function refreshMenuCache(): void
{
    // database driver has no tags; versioned key invalidation
    Cache::increment('menu_version');
}

}
