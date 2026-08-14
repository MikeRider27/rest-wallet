<?php

namespace Tests\Feature;

use App\Exceptions\SoapGatewayException;
use App\Services\SoapClientService;
use Tests\TestCase;

class WalletApiTest extends TestCase
{
    private const TOKEN = 'un-token-de-sesion';

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer '.self::TOKEN];
    }

    public function test_registro_cliente_reenvia_los_parametros_en_orden_y_relaya_la_respuesta(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('registroCliente', ['12345678', 'Juan Perez', 'juan@example.com', '3001234567', 'secreto123'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Cliente registrado exitosamente', 'data' => null]);
        });

        $response = $this->postJson('/api/registro-cliente', [
            'documento' => '12345678',
            'nombre' => 'Juan Perez',
            'email' => 'juan@example.com',
            'celular' => '3001234567',
            'password' => 'secreto123',
        ]);

        $response->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_registro_cliente_valida_los_campos_requeridos(): void
    {
        $this->postJson('/api/registro-cliente', [])
            ->assertStatus(422);
    }

    public function test_login_reenvia_documento_y_password(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('login', ['12345678', 'secreto123'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Login exitoso', 'data' => ['token' => self::TOKEN]]);
        });

        $this->postJson('/api/login', [
            'documento' => '12345678',
            'password' => 'secreto123',
        ])->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_recargar_billetera_reenvia_el_token_de_sesion_y_el_monto(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('recargarBilletera', [self::TOKEN, '50'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Recarga exitosa', 'data' => ['saldo' => '50.00']]);
        });

        $this->postJson('/api/recargar-billetera', ['monto' => '50'], $this->authHeaders())
            ->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_recargar_billetera_sin_authorization_devuelve_401(): void
    {
        $this->postJson('/api/recargar-billetera', ['monto' => '50'])
            ->assertStatus(401)
            ->assertJson(['codigo' => '99']);
    }

    public function test_consultar_saldo_reenvia_el_token_de_sesion(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('consultarSaldo', [self::TOKEN])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Consulta exitosa', 'data' => ['saldo' => '50.00']]);
        });

        $this->postJson('/api/consultar-saldo', [], $this->authHeaders())
            ->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_consultar_saldo_sin_authorization_devuelve_401(): void
    {
        $this->postJson('/api/consultar-saldo', [])->assertStatus(401);
    }

    public function test_generar_compra_reenvia_el_token_de_sesion_y_el_monto(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('generarCompra', [self::TOKEN, '25'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Compra creada', 'data' => ['session_id' => 'abc', 'token' => '123456']]);
        });

        $this->postJson('/api/generar-compra', ['montoCompra' => '25'], $this->authHeaders())
            ->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_generar_compra_sin_authorization_devuelve_401(): void
    {
        $this->postJson('/api/generar-compra', ['montoCompra' => '25'])->assertStatus(401);
    }

    public function test_confirmar_compra_reenvia_token_de_sesion_sessionid_y_otp(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('confirmarPago', [self::TOKEN, 'abc', '123456'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Pago confirmado', 'data' => null]);
        });

        $this->postJson('/api/confirmar-compra', [
            'sessionId' => 'abc',
            'token' => '123456',
        ], $this->authHeaders())->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_confirmar_compra_sin_authorization_devuelve_401(): void
    {
        $this->postJson('/api/confirmar-compra', ['sessionId' => 'abc', 'token' => '123456'])
            ->assertStatus(401);
    }

    public function test_devuelve_502_si_soap_wallet_no_responde(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->andThrow(new SoapGatewayException('el servicio no respondió'));
        });

        $this->postJson('/api/consultar-saldo', [], $this->authHeaders())
            ->assertStatus(502)
            ->assertJson(['codigo' => '99']);
    }
}
