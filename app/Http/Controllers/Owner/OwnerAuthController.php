<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class OwnerAuthController extends Controller
{
    /**
     * Tampilkan form login owner.
     */
    public function showLoginForm(Request $request)
    {
        // Kalau sudah login sebagai owner, langsung ke dashboard
        if (Auth::guard('owner')->check()) {
            return redirect()->route('owner.dashboard');
        }

        // SESUAIKAN dengan path view lu
        return view('Owner.login'); // atau 'owner.login' kalau folder lowercase
    }

    /**
     * Proses login owner.
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Cari user dengan role owner
        $user = User::where('email', $data['email'])
            ->where('role', 'owner')
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Kredensial owner tidak valid.',
            ]);
        }

        // Login pakai guard owner
        $remember = $request->boolean('remember');
        Auth::guard('owner')->login($user, $remember);

        // regenerasi session id (tapi jangan invalidate global)
        $request->session()->regenerate();

        return redirect()->route('owner.dashboard');
    }

    /**
     * Logout owner.
     */
    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();

        // Jangan invalidate session global supaya user/admin tidak ikut mati
        // $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('owner.login');
    }
}
