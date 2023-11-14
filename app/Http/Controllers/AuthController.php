<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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

        $cbuArs = Str::random(22);
        $cbuUsd = Str::random(22);

        while (Account::where('cbu', $cbuArs)->exists()) {
            $cbuArs = Str::random(22);
        }

        while (Account::where('cbu', $cbuUsd)->exists()) {
            $cbuUsd = Str::random(22);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => Role::where('name', 'USER')->first()->id,
        ]);

        $pesosAccount = Account::create([
            'user_id' => $user->id,
            'cbu' => $cbuArs,
            'currency' => 'ARS',
            'balance' => 0,
            'transaction_limit' => 300000,
        ]);

        $usdAccount = Account::create([
            'user_id' => $user->id,
            'cbu' => $cbuUsd,
            'currency' => 'USD',
            'balance' => 0,
            'transaction_limit' => 1000,
        ]);

        return response()->created(['user' => $user]);
    }
}
