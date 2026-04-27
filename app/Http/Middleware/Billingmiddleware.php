<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BillingMiddleware
{
    public function handle(Request $request, Closure $next, string $permission = 'access')
    {
        if (! auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $accounttype = (int) (auth()->user()->accounttype ?? 0);

        $canAccess = in_array($accounttype, [1, 99]);
        $canDelete = ($accounttype === 99);

        if (! $canAccess) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
            }
            abort(403, 'You do not have permission to access billing.');
        }

        if ($permission === 'delete' && ! $canDelete) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Only super admin can delete billing records.'], 403);
            }
            abort(403, 'Only super admin can delete billing records.');
        }

        return $next($request);
    }
}