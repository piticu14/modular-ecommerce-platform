<?php

    namespace App\Stock\Application\Actions;

    use App\Messaging\DTO\StockFailedPayload;
    use App\Messaging\DTO\StockReservedPayload;
    use App\Messaging\Infrastructure\Models\OutboxEvent;
    use App\Product\Domain\Models\Product;
    use App\Stock\Domain\Models\StockReservation;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Str;

    class ReserveStockAction
    {
        public function handle(array $event): void
        {

            $data = $event['data'];
            $correlationId = $event['correlation_id'] ?? null;

            $orderId = $data['order_id'];
            $items = collect($data['items']);

            DB::transaction(function () use ($items, $orderId, $correlationId) {

                $products = $this->lockProducts($items);

                if (!$this->allStockAvailable($products, $items)) {

                    $this->storeFailedEvent($orderId, $items, $correlationId);

                    return;
                }

                $this->reserveStock($products, $items, $orderId, $correlationId);

                $this->storeReservedEvent($orderId, $items, $correlationId);

            });
        }

        private function lockProducts($items)
        {
            $productIds = $items
                ->pluck('product_id')
                ->unique()
                ->sort()
                ->values();

            return Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
        }

        private function allStockAvailable($products, $items): bool
        {
            foreach ($items as $item) {

                $product = $products->get($item['product_id']);

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

        private function reserveStock($products, $items, $orderId, $correlationId): void
        {
            foreach ($items as $item) {

                $product = $products->get($item['product_id']);

                $product->increment('stock_reserved', $item['quantity']);

                StockReservation::firstOrCreate([
                    'order_item_id' => $item['order_item_id'],
                ], [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'status' => 'reserved',
                    'correlation_id' => $correlationId,
                ]);
            }
        }

        private function storeReservedEvent($orderId, $items, $correlationId): void
        {
            $eventId = (string) Str::uuid();
            $occurredAt = now();

            OutboxEvent::create([
                'id' => $eventId,
                'event_type' => 'StockReserved',
                'routing_key' => 'stock.reserved',
                'payload' => StockReservedPayload::build(
                    orderId: $orderId,
                    eventId: $eventId,
                    occurredAt: $occurredAt,
                    correlationId: $correlationId,
                    items: $items
                ),
                'correlation_id' => $correlationId,
                'occurred_at' => now(),
            ]);

            Log::info('Stock reserved', [
                'order_id' => $orderId,
                'items' => $items,
                'correlation_id' => $correlationId,
            ]);
        }

        private function storeFailedEvent($orderId, $items, $correlationId): void
        {
            $eventId = (string) Str::uuid();
            $occurredAt = now();

            OutboxEvent::create([
                'id' => (string) Str::uuid(),
                'event_type' => 'StockFailed',
                'routing_key' => 'stock.failed',
                'payload' => StockFailedPayload::build(
                    orderId: $orderId,
                    eventId: $eventId,
                    occurredAt: $occurredAt,
                    correlationId: $correlationId,
                    items: $items
                ),
                'correlation_id' => $correlationId,
                'occurred_at' => now(),
            ]);

            Log::warning('Stock reservation failed', [
                'order_id' => $orderId,
                'items' => $items,
                'correlation_id' => $correlationId,
            ]);
        }
    }
