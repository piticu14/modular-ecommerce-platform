<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'price' => $this->price,
            'currency' => $this->currency,
            'status' => $this->status,
            'stock_on_hand' => $this->stock_on_hand,
            'stock_reserved' => $this->stock_reserved,
            'stock_available' => $this->stock_available,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
