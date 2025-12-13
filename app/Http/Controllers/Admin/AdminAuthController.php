<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /* ================= Helper Captcha (admin) ================= */

    private function makeCaptcha(Request $request, string $key = 'captcha.admin.login'): array
    {
        $captcha = $request->session()->get($key);

        if (! $captcha) {
            $a = random_int(1, 9);
            $b = random_int(1, 9);

            $captcha = [
                'a'        => $a,
                'b'        => $b,
                'question' => "{$a} + {$b} = ?",
                'answer'   => $a + $b,
            ];

            $request->session()->put($key, $captcha);
        }

        return $captcha;
    }

    private function validateCaptcha(Request $request, string $key = 'captcha.admin.login'): void
    {
        $captcha = $request->session()->get($key);

        if (! $captcha) {
            throw ValidationException::withMessages([
                'captcha' => 'Captcha tidak ditemukan, silakan muat ulang halaman.',
            ]);
        }

        if ((int) $request->input('captcha') !== (int) $captcha['answer']) {
            throw ValidationException::withMessages([
                'captcha' => 'Jawaban verifikasi tidak sesuai.',
            ]);
        }

        // kalau lolos, hapus supaya tidak bisa dipakai ulang
        $request->session()->forget($key);
    }

    /* ================= Login admin (kasir) ================= */

    public function showLogin(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.orders.index');
        }

        $captcha = $this->makeCaptcha($request);

        return view('admin.auth.login', compact('captcha'));
    }

    public function attempt(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'captcha'  => ['required', 'numeric'],
        ]);

        // Validasi captcha
        $this->validateCaptcha($request);

        // Cari user admin
        $user = User::where('email', $data['email'])
            ->where('role', 'admin')
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Kredensial admin tidak valid.',
            ]);
        }

        $remember = $request->boolean('remember');

        // LOGIN PAKAI GUARD ADMIN
        Auth::guard('admin')->login($user, $remember);
        $request->session()->regenerate();

        return redirect()->route('admin.orders.index');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Jangan invalidate semua session supaya user/owner tidak ikut logout.
        // $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /* ================= Optional: register admin ================= */

    public function showRegister()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:190', 'unique:pengguna,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $user = User::create([
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'admin',
        ]);

        Auth::guard('admin')->login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.orders.index');
    }
}
