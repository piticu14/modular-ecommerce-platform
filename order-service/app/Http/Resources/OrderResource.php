<?php

namespace App\Http\Resources;

use App\Order\Domain\Models\Order;
use App\Order\Domain\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    public function toArray($request): array
    {

        /** @var Collection<int, OrderItem> $items */
        $items = $this->items;

        return [
            'id' => $this->uuid,
            'status' => $this->status,
            'currency' => $this->currency,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'items' => $items->map(fn (OrderItem $item) => [
                'product_uuid' => $item->product_uuid,
                'product_name' => $item->product_name,
                'unit_price' => $item->unit_price,
                'currency' => $item->currency,
                'quantity' => $item->quantity,
                'line_total' => $item->line_total,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
