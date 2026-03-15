<?php

    namespace App\Messaging\Consumers;

    use App\Messaging\Infrastructure\Models\ProcessedEvent;
    use App\Order\Domain\Models\Order;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;

    class StockFailedHandler
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
                'consumer' => 'stock_failed',
                'processed_at' => now(),
            ]);

            DB::transaction(function () use ($orderId) {

                $order = Order::lockForUpdate()->findOrFail($orderId);

                if ($order->status !== 'PENDING') {
                    return;
                }

                $order->update([
                    'status' => 'FAILED'
                ]);

                Log::warning('StockFailed received', [
                    'order_id' => $orderId,
                    'correlation_id' => $event['correlation_id'] ?? null,
                ]);

            });
        }
    }
