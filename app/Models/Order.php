<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'status',
        'subtotal',
        'discount',
        'grand_total',
        'payment_method',
        'cash_received',
        'change_returned',
        'notes',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'change_returned' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
