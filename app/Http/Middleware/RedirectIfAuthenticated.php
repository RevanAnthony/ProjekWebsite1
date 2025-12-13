<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // kalau tidak diset, anggap satu guard default (web)
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {

                switch ($guard) {
                    case 'admin':
                        // sudah login sebagai admin (kasir)
                        return redirect()->route('admin.orders.index');

                    case 'owner':
                        // sudah login sebagai owner
                        return redirect()->route('owner.dashboard');

                    case 'web':
                    case null:
                    default:
                        // default user (pelanggan)
                        return redirect()->intended(RouteServiceProvider::HOME);
                }
            }
        }

        return $next($request);
    }
}
