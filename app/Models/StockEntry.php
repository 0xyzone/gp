<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEntry extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'entry_date',
        'reference_number',
        'notes',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'entry_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Booted to recalculate product stock automatically whenever an entry is created, updated, or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function (StockEntry $entry): void {
            $entry->product?->recalculateStock();
        });

        static::deleted(function (StockEntry $entry): void {
            $entry->product?->recalculateStock();
        });
    }
}
