<?php

    namespace App\Actions;

    use App\Data\CreateOrderItemData;
    use App\Exceptions\ProductNotFoundException;
    use App\Messaging\Payloads\OrderCreatedPayload;
    use App\Models\Order;
    use App\Models\OutboxEvent;
    use App\Services\ProductServiceClient;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;

    readonly class CreateOrderAction
    {
        public function __construct(
            private ProductServiceClient $productServiceClient
        ) {}

        /**
         * @throws RequestException
         * @throws \Throwable
         * @throws ConnectionException
         */
        public function execute(array $items, $user): Order
        {
            $productIds = array_map(
                fn (CreateOrderItemData $item): int => $item->productId,
                $items
            );

            $products = $this->productServiceClient->getProducts($productIds);

            return DB::transaction(function () use ($items, $products, $user) {

                $order = Order::create([
                    'user_id' => $user['id'] ?? null,
                    'status' => 'PENDING',
                    'currency' => $this->resolveCurrency($items, $products),
                    'subtotal' => 0,
                    'total' => 0
                ]);

                $total = $subtotal = 0;

                foreach ($items as $item) {

                    $product = $products[$item['product_id']] ?? null;

                    $unitPrice = (int) $product->price;

                    $lineTotal = $unitPrice * $item['quantity'];

                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $unitPrice,
                        'quantity' => $item->quantity,
                        'line_total' => $lineTotal,
                    ]);

                    $subtotal += $lineTotal;
                }

                $order->update([
                    'subtotal' => $subtotal,
                    'total' => $subtotal
                ]);



                DB::afterCommit(function () use ($order) {

                    OutboxEvent::create([
                        'id' => Str::uuid(),
                        'event_type' => 'OrderCreated',
                        'routing_key' => 'order.created',
                        'payload' => OrderCreatedPayload::build($order),
                        'occurred_at' => now()
                    ]);

                });

                return $order->fresh('items');
            });
        }


        /**
         * @param array<int, CreateOrderItemData> $items
         * @param array<int, \App\Data\ProductSnapshot> $products
         */
        private function resolveCurrency(array $items, array $products): string
        {
            $currencies = [];

            foreach ($items as $item) {
                $currencies[] = $products[$item->productId]->currency;
            }

            $currencies = array_values(array_unique($currencies));

            if (count($currencies) !== 1) {
                throw new \LogicException('Order contains products with different currencies.');
            }

            return $currencies[0];
        }
    }
