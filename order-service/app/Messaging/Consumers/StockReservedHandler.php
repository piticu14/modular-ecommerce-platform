<?php

    namespace App\Messaging\Consumers;

    use App\Messaging\Infrastructure\Models\ProcessedEvent;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    use Order;

    class StockReservedHandler
    {
        public function handle(array $event): void
        {

            $eventId = $event['event_id'];
            $orderId = $event['data']['order_id'];

            $exists = ProcessedEvent::where([
                'event_id' => $eventId,
                'consumer' => 'stock_reserved'
            ])->exists();

            if ($exists) {
                return;
            }

            ProcessedEvent::create([
                'event_id' => $eventId,
                'consumer' => 'stock_reserved',
                'processed_at' => now(),
            ]);

            DB::transaction(function () use ($orderId, $event) {

                $order = Order::lockForUpdate()->findOrFail($orderId);

                if ($order->status !== 'PENDING') {
                    return;
                }

                $order->update([
                    'status' => 'CONFIRMED'
                ]);

                Log::info('StockReserved received', [
                    'order_id' => $orderId,
                    'correlation_id' => $event['correlation_id'] ?? null,
                ]);

            });
        }
    }
