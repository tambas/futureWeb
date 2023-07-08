<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/account/register',
        'api/account/login',
        'api/account/profile',
        'api/account/update',
        'api/account/game/create',
        'api/account/game/update',
        'api/account/game/characters/{accountId}',
        'api/support/create',
        'api/support/child/*',
        'api/support/store',
    ];
}
