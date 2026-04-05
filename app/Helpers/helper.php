<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

/**
 * Common RoleCheck() helper — like CodeIgniter version
 * Shows Access Denied view (not redirect)
 */
if (!function_exists('RoleCheck')) {
    function RoleCheck($methodCode)
    {
        // get current user
        $userpin = Auth::id() ?? session('userpin');

        // if not logged in, show Access Denied
        if (!$userpin) {
            return View::make('errors.accessdenied', [
                'msg' => 'You are not logged in.',
            ]);
        }

      
        // check permission from user_sub_menu
        $hasPermission = DB::table('user_sub_menu')
            ->where('userpin', $userpin)           
            ->where('submenucode', $methodCode)
            ->exists();

        // if allowed -> true; otherwise show view
        if ($hasPermission) {
            return true;
        } else {
            return View::make('errors.accessdenied', [
                'msg' => 'You do not have permission to access this page.',
            ]);
        }
    }
}



