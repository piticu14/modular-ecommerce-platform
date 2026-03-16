<?php

    namespace App\Stock\Application\Actions;

    use App\Messaging\DTO\StockFailedPayload;
    use App\Messaging\DTO\StockReservedPayload;
    use App\Messaging\Infrastructure\Models\OutboxEvent;
    use App\Product\Domain\Models\Product;
    use App\Stock\Domain\Models\StockReservation;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;

    class ReserveStockAction
    {
        public function handle(array $event): void
        {

            $data = $event['data'];
            $correlationId = $event['correlation_id'] ?? null;

            $orderUuid = $data['order_uuid'];
            $items = collect($data['items']);


            $products = $this->lockProducts($items);

            if (!$this->allStockAvailable($products, $items)) {

                $this->storeFailedEvent($orderUuid, $items, $correlationId);

                return;
            }

            $this->reserveStock($products, $items, $orderUuid, $correlationId);

            $this->storeReservedEvent($orderUuid, $items, $correlationId);

        }

        private function lockProducts($items)
        {
            $productUuids = $items
                ->pluck('product_uuid')
                ->unique()
                ->sort()
                ->values();

            return Product::query()
                ->whereIn('uuid', $productUuids)
                ->lockForUpdate()
                ->get()
                ->keyBy('uuid');
        }

        private function allStockAvailable($products, $items): bool
        {
            foreach ($items as $item) {

                $product = $products->get($item['product_uuid']);

                if (!$product) {
                    Log::error('Product missing during reservation', [
                        'product_id' => $item['product_id']
                    ]);
                    return false;
                }

                if ($product->available_stock < $item['quantity']) {
                    return false;
                }
            }

            return true;
        }

        private function reserveStock($products, $items, $orderUuid, $correlationId): void
        {
            foreach ($items as $item) {

                $product = $products->get($item['product_uuid']);

                $product->increment('stock_reserved', $item['quantity']);

                StockReservation::firstOrCreate([
                    'order_item_uuid' => $item['order_item_uuid'],
                ], [
                    'order_uuid' => $orderUuid,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'reserved',
                    'correlation_id' => $correlationId,
                ]);
            }
        }

        private function storeReservedEvent($orderUuid, $items, $correlationId): void
        {
            $eventId = (string) Str::uuid();
            $occurredAt = now();

            OutboxEvent::create([
                'id' => $eventId,
                'event_type' => 'StockReserved',
                'routing_key' => 'stock.reserved',
                'payload' => StockReservedPayload::build(
                    orderUuid: $orderUuid,
                    eventId: $eventId,
                    occurredAt: $occurredAt,
                    correlationId: $correlationId,
                    items: $items
                ),
                'correlation_id' => $correlationId,
                'occurred_at' => now(),
            ]);

            Log::info('Stock reserved', [
                'order_uuid' => $orderUuid,
                'items' => $items,
                'correlation_id' => $correlationId,
            ]);
        }

        private function storeFailedEvent($orderUuid, $items, $correlationId): void
        {
            $eventId = (string) Str::uuid();
            $occurredAt = now();

            OutboxEvent::create([
                'id' => (string) Str::uuid(),
                'event_type' => 'StockFailed',
                'routing_key' => 'stock.failed',
                'payload' => StockFailedPayload::build(
                    orderUuid: $orderUuid,
                    eventId: $eventId,
                    occurredAt: $occurredAt,
                    correlationId: $correlationId,
                    items: $items
                ),
                'correlation_id' => $correlationId,
                'occurred_at' => now(),
            ]);

            Log::warning('Stock reservation failed', [
                'order_uuid' => $orderUuid,
                'items' => $items,
                'correlation_id' => $correlationId,
            ]);
        }
    }
