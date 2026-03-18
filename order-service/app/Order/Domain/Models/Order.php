<?php

namespace App\Order\Domain\Models;

use App\Order\Domain\Enums\OrderStatus;
use App\Order\Domain\Exceptions\OrderAlreadyFinalException;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Override;

/**
 * @property string $uuid,
 * @property int $user_id,
 * @property OrderStatus $status,
 * @property string $currency,
 * @property int $subtotal,
 * @property int $total,
 */
class Order extends Model
{
    /**
     * @use HasFactory<OrderFactory>
     */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'status',
        'currency',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    #[Override]
    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (! $order->uuid) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cancel(): void
    {
        $updated = $this->newQuery()
            ->where('id', $this->getKey())
            ->where('status', OrderStatus::PENDING)
            ->update([
                'status' => OrderStatus::CANCELLED,
            ]);

        if ($updated === 0) {
            throw new OrderAlreadyFinalException('Order cannot be cancelled.');
        }

        $this->status = OrderStatus::CANCELLED;
    }

    #[Override]
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function newFactory()
    {
        return OrderFactory::new();
    }
}
