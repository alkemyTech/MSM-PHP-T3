<?php

namespace App\Http\Controllers;

use App\Http\DTO\BalanceDTO;
use App\Models\Account;
use App\Models\FixedTerm;
use App\Models\Role;
use App\Models\Transaction;
use Illuminate\Http\Request;

use function PHPSTORM_META\map;

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

    private function generateCbu()
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
  
    public function showBalance()
    {
        $currentUser = auth()->user();

        $accounts = Account::where('user_id', $currentUser->id)->get();

        $accountsPesos = $accounts->where('currency', 'ARS');
        $accountsDolares = $accounts->where('currency', 'USD');

        $totalBalancePesos = $accountsPesos->sum('balance');
        $totalBalanceDolares = $accountsDolares->sum('balance');

        $transactions = [];

        foreach ($accounts as $account) {
            $accountTransactions = Transaction::where('account_id', $account->id)->get();

            if ($accountTransactions->isNotEmpty()) {
                $transactions = array_merge($transactions, $accountTransactions->toArray());
            }
        }

        $fixedTermDeposits = [];

        foreach ($accountsPesos as $account) {
            $accountFixedTerms = FixedTerm::where('account_id', $account->id)->get();

            if ($accountFixedTerms->isNotEmpty()) {
                $fixedTermDeposits = array_merge($fixedTermDeposits, $accountFixedTerms->toArray());
            }
        }


        $balanceDTO = new BalanceDTO($accounts, [
            'total_pesos' => $totalBalancePesos,
            'total_dolares' => $totalBalanceDolares,
        ], $transactions, $fixedTermDeposits);

        return response()->ok($balanceDTO);
    }

    public function editTransactionLimit(Request $request, $id)
    {
        // Validar los datos de la solicitud
        $validator = validator($request->all(), [
            'transaction_limit' => 'required|numeric|min:0.01', // El límite de transferencia debe ser mayor o igual a $0.1
        ]);

        // Comprobar si la validación falla y devolver una respuesta de error
        if ($validator->fails()) {
            return response()->badRequest(['message' => 'Datos de edición de límite de transferencia no válidos']);
        }

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Obtener la cuenta a través del ID de la solicitud
        $account = Account::find($id);

        // Respuesta en caso de error
        if (!$account) {
            return response()->notFound(['message' => 'Cuenta no encontrada']);
        }

        // Verificar que la cuenta pertenezca al usuario autenticado
        if ($account->user_id !== $user->id) {
            return response()->forbidden(['message' => 'No tienes permiso para editar esta cuenta']);
        }

        // Actualizar el limite de transferencia de la cuenta
        $account->update(['transaction_limit' => $request->input('transaction_limit')]);

        // Devolver el registro actualizado
        return response()->ok(['message' => 'Límite de transferencia actualizado', 'account' => $account]);
    }
}
