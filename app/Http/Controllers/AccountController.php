<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Role;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function show(string $id)
    {
        $adminId = Role::where('name', 'ADMIN')->first()->id;
        $currentUser = auth()->user();

        if ($currentUser->role_id == $adminId) {
            $accounts = Account::where('user_id', $id)->get();
            return response()->ok(['accounts' => $accounts]);
        } else {
            return response()->unauthorized();
        }
    }
}
