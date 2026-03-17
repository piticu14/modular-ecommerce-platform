<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockReservationResource extends JsonResource
{
    /**
     * @return array{id: int, order_uuid: string, order_item_uuid: string, product_id: int, quantity: int, status: string, created_at: string, updated_at: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) ($this->resource['id'] ?? 1),
            'order_uuid' => (string) ($this->resource['order_uuid'] ?? '9a2e88a5-0000-0000-0000-000000000000'),
            'order_item_uuid' => (string) ($this->resource['order_item_uuid'] ?? '9a2e88a5-1111-1111-1111-111111111111'),
            'product_id' => (int) ($this->resource['product_id'] ?? 123),
            'quantity' => (int) ($this->resource['quantity'] ?? 2),
            'status' => (string) ($this->resource['status'] ?? 'reserved'),
            'created_at' => (string) ($this->resource['created_at'] ?? now()->toIso8601String()),
            'updated_at' => (string) ($this->resource['updated_at'] ?? now()->toIso8601String()),
        ];
    }
}
