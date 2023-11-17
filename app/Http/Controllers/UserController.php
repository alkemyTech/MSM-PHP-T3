<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
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