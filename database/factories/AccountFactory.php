<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        //crea una instancia de Faker para generar datos aleatorios
        $faker = Faker::create();

        //define las monedas posibles
        $currencies = ['ARS', 'USD'];

        //selecciona aleatoriamente una moneda
        $currency = $faker->randomElement($currencies);

        //establece el límite de transacción según la moneda
        $transactionLimit = ($currency === 'ARS') ? 300000 : 1000;

        //obtiene los id de usuarios
        $userIds = User::pluck('id')->toArray();

        //construye el CBU con el prefijo 101 identificandose como cuenta bancaria
        $cbu = '101' . $faker->numerify('00##0000###########');

        //define los atributos para la cuenta
        return [
            'currency' => $currency,
            'transaction_limit' => $transactionLimit,
            'balance' => 0,
            'user_id' => $faker->randomElement($userIds),
            'cbu' => $cbu,
        ];
    }

    /**
     * Define a state for USD currency.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inUSD()
    {
        // Definir un estado para generar cuentas en USD
        return $this->state(function (array $attributes) {
            return [
                'currency' => 'USD',
                'transaction_limit' => 1000,
            ];
        });
    }
}




    



