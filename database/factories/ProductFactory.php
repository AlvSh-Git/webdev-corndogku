<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name'        => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->randomElement([10000, 15000, 20000, 25000, 30000]),
            'cost_price'  => 8000,
            'stock'       => 10,
            'image'       => null,
            'is_custom'   => false,
            'is_available'=> true,
        ];
    }
}
