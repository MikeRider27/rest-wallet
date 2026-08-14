<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SoapClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function __construct(private readonly SoapClientService $soapClient)
    {
    }

    public function generarCompra(Request $request): JsonResponse
    {
        $data = $request->validate([
            'montoCompra' => 'required|numeric',
        ]);

        return $this->forwardToSoap($this->soapClient, 'generarCompra', [
            $request->bearerToken(),
            $data['montoCompra'],
        ]);
    }

    public function confirmarCompra(Request $request): JsonResponse
    {
        $data = $request->validate([
            'sessionId' => 'required|string',
            'token' => 'required|string',
        ]);

        return $this->forwardToSoap($this->soapClient, 'confirmarPago', [
            $request->bearerToken(),
            $data['sessionId'],
            $data['token'],
        ]);
    }
}
