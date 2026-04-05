<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
	
	public function edit()
	{
		return view('auth.change-password');
	}
    

public function update(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'password' => ['required', 'confirmed', 'min:4'],
    ]);

    if (!Hash::check($request->current_password, Auth::user()->password)) {
        return back()->withErrors([
            'current_password' => 'Current password is incorrect.'
        ]);
    }

    Auth::user()->update([
        'password' => Hash::make($request->password),
    ]);

    return back()->with('success', 'Password changed successfully!');
}

}
