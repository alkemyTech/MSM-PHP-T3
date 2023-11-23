<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $accountsId = Account::all()->pluck('id')->toArray();

        return [
            'amount' => fake()->randomFloat(2, 1, 1000),
            'type' => fake()->randomElement(['INCOME', 'PAYMENT', 'DEPOSIT']),
            'description' => fake()->sentence(),
            'account_id' =>  fake()->randomElement($accountsId),
            'transaction_date' => fake()->dateTime(),
        ];
    }
}
