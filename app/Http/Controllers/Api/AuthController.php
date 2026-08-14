<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SoapClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly SoapClientService $soapClient)
    {
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'documento' => 'required|string',
            'password' => 'required|string',
        ]);

        return $this->forwardToSoap($this->soapClient, 'login', [
            $data['documento'],
            $data['password'],
        ]);
    }
}
