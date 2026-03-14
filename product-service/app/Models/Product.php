<?php
    namespace App\Models;

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
        ];

        protected $casts = [
            'price' => 'integer',
            'stock_on_hand' => 'integer',
            'stock_reserved' => 'integer',
        ];

        public function reservations(): HasMany
        {
            return $this->hasMany(StockReservation::class);
        }

        public function getAvailableStockAttribute(): int
        {
            return $this->stock_on_hand - $this->stock_reserved;
        }
    }
