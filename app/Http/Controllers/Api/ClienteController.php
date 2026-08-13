<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SoapClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct(private readonly SoapClientService $soapClient)
    {
    }

    public function registro(Request $request): JsonResponse
    {
        $data = $request->validate([
            'documento' => 'required|string',
            'nombre' => 'required|string',
            'email' => 'required|email',
            'celular' => 'required|string',
        ]);

        return $this->forwardToSoap($this->soapClient, 'registroCliente', [
            $data['documento'],
            $data['nombre'],
            $data['email'],
            $data['celular'],
        ]);
    }
}
