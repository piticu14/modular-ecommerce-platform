<?php

namespace App\Messaging\DTO;

use App\Order\Domain\Models\Order;
use App\Order\Domain\Models\OrderItem;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderCreatedPayload extends EventPayload
{
    public static function build(
        Order $order,
        string $eventId,
        CarbonInterface $occurredAt,
        string $correlationId,
    ): array {

        /** @var Collection<int, OrderItem> $items */
        $items = $order->items;

        return self::envelope(
            $eventId,
            'OrderCreated',
            'order-service',
            $occurredAt,
            $correlationId,
            [
                'order_uuid' => $order->uuid,
                'items' => $items
                    ->toBase()
                    ->map(fn (OrderItem $item) => [
                        'order_item_uuid' => $item->uuid,
                        'product_uuid' => $item->product_uuid,
                        'quantity' => $item->quantity,
                    ])
                    ->values()
                    ->all(),
                'total' => $order->total,
            ]
        );
    }
}
