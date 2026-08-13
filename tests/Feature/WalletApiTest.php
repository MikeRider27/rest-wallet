<?php

namespace Tests\Feature;

use App\Exceptions\SoapGatewayException;
use App\Services\SoapClientService;
use Tests\TestCase;

class WalletApiTest extends TestCase
{
    public function test_registro_cliente_reenvia_los_parametros_en_orden_y_relaya_la_respuesta(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('registroCliente', ['12345678', 'Juan Perez', 'juan@example.com', '3001234567'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Cliente registrado exitosamente', 'data' => null]);
        });

        $response = $this->postJson('/api/registro-cliente', [
            'documento' => '12345678',
            'nombre' => 'Juan Perez',
            'email' => 'juan@example.com',
            'celular' => '3001234567',
        ]);

        $response->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_registro_cliente_valida_los_campos_requeridos(): void
    {
        $this->postJson('/api/registro-cliente', [])
            ->assertStatus(422);
    }

    public function test_recargar_billetera_reenvia_los_parametros_en_orden(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('recargarBilletera', ['12345678', '3001234567', '50'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Recarga exitosa', 'data' => ['saldo' => '50.00']]);
        });

        $this->postJson('/api/recargar-billetera', [
            'documento' => '12345678',
            'celular' => '3001234567',
            'monto' => '50',
        ])->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_consultar_saldo_reenvia_los_parametros_en_orden(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('consultarSaldo', ['12345678', '3001234567'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Consulta exitosa', 'data' => ['saldo' => '50.00']]);
        });

        $this->postJson('/api/consultar-saldo', [
            'documento' => '12345678',
            'celular' => '3001234567',
        ])->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_generar_compra_reenvia_los_parametros_en_orden(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('generarCompra', ['12345678', '3001234567', '25'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Compra creada', 'data' => ['session_id' => 'abc', 'token' => '123456']]);
        });

        $this->postJson('/api/generar-compra', [
            'documento' => '12345678',
            'celular' => '3001234567',
            'montoCompra' => '25',
        ])->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_confirmar_compra_reenvia_los_parametros_en_orden(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->with('confirmarPago', ['abc', '123456'])
                ->andReturn(['codigo' => '00', 'mensaje' => 'Pago confirmado', 'data' => null]);
        });

        $this->postJson('/api/confirmar-compra', [
            'sessionId' => 'abc',
            'token' => '123456',
        ])->assertOk()->assertJson(['codigo' => '00']);
    }

    public function test_devuelve_502_si_soap_wallet_no_responde(): void
    {
        $this->mock(SoapClientService::class, function ($mock) {
            $mock->shouldReceive('call')
                ->once()
                ->andThrow(new SoapGatewayException('el servicio no respondió'));
        });

        $this->postJson('/api/consultar-saldo', [
            'documento' => '12345678',
            'celular' => '3001234567',
        ])
            ->assertStatus(502)
            ->assertJson(['codigo' => '99']);
    }
}
