<?php

namespace App\Http\Resources;

use App\Order\Domain\Models\Order;
use App\Order\Domain\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    #[Override]
    public function toArray($request): array
    {

        /** @var Order $order */
        $order = $this->resource;

        /** @var Collection<int, OrderItem> $items */
        $items = $order->items;

        return [
            'id' => $order->uuid,
            'status' => $order->status->value,
            'currency' => $order->currency,
            'subtotal' => $order->subtotal,
            'total' => $order->total,
            'items' => $items
                ->toBase()
                ->map(fn (OrderItem $item) => [
                    'product_uuid' => $item->product_uuid,
                    'product_name' => $item->product_name,
                    'unit_price' => $item->unit_price,
                    'currency' => $item->currency,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                ])
                ->values()
                ->all(),
            'created_at' => $order->created_at,
        ];
    }
}
