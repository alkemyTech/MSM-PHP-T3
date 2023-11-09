<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $users = User::all();
        // if (auth()->user()->role != 'ADMIN') {
        //     return response()->json([
        //         'message' => 'Usted no tiene permisos para acceder a este recurso',
        //     ]);
        // }
        return response()->json([$users]);
    }
}
