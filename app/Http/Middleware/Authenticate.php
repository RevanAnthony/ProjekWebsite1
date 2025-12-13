<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Authenticate extends Middleware
{
    /**
     * Ke mana user diarahkan jika tidak terautentikasi.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Nama route & path untuk nentuin panel mana
        $routeName = optional($request->route())->getName();
        $path      = ltrim($request->path(), '/');

        // ADMIN PANEL (kasir)
        if (
            ($routeName && Str::startsWith($routeName, 'admin.')) ||
            Str::startsWith($path, 'gs-kasir-panel-x01')
        ) {
            return route('admin.login');
        }

        // OWNER PANEL
        if (
            ($routeName && Str::startsWith($routeName, 'owner.')) ||
            Str::startsWith($path, 'gs-owner-panel-x01')
        ) {
            return route('owner.login');
        }

        // Default: login user (pelanggan)
        return route('login');
    }
}
