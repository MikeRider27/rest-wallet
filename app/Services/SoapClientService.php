<?php

namespace App\Services;

use App\Exceptions\SoapGatewayException;
use Illuminate\Support\Facades\Log;
use SoapClient;
use Throwable;

class SoapClientService
{
    protected ?SoapClient $client = null;

    public function call(string $method, array $params)
    {
        try {
            return $this->client()->__soapCall($method, $params);
        } catch (Throwable $e) {
            Log::error("SoapClientService::call [{$method}] falló", [
                'exception' => $e->getMessage(),
            ]);

            throw new SoapGatewayException('El servicio de billetera no respondió correctamente', 0, $e);
        }
    }

    /**
     * El SoapClient se crea recién al primer uso: así una petición que ni
     * siquiera pasa la validación del controller no paga el costo (ni el
     * riesgo) de inicializar SOAP.
     */
    protected function client(): SoapClient
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $location = config('services.soap.location');
        $timeout = (int) config('services.soap.timeout', 10);

        try {
            return $this->client = new SoapClient(null, [
                'location' => $location,
                'uri' => $location,
                'trace' => 1,
                'exceptions' => true,
                'connection_timeout' => $timeout,
                'stream_context' => stream_context_create([
                    'http' => [
                        'header' => 'X-API-KEY: '.config('services.soap.api_key')."\r\n",
                        'timeout' => $timeout,
                    ],
                ]),
            ]);
        } catch (Throwable $e) {
            Log::error('SoapClientService: no se pudo inicializar el cliente SOAP', [
                'exception' => $e->getMessage(),
            ]);

            throw new SoapGatewayException('No se pudo inicializar la conexión con el servicio de billetera', 0, $e);
        }
    }
}
