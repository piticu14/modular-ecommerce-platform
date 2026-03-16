<?php

namespace Database\Factories;

use App\Product\Domain\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => fake()->randomElement([
                    'iPhone',
                    'MacBook',
                    'AirPods',
                    'Samsung Galaxy',
                    'PlayStation',
                ]) . ' ' . fake()->numberBetween(1,20),
            'price' => fake()->numberBetween(10000, 5000000),
            'currency' => 'CZK',
            'stock_on_hand' => fake()->numberBetween(10, 500),
            'stock_reserved' => 0,
        ];
    }
}
