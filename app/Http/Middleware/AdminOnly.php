<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    /**
     * Hanya izinkan user dengan role = 'admin' DI GUARD ADMIN.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();

        if (! $user || $user->role !== 'admin') {
            // kalau belum login admin, arahkan ke login admin
            return redirect()
                ->route('admin.login')
                ->with('error', 'Silakan login sebagai admin kasir.');
        }

        return $next($request);
    }
}
