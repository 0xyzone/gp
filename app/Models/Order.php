<?php

namespace App\Models;

use App\Events\OrderStatusChanged;
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
        'payment_status',
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

    /**
     * Mark order as paid when customer tenders payment (cash or qr).
     */
    public function markAsPaid(string $method = 'cash', ?float $cashReceived = null, ?float $changeReturned = null): bool
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $method,
            'cash_received' => $method === 'cash' ? ($cashReceived ?? $this->grand_total) : null,
            'change_returned' => $method === 'cash' ? ($changeReturned ?? 0.00) : null,
        ]);

        OrderStatusChanged::dispatch([
            'order_id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'payment_status' => 'paid',
            'payment_method' => $method,
        ]);

        return true;
    }

    /**
     * Mark order as served / fulfilled to the customer.
     */
    public function markAsServed(): bool
    {
        $this->update(['status' => 'served']);

        OrderStatusChanged::dispatch([
            'order_id' => $this->id,
            'order_number' => $this->order_number,
            'status' => 'served',
        ]);

        return true;
    }

    /**
     * Cancel order and automatically return stock to inventory.
     */
    public function cancelAndRestock(): bool
    {
        if ($this->status === 'cancelled') {
            return false;
        }

        $this->loadMissing('items.product');

        // Automatically restock products
        foreach ($this->items as $item) {
            if ($item->product && $item->product->track_stock) {
                $item->product->incrementStock($item->quantity);
            }
        }

        $this->update(['status' => 'cancelled']);

        OrderStatusChanged::dispatch([
            'order_id' => $this->id,
            'order_number' => $this->order_number,
            'status' => 'cancelled',
        ]);

        return true;
    }
}
