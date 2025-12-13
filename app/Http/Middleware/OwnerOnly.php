<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class OwnerOnly
{
    /**
     * Hanya izinkan user dengan role = 'owner' DI GUARD OWNER.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('owner')->user();

        if (! $user || $user->role !== 'owner') {
            return redirect()
                ->route('owner.login')
                ->with('error', 'Silakan login sebagai owner.');
        }

        return $next($request);
    }
}
