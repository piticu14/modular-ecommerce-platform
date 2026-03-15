<?php


    namespace App\Http\Resources\Stock;

    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\JsonResource;

    class StockReservationResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            return [
                'id' => $this->id,
                'order_id' => $this->order_id,
                'order_item_id' => $this->order_item_id,
                'product_id' => $this->product_id,
                'quantity' => $this->quantity,
                'status' => $this->status,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        }
    }
