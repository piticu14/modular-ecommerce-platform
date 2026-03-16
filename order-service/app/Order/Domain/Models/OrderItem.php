<?php

    namespace App\Order\Domain\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    class OrderItem extends Model
    {
        use HasFactory;

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

        public function order(): BelongsTo
        {
            return $this->belongsTo(Order::class);
        }
    }
