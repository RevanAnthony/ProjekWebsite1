<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\RegisteredUserController; // <— controller register milikmu

// Landing: kalau sudah login ke home, kalau belum ke login
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('home')
        : redirect()->route('login');
})->name('landing');

// ==========================
// GUEST ONLY
// ==========================
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'attempt'])->name('login.attempt');

    // Register
    Route::get('/register',  [RegisteredUserController::class, 'create'])->name('register');         // link navbar
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');   // action form

    // Forgot/Reset Password
    Route::get('/forgot-password',        [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password',       [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class,     'create'])->name('password.reset');
    Route::post('/reset-password',        [NewPasswordController::class,     'store'])->name('password.update');

    // Google OAuth
    Route::get('/auth/google',          [AuthController::class, 'googleRedirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');
});

// ==========================
// AUTH ONLY
// ==========================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/home',    [PageController::class, 'home'])->name('home');
    Route::get('/menu',    [PageController::class, 'menu'])->name('menu');
    Route::get('/about',   [PageController::class, 'about'])->name('about');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::post('/contact',[PageController::class, 'sendContact'])->name('contact.send');
});
