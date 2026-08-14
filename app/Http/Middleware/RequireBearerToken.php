<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige que la request traiga un token (Authorization: Bearer <token>).
 * No valida que el token sea válido -- eso lo resuelve soap-wallet, que es
 * quien tiene la base de datos; rest-wallet sigue sin persistencia propia.
 */
class RequireBearerToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'codigo' => '99',
                'mensaje' => 'No autenticado',
                'data' => null,
            ], 401);
        }

        return $next($request);
    }
}
