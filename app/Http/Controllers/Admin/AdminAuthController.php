<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // pakai model User yang tabelnya `pengguna`
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /* ================= Helper Captcha (admin) ================= */
    private function makeCaptcha(Request $request, string $key): array
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);

        $request->session()->put($key, $a + $b);

        return [
            'text'   => "Berapa hasil $a + $b ?",
            'a'      => $a,
            'b'      => $b,
            'result' => $a + $b,
        ];
    }

    /* ================== LOGIN ================== */
    public function showLogin(Request $request)
{
    // Kalau sudah login sebagai admin kasir, jangan buka halaman login lagi
    if (Auth::check() && (Auth::user()->role ?? null) === 'admin') {
        return redirect()->route('admin.orders.index');
    }

    $captcha = $this->makeCaptcha($request, 'captcha.admin.login');

    return view('admin.auth.login', compact('captcha'));
}


    public function attempt(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
            'captcha'  => ['required', 'numeric'],
        ]);

        // cek captcha
        $expected = (int) $request->session()->pull('captcha.admin.login', 0);
        if ((int) $data['captcha'] !== $expected) {
            $this->makeCaptcha($request, 'captcha.admin.login');

            return back()
                ->withErrors(['captcha' => 'Jawaban verifikasi salah, silakan coba lagi.'])
                ->withInput($request->except('password'));
        }

        // cari user di tabel `pengguna`
        $user = User::where('email', $data['email'])->first();

        // izinkan hanya role 'admin' (admin kasir)
        if (
            !$user ||
            $user->role !== 'admin' ||
            !Hash::check($data['password'], $user->password)
        ) {
            $this->makeCaptcha($request, 'captcha.admin.login');

            throw ValidationException::withMessages([
                'email' => 'Email/password salah atau akun bukan admin kasir.',
            ]);
        }

        $remember = $request->boolean('remember');

        // LOGIN PAKAI GUARD ADMIN
        Auth::guard('admin')->login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.orders.index'));
    }

    /* ================== LOGOUT ================== */
    public function logout(Request $request)
    {
        // LOGOUT PAKAI GUARD ADMIN
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /* ================== REGISTER ADMIN KASIR ================== */
    public function showRegister(Request $request)
    {
        // kalau sudah login admin, nggak perlu daftar lagi
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.orders.index');
        }

        $captcha = $this->makeCaptcha($request, 'captcha.admin.register');

        return view('admin.auth.register', compact('captcha'));
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
            'captcha'  => ['required', 'numeric'],
        ]);

        // cek captcha
        $expected = (int) $request->session()->pull('captcha.admin.register', 0);
        if ((int) $data['captcha'] !== $expected) {
            $this->makeCaptcha($request, 'captcha.admin.register');

            return back()
                ->withErrors(['captcha' => 'Jawaban verifikasi salah, silakan coba lagi.'])
                ->withInput($request->except('password'));
        }

        // buat user dengan role 'admin'
        $user = User::create([
            'nama'     => $data['nama'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'admin',
        ]);

        // LOGIN PAKAI GUARD ADMIN
        Auth::guard('admin')->login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.orders.index');
    }
}
