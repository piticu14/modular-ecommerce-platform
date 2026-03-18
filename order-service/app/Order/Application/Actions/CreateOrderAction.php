<?php

namespace App\Order\Application\Actions;

use App\Messaging\DTO\OrderCreatedPayload;
use App\Messaging\Infrastructure\Models\OutboxEvent;
use App\Order\Application\DTO\CreateOrderItemData;
use App\Order\Application\DTO\ProductSnapshot;
use App\Order\Application\Exceptions\OrderCreationFailedException;
use App\Order\Domain\Enums\OrderStatus;
use App\Order\Domain\Models\Order;
use App\Order\Infrastructure\Clients\ProductServiceClient;
use App\Order\Infrastructure\Exceptions\ProductServiceUnavailableException;
use App\Support\RequestContext;
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
    public function execute(array $items, int $userId): Order
    {

        $items = array_map(
            fn (array $item) => CreateOrderItemData::from($item),
            $items
        );

        $productUuids = array_map(
            fn (CreateOrderItemData $item): string => $item->productUuid,
            $items
        );

        try {
            $products = $this->productServiceClient->getProductsByUuid($productUuids);
        } catch (ProductServiceUnavailableException $e) {

            throw new OrderCreationFailedException(
                'Cannot create order because product service is unavailable.',
                previous: $e
            );
        }

        return DB::transaction(function () use ($items, $products, $userId) {

            $order = Order::create([
                'user_id' => $userId,
                'status' => OrderStatus::PENDING,
                'currency' => $this->resolveCurrency($items, $products),
                'subtotal' => 0,
                'total' => 0,
            ]);

            $subtotal = 0;

            foreach ($items as $item) {

                $product = $products[$item->productUuid] ?? null;

                $unitPrice = $product->price;

                $lineTotal = $unitPrice * $item->quantity;

                $order->items()->create([
                    'uuid' => (string) Str::uuid(),
                    'product_uuid' => $product->uuid,
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
                'total' => $subtotal,
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
     * @param  array<int, CreateOrderItemData>  $items
     * @param  array<string, ProductSnapshot>  $products
     */
    private function resolveCurrency(array $items, array $products): string
    {
        $currencies = [];

        foreach ($items as $item) {
            $currencies[] = $products[$item->productUuid]->currency;
        }

        $currencies = array_values(array_unique($currencies));

        if (count($currencies) !== 1) {
            throw new \LogicException('Order contains products with different currencies.');
        }

        return $currencies[0];
    }
}
