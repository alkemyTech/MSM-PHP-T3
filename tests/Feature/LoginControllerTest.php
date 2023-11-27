<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class LoginControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_user_can_login_and_receive_jwt()
    {
        // Crear un rol de prueba en la base de datos
        $rol = Role::create([
            'name' => 'usuario',
            'description'=> 'rol de prueba',
        ]);
        // Crear un usuario de prueba en la base de datos
        $user = User::create([
            'name' => 'usuario de prueba',
            'last_name'=> 'usuario de prueba',
            'role_id' => 1,
            'email' => 'u@p.com',
            'password' => bcrypt('contrasena'),
        ]);

        // Realizar la solicitud de inicio de sesión
        $response = $this->postJson('/api/auth/login', [
            'email' => 'u@p.com',
            'password' => 'contrasena',
        ]);

        // Verificar que la solicitud fue exitosa y tiene la estructura esperada
        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user', 'message']);
        
        // Verificar que el token no esté vacío
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_user_login_fake_credentails(){
        // Realizar la solicitud de inicio de sesión
        $response = $this->postJson('/api/auth/login', [
            'email' => 'u@fake.com',
            'password' => 'contrasena',
        ]);

        // Verificar que la solicitud obtuvo un error y tiene la estructura esperada
        $response->assertStatus(401)
            ->assertJsonStructure(['error']);
    }

}
