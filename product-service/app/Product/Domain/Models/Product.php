<?php

namespace App\Product\Domain\Models;

use App\Product\Domain\Enums\ProductStatus;
use App\Product\Domain\Exceptions\ProductAlreadyArchivedException;
use App\Stock\Domain\Models\StockReservation;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Override;

/**
 * @property string $uuid,
 * @property string $name,
 * @property int $price,
 * @property string $currency,
 * @property int $stock_on_hand,
 * @property int $stock_reserved,
 * @property ProductStatus $status,
 */
class Product extends Model
{
    /**
     * @use HasFactory<ProductFactory>
     */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'price',
        'currency',
        'stock_on_hand',
        'stock_reserved',
        'status',
    ];

    #[Override]
    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (! $product->uuid) {
                $product->uuid = (string) Str::uuid();
            }
        });
    }

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
            throw new ProductAlreadyArchivedException;
        }

        $this->update([
            'status' => ProductStatus::ARCHIVED,
        ]);
    }

    #[Override]
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function newFactory()
    {
        return ProductFactory::new();
    }
}
