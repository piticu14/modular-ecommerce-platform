<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array{uuid: string, name: string, price: int, currency: string, status: string, stock_on_hand: int, stock_reserved: int, stock_available: int, created_at: string, updated_at: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => (string) ($this->resource['uuid'] ?? '9a2e88a5-0000-0000-0000-000000000000'),
            'name' => (string) ($this->resource['name'] ?? 'My Product'),
            'price' => (int) ($this->resource['price'] ?? 29999),
            'currency' => (string) ($this->resource['currency'] ?? 'CZK'),
            'status' => (string) ($this->resource['status'] ?? 'active'),
            'stock_on_hand' => (int) ($this->resource['stock_on_hand'] ?? 100),
            'stock_reserved' => (int) ($this->resource['stock_reserved'] ?? 0),
            'stock_available' => (int) ($this->resource['stock_available'] ?? 100),
            'created_at' => (string) ($this->resource['created_at'] ?? now()->toIso8601String()),
            'updated_at' => (string) ($this->resource['updated_at'] ?? now()->toIso8601String()),
        ];
    }
}
