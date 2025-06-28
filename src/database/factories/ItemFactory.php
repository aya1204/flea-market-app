<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Brand;
use App\Models\Condition;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->words(2, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->numberBetween(100, 10000),
            'image' => 'test.jpg',
            'seller_user_id' => User::factory(), // 出品者
            'purchase_user_id' => null, // 購入者(デフォルトは未購入)
            'brand_id' => Brand::factory(),
            'condition_id' => Condition::factory(),
        ];
    }
}
