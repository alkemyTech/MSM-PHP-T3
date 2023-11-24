<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\FixedTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FixedTermController extends Controller
{
    public function simulateFixedTerm(Request $request){
        $interestRate = env('INTEREST_RATE', 0.2); 

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $amount = $request->input('amount');
        $duration = $request->input('duration');

        $interest = $interestRate * $duration; 
        $totalInterest = $amount * $interestRate * $duration / 100; 
        $totalAmount = $amount + $totalInterest; 

        $simulationDetails = [
            'creation_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays($duration)->format('Y-m-d'),
            'amount_invested' => $amount,
            'interest_earned' => $totalInterest,
            'total_to_receive' => $totalAmount,
        ];

        return response()->json($simulationDetails, 200);
    }
}
