<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function store(Request $request){
        $user = $request->user();
        $account = new Account();
        $account->cbu = Str::random(22);
        $account->currency = $request->input('currency', 'ARS');
        $account->balance = 0;
        $account->transaction_limit = $request->input('transaction_limit', 300000);
        $account->user_id = $user->id;
      
        if ($account->currency === 'USD') {
          $account->transaction_limit = 1000;
        } else {
          $account->transaction_limit = $request->input('transaction_limit', 300000);
        }
      
        $account->save();
      
        return $account;
      }
      
}
