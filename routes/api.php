<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\BilleteraController;
use App\Http\Controllers\Api\CompraController;
use App\Http\Middleware\RequireBearerToken;

// Públicas: registro y login
Route::post('/registro-cliente', [ClienteController::class, 'registro']);
Route::post('/login', [AuthController::class, 'login']);

// Protegidas: requieren "Authorization: Bearer <token>" (emitido por soap-wallet en /login)
Route::middleware(RequireBearerToken::class)->group(function () {
    Route::post('/recargar-billetera', [BilleteraController::class, 'recargar']);
    Route::post('/consultar-saldo', [BilleteraController::class, 'consultarSaldo']);
    Route::post('/generar-compra', [CompraController::class, 'generarCompra']);
    Route::post('/confirmar-compra', [CompraController::class, 'confirmarCompra']);
});
