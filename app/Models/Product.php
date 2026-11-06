<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'code',
        'price',
        'offer_price',
        'track_stock',
        'stock_quantity',
        'low_stock_threshold',
        'image',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'track_stock' => 'boolean',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Determine if product is in stock or available for sale.
     */
    public function getIsInStockAttribute(): bool
    {
        if (! $this->track_stock) {
            return true;
        }

        return (int) $this->stock_quantity > 0;
    }

    /**
     * Determine if product has reached low stock warning level.
     */
    public function getIsLowStockAttribute(): bool
    {
        if (! $this->track_stock) {
            return false;
        }

        return (int) $this->stock_quantity > 0 && (int) $this->stock_quantity <= (int) $this->low_stock_threshold;
    }

    /**
     * Decrement stock quantity safely if tracking is enabled.
     */
    public function decrementStock(int $quantity = 1): bool
    {
        if (! $this->track_stock) {
            return true;
        }

        $newStock = max(0, (int) $this->stock_quantity - $quantity);
        $this->update(['stock_quantity' => $newStock]);

        return true;
    }

    /**
     * Increment stock quantity if tracking is enabled.
     */
    public function incrementStock(int $quantity = 1): bool
    {
        if (! $this->track_stock) {
            return true;
        }

        $this->increment('stock_quantity', $quantity);

        return true;
    }

    /**
     * Get the resolved public URL of the product image, or null if not set.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }

    /**
     * Get the effective selling price (offer_price if set, positive, and strictly less than price, otherwise regular price).
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->offer_price !== null && (float) $this->offer_price > 0 && (float) $this->offer_price < (float) $this->price) {
            return (float) $this->offer_price;
        }

        return (float) $this->price;
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if ($product->sort_order === null || $product->sort_order === 0) {
                $maxSort = static::where('category_id', $product->category_id)->max('sort_order')
                    ?? static::max('sort_order')
                    ?? 0;
                $product->sort_order = $maxSort + 1;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    /**
     * Recalculate total stock from all stock entries minus completed orders.
     */
    public function recalculateStock(): void
    {
        $added = (int) $this->stockEntries()->where('type', 'add')->sum('quantity');
        $subtracted = (int) $this->stockEntries()->where('type', 'subtract')->sum('quantity');

        // Count sold items from completed and served orders
        $sold = (int) $this->orderItems()
            ->whereHas('order', fn ($q) => $q->whereIn('status', ['completed', 'served']))
            ->sum('quantity');

        $computedStock = max(0, $added - $subtracted - $sold);

        $this->updateQuietly(['stock_quantity' => $computedStock]);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
