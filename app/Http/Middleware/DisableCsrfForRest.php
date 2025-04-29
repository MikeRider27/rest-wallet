<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class DisableCsrfForRest extends Middleware
{
    protected $except = [
        'api/*', // Tu ruta SOAP
    ];
}
