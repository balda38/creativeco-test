<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyExchangeRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'from_currency_id' => 1,
            'to_currency_id' => 2,
            'value' => $this->faker->randomFloat(),
        ];
    }
}
