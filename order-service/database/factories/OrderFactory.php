<?php


namespace Database\Factories;

use App\Order\Domain\Models\Order;
use App\Order\Domain\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'user_id' => fake()->optional()->numberBetween(1, 10),
            'status' => fake()->randomElement(['pending', 'confirmed', 'failed', 'cancelled']),
            'currency' => 'CZK',
            'subtotal' => 0,
            'total' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order) {

            $itemsCount = fake()->numberBetween(1, 3);

            $subtotal = 0;

            for ($i = 0; $i < $itemsCount; $i++) {

                $price = fake()->numberBetween(10000, 2000000);
                $qty = fake()->numberBetween(1, 3);
                $lineTotal = $price * $qty;

                OrderItem::create([
                    'uuid' => fake()->uuid(),
                    'order_id' => $order->id,
                    'product_uuid' => fake()->uuid(),
                    'product_name' => fake()->randomElement([
                            'iPhone',
                            'MacBook',
                            'AirPods',
                            'Samsung Galaxy',
                            'PlayStation',
                        ]) . ' ' . fake()->numberBetween(1,20),
                    'unit_price' => $price,
                    'currency' => 'CZK',
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ]);

                $subtotal += $lineTotal;
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);
        });
    }
}
