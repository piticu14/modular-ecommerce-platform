<?php

    namespace App\Product\Domain\Models;

    use App\Product\Domain\Enums\ProductStatus;
    use App\Product\Domain\Exceptions\ProductAlreadyArchivedException;
    use App\Stock\Domain\Models\StockReservation;
    use Database\Factories\ProductFactory;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\HasMany;

    class Product extends Model
    {

        use HasFactory;
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

        public function archive(): void
        {

            if ($this->status->isArchived()) {
                throw new ProductAlreadyArchivedException();
            }

            $this->update([
                'status' => ProductStatus::ARCHIVED
            ]);
        }

        public function getRouteKeyName(): string
        {
            return 'uuid';
        }


        protected static function newFactory()
        {
            return ProductFactory::new();
        }
    }
