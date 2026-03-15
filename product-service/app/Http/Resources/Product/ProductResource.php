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
                'id' => $this->id,
                'name' => $this->name,
                'price' => $this->price,
                'currency' => $this->currency,
                'stock_on_hand' => $this->stock_on_hand,
                'stock_reserved' => $this->stock_reserved,
                'stock_available' => $this->stock_on_hand - $this->stock_reserved,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }
    }
