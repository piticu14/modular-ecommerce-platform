<?php

namespace App\Stock\Domain\Models;

use App\Product\Domain\Models\Product;
use App\Stock\Domain\Enums\StockReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReservation extends Model
{
    protected $fillable = [
        'order_uuid',
        'order_item_uuid',
        'product_id',
        'quantity',
        'status',
        'event_id',
        'correlation_id',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'quantity' => 'integer',
        'status' => StockReservationStatus::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
