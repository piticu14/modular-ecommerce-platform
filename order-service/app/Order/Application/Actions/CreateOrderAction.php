<?php

    namespace Actions;

    use App\Messaging\Infrastructure\Models\OutboxEvent;
    use App\Messaging\Payloads\OrderCreatedPayload;
    use App\Order\Application\Exceptions\OrderCreationFailedException;
    use App\Support\RequestContext;
    use Dto\CreateOrderItemData;
    use Dto\ProductSnapshot;
    use Exceptions\ProductServiceUnavailableException;
    use Illuminate\Http\Client\ConnectionException;
    use Illuminate\Http\Client\RequestException;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;
    use Order;
    use ProductServiceClient;

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
        public function execute(array $items, int $userId): Order
        {

            $items = array_map(
                fn (array $item) => CreateOrderItemData::from($item),
                $items
            );


            $productIds = array_map(
                fn (CreateOrderItemData $item): int => $item->productId,
                $items
            );

            try {
                $products = $this->productServiceClient->getProducts($productIds);
            } catch (ProductServiceUnavailableException $e) {

                throw new OrderCreationFailedException(
                    'Cannot create order because product service is unavailable.',
                    previous: $e
                );
            }


            return DB::transaction(function () use ($items, $products, $userId) {

                $order = Order::create([
                    'user_id' => $userId,
                    'status' => 'PENDING',
                    'currency' => $this->resolveCurrency($items, $products),
                    'subtotal' => 0,
                    'total' => 0
                ]);

                $subtotal = 0;

                foreach ($items as $item) {

                    $product = $products[$item->productId] ?? null;

                    $unitPrice = (int) $product->price;

                    $lineTotal = $unitPrice * $item->quantity;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $unitPrice,
                        'currency' => $product->currency,
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
                    $eventId = (string) Str::uuid();
                    $occurredAt = now();
                    $correlationId = RequestContext::correlationId();

                    OutboxEvent::create([
                        'id' => $eventId,
                        'event_type' => 'OrderCreated',
                        'routing_key' => 'order.created',
                        'payload' => OrderCreatedPayload::build(
                            order: $order,
                            eventId: $eventId,
                            occurredAt: $occurredAt,
                            correlationId: $correlationId,
                        ),
                        'correlation_id' => $correlationId,
                        'occurred_at' => $occurredAt,
                    ]);

                });

                return $order->fresh('items');
            });
        }


        /**
         * @param array<int, CreateOrderItemData> $items
         * @param array<int, ProductSnapshot> $products
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
