<?php
    namespace App\Messaging\Payloads;

    use Carbon\CarbonInterface;
    use Order;

    class OrderCreatedPayload extends EventPayload
    {
        public static function build(
            Order $order,
            string $eventId,
            CarbonInterface $occurredAt,
            string $correlationId,
        ): array {
            return self::envelope(
                $eventId,
                'OrderCreated',
                'order-service',
                $occurredAt,
                $correlationId,
                [
                    'order_id' => $order->id,
                    'items' => $order->items->map(fn ($item) => [
                        'order_item_id' => $item->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                    ])->values()->all(),
                    'total' => $order->total,
                ]
            );
        }
    }
