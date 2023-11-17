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
}

