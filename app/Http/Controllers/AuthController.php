<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = $request->validate([
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return response()->validationError($errors);
        }

        function bigNumber() {
            # prevent the first number from being 0
            $output = rand(1,9);
        
            for($i=0; $i<21; $i++) {
                $output .= rand(0,9);
            }
        
            return $output;
        }

        $cbuArs = bigNumber();
        $cbuUsd = bigNumber();

        while (Account::where('cbu', $cbuArs)->exists()) {
            $cbuArs = bigNumber();
        }

        while (Account::where('cbu', $cbuUsd)->exists()) {
            $cbuUsd = bigNumber();
        }

        $userRole = Role::where('name', 'USER')->first();
        if (!$userRole) {
            $userRole = Role::create(['name' => 'USER']);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $userRole->id
        ]);

        $pesosAccount = Account::create([
            'user_id' => $user->id,
            'cbu' => (string) $cbuArs,
            'currency' => 'ARS',
            'balance' => 0,
            'transaction_limit' => 300000,
        ]);

        $usdAccount = Account::create([
            'user_id' => $user->id,
            'cbu' => (string) $cbuUsd,
            'currency' => 'USD',
            'balance' => 0,
            'transaction_limit' => 1000,
        ]);

        return response()->created(['user' => $user]);
    }

    public function login(Request $request) {
    $credentials = $request->only('email', 'password');

    if (! $token = Auth::attempt($credentials)) {
        return response()->json(['error' => 'Usuario no autorizado'], 401);
    }

    $user = Auth::user();

    $token = JWTAuth::claims(['exp' => now()->addMinutes(2)->timestamp])->fromUser($user);

    return response()->json([
        'token' => $token,
        'user' => $user,
        'message' => 'Inicio de sesión exitoso'
    ]);

    }
}
