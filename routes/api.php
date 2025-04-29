<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteController;


// Aquí defines tus rutas de API
Route::post('/registro-cliente', [ClienteController::class, 'registro']);
