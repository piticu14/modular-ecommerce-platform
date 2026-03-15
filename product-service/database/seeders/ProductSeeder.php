<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Product;

    class ProductSeeder extends Seeder
    {
        public function run(): void
        {
            Product::insert([
                [
                    'name' => 'iPhone 15',
                    'price' => 2999900,
                    'currency' => 'CZK',
                    'stock_on_hand' => 100,
                    'stock_reserved' => 0,
                ],
                [
                    'name' => 'MacBook Pro',
                    'price' => 6999900,
                    'currency' => 'CZK',
                    'stock_on_hand' => 50,
                    'stock_reserved' => 0,
                ],
                [
                    'name' => 'AirPods Pro',
                    'price' => 699900,
                    'currency' => 'CZK',
                    'stock_on_hand' => 200,
                    'stock_reserved' => 0,
                ],
            ]);
        }
    }
