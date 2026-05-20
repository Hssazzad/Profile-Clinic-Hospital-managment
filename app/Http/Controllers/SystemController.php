<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function clearAllCaches()
    {
        try {
            // Run all cache clear commands
            Artisan::call('view:clear');
            Artisan::call('route:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('optimize:clear');

            // ✅ Return a success view
            return view('system.clear_success', [
                'title' => 'Cache Cleared Successfully',
                'message' => 'All Laravel caches have been cleared successfully!',
                'commands' => [
                    'view:clear',
                    'route:clear',
                    'config:clear',
                    'cache:clear',
                    'optimize:clear'
                ]
            ]);
        } catch (\Throwable $e) {
            // ⚠️ Return an error view
            return view('system.clear_error', [
                'title' => 'Cache Clear Failed',
                'message' => $e->getMessage()
            ]);
        }
    }
}
