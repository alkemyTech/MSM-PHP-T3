<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // $senderUser = Auth::user();
        $senderUser = User::whereHas('account', function ($query) use ($request) {
            $query->where('id', $request->input('sender_account_id'));
        })->first();

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

        // Obtener el monto de la transferencia
        $transactionAmount = $request->input('amount');

        // Verificar que la cuenta emisora tenga suficiente saldo y que la transferencia no excede el límite de transacción
        if ($senderAccount->balance >= $request->input('amount') && $request->input('amount') <= $senderAccount->transaction_limit) {

            // Crear transacción INCOME para el receptor
            Transaction::create([
                'amount' => $transactionAmount,
                'type' => 'INCOME',
                'account_id' => $receiverAccount->id,
                'transaction_date' => now(),
            ]);

            // Crear transacción PAYMENT para el emisor
            Transaction::create([
                'amount' => $transactionAmount,
                'type' => 'PAYMENT',
                'account_id' => $senderAccount->id,
                'transaction_date' => now(),
            ]);

            // Actualizar los balances de cuentas
            $senderAccount->decrement('balance', $transactionAmount);
            $receiverAccount->increment('balance', $transactionAmount);

            // Registrar transaction DEPOSIT en la tabla transactions
            $depositTransactionSender = Transaction::create([
                'amount' => $transactionAmount,
                'type' => 'DEPOSIT',
                'description' => "Depósito de {$transactionAmount} a {$receiverUser->name} {$receiverUser->last_name} proveniente de {$senderUser->name} {$senderUser->last_name}",
                'account_id' => $senderAccount->id,
                'transaction_date' => now(),
            ]);
            $depositTransactionReceiver = Transaction::create([
                'amount' => $transactionAmount,
                'type' => 'DEPOSIT',
                'description' => "Depósito de {$transactionAmount} a {$receiverUser->name} {$receiverUser->last_name} proveniente de {$senderUser->name} {$senderUser->last_name}",
                'account_id' => $receiverAccount->id,
                'transaction_date' => now(),
            ]);

            return response()->ok(['message' => 'Transferencia realizada con éxito', $depositTransactionSender, $depositTransactionReceiver]);
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
}
