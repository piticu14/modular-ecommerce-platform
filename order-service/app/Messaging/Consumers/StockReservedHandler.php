<?php

    namespace App\Messaging\Consumers;

    use App\Messaging\Infrastructure\Models\ProcessedEvent;
    use App\Order\Domain\Models\Order;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Log;
    class StockReservedHandler
    {
        public function handle(array $event): void
        {

            DB::transaction(function () use ($event) {
                $eventId = $event['event_id'];
                $orderId = $event['data']['order_id'];

                if (ProcessedEvent::where([
                    'event_id' => $eventId,
                    'consumer' => 'stock_reserved'
                ])->lockForUpdate()->exists()) {
                    return;
                }

                $order = Order::lockForUpdate()->findOrFail($orderId);

                if ($order->status !== 'PENDING') {
                    return;
                }

                $order->update([
                    'status' => 'CONFIRMED'
                ]);

                ProcessedEvent::create([
                    'event_id' => $eventId,
                    'consumer' => 'stock_reserved',
                    'processed_at' => now(),
                ]);

                Log::info('StockReserved received', [
                    'order_id' => $orderId,
                    'correlation_id' => $event['correlation_id'] ?? null,
                ]);

            });
        }
    }
