<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{

  public function generateCbu()
  {
      $cbu = '';
      $cbu .= '00000123';
      for ($i = 0; $i < 14; $i++) {
          $cbu .= mt_rand(0, 9);
      }
      return $cbu;
  }

  public function store(Request $request) {
    $user = $request->user();
    $currency = $request->get('currency', 'ARS');
    $transactionLimit = $request->get('transaction_limit', 300000);

    $account = new Account;
    $account->cbu = $this->generateCbu();
    $account->currency = $currency;
    $account->balance = 0;

    if ($currency === 'USD') {
        $transactionLimit = 1000;
    }

    $account->transaction_limit = $transactionLimit;
    $account->user_id = $user->id;
    $account->save();

    return $account;
}
      
}
