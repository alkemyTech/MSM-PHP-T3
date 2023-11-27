<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Role;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function sendMoney(Request $request)
    {

        // Validar los datos de la solicitud
        $validator = validator($request->all(), [
            'sender_account_id' => 'required',
            'receiver_account_id' => 'required',
            'amount' => 'required|numeric|min:0.01', // El monto debe ser mayor o igual a $0.1
        ]);

        // Comprobar si la validación falla y devolver una respuesta de error
        if ($validator->fails()) {
            return response()->badRequest(['message' => 'Datos de transferencia no válidos']);
        }

        // Obtener el usuario emisor del token
        $senderUser = Auth::user();

        // Obtener la cuenta del usuario emisor a través del sender_account_id
        $senderAccount = $senderUser->account
            ->where('id', $request->input('sender_account_id'))
            ->where('deleted', false) // Evaluar si tiene un borrado lógico
            ->first();

        // Respuesta en caso de error
        if (!$senderAccount) {
            return response()->notFound(['message' => 'Cuenta emisora no encontrada']);
        }

        // Obtener la cuenta del usuario receptor a través del receiver_account_id
        $receiverAccount = Account::where('id', $request->input('receiver_account_id'))
            ->where('deleted', false) // Evaluar si tiene un borrado lógico
            ->first();

        // Respuesta en caso de error
        if (!$receiverAccount) {
            return response()->notFound(['message' => 'Cuenta receptora no encontrada']);
        }

        // Obtener el usuario receptor a través del $receiverAccount
        $receiverUser = User::where('id', $receiverAccount->user_id)->first();

        // Verificar si el usuario receptor existe
        if (!$receiverUser) {
            return response()->notFound(['message' => 'Usuario receptor no encontrado']);
        }

        // Verificar si las cuentas tienen la misma moneda
        if ($senderAccount->currency !== $receiverAccount->currency) {
            return response()->badRequest(['message' => 'No coinciden los tipos de moneda']);
        }

        // Obtener el monto de la transferencia
        $transactionAmount = $request->input('amount');

        // Verificar que la cuenta emisora tenga suficiente saldo y que la transferencia no excede el límite de transacción
        if ($senderAccount->balance >= $request->input('amount') && $request->input('amount') <= $senderAccount->transaction_limit) {

            // Crear transacción INCOME para el receptor
            $incomeTransaction = Transaction::create([
                'amount' => $transactionAmount,
                'type' => 'INCOME',
                'account_id' => $receiverAccount->id,
                'transaction_date' => now(),
            ]);

            // Crear transacción PAYMENT para el emisor
            $paymentTransaction = Transaction::create([
                'amount' => $transactionAmount,
                'type' => 'PAYMENT',
                'account_id' => $senderAccount->id,
                'transaction_date' => now(),
            ]);

            // Actualizar los balances de cuentas
            $senderAccount->decrement('balance', $transactionAmount);
            $receiverAccount->increment('balance', $transactionAmount);



            return response()->ok(['message' => 'Transferencia realizada con éxito', $paymentTransaction, $incomeTransaction]);
        } else {

            $errorMessage = '';

            if ($senderAccount->balance < $request->input('amount')) {

                $errorMessage = 'Saldo insuficiente.';
            } else {

                $errorMessage = 'Límite de transacción excedido.';
            }

            return response()->badRequest(['message' => $errorMessage]);
        }
    }
    public function depositMoney(Request $request)
    {
        // Validar los datos de la solicitud
        $validator = validator($request->all(), [
            'account_id' => 'required',
            'amount' => 'required|numeric|min:0.01', // El monto debe ser mayor o igual a $0.1
        ]);

        // Comprobar si la validación falla y devolver una respuesta de error
        if ($validator->fails()) {
            return response()->badRequest(['message' => 'Datos de depósito no válidos']);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener la cuenta a través del account_id
        $account = Account::where('id', $request->input('account_id'))
            ->where('deleted', false) // Evaluar si tiene un borrado lógico
            ->first();

        // Respuesta en caso de error
        if (!$account) {
            return response()->notFound(['message' => 'Cuenta no encontrada']);
        }

        // Verificar que la cuenta pertenezca al usuario autenticado
        if ($account->user_id !== $user->id) {
            return response()->forbidden(['message' => 'No tienes permiso para realizar un depósito en esta cuenta']);
        }

        // Crear transacción DEPOSIT para la cuenta
        $depositTransaction = Transaction::create([
            'amount' => $request->input('amount'),
            'type' => 'DEPOSIT',
            'account_id' => $account->id,
            'transaction_date' => now(),
        ]);

        // Actualizar el balance de la cuenta
        $account->increment('balance', $request->input('amount'));

        return response()->ok(['message' => 'Depósito realizado con éxito', 'transaction' => $depositTransaction, 'account' => $account]);
    }
    public function makePayment(Request $request)
    {
        // Validar los datos de la solicitud
        $validator = validator($request->all(), [
            'account_id' => 'required',
            'amount' => 'required|numeric|min:0.01', // El monto debe ser mayor o igual a $0.1
        ]);


        // Comprobar si la validación falla y devolver una respuesta de error
        if ($validator->fails()) {
            return response()->badRequest(['message' => 'Datos de pago no válidos']);
        }

        // Obtener la cuenta a través del account_id
        $account = Account::where('id', $request->input('account_id'))
            ->where('deleted', false) // Evaluar si tiene un borrado lógico
            ->first();

        // Respuesta en caso de error
        if (!$account) {
            return response()->notFound(['message' => 'Cuenta no encontrada']);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar que la cuenta pertenezca al usuario autenticado
        if ($account->user_id !== $user->id) {
            return response()->forbidden(['message' => 'No tienes permiso para realizar un pago desde esta cuenta']);
        }

        // Verificar que la cuenta tenga saldo suficiente para el pago
        if ($account->balance < $request->input('amount')) {
            return response()->badRequest(['message' => 'Saldo insuficiente para realizar el pago']);
        }

        // Crear transacción PAYMENT para la cuenta
        $paymentTransaction = Transaction::create([
            'amount' => $request->input('amount'),
            'type' => 'PAYMENT',
            'account_id' => $account->id,
            'transaction_date' => now(),
        ]);

        // Actualizar el balance de la cuenta
        $account->decrement('balance', $request->input('amount'));

        return response()->ok(['message' => 'Pago realizado con éxito', 'transaction' => $paymentTransaction, 'account' => $account]);
    }


    public function edit(Request $request, $id)
    {
       $currentUser = auth()->user();
        $transaction = Transaction::where('id', $id)->first();
        $account = Account::where('id', $transaction->account_id)->first();

        if ($account->user_id != $currentUser->id) {
            return response()->unauthorized();
        }

        try {
            $validator = $request->validate([
                'description' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return response()->validationError($errors);
        }

        $transaction->update(['description' => $request->input('description')]);
         
        return response()->ok();
    }

    public function listTransactions()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener las cuentas del usuario autenticado
        $accounts = $user->account;

        // Verificar si el usuario tiene cuentas asociadas
        if ($accounts->isEmpty()) {
            return response()->ok(['message' => 'El usuario no tiene cuentas asociadas']);
        }

        // Obtener las transacciones del usuario 
        $transactions = Transaction::whereIn('account_id', $accounts->pluck('id'))->get();

        // Verificar si hay transacciones
        if ($transactions->isEmpty()) {
            return response()->ok(['message' => 'El usuario no tiene transacciones asociadas']);
        }

            // Verificar si el usuario es administrador
    $adminRole = Role::where('name', 'ADMIN')->first();
    if ($adminRole && $user->role_id === $adminRole->id) {
        // Paginar las transacciones
        $page = request()->get('page');
        if (!is_numeric($page) || $page < 1) {
            return response()->json(['error' => 'The "page" parameter must be an integer greater than or equal to 1'], 400);
        }

        $transactions = Transaction::whereIn('account_id', $accounts->pluck('id'))->paginate(10);
        $transactions->appends(['page' => $page]);

        // Devolver la respuesta
        return response()->ok(['transactions' => $transactions]);
    } else {
        // Devolver las transacciones del usuario
        return response()->ok(['transactions' => $transactions]);
    }
    }




    public function showTransaction($id)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Obtener la transacción a través del id
        $transaction = Transaction::find($id);

        // Verificar si la transacción existe y pertenece al usuario autenticado
        if (!$transaction) {
            return response()->notFound(['message' => 'Transacción no encontrada']);
        } elseif ($transaction->account->user_id === $user->id) {
            return response()->ok(['transaction' => $transaction]);
        } else {
            return response()->notFound(['message' => 'Transacción no autorizada para el usuario actual']);
        }
    }



}
