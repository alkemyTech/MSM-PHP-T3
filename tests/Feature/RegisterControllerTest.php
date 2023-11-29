<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class RegisterControllerTest extends TestCase
{

    public function test_register_user()
    {
        //Datos para el registro de usuario
        $userData = [
            'name' => 'Usuario',
            'last_name' => 'Prueba',
            'email' => 'php@php.com',
            'password' => '123123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        //Verifica que la respuesta sigue una estructura JSON esperada
        $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'data' => [
                'user' => [
                    'name',
                    'last_name',
                    'email',
                    'role_id',
                    'id',
                ],
            ],
        ]);
        // Verifica que el usuario se encuentra en la base de datos
        $this->assertDatabaseHas('users', [
            'email' => 'php@php.com',
        ]);
        // Deshabilita las restricciones de clave externa antes de eliminar el usuario
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Elimina el usuario de la base de datos después de la prueba
        $userToDelete = User::where('email', 'php@php.com')->first();
        $userToDelete->delete();

        // Habilita las restricciones de clave externa nuevamente
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function test_invalid_data()
    {
        //Datos inválidos para provocar un error de validación
        $userData = [
            'name' => '',
            'last_name' => '',
            'email' => 'ff@php.com',
            'password' => '123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        //Verifica que la respuesta sea un HTTP 422 y que el error de validación esté presente
        $response->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errores'
        ]);

        // Verifica que el usuario no se guarda en la base de datos
        $this->assertDatabaseMissing('users', [
            'email' => 'ff@php.com',
        ]);
    }
    public function test_register_user_with_duplicate_email()
    {
    //Crea un usuario
    $existingUser = User::factory()->create();

    //Añade data con un email existente en la base de datos
    $userData = [
        'name' => 'Nuevo Usuario',
        'last_name' => 'Prueba',
        'email' => $existingUser->email,
        'password' => 'password123',
    ];

    $response = $this->postJson('/api/auth/register', $userData);

    //Verifica que la respuesta sea un HTTP 422 y que el error de validación esté presente
    $response->assertStatus(422)
    ->assertJsonStructure([
        'message',
        'errores'
    ]);
    
    // Elimina el usuario existente después de la prueba
    $existingUser->delete();}
    
}
