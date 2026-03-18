<?php

namespace App\Http\Resources\Stock;

use App\Stock\Domain\Models\StockReservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin StockReservation
 */
class StockReservationResource extends JsonResource
{
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var StockReservation $stockReservation */
        $stockReservation = $this->resource;

        return [
            'id' => $stockReservation->id,
            'order_uuid' => $stockReservation->order_uuid,
            'order_item_uuid' => $stockReservation->order_item_uuid,
            'product_id' => $stockReservation->product_id,
            'quantity' => $stockReservation->quantity,
            'status' => $stockReservation->status,
            'created_at' => $stockReservation->created_at,
            'updated_at' => $stockReservation->updated_at,
        ];
    }
}
