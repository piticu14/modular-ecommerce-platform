<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

class ProductResource extends JsonResource
{
    /**
     * @return array{
     *     uuid: string,
     *     name: string,
     *     price: int,
     *     currency: string,
     *     status: string,
     *     stock_on_hand: int,
     *     stock_reserved: int,
     *     stock_available: int,
     *     created_at: string,
     *     updated_at: string
     * }
     */
    #[Override]
    public function toArray(Request $request): array
    {
        // Scramble (docs)
        if ($this->resource === null) {
            return [
                'uuid' => '9a2e88a5-0000-0000-0000-000000000000',
                'name' => 'My Product',
                'price' => 29999,
                'currency' => 'CZK',
                'status' => 'active',
                'stock_on_hand' => 100,
                'stock_reserved' => 0,
                'stock_available' => 100,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ];
        }

        /**
         * @var array{
         *     uuid: string,
         *     name: string,
         *     price: int,
         *     currency: string,
         *     status: string,
         *     stock_on_hand: int,
         *     stock_reserved: int,
         *     stock_available: int,
         *     created_at: string,
         *     updated_at: string
         * } $data
         */
        $data = $this->resource;

        return $data;
    }
}
