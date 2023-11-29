<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Account;
use Tests\TestCase;

class TransactionDepositTest extends TestCase
{
    use DatabaseTransactions;

    public function test_deposit_money_successfully()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Autenticar al usuario
        $this->actingAs($user);

        // Crear una cuenta asociada al usuario autenticado
        $account = Account::factory()->create(['user_id' => $user->id]);

        // Datos de la solicitud de depósito
        $dataRequest = [
            'account_id' => $account->id,
            'amount' => 200,
        ];

        // Realizar la solicitud de depósito y verificar el código de estado 200
        $response = $this->post('/api/transactions/deposit', $dataRequest);
        $response->assertStatus(200);
    }

    public function test_deposit_money_with_invalid_account()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Autenticar al usuario
        $this->actingAs($user);

        // Datos de la solicitud con un ID de cuenta no válido
        $data = [
            'account_id' => 9999,
            'amount' => 100,
        ];

        // Realizar la solicitud de depósito y verificar el código de estado 404
        $response = $this->post('/api/transactions/deposit', $data);
        $response->assertStatus(404);
    }

    public function test_deposit_money_with_invalid_user_permission()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Autenticar al usuario
        $this->actingAs($user);

        // Crear un segundo usuario
        $user_2 = User::factory()->create();

        // Asignarle una cuenta al segundo usuario
        $user2Account = Account::factory()->create(['user_id' => $user_2->id]);

        // Datos de la solicitud con un ID de cuenta no perteneciente al usuario autenticado
        $data = [
            'account_id' => $user2Account->id,
            'amount' => 100,
        ];

        // Realizar la solicitud de depósito y verificar el código de estado 403
        $response = $this->post('/api/transactions/deposit', $data);
        $response->assertStatus(403);
    }

    public function test_deposit_money_with_invalid_amount()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Autenticar al usuario
        $this->actingAs($user);

        // Crear una cuenta para el usuario
        $account = Account::factory()->create(['user_id' => $user->id]);

        // Datos de la solicitud con un monto negativo no válido
        $data = [
            'account_id' => $account->id,
            'amount' => -50
        ];

        // Realizar la solicitud de depósito y verificar el código de estado 400
        $response = $this->post('/api/transactions/deposit', $data);
        $response->assertStatus(400);
    }
}
