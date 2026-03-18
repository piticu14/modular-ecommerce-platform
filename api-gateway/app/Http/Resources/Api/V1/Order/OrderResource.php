<?php

namespace App\Http\Resources\Api\V1\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     status: string,
     *     currency: string,
     *     subtotal: int,
     *     total: int,
     *     items: array<int, array{
     *         product_uuid: string,
     *         product_name: string,
     *         price: int,
     *         currency: string,
     *         quantity: int,
     *         line_total: int
     *     }>,
     *     created_at: string
     * }
     */
    public function toArray(Request $request): array
    {

        // Scramble (docs)
        if ($this->resource === null) {
            return [
                'id' => '9a2e88a5-0000-0000-0000-000000000000',
                'status' => 'pending',
                'currency' => 'CZK',
                'subtotal' => 19998,
                'total' => 19998,
                'items' => [
                    [
                        'product_uuid' => '8b3f99b6-0000-0000-0000-000000000000',
                        'product_name' => 'My Product',
                        'price' => 29999,
                        'currency' => 'CZK',
                        'quantity' => 2,
                        'line_total' => 19998,
                    ],
                ],
                'created_at' => now()->toISOString(),
            ];
        }
        /**
         * @var array{
         *     id: string,
         *     status: string,
         *     currency: string,
         *     subtotal: int,
         *     total: int,
         *     items: array<int, array{
         *         product_uuid: string,
         *         product_name: string,
         *         price: int,
         *         currency: string,
         *         quantity: int,
         *         line_total: int
         *     }>,
         *     created_at: string
         * } $data
         */
        $data = $this->resource;

        return $data;
    }
}
