<?php

namespace App\Http\Resources\Api\V1\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array{id: string, status: string, currency: string, subtotal: int, total: int, items: array<int, array{product_uuid: string, product_name: string, price: int, currency: string, quantity: int, line_total: int}>, created_at: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) ($this->resource['id'] ?? '9a2e88a5-0000-0000-0000-000000000000'),
            'status' => (string) ($this->resource['status'] ?? 'pending'),
            'currency' => (string) ($this->resource['currency'] ?? 'CZK'),
            'subtotal' => (int) ($this->resource['subtotal'] ?? 19998),
            'total' => (int) ($this->resource['total'] ?? 19998),
            'items' => (array) ($this->resource['items'] ?? [
                [
                    'product_uuid' => '8b3f99b6-0000-0000-0000-000000000000',
                    'product_name' => 'My Product',
                    'price' => 29999,
                    'currency' => 'CZK',
                    'quantity' => 2,
                    'line_total' => 19998,
                ]
            ]),
            'created_at' => (string) ($this->resource['created_at'] ?? now()->toIso8601String()),
        ];
    }
}
