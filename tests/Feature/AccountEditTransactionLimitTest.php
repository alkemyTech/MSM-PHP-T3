<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AccountEditTransactionLimitTest extends TestCase
{
    use DatabaseTransactions;

    public function test_edit_transaction_limit_successfully()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Autenticar al usuario
        $this->actingAs($user);

        // Crear una cuenta asociada al usuario autenticado
        $account = Account::factory()->create(['user_id' => $user->id]);

        // Datos de la solicitud de edición de límite de transferencia
        $dataRequest = [
            'transaction_limit' => 500,
        ];

        // Realizar la solicitud de edición de límite de transferencia
        $response = $this->patch("/api/accounts/{$account->id}", $dataRequest);
        $response->assertStatus(200);
    }

    public function test_edit_transaction_limit_with_invalid_account()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Autenticar al usuario
        $this->actingAs($user);

        // Datos de la solicitud
        $data = [
            'transaction_limit' => 500,
        ];

        // Realizar la solicitud de edición de límite de transferencia a una cuenta inexistente
        $response = $this->patch('/api/accounts/9999', $data);
        $response->assertStatus(404);
    }

    public function test_edit_transaction_limit_with_invalid_user_permission()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Autenticar al usuario
        $this->actingAs($user);

        // Crear un segundo usuario
        $user_2 = User::factory()->create();

        // Asignarle una cuenta al segundo usuario
        $user2Account = Account::factory()->create(['user_id' => $user_2->id]);

        // Datos de la solicitud
        $data = [
            'transaction_limit' => 500,
        ];

        // Realizar la solicitud de edición de límite de transferencia a una cuenta no perteneciente al usuario autenticado
        $response = $this->patch("/api/accounts/{$user2Account->id}", $data);
        $response->assertStatus(403);
    }

    public function test_edit_transaction_limit_with_invalid_data()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Autenticar al usuario
        $this->actingAs($user);

        // Crear una cuenta asociada al usuario autenticado
        $account = Account::factory()->create(['user_id' => $user->id]);

        // Datos de la solicitud de edición de límite de transferencia con un valor no válido
        $dataRequest = [
            'transaction_limit' => 'xxxx', // Valor no válido, debería ser numérico
        ];

        // Realizar la solicitud de edición de límite de transferencia
        $response = $this->patch("/api/accounts/{$account->id}", $dataRequest);
        $response->assertStatus(400);
    }
}
