<x-filament-panels::page class="fi-page-pos">
    <div class="flex flex-col xl:flex-row gap-5 -mt-3 w-full">
        <!-- LEFT: Products Catalog Screen -->
        <div class="flex-1 flex flex-col gap-4 min-w-0">
            <!-- Filter & Search Bar with Frosted Glass -->
            <div class="rounded-2xl glass-panel p-3.5 flex flex-col sm:flex-row items-center gap-3 justify-between">
                <!-- Search input -->
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-rose-400">
                        <x-heroicon-m-magnifying-glass class="w-4 h-4" />
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.250ms="search" 
                        placeholder="Search product..." 
                        class="w-full rounded-xl border border-rose-200/50 dark:border-rose-900/60 bg-white/60 dark:bg-black/30 backdrop-blur-md py-2 pl-10 pr-4 text-xs font-semibold text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-rose-300/40 focus:outline-none focus:ring-2 focus:ring-rose-500/50 focus:border-transparent transition-all shadow-[inset_0_1px_2px_rgba(0,0,0,0.06)]"
                    />
                </div>

                <!-- Category Glass Navigation Pills -->
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 scrollbar-none">
                    <button 
                        type="button"
                        wire:click="filterCategory(null)"
                        class="glass-pill px-4 py-2 rounded-xl text-xs font-black shrink-0 {{ is_null($selectedCategory) ? 'glass-pill-active' : 'glass-pill-inactive' }}"
                    >
                        All ({{ \App\Models\Product::where('is_active', true)->count() }})
                    </button>
                    @foreach($this->categories as $category)
                        <button 
                            type="button"
                            wire:click="filterCategory({{ $category->id }})"
                            class="glass-pill px-4 py-2 rounded-xl text-xs font-black shrink-0 {{ $selectedCategory === $category->id ? 'glass-pill-active' : 'glass-pill-inactive' }}"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Cards Grid: Warm Glassmorphic Surfaces -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 2xl:grid-cols-5 gap-4 overflow-y-auto max-h-[calc(100vh-215px)] p-1 scrollbar-thin">
                @forelse($this->products as $product)
                    @php
                        $inCart = isset($cart[$product->id]);
                        $qty = $inCart ? $cart[$product->id]['quantity'] : 0;
                        $imgUrl = $product->image_url ?? asset('images/placeholder-product.svg');
                        $isOutOfStock = $product->track_stock && $product->stock_quantity <= 0;
                        $isLowStock = $product->track_stock && $product->stock_quantity > 0 && $product->stock_quantity <= $product->low_stock_threshold;
                    @endphp
                    <!-- Glassmorphic Product Card -->
                    <div 
                        wire:key="product-card-{{ $product->id }}"
                        @if(! $isOutOfStock)
                            wire:click="addToCart({{ $product->id }})"
                        @endif
                        class="relative rounded-2xl p-3.5 select-none flex flex-col justify-between glass-product-card {{ $inCart ? 'glass-product-card-active' : '' }} {{ $isOutOfStock ? 'opacity-60 cursor-not-allowed filter grayscale-[30%]' : 'cursor-pointer' }}"
                    >
                        <div>
                            <!-- Glass Inset Image Container -->
                            <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-white/40 dark:bg-black/35 mb-3 border border-rose-200/40 dark:border-rose-900/40 backdrop-blur-sm flex items-center justify-center p-2.5 shadow-[inset_0_2px_4px_rgba(0,0,0,0.06)] dark:shadow-[inset_0_2px_6px_rgba(0,0,0,0.4)]">
                                <img 
                                    src="{{ $imgUrl }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-full object-contain drop-shadow-md" 
                                    loading="lazy"
                                />

                                @if($isOutOfStock)
                                    <div class="absolute inset-0 bg-black/60 backdrop-blur-[2px] flex items-center justify-center p-2">
                                        <span class="px-2.5 py-1 rounded-full bg-rose-600/90 text-white font-black text-[10px] uppercase tracking-wider shadow-lg">
                                            Out of Stock
                                        </span>
                                    </div>
                                @elseif($inCart)
                                    <div class="absolute top-2 right-2 bg-gradient-to-r from-rose-600 to-rose-700 text-white text-[11px] font-black px-2.5 py-1 rounded-full shadow-lg shadow-rose-900/50 border border-white/30 backdrop-blur-md">
                                        {{ $qty }} in cart
                                    </div>
                                @elseif($isLowStock)
                                    <div class="absolute top-2 right-2 bg-amber-500/90 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow border border-white/20 backdrop-blur-sm">
                                        {{ $product->stock_quantity }} left
                                    </div>
                                @endif
                            </div>

                            <!-- Product Name Only -->
                            <div>
                                <h3 class="text-xs font-black text-gray-900 dark:text-rose-100 leading-snug line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                            </div>
                        </div>

                        <!-- Quantity Stepper & Price Display -->
                        <div class="mt-3.5 pt-2.5 border-t border-rose-200/40 dark:border-rose-900/40 flex items-center justify-between">
                            <div class="flex flex-col leading-tight">
                                @if($product->offer_price && (float) $product->offer_price > 0 && (float) $product->offer_price < (float) $product->price)
                                    <span class="text-[10px] line-through text-gray-400 dark:text-rose-300/50">
                                        रू {{ number_format($product->price, 2) }}
                                    </span>
                                    <span class="text-xs font-black text-rose-700 dark:text-rose-300">
                                        रू {{ number_format($product->offer_price, 2) }}
                                    </span>
                                @else
                                    <span class="text-xs font-black text-rose-700 dark:text-rose-300">
                                        रू {{ number_format($product->price, 2) }}
                                    </span>
                                @endif
                            </div>

                            @if($inCart)
                                <div class="flex items-center gap-1.5 bg-rose-200/40 dark:bg-black/40 border border-rose-300/40 dark:border-rose-800/40 backdrop-blur-md rounded-lg p-0.5" wire:click.stop>
                                    <button 
                                        type="button" 
                                        wire:click.stop="decrementQuantity({{ $product->id }})"
                                        class="w-6 h-6 rounded flex items-center justify-center font-black text-rose-800 dark:text-rose-200 hover:bg-white/80 dark:hover:bg-rose-900/80 text-xs transition-colors"
                                    >
                                        -
                                    </button>
                                    <span class="text-xs font-black px-1.5 text-gray-900 dark:text-white min-w-[16px] text-center">
                                        {{ $qty }}
                                    </span>
                                    <button 
                                        type="button" 
                                        wire:click.stop="incrementQuantity({{ $product->id }})"
                                        class="w-6 h-6 rounded flex items-center justify-center font-black text-rose-800 dark:text-rose-200 hover:bg-white/80 dark:hover:bg-rose-900/80 text-xs transition-colors"
                                    >
                                        +
                                    </button>
                                </div>
                            @else
                                <div class="w-7 h-7 rounded-lg bg-rose-100/50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200/50 dark:border-rose-900/40 flex items-center justify-center font-bold text-xs backdrop-blur-sm">
                                    +
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center rounded-2xl glass-panel p-8">
                        <x-heroicon-o-shopping-bag class="w-14 h-14 mx-auto mb-3 text-rose-300 dark:text-rose-500" />
                        <h4 class="text-base font-bold text-gray-700 dark:text-rose-200">No products found</h4>
                        <p class="text-xs text-gray-400 mt-1">Try another search keyword.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT: Cashier Bill Summary & Fast Checkout (Frosted Glass Panel) -->
        <div class="w-full xl:w-[410px] shrink-0 flex flex-col">
            <div class="rounded-2xl glass-panel flex flex-col h-[calc(100vh-130px)] sticky top-4 overflow-hidden">
                <!-- Sidebar Top Glass Banner -->
                <div class="p-4 bg-gradient-to-r from-rose-950/90 via-[#881337]/90 to-rose-950/90 backdrop-blur-md text-white flex items-center justify-between border-b border-white/10 shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-white/15 border border-white/25 text-white flex items-center justify-center backdrop-blur-md shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)]">
                            <x-heroicon-s-shopping-bag class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="font-black text-sm tracking-wide text-white">ORDER BILL</h2>
                            <p class="text-[11px] text-rose-200 font-semibold">{{ collect($cart)->sum('quantity') }} items in cart</p>
                        </div>
                    </div>

                    @if(count($cart) > 0)
                        <button 
                            type="button"
                            wire:click="clearCart"
                            wire:confirm="Reset and clear all items from the current bill?"
                            class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white border border-white/20 flex items-center gap-1 backdrop-blur-md transition-all"
                        >
                            <x-heroicon-m-trash class="w-3.5 h-3.5" />
                            Clear
                        </button>
                    @endif
                </div>

                <!-- Scrollable Order Item Breakdown -->
                <div class="flex-1 overflow-y-auto p-3.5 space-y-2 divide-y divide-rose-200/20 dark:divide-rose-950/50 scrollbar-thin">
                    @forelse($cart as $item)
                        <div class="pt-2 first:pt-0 flex items-center justify-between gap-3" wire:key="cart-item-{{ $item['id'] }}">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-gray-900 dark:text-rose-100 truncate">
                                    {{ $item['name'] }}
                                </h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[11px] text-gray-500 dark:text-rose-300">
                                        रू {{ number_format($item['price'], 2) }}
                                    </span>
                                    <span class="text-rose-300 dark:text-rose-800">•</span>
                                    <span class="text-xs font-extrabold text-[#881337] dark:text-rose-400">
                                        रू {{ number_format($item['total'], 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <div class="flex items-center border border-rose-200/50 dark:border-rose-900/60 bg-white/50 dark:bg-black/40 backdrop-blur-sm rounded-lg p-0.5">
                                    <button 
                                        type="button" 
                                        wire:click="decrementQuantity({{ $item['id'] }})"
                                        class="w-6 h-6 flex items-center justify-center font-bold text-rose-700 dark:text-rose-300 hover:bg-white dark:hover:bg-rose-900 rounded text-xs transition-colors"
                                    >
                                        -
                                    </button>
                                    <span class="text-xs font-black px-1.5 min-w-[20px] text-center text-gray-900 dark:text-white">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button 
                                        type="button" 
                                        wire:click="incrementQuantity({{ $item['id'] }})"
                                        class="w-6 h-6 flex items-center justify-center font-bold text-rose-700 dark:text-rose-300 hover:bg-white dark:hover:bg-rose-900 rounded text-xs transition-colors"
                                    >
                                        +
                                    </button>
                                </div>

                                <button 
                                    type="button"
                                    wire:click="removeFromCart({{ $item['id'] }})"
                                    class="p-1 rounded-lg text-gray-400 hover:text-rose-500 transition-colors"
                                >
                                    <x-heroicon-m-x-mark class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center text-gray-400 dark:text-gray-500 py-16">
                            <div class="w-16 h-16 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-[#881337] dark:text-rose-400 flex items-center justify-center mb-3 backdrop-blur-sm">
                                <x-heroicon-o-shopping-cart class="w-8 h-8" />
                            </div>
                            <p class="text-sm font-bold text-gray-700 dark:text-rose-200">Cart is Empty</p>
                            <span class="text-xs text-gray-400 dark:text-rose-300/70 mt-1 max-w-[220px]">Click any product card to add to order.</span>
                        </div>
                    @endforelse
                </div>

                <!-- Bottom Calculations & Action (Glass Inset) -->
                <div class="p-4 bg-white/40 dark:bg-black/30 backdrop-blur-md border-t border-rose-200/40 dark:border-rose-900/40 space-y-3">
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between text-gray-600 dark:text-rose-300 font-medium">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900 dark:text-rose-100">रू {{ number_format($this->subtotal, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-600 dark:text-rose-300">
                            <span>Discount:</span>
                            <div class="flex items-center gap-1 w-32">
                                <span class="text-xs font-semibold text-rose-400">रू</span>
                                <input 
                                    type="number" 
                                    min="0"
                                    step="1"
                                    wire:model.live.debounce.250ms="discount"
                                    class="w-full text-right py-1 px-2 text-xs font-bold rounded-lg border border-rose-200 dark:border-rose-900/60 bg-white/70 dark:bg-black/40 backdrop-blur-sm text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-rose-500"
                                    placeholder="0"
                                />
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-2 border-t border-rose-200/40 dark:border-rose-900/40">
                            <span class="text-xs font-black text-gray-950 dark:text-white uppercase tracking-wider">NET PAYABLE:</span>
                            <span class="text-lg font-black text-[#881337] dark:text-rose-400">
                                रू {{ number_format($this->grand_total, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Payment Mode Switch Glass Buttons -->
                    <div>
                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                type="button"
                                wire:click="$set('paymentMethod', 'cash')"
                                class="glass-pill py-2 px-3 rounded-xl text-xs font-black flex items-center justify-center gap-2 {{ $paymentMethod === 'cash' ? 'glass-pill-active' : 'glass-pill-inactive' }}"
                            >
                                <x-heroicon-s-banknotes class="w-4 h-4" />
                                CASH
                            </button>
                            <button 
                                type="button"
                                wire:click="$set('paymentMethod', 'qr')"
                                class="glass-pill py-2 px-3 rounded-xl text-xs font-black flex items-center justify-center gap-2 {{ $paymentMethod === 'qr' ? 'glass-pill-active' : 'glass-pill-inactive' }}"
                            >
                                <x-heroicon-s-qr-code class="w-4 h-4" />
                                QR SCAN
                            </button>
                        </div>
                    </div>

                    <!-- Cash Calculation Box -->
                    @if($paymentMethod === 'cash')
                        <div class="p-3 rounded-xl bg-white/50 dark:bg-black/35 border border-rose-200/40 dark:border-rose-900/40 backdrop-blur-md space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-700 dark:text-rose-200">RECEIVED:</span>
                                <div class="flex items-center gap-1 w-32">
                                    <span class="text-xs font-semibold text-rose-400">रू</span>
                                    <input 
                                        type="number" 
                                        min="0"
                                        step="1"
                                        wire:model.live.debounce.150ms="cashReceived"
                                        class="w-full text-right py-1 px-2 text-xs font-bold rounded-lg border border-rose-200 dark:border-rose-900/60 bg-white dark:bg-black/50 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-rose-500"
                                        placeholder="0"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 overflow-x-auto pb-0.5 scrollbar-none">
                                <button 
                                    type="button" 
                                    wire:click="setExactCash"
                                    class="glass-pill glass-pill-inactive px-2 py-1 text-[10px] font-black rounded-lg"
                                >
                                    Exact
                                </button>
                                @foreach([100, 200, 500, 1000] as $preset)
                                    <button 
                                        type="button" 
                                        wire:click="setQuickCash({{ $preset }})"
                                        class="glass-pill glass-pill-inactive px-2 py-1 text-[10px] font-black rounded-lg"
                                    >
                                        रू{{ $preset }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="flex justify-between items-center pt-1.5 border-t border-rose-200/40 dark:border-rose-900/40 text-xs font-black">
                                <span class="text-gray-600 dark:text-rose-200">Return Change:</span>
                                <span class="{{ $this->change_due >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                    रू {{ number_format(max(0, $this->change_due), 2) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <!-- QR Mode Feedback -->
                        <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/25 backdrop-blur-md flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-600 dark:text-rose-300 flex items-center justify-center shrink-0">
                                <x-heroicon-s-qr-code class="w-5 h-5" />
                            </div>
                            <div class="text-[11px] text-rose-900 dark:text-rose-200 leading-tight">
                                <span class="font-bold">QR Broadcast Active</span>
                                <p class="text-[10px] opacity-75">Customer screen is rendering scan QR code for रू {{ number_format($this->grand_total, 2) }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Complete Bill Action Button -->
                    <button 
                        type="button"
                        wire:click="completeOrder"
                        @disabled(count($cart) === 0 || ($paymentMethod === 'cash' && $this->change_due < 0))
                        class="w-full py-3 px-4 rounded-xl font-black text-xs text-white bg-gradient-to-r from-rose-700 via-rose-600 to-rose-700 hover:from-rose-600 hover:to-rose-600 border border-white/20 shadow-lg shadow-rose-950/40 backdrop-blur-md disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-all uppercase tracking-wider"
                    >
                        <x-heroicon-m-check class="w-4 h-4" />
                        Complete Bill (रू {{ number_format($this->grand_total, 2) }})
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
