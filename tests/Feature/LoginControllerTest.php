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
            'name' => 'USER',
            'description'=> 'usuario de prueba',
        ]);
        // Crear un usuario de prueba en la base de datos
        $user = User::create([
            'name' => 'usuario de prueba',
            'last_name'=> 'usuario de prueba',
            'role_id' => 1,
            'email' => 'us@pr.com',
            'password' => bcrypt('contrasena'),
        ]);

        // Realizar la solicitud de inicio de sesión
        $response = $this->postJson('/api/auth/login', [
            'email' => 'us@pr.com',
            'password' => 'contrasena',
        ]);

        // Verificar que la solicitud fue exitosa y tiene la estructura esperada
        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user', 'message']);
        
        // Verificar que el token no esté vacío
        $this->assertNotEmpty($response->json('token'));
        // Elimina el usuario de la base de datos después de la prueba
        $userToDelete = User::where('email', 'us@pr.com')->first();
        $userToDelete->delete();

        // Verifica que el usuario ha sido eliminado de la base de datos
        $this->assertDatabaseMissing('users', [
            'email' => 'us@pr.com',
        ]);
    }

    public function test_user_login_fake_credentails(){
        // Realizar la solicitud de inicio de sesión
        $response = $this->postJson('/api/auth/login', [
            'email' => 'u@f.com',
            'password' => 'contrasena',
        ]);

        // Verificar que la solicitud obtuvo un error y tiene la estructura esperada
        $response->assertStatus(401)->assertJsonStructure(['error']);
    }

}
