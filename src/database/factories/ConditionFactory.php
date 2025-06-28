<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConditionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い'])
        ];
    }
}
