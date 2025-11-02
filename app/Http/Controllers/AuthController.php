<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /** ---------------- Login ---------------- */
    public function showLogin()
    {
        return view('auth.login');
    }

    public function attempt(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string','min:6'],
            'remember' => ['nullable'],
        ]);

        if (Auth::attempt(
            ['email' => $data['email'], 'password' => $data['password']],
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Arahkan ke login agar jelas
        return redirect()->route('login');
    }

    /** ---------------- Register ---------------- */
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama'                  => ['required','string','max:100'],
            'email'                 => ['required','email','max:190','unique:pengguna,email'],
            'password'              => ['required','string','min:6','confirmed'],
        ]);

        $user = User::create([
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /** ---------------- Google OAuth ---------------- */
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        $gUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $gUser->getId())->first()
             ?? User::where('email', $gUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'nama'      => $gUser->getName() ?: $gUser->getNickname() ?: 'User',
                'email'     => $gUser->getEmail(),
                'google_id' => $gUser->getId(),
                'password'  => Hash::make(Str::random(16)),
            ]);
        } else {
            if (empty($user->google_id)) {
                $user->google_id = $gUser->getId();
                $user->save();
            }
        }

        Auth::login($user, true);
        return redirect()->intended(route('home'));
    }
}
