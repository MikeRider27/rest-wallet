<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SoapClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BilleteraController extends Controller
{
    public function __construct(private readonly SoapClientService $soapClient)
    {
    }

    public function recargar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'documento' => 'required|string',
            'celular' => 'required|string',
            'monto' => 'required|numeric',
        ]);

        return $this->forwardToSoap($this->soapClient, 'recargarBilletera', [
            $data['documento'],
            $data['celular'],
            $data['monto'],
        ]);
    }

    public function consultarSaldo(Request $request): JsonResponse
    {
        $data = $request->validate([
            'documento' => 'required|string',
            'celular' => 'required|string',
        ]);

        return $this->forwardToSoap($this->soapClient, 'consultarSaldo', [
            $data['documento'],
            $data['celular'],
        ]);
    }
}
