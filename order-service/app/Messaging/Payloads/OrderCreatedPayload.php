<?php
    namespace App\Messaging\Payloads;

    use App\Models\Order;
    use Illuminate\Support\Str;

    class OrderCreatedPayload
    {
        public static function build(Order $order): array
        {
            return [
                'event_id' => (string) Str::uuid(),

                'event_type' => 'OrderCreated',

                'event_version' => 1,

                'source' => 'order-service',

                'occurred_at' => now()->toISOString(),

                'correlation_id' => request()->header('X-Correlation-ID') ?? (string) Str::uuid(),

                'payload' => [

                    'order_id' => $order->id,

                    'items' => $order->items->map(fn ($item) => [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                    ])->values(),

                    'total_price' => $order->total_price,
                ],
            ];
        }
    }
