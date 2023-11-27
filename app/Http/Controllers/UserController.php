<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        $adminId = Role::where("name", "ADMIN")->first()->id;
        if (auth()->check() && auth()->user()->role_id == $adminId) {

            // Obtener el término de búsqueda del parámetro de consulta ?search
            $searchTerm = $request->query('search', '');

            // Obtener una consulta de búsqueda si se proporciona un término de búsqueda
            $query = User::query();
            if ($searchTerm !== '') {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%');
            }

            // Paginar los usuarios 
            $users = $query->paginate(10);

            // Obtener la URL de la página anterior
            $prevPageUrl = $users->previousPageUrl();

            // Obtener la URL de la página siguiente
            $nextPageUrl = $users->nextPageUrl();

            // Obtener la respuesta JSON
            $response = [
                'data' => $users->items(),
            ];

            // Agregar las URLs si no son nulas
            if ($prevPageUrl !== null) {
                $response['prev_page_url'] = $prevPageUrl;
            }

            if ($nextPageUrl !== null) {
                $response['next_page_url'] = $nextPageUrl;
            }

            return response()->json($response);
            
        } else {
            return response()->json(['error' => 'No tienes acceso a este endpoint']);
        }
    }
}
