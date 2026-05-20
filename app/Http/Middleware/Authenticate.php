<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            // If login route exists, use it; otherwise send to homepage
            return Route::has('login') ? route('login') : url('/');
        }
        return null;
    }
}
