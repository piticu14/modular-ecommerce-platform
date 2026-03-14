<?php

    namespace app\Messaging\Consumers;

    use App\Models\Order;
    use App\Models\ProcessedEvent;
    use Illuminate\Support\Facades\DB;

    class StockFailedHandler
    {
        public function handle(array $event): void
        {
            $eventId = $event['event_id'];
            $orderId = $event['payload']['order_id'];

            try {

                ProcessedEvent::create([
                    'event_id' => $eventId,
                    'processed_at' => now(),
                ]);

            } catch (\Illuminate\Database\QueryException $e) {

                return; // duplicate
            }

            DB::transaction(function () use ($orderId) {

                $order = Order::lockForUpdate()->findOrFail($orderId);

                if ($order->status !== 'PENDING') {
                    return;
                }

                $order->update([
                    'status' => 'FAILED'
                ]);

            });
        }
    }
