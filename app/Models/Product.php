<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the effective selling price (offer_price if set and positive, otherwise regular price).
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->offer_price !== null && (float) $this->offer_price > 0) {
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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
