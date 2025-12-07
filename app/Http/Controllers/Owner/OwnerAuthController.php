<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerAuthController extends Controller
{
    public function showLoginForm()
    {
        // SESUAIKAN dengan path view lu:
        // kalau file view: resources/views/Owner/login.blade.php
        return view('Owner.login');
        // kalau foldernya "owner" kecil semua: return view('owner.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('owner')->attempt(array_merge($credentials, [
            'role' => 'owner',
        ]), $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('owner.dashboard'));
        }

        return back()
            ->withErrors([
                'email' => 'Email/password salah atau anda bukan owner.',
            ])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('owner.login');
    }
}
