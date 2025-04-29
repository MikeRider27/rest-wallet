<?php

namespace App\Services;

use SoapClient;

class SoapClientService
{
    protected $client;

    public function __construct()
    {
        $this->client = new SoapClient(null, [
            'location' => env('SOAP_SERVER_URL', 'http://192.168.11.220:9000/soap/server'),
            'uri' => env('SOAP_SERVER_URL', 'http://192.168.11.220:9000/soap/server'),
            'trace' => 1,
            'exceptions' => true,
        ]);
    }

    public function call($method, $params)
    {
        return $this->client->__soapCall($method, $params);
    }
}
