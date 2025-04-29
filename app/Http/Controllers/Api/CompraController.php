<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SoapClientService;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    protected $soapClient;

    public function __construct()
    {
        $this->soapClient = new SoapClientService();
    }

    public function generarCompra(Request $request)
    {
        $data = $request->validate([
            'documento' => 'required|string',
            'celular' => 'required|string',
            'montoCompra' => 'required|numeric',
        ]);

        $response = $this->soapClient->call('generarCompra', [
            $data['documento'],
            $data['celular'],
            $data['montoCompra'],
        ]);

        return response()->json($response);
    }

   
}
