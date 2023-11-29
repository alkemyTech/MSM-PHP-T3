<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use App\Models\Account;
use Tests\TestCase;

class TransactionSendTest extends TestCase
{

    use DatabaseTransactions;

    
    public function test_send_money_successfully()
    {
        // Crear usuario emisor
        $senderUser = User::factory()->create();
        // Autenticar usuario emisor
        $this->actingAs($senderUser);

        // Crear una cuenta para el usuario emisor con un balance de 1000 ARS
        $senderAccount = Account::factory()->create(['balance' => 1000, 'currency' => 'ARS', 'user_id' => $senderUser->id]);

        // Crear una cuenta receptora
        $receiverAccount = Account::factory()->create(['currency' => 'ARS']);

        // Datos de la solicitud de transacción
        $dataRequest = [
            'sender_account_id' => $senderAccount->id,
            'receiver_account_id' => $receiverAccount->id,
            'amount' => 100,
        ];

        // Realizar la solicitud de transacción y verificar el código de estado 200
        $response = $this->post('/api/transactions/send', $dataRequest);
        $response->assertStatus(200);
    }

    public function test_send_money_with_invalid_sender_account()
    {
        // Crear un usuario receptor
        $receiverUser = User::factory()->create();
        // Autenticar usuario receptor
        $this->actingAs($receiverUser);

        // Crear una cuenta asociada al usuario receptor
        $receiverAccount = Account::factory()->create(['user_id' => $receiverUser->id]);

        // Solicitud con un ID de cuenta emisora no válido
        $data = [
            'sender_account_id' => 9999,
            'receiver_account_id' => $receiverAccount->id,
            'amount' => 100,
        ];

        // Realizar la solicitud de transacción y verificar el código de estado 404
        $response = $this->post('/api/transactions/send', $data);
        $response->assertStatus(404);
    }

    public function test_send_money_with_invalid_receiver_account()
    {
        // Crear un usuario emisor
        $senderUser = User::factory()->create();
        // Autenticar usuario emisor
        $this->actingAs($senderUser);

        // Crear una cuenta asociada al usuario emisor
        $senderAccount = Account::factory()->create(['user_id' => $senderUser->id]);

        // Datos de la solicitud con un ID de cuenta de receptor no válido
        $data = [
            'sender_account_id' => $senderAccount->id,
            'receiver_account_id' => 9999,
            'amount' => 100,
        ];

        // Realizar la solicitud de transacción y verificar el código de estado 404
        $response = $this->post('/api/transactions/send', $data);
        $response->assertStatus(404);
    }

    public function test_send_money_with_invalid_currency()
    {
        // Crear un usuario emisor
        $senderUser = User::factory()->create();
        $this->actingAs($senderUser);

        // Crear un usuario receptor
        $receiverUser = User::factory()->create();

        // Crear una cuenta emisora con moneda USD y una cuenta receptora con moneda ARS
        $senderAccount = Account::factory()->create(['user_id' => $senderUser->id, 'currency' => 'USD']);
        $receiverAccount = Account::factory()->create(['user_id' => $receiverUser->id, 'currency' => 'ARS']);

        // Datos de la solicitud con monedas distintas
        $data = [
            'sender_account_id' => $senderAccount->id,
            'receiver_account_id' => $receiverAccount->id,
            'amount' => 100,
        ];

        // Realizar la solicitud de transacción y verificar el código de estado 400
        $response = $this->post('/api/transactions/send', $data);
        $response->assertStatus(400);
    }

    public function test_send_money_with_insufficient_balance()
    {
        // Crear un usuario emisor
        $senderUser = User::factory()->create();
        $this->actingAs($senderUser);

        // Crear un usuario receptor
        $receiverUser = User::factory()->create();

        // Crear una cuenta emisora con balance de 50 ars y cuenta receptora
        $senderAccount = Account::factory()->create(['user_id' => $senderUser->id, 'currency' => 'ARS', 'balance' => 50]);
        $receiverAccount = Account::factory()->create(['user_id' => $receiverUser->id, 'currency' => 'ARS']);

        // Datos de la solicitud con un monto mayor al saldo disponible
        $data = [
            'sender_account_id' => $senderAccount->id,
            'receiver_account_id' => $receiverAccount->id,
            'amount' => 100,
        ];

        // Realizar la solicitud de transacción y verificar el código de estado 400
        $response = $this->post('/api/transactions/send', $data);
        $response->assertStatus(400);
    }

    public function test_send_money_with_transaction_limit_exceeded()
    {
        // Crear un usuario emisor
        $senderUser = User::factory()->create();
        $this->actingAs($senderUser);

        // Crear un usuario receptor
        $receiverUser = User::factory()->create();

        // Crear una cuenta emisora con un límite de transacción de 50
        $senderAccount = Account::factory()->create(['user_id' => $senderUser->id, 'transaction_limit' => 50]);
        $receiverAccount = Account::factory()->create(['user_id' => $receiverUser->id]);

        // Datos de la solicitud con un monto que excede el límite de transacción
        $data = [
            'sender_account_id' => $senderAccount->id,
            'receiver_account_id' => $receiverAccount->id,
            'amount' => 100,
        ];

        // Realizar la solicitud de transacción y verificar el código de estado 400
        $response = $this->post('/api/transactions/send', $data);
        $response->assertStatus(400);
    }
}
