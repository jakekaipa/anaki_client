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
        'user/username/check',
        'user/check/email',
        '/admin/manage-notice/upload',
        // SSl Commerze
        '/add-money/sslcommerz/success',
        '/add-money/sslcommerz/cancel',
        '/add-money/sslcommerz/fail',
        '/api/v1/user/add-money/sslcommerz/success',
        '/api/v1/user/add-money/sslcommerz/cancel',
        '/api/v1/user/add-money/sslcommerz/fail',
    ];
}
