<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserAccountBuyTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_account_id' => 1,
            'goal_user_account_id' => 2,
            'value' => $this->faker->randomFloat(),
            'count' => $this->faker->randomFloat(),
            'buy_before' => $this->faker->dateTime(),
            'completed_at' => $this->faker->dateTime(),
            'canceled_at' => $this->faker->dateTime(),
        ];
    }
}
