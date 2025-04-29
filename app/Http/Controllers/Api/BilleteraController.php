<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SoapClientService; // Asegúrate de que la ruta sea correcta

class BilleteraController extends Controller
{
    protected $soapClient;

    public function __construct()
    {
        $this->soapClient = new SoapClientService();
    }

    public function recargar(Request $request)
    {
        $data = $request->validate([
            'documento' => 'required|string',
            'celular' => 'required|string',
            'monto' => 'required|numeric',
        ]);

        $response = $this->soapClient->call('recargarBilletera', [
            $data['documento'],
            $data['celular'],
            $data['monto'],
        ]);

        return response()->json($response);
    }

    public function consultarSaldo(Request $request)
    {
        $data = $request->validate([
            'documento' => 'required|string',
            'celular' => 'required|string',
        ]);

        $response = $this->soapClient->call('consultarSaldo', [
            $data['documento'],
            $data['celular'],
        ]);

        return response()->json($response);
    }
}
