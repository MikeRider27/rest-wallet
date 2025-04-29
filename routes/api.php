<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\BilleteraController;

// Aquí defines tus rutas de API
Route::post('/registro-cliente', [ClienteController::class, 'registro']);
Route::post('/recargar-billetera', [BilleteraController::class, 'recargar']);

