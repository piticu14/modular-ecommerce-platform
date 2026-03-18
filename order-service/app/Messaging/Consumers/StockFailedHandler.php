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

        DB::transaction(function () use ($event) {

            $eventId = $event['event_id'];
            $orderUuid = $event['data']['order_uuid'];

            if (ProcessedEvent::where([
                'event_id' => $eventId,
                'consumer' => 'stock_failed',
            ])->lockForUpdate()->exists()) {
                return;
            }

            $order = Order::where('uuid', $orderUuid)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->status !== 'PENDING') {
                return;
            }

            $order->update([
                'status' => 'FAILED',
            ]);

            ProcessedEvent::create([
                'event_id' => $eventId,
                'consumer' => 'stock_failed',
                'processed_at' => now(),
            ]);

            Log::warning('StockFailed received', [
                'order_uuid' => $orderUuid,
                'correlation_id' => $event['correlation_id'] ?? null,
            ]);

        });
    }
}
