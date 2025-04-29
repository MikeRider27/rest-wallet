<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs que están exceptuadas de CSRF
     */
    protected $except = [
       'api/*', // Excluir todo lo que sea /api
    ];
}
