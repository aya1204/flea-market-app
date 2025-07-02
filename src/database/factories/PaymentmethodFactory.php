<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Paymentmethod;

class PaymentmethodFactory extends Factory
{
    protected $model = Paymentmethod::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // 支払い方法(カード払い、コンビニ払い)
        $methods = ['カード払い', 'コンビニ払い'];

        return [
            'name' => $this->faker->randomElement($methods),
        ];
    }
}
