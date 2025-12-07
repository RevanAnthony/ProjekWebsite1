<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;

use App\Http\Controllers\Owner\OwnerAuthController;
use App\Http\Controllers\Owner\OwnerMenuController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Owner\OwnerInboxController;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminChatController;

/*
|--------------------------------------------------------------------------
| PUBLIC / LANDING
|--------------------------------------------------------------------------
*/

// Validasi kupon (dipakai di payment)
Route::post('/coupons/validate', [CouponController::class, 'validateCoupon'])
    ->name('coupons.validate');

// Landing: kalau sudah login user -> home, kalau belum -> login user
Route::get('/', function () {
    return Auth::guard('web')->check()
        ? redirect()->route('home')
        : redirect()->route('login');
})->name('landing');

/*
|--------------------------------------------------------------------------
| USER (PELANGGAN) - GUARD: web
|--------------------------------------------------------------------------
*/

// Guest user (belum login sebagai pelanggan)
Route::middleware('guest:web')->group(function () {

    // Login user
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'attempt'])->name('login.attempt');

    // Register user
    Route::get('/register',  [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    // Forgot / Reset Password (dipakai user)
    Route::get('/forgot-password',        [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password',       [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class,     'create'])->name('password.reset');
    Route::post('/reset-password',        [NewPasswordController::class,     'store'])->name('password.update');

    // Google OAuth
    Route::get('/auth/google',          [AuthController::class, 'googleRedirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');
});

// Auth user (sudah login sebagai pelanggan)
Route::middleware('auth:web')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Halaman utama user
    Route::get('/home',    [PageController::class, 'home'])->name('home');
    Route::get('/menu',    [PageController::class, 'menu'])->name('menu');
    Route::get('/about',   [PageController::class, 'about'])->name('about');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::post('/contact',[PageController::class, 'sendContact'])->name('contact.send');

    // CART (persist & per-user)
    Route::get   ('/cart',               [CartController::class, 'show'])->name('cart.show');
    Route::post  ('/cart/add',           [CartController::class, 'add'])->name('cart.add');
    Route::patch ('/cart/item/{detail}', [CartController::class, 'updateQty'])->name('cart.update');
    Route::delete('/cart/item/{detail}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart',               [CartController::class, 'clear'])->name('cart.clear');
    Route::post  ('/cart/sync',          [CartController::class, 'sync'])->name('cart.sync');
    Route::get   ('/cart/review',        [CartController::class, 'review'])->name('cart.review');

    // PAYMENT (halaman pengiriman + kupon)
    Route::get('/payment', [CartController::class, 'payment'])->name('payment.start');

    // CHECKOUT (buat pesanan dari keranjang)
    Route::post('/checkout', [CartController::class, 'checkoutStore'])->name('orders.checkout');

    // ORDERS (user)
    Route::get('/orders',                    [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}',              [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/qris',         [OrderController::class, 'showQris'])->name('orders.qris');
    Route::post('/orders/{id}/qris/confirm',[OrderController::class, 'confirmQris'])->name('orders.qris.confirm');

    Route::post('/orders/{id}/review',      [OrderController::class, 'submitReview'])->name('orders.review.submit');

    // CHAT PESANAN (user)
    Route::get('/orders/{id}/chat',  [ChatController::class, 'showOrderChat'])->name('orders.chat');
    Route::post('/orders/{id}/chat', [ChatController::class, 'sendOrderMessage'])->name('orders.chat.send');
});

/*
|--------------------------------------------------------------------------
| ADMIN / KASIR PANEL - GUARD: admin
|--------------------------------------------------------------------------
*/

Route::prefix('gs-kasir-panel-x01')
    ->name('admin.')
    ->group(function () {

        // Admin guest (belum login sebagai admin/kasir)
        Route::middleware('guest:admin')->group(function () {
            Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
            Route::post('/login', [AdminAuthController::class, 'attempt'])->name('login.attempt');
            // Tidak ada register admin (buat manual)
        });

        // Admin / kasir yang sudah login
        Route::middleware('auth:admin')->group(function () {

            Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

            // MENU KASIR
            Route::get('/menu', [AdminMenuController::class, 'index'])->name('menu.index');

            // PESANAN (kasir)
            Route::get('/orders',                [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}',        [AdminOrderController::class, 'show'])->name('orders.show');
            Route::post('/orders/{order}/status',[AdminOrderController::class, 'updateStatus'])
                ->name('orders.update-status');

            // CHAT (admin / kasir)
            Route::get('/chats',              [AdminChatController::class, 'index'])->name('chats.index');
            Route::get('/chats/{chat}',       [AdminChatController::class, 'show'])->name('chats.show');
            Route::post('/chats/{chat}/send', [AdminChatController::class, 'sendMessage'])->name('chats.send');

            // Buka chat dari kartu pesanan
            Route::get('/orders/{order}/chat', [AdminChatController::class, 'orderChat'])->name('orders.chat');
        });
    });

/*
|--------------------------------------------------------------------------
| OWNER PANEL - GUARD: owner
|--------------------------------------------------------------------------
*/
Route::prefix('gs-owner-panel-x01')
    ->name('owner.')
    ->group(function () {

        Route::middleware('guest:owner')->group(function () {
            Route::get('/login',  [OwnerAuthController::class, 'showLoginForm'])->name('login');
            Route::post('/login', [OwnerAuthController::class, 'login'])->name('login.submit');
        });

        Route::middleware('auth:owner')->group(function () {
            Route::post('/logout', [OwnerAuthController::class, 'logout'])->name('logout');

            Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');

            // ⬇⬇⬇ INI YANG PENTING
            Route::get('/menu', [OwnerMenuController::class, 'index'])->name('menu.index');
            // ⬆⬆⬆
            Route::get('/menu/{produk}/edit', [OwnerMenuController::class, 'edit'])->name('menu.edit');
            Route::put('/menu/{produk}',      [OwnerMenuController::class, 'update'])->name('menu.update');
            Route::delete('/menu/{produk}',   [OwnerMenuController::class, 'destroy'])->name('menu.destroy');

            Route::get('/inbox', [OwnerInboxController::class, 'index'])->name('inbox.index');
        });
    });


/*
|--------------------------------------------------------------------------
| DEV / UTILITIES
|--------------------------------------------------------------------------
*/

Route::get('/dev/seed-menu', [PageController::class, 'seedProductsFromMenuItems'])
    ->name('dev.seed');
