<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SoapClientService;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $soapClient;

    public function __construct()
    {
        $this->soapClient = new SoapClientService();
    }

    public function registro(Request $request)
    {
        $data = $request->validate([
            'documento' => 'required|string',
            'nombre' => 'required|string',
            'email' => 'required|email',
            'celular' => 'required|string',
        ]);

        $response = $this->soapClient->call('registroCliente', [
            $data['documento'],
            $data['nombre'],
            $data['email'],
            $data['celular'],
        ]);

        return response()->json($response);
    }
}
