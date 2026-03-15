<?php

    namespace App\Stock\Domain\Models;

    use App\Product\Domain\Models\Product;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    class StockReservation extends Model
    {
        protected $fillable = [
            'order_id',
            'order_item_id',
            'product_id',
            'quantity',
            'status',
            'event_id',
            'correlation_id',
        ];

        protected $casts = [
            'order_id' => 'integer',
            'order_item_id' => 'integer',
            'product_id' => 'integer',
            'quantity' => 'integer',
        ];

        public function product(): BelongsTo
        {
            return $this->belongsTo(Product::class);
        }
    }
