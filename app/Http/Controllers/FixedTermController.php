<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\FixedTerm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FixedTermController extends Controller
{
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $interestRate = env('INTEREST_RATE');

        try {
            $validator = $request->validate([
                'amount' => 'required|numeric|min:0',
                'duration' => 'required|int|min:30',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            return response()->validationError($errors);
        }

        $amount = $request->input('amount');

        $account = Account::where('user_id', $currentUser->id)
            ->where('currency', 'ARS')
            ->first();

        if (!$account || $account->balance < $amount) {
            return response()->validationError();
        }

        $duration = $request->input('duration');
        $endDate = now()->addDays($duration);
        $interest = $interestRate * $duration;
        $total =  $amount + $interest / 100 * $amount;

        $fixedTerm = new FixedTerm([
            'amount' => $amount,
            'account_id' => $account->id,
            'interest' => $interest,
            'total' => $total,
            'duration' => $duration,
            'closed_at' => $endDate,
        ]);

        $account->balance -= $amount;
        $account->save();

        $fixedTerm->save();

        return response()->ok();
    }
}
