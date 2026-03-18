<?php

namespace App\Http\Resources\Product;

use App\Product\Domain\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Override;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    #[Override]
    public function toArray(Request $request): array
    {
        /** @var Product $product */
        $product = $this->resource;

        return [
            'uuid' => $product->uuid,
            'name' => $product->name,
            'price' => $product->price,
            'currency' => $product->currency,
            'status' => $product->status,
            'stock_on_hand' => $product->stock_on_hand,
            'stock_reserved' => $product->stock_reserved,
            'stock_available' => $product->stock_available,
            'created_at' => $product->created_at?->toISOString(),
            'updated_at' => $product->updated_at?->toISOString(),
        ];
    }
}
