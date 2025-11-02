<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Exception;

class GoogleController extends Controller
{
    // Redirect user ke Google untuk login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Setelah login sukses dari Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(str()->random(12)), // password random
                ]);
            }

            Auth::login($user);

            return redirect()->route('home'); // ke dashboard
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Login gagal, coba lagi ya!');
        }
    }
}
