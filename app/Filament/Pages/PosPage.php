<?php

namespace App\Filament\Pages;

use App\Events\CartCleared;
use App\Events\CartUpdated;
use App\Events\OrderCompleted;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use UnitEnum;

class PosPage extends Page
{
    protected string $view = 'filament.pages.pos-page';

    protected static ?string $navigationLabel = 'POS Terminal';

    protected static ?string $title = 'Expo POS Terminal';

    protected static ?string $slug = 'pos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static string|UnitEnum|null $navigationGroup = 'Expo POS';

    protected static ?int $navigationSort = 1;

    // Filter & Search
    public string $search = '';

    public ?int $selectedCategory = null;

    // Cart items: [ productId => [ 'id' => ..., 'name' => ..., 'price' => ..., 'quantity' => ..., 'total' => ..., 'image' => ... ] ]
    public array $cart = [];

    // Order calculation
    public float $discount = 0.00;

    public string $paymentMethod = 'cash'; // 'cash' or 'qr'

    public ?float $cashReceived = null;

    public ?string $notes = '';

    public function mount(): void
    {
        $this->broadcastCartState();
    }

    public function getCategoriesProperty(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function getProductsProperty(): Collection
    {
        return Product::query()
            ->where('is_active', true)
            ->when($this->selectedCategory, fn ($q) => $q->where('category_id', $this->selectedCategory))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function filterCategory(?int $categoryId = null): void
    {
        $this->selectedCategory = $categoryId;
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['total'] = round($this->cart[$productId]['quantity'] * $this->cart[$productId]['price'], 2);
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'price' => (float) $product->price,
                'quantity' => 1,
                'total' => (float) $product->price,
                'image' => $product->image ? asset('storage/'.$product->image) : null,
            ];
        }

        $this->broadcastCartState();
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);

            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity'] = $quantity;
            $this->cart[$productId]['total'] = round($quantity * $this->cart[$productId]['price'], 2);
            $this->broadcastCartState();
        }
    }

    public function incrementQuantity(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['total'] = round($this->cart[$productId]['quantity'] * $this->cart[$productId]['price'], 2);
            $this->broadcastCartState();
        }
    }

    public function decrementQuantity(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
                $this->cart[$productId]['total'] = round($this->cart[$productId]['quantity'] * $this->cart[$productId]['price'], 2);
            } else {
                unset($this->cart[$productId]);
            }
            $this->broadcastCartState();
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
        $this->broadcastCartState();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->discount = 0.00;
        $this->cashReceived = null;
        $this->paymentMethod = 'cash';
        $this->notes = '';

        CartCleared::dispatch();

        Notification::make()
            ->title('Cart Cleared')
            ->info()
            ->send();
    }

    public function setQuickCash(float $amount): void
    {
        $this->cashReceived = $amount;
        $this->broadcastCartState();
    }

    public function setExactCash(): void
    {
        $this->cashReceived = $this->getGrandTotalProperty();
        $this->broadcastCartState();
    }

    public function updatedPaymentMethod(): void
    {
        $this->broadcastCartState();
    }

    public function updatedCashReceived(): void
    {
        $this->broadcastCartState();
    }

    public function updatedDiscount(): void
    {
        if ($this->discount < 0) {
            $this->discount = 0.00;
        }
        $this->broadcastCartState();
    }

    public function getSubtotalProperty(): float
    {
        return round(collect($this->cart)->sum('total'), 2);
    }

    public function getGrandTotalProperty(): float
    {
        $total = $this->getSubtotalProperty() - (float) $this->discount;

        return round(max(0, $total), 2);
    }

    public function getChangeDueProperty(): float
    {
        if ($this->paymentMethod !== 'cash' || $this->cashReceived === null) {
            return 0.00;
        }

        $change = (float) $this->cashReceived - $this->getGrandTotalProperty();

        return round($change, 2);
    }

    public function broadcastCartState(): void
    {
        $payload = [
            'cart' => array_values($this->cart),
            'item_count' => collect($this->cart)->sum('quantity'),
            'subtotal' => $this->getSubtotalProperty(),
            'discount' => (float) $this->discount,
            'grand_total' => $this->getGrandTotalProperty(),
            'payment_method' => $this->paymentMethod,
            'cash_received' => (float) ($this->cashReceived ?? 0),
            'change_due' => $this->getChangeDueProperty(),
        ];

        CartUpdated::dispatch($payload);
    }

    public function completeOrder(): void
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Cannot place empty order')
                ->warning()
                ->send();

            return;
        }

        $grandTotal = $this->getGrandTotalProperty();

        if ($this->paymentMethod === 'cash') {
            if ($this->cashReceived === null || $this->cashReceived < $grandTotal) {
                Notification::make()
                    ->title('Insufficient cash received')
                    ->body('Cash received must be equal to or greater than the grand total.')
                    ->danger()
                    ->send();

                return;
            }
        }

        $order = Order::create([
            'order_number' => 'ORD-'.strtoupper(Str::random(6)),
            'status' => 'completed',
            'subtotal' => $this->getSubtotalProperty(),
            'discount' => $this->discount,
            'grand_total' => $grandTotal,
            'payment_method' => $this->paymentMethod,
            'cash_received' => $this->paymentMethod === 'cash' ? $this->cashReceived : null,
            'change_returned' => $this->paymentMethod === 'cash' ? $this->getChangeDueProperty() : null,
            'notes' => $this->notes,
        ]);

        foreach ($this->cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'unit_price' => $item['price'],
                'quantity' => $item['quantity'],
                'total_price' => $item['total'],
            ]);
        }

        OrderCompleted::dispatch([
            'order_number' => $order->order_number,
            'items' => array_values($this->cart),
            'grand_total' => $grandTotal,
            'payment_method' => $this->paymentMethod,
            'cash_received' => $order->cash_received,
            'change_returned' => $order->change_returned,
        ]);

        Notification::make()
            ->title('Order '.$order->order_number.' Completed!')
            ->success()
            ->send();

        // Reset cart
        $this->cart = [];
        $this->discount = 0.00;
        $this->cashReceived = null;
        $this->paymentMethod = 'cash';
        $this->notes = '';
    }
}
