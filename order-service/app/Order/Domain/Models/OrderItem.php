<?php

namespace App\Order\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Override;

/**
 * @property string $uuid,
 * @property int $order_id,
 * @property string $product_uuid,
 * @property string $product_name,
 * @property int $unit_price,
 * @property int $quantity,
 * @property string $currency,
 * @property int $line_total,
 */
class OrderItem extends Model
{
    protected $fillable = [
        'uuid',
        'order_id',
        'product_uuid',
        'product_name',
        'unit_price',
        'quantity',
        'currency',
        'line_total',
    ];

    #[Override]
    protected static function booted(): void
    {
        static::creating(function (OrderItem $orderItem): void {
            if (! $orderItem->uuid) {
                $orderItem->uuid = (string) Str::uuid();
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
