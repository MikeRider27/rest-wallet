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
            'documento' => 'required|string',
            'celular' => 'required|string',
            'montoCompra' => 'required|numeric',
        ]);

        return $this->forwardToSoap($this->soapClient, 'generarCompra', [
            $data['documento'],
            $data['celular'],
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
            $data['sessionId'],
            $data['token'],
        ]);
    }
}
