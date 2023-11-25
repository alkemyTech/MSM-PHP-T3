<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    public function destroy(string $id)
    {
        $adminId = Role::where('name', 'ADMIN')->first()->id;
        $currentUser = auth()->user();

        if ($currentUser->role_id == $adminId) {
            $userToDelete = User::find($id);
            $userToDelete->update(['deleted' => 1]);
            return response()->ok();
        } elseif ($currentUser->id == $id) {
            $userToDelete = User::find($id);
            $userToDelete->update(['deleted' => 1]);
            return response()->ok();
        } else {
            return response()->unauthorized();
        }
    }

    public function index(){
        $adminId = Role::where("name","ADMIN")->first()->id;
        if (auth()->check() && auth()->user()->role_id == $adminId){
            $users = User::all();
            return response()->json($users);
        } else{
            return response()->json(['error' => 'No tienes acceso a este endpoint']);
        }
    }
    public function update(Request $request){
        try {
            $user = JWTAuth::user();

            // Validar los datos recibidos en la solicitud
            $validator = $request->validate([
                'name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'password' => 'sometimes|required|string|min:6',
            ]);

            // No se permite actualizar email ni rol
            if ($request->has('email') || $request->has('role_id')) {
                return response()->json(['error' => 'No se puede actualizar email ni rol'], 400);
            }

            // Actualizar campos permitidos
            if ($request->has('name')) {
                $user->name = $request->input('name');
            }

            if ($request->has('last_name')) {
                $user->last_name = $request->input('last_name');
            }

            if ($request->has('password')) {
                $user->password = Hash::make($request->input('password'));
            }

            // Guardar los cambios en el usuario
            $user->save();

            return response()->json(['message' => 'Datos actualizados con éxito', 'user' => $user]);

        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return response()->json(['error' => $errors], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar la solicitud'], 500);
        }
    }    
    }