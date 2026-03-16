<?php

    namespace App\Http\Resources;

    use Illuminate\Http\Resources\Json\JsonResource;

    class OrderResource extends JsonResource
    {
        public function toArray($request): array
        {
            return [
                'id' => $this->uuid,
                'status' => $this->status,
                'currency' => $this->currency,
                'subtotal' => $this->subtotal,
                'total' => $this->total,
                'items' => $this->items->map(function ($item) {
                    return [
                        'product_uuid' => $item->product_uuid,
                        'product_name' => $item->product_name,
                        'price' => $item->price,
                        'currency' => $item->currency,
                        'quantity' => $item->quantity,
                        'line_total' => $item->line_total,
                    ];
                }),
                'created_at' => $this->created_at,
            ];
        }
    }
