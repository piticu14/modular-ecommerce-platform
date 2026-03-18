<?php

namespace App\Stock\Domain\Models;

use App\Product\Domain\Enums\ProductStatus;
use App\Product\Domain\Models\Product;
use App\Stock\Domain\Enums\StockReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $order_uuid,
 * @property string $order_item_uuid,
 * @property int $product_id,
 * @property int $quantity,
 * @property ProductStatus $status,
 * @property string $event_id,
 * @property string $correlation_id,
 */
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
