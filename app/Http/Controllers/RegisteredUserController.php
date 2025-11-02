<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /** Form register */
    public function create()
    {
        return view('auth.register');
    }

    /** Simpan akun baru */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:190', 'unique:pengguna,email'], // tabel kamu: pengguna
            'password' => [
                'required',
                'confirmed',                        // butuh field password_confirmation
                Password::min(6),                   // boleh kamu atur syarat password
            ],
        ]);

        // Kalau di model User casts password => 'hashed', sebenarnya Hash::make opsional.
        $user = User::create([
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }
}
