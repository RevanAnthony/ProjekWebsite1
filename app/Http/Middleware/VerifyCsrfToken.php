<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // EXCLUDE semua route owner panel (dev only)
        'gs-owner-panel-x01/*',

        // kalau mau exclude sesuatu yang lain, tambahin di bawah ini
        // 'gs-kasir-panel-x01/*',
    ];
}
