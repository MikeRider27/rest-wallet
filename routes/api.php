<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\BilleteraController;
use App\Http\Controllers\Api\CompraController;

// Aquí defines tus rutas de API
Route::post('/registro-cliente', [ClienteController::class, 'registro']);
Route::post('/recargar-billetera', [BilleteraController::class, 'recargar']);
Route::post('/consultar-saldo', [BilleteraController::class, 'consultarSaldo']);
Route::post('/generar-compra', [CompraController::class, 'generarCompra']);
Route::post('/confirmar-compra', [CompraController::class, 'confirmarCompra']);
