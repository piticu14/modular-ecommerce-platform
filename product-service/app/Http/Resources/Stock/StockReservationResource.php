<?php

namespace App\Http\Resources\Stock;

use App\Stock\Domain\Models\StockReservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StockReservation
 */
class StockReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_uuid' => $this->order_uuid,
            'order_item_uuid' => $this->order_item_uuid,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
