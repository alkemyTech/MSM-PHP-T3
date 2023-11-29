<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;


class UpdateUserControllerTest extends TestCase
{
    public function test_update()
    {
        // Crear un usuario de prueba
        $user = User::factory()->create();

        // Generar token para el usuario
        $token = JWTAuth::fromUser($user);

        // Realizar una solicitud al endpoint '/auth/me' con el token de autenticación
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])->json('POST', '/api/auth/me');

        // Verificar que la respuesta tiene el código 200
        $response->assertStatus(200);

        // Verificar que la respuesta contiene los datos del usuario
        $response->assertJsonStructure([
            'message',
            'user' => [
                'id',
                'name',
                'last_name',
                'email'
            ]
        ]);

        $user->delete();
    }

    public function testAuthMeEndpointHandlesInvalidToken()
    {
        // Realizar una solicitud al endpoint '/auth/me' con un token inválido
        $response = $this->withHeaders(['Authorization' => 'Bearer invalid_token'])->json('POST', '/api/auth/me');
        
        // Verificar que la respuesta tiene el código 401 (Unauthorized)
        $response->assertStatus(401);

        // Verificar que la respuesta contiene un mensaje de error
        $response->assertJson([
            "message" => "Unauthenticated."
        ]);
    }

}
