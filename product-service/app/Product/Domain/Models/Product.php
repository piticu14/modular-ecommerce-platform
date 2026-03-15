<?php

    namespace App\Product\Domain\Models;

    use App\Product\Domain\Enums\ProductStatus;
    use App\Stock\Domain\Models\StockReservation;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasMany;

    class Product extends Model
    {
        protected $fillable = [
            'name',
            'price',
            'currency',
            'stock_on_hand',
            'stock_reserved',
            'status',
        ];

        protected $casts = [
            'status' => ProductStatus::class,
        ];

        public function reservations(): HasMany
        {
            return $this->hasMany(StockReservation::class);
        }

        public function getStockAvailableAttribute(): int
        {
            return max(0, $this->stock_on_hand - $this->stock_reserved);
        }
    }
