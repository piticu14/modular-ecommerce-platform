<?php

    namespace App\Order\Domain\Models;

    use App\Order\Domain\Enums\OrderStatus;
    use App\Order\Domain\Exceptions\OrderAlreadyFinalException;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Support\Str;

    class Order extends Model
    {
        protected $fillable = [
            'uuid',
            'user_id',
            'status',
            'currency',
            'subtotal',
            'total',
        ];

        protected static function booted(): void
        {
            static::creating(function (Order $order): void {
                if (!$order->uuid) {
                    $order->uuid = (string) Str::uuid();
                }
            });
        }

        public function items(): HasMany
        {
            return $this->hasMany(OrderItem::class);
        }

        public function cancel(): void
        {
            $updated = $this->newQuery()
                ->where('id', $this->id)
                ->where('status', OrderStatus::PENDING)
                ->update([
                    'status' => OrderStatus::CANCELLED
                ]);

            if ($updated === 0) {
                throw new OrderAlreadyFinalException();
            }

            $this->status = OrderStatus::CANCELLED;
        }

        public function getRouteKeyName(): string
        {
            return 'uuid';
        }
    }
