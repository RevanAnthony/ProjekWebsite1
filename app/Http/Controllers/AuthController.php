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
    /* ================= Helper Captcha ================= */
    private function makeCaptcha(Request $request, string $key): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $request->session()->put($key, $a + $b); // simpan hasil ke session
        return [$a, $b]; // kirim angka ke view
    }

    /* ================= LOGIN ================= */
    // GET: tampilkan form + generate captcha
    public function showLogin(Request $request)
    {
        [$c1, $c2] = $this->makeCaptcha($request, 'captcha.login');
        return view('auth.login', compact('c1', 'c2'));
    }

    // POST: proses login + validasi captcha
    public function attempt(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string','min:6'],
            'remember' => ['nullable'],
            'captcha'  => ['required','integer'],
        ]);

        // cek captcha (sekali pakai)
        $expected = (int) $request->session()->pull('captcha.login', 0); // pull = hapus setelah dibaca
        if ((int)$data['captcha'] !== $expected) {
            // siapkan captcha baru untuk tampilan berikutnya
            $this->makeCaptcha($request, 'captcha.login');

            return back()
                ->withErrors(['captcha' => 'Jawaban verifikasi salah, silakan coba lagi.'])
                ->withInput($request->except('password'));
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        // kredensial salah → buat captcha baru lagi
        $this->makeCaptcha($request, 'captcha.login');

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

   public function logout(Request $request)
{
    Auth::guard('web')->logout(); // atau Auth::logout();

    // $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect()->route('home');
}


    /* ================= REGISTER ================= */
    // GET: tampilkan form + generate captcha
    public function showRegister(Request $request)
    {
        [$c1, $c2] = $this->makeCaptcha($request, 'captcha.register');
        return view('auth.register', compact('c1', 'c2'));
    }

    // POST: proses register + validasi captcha
    public function register(Request $request)
    {
        $data = $request->validate([
            'nama'     => ['required','string','max:100'],
            'email'    => ['required','email','max:190','unique:pengguna,email'],
            'password' => ['required','string','min:6','confirmed'],
            'captcha'  => ['required','integer'],
        ]);

        $expected = (int) $request->session()->pull('captcha.register', 0);
        if ((int)$data['captcha'] !== $expected) {
            $this->makeCaptcha($request, 'captcha.register');

            return back()
                ->withErrors(['captcha' => 'Jawaban verifikasi salah, silakan coba lagi.'])
                ->withInput($request->except('password'));
        }

        $user = User::create([
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /* ================= Google OAuth ================= */
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
        } elseif (empty($user->google_id)) {
            $user->google_id = $gUser->getId();
            $user->save();
        }

        Auth::login($user, true);
        return redirect()->intended(route('home'));
    }
}
