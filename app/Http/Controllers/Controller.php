<?php

namespace App\Http\Controllers;

use App\Exceptions\SoapGatewayException;
use App\Services\SoapClientService;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Llama a soap-wallet y homogeneiza la respuesta: si el servicio SOAP falla
     * (caído, timeout, credencial inválida), se devuelve un JSON prolijo en vez
     * de dejar que la excepción reviente como un 500 crudo de Laravel.
     */
    protected function forwardToSoap(SoapClientService $soapClient, string $method, array $params): JsonResponse
    {
        try {
            $response = $soapClient->call($method, $params);
        } catch (SoapGatewayException $e) {
            return response()->json([
                'codigo' => '99',
                'mensaje' => 'No se pudo comunicar con el servicio de billetera. Intentá nuevamente.',
                'data' => null,
            ], 502);
        }

        return response()->json($response);
    }
}
