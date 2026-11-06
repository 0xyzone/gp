<x-filament-panels::page class="fi-page-pos">
    <div class="flex flex-col xl:flex-row gap-5 -mt-3 w-full">
        <!-- LEFT: Products Catalog Screen -->
        <div class="flex-1 flex flex-col gap-4 min-w-0">
            <!-- Filter & Search Bar -->
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 p-3.5 shadow-sm flex flex-col sm:flex-row items-center gap-3 justify-between">
                <!-- Search input -->
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <x-heroicon-m-magnifying-glass class="w-5 h-5" />
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.250ms="search" 
                        placeholder="Search product..." 
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80 py-2 pl-10 pr-4 text-sm text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                    />
                </div>

                <!-- Category Navigation Pills -->
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0 scrollbar-none">
                    <button 
                        type="button"
                        wire:click="filterCategory(null)"
                        class="px-4 py-2 rounded-xl text-xs font-bold shadow-sm shrink-0 {{ is_null($selectedCategory) ? 'bg-amber-500 text-white shadow-amber-500/25 ring-2 ring-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}"
                    >
                        All ({{ \App\Models\Product::where('is_active', true)->count() }})
                    </button>
                    @foreach($this->categories as $category)
                        <button 
                            type="button"
                            wire:click="filterCategory({{ $category->id }})"
                            class="px-4 py-2 rounded-xl text-xs font-bold shadow-sm shrink-0 {{ $selectedCategory === $category->id ? 'bg-amber-500 text-white shadow-amber-500/25 ring-2 ring-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Cards Grid: Product Image & Name Only, 3D Clean Surface, Zero Clipping, Zero Hover Scaling -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 2xl:grid-cols-5 gap-4 overflow-y-auto max-h-[calc(100vh-215px)] p-1 scrollbar-thin">
                @forelse($this->products as $product)
                    @php
                        $inCart = isset($cart[$product->id]);
                        $qty = $inCart ? $cart[$product->id]['quantity'] : 0;
                        $imgUrl = $product->image ? asset('storage/' . $product->image) : asset('images/placeholder-product.svg');
                    @endphp
                    <!-- Solid 3D Surface Card without hover zoom or translate to prevent any clipping -->
                    <div 
                        wire:key="product-card-{{ $product->id }}"
                        wire:click="addToCart({{ $product->id }})"
                        class="relative rounded-2xl p-3.5 bg-white dark:bg-gray-900 border select-none cursor-pointer flex flex-col justify-between shadow-[0_4px_12px_rgba(0,0,0,0.06),inset_0_1px_0_rgba(255,255,255,0.6)] dark:shadow-[0_4px_12px_rgba(0,0,0,0.4),inset_0_1px_0_rgba(255,255,255,0.08)] {{ $inCart ? 'border-amber-500 ring-2 ring-amber-400/50 bg-amber-50/15 dark:bg-amber-950/20' : 'border-gray-200 dark:border-gray-800' }}"
                    >
                        <div>
                            <!-- Clean Image Container (Image Only) -->
                            <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-950 mb-3 border border-gray-100 dark:border-gray-800 flex items-center justify-center p-2">
                                <img 
                                    src="{{ $imgUrl }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-full object-contain" 
                                    loading="lazy"
                                />

                                @if($inCart)
                                    <div class="absolute top-2 right-2 bg-[#881337] text-white text-xs font-black px-2.5 py-1 rounded-full shadow-md">
                                        {{ $qty }} in cart
                                    </div>
                                @endif
                            </div>

                            <!-- Product Name Only -->
                            <div>
                                <h3 class="text-sm font-extrabold text-gray-900 dark:text-gray-100 leading-snug line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                            </div>
                        </div>

                        <!-- Quantity Stepper & Price Display -->
                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <span class="text-sm font-black text-[#881337] dark:text-rose-400">
                                रू {{ number_format($product->price, 2) }}
                            </span>

                            @if($inCart)
                                <div class="flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 rounded-lg p-0.5" wire:click.stop>
                                    <button 
                                        type="button" 
                                        wire:click.stop="decrementQuantity({{ $product->id }})"
                                        class="w-7 h-7 rounded flex items-center justify-center font-black text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 text-xs"
                                    >
                                        -
                                    </button>
                                    <span class="text-xs font-black px-1.5 text-gray-900 dark:text-white min-w-[16px] text-center">
                                        {{ $qty }}
                                    </span>
                                    <button 
                                        type="button" 
                                        wire:click.stop="incrementQuantity({{ $product->id }})"
                                        class="w-7 h-7 rounded flex items-center justify-center font-black text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 text-xs"
                                    >
                                        +
                                    </button>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 flex items-center justify-center font-bold text-sm">
                                    +
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center rounded-2xl bg-white dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-800 p-8">
                        <x-heroicon-o-shopping-bag class="w-14 h-14 mx-auto mb-3 text-gray-400" />
                        <h4 class="text-base font-bold text-gray-700 dark:text-gray-300">No products found</h4>
                        <p class="text-xs text-gray-400 mt-1">Try another search keyword.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT: Cashier Bill Summary & Fast Checkout -->
        <div class="w-full xl:w-[410px] shrink-0 flex flex-col">
            <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200/90 dark:border-gray-800 shadow-md flex flex-col h-[calc(100vh-130px)] sticky top-4 overflow-hidden">
                <!-- Sidebar Top Banner -->
                <div class="p-4 bg-gradient-to-r from-gray-900 via-slate-900 to-gray-900 text-white flex items-center justify-between border-b border-gray-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-400 flex items-center justify-center">
                            <x-heroicon-s-shopping-bag class="w-5 h-5" />
                        </div>
                        <div>
                            <h2 class="font-black text-sm tracking-wide text-white">ORDER BILL</h2>
                            <p class="text-[11px] text-amber-400 font-semibold">{{ collect($cart)->sum('quantity') }} items in cart</p>
                        </div>
                    </div>

                    @if(count($cart) > 0)
                        <button 
                            type="button"
                            wire:click="clearCart"
                            wire:confirm="Reset and clear all items from the current bill?"
                            class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center gap-1"
                        >
                            <x-heroicon-m-trash class="w-3.5 h-3.5" />
                            Clear
                        </button>
                    @endif
                </div>

                <!-- Scrollable Order Item Breakdown -->
                <div class="flex-1 overflow-y-auto p-3.5 space-y-2.5 divide-y divide-gray-100 dark:divide-gray-800/80 scrollbar-thin">
                    @forelse($cart as $item)
                        <div class="pt-2.5 first:pt-0 flex items-center justify-between gap-3" wire:key="cart-item-{{ $item['id'] }}">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-bold text-gray-900 dark:text-gray-100 truncate">
                                    {{ $item['name'] }}
                                </h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[11px] text-gray-500 dark:text-gray-400">
                                        रू {{ number_format($item['price'], 2) }}
                                    </span>
                                    <span class="text-gray-300 dark:text-gray-700">•</span>
                                    <span class="text-xs font-extrabold text-amber-600 dark:text-amber-400">
                                        रू {{ number_format($item['total'], 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <div class="flex items-center border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg p-0.5">
                                    <button 
                                        type="button" 
                                        wire:click="decrementQuantity({{ $item['id'] }})"
                                        class="w-6 h-6 flex items-center justify-center font-bold text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded text-xs"
                                    >
                                        -
                                    </button>
                                    <span class="text-xs font-black px-1.5 min-w-[20px] text-center text-gray-900 dark:text-white">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button 
                                        type="button" 
                                        wire:click="incrementQuantity({{ $item['id'] }})"
                                        class="w-6 h-6 flex items-center justify-center font-bold text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded text-xs"
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
                            <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center mb-3">
                                <x-heroicon-o-shopping-cart class="w-8 h-8" />
                            </div>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">Cart is Empty</p>
                            <span class="text-xs text-gray-400 mt-1 max-w-[220px]">Click any product card to add to order.</span>
                        </div>
                    @endforelse
                </div>

                <!-- Bottom Calculations & Action -->
                <div class="p-4 bg-gray-50/90 dark:bg-gray-950/80 border-t border-gray-200 dark:border-gray-800 space-y-3">
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400 font-medium">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">रू {{ number_format($this->subtotal, 2) }}</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-600 dark:text-gray-400">
                            <span>Discount:</span>
                            <div class="flex items-center gap-1 w-32">
                                <span class="text-xs font-semibold text-gray-400">रू</span>
                                <input 
                                    type="number" 
                                    min="0"
                                    step="1"
                                    wire:model.live.debounce.250ms="discount"
                                    class="w-full text-right py-1 px-2 text-xs font-bold rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                    placeholder="0.00"
                                />
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-800">
                            <span class="text-sm font-black text-gray-950 dark:text-white uppercase tracking-wider">Net Payable:</span>
                            <span class="text-lg font-black text-amber-500 dark:text-amber-400">
                                रू {{ number_format($this->grand_total, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Payment Mode Switch -->
                    <div>
                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                type="button"
                                wire:click="$set('paymentMethod', 'cash')"
                                class="py-2 px-3 rounded-xl border text-xs font-black flex items-center justify-center gap-2 {{ $paymentMethod === 'cash' ? 'bg-[#881337] text-white border-[#881337] shadow-sm' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-800' }}"
                            >
                                <x-heroicon-s-banknotes class="w-4 h-4" />
                                CASH
                            </button>
                            <button 
                                type="button"
                                wire:click="$set('paymentMethod', 'qr')"
                                class="py-2 px-3 rounded-xl border text-xs font-black flex items-center justify-center gap-2 {{ $paymentMethod === 'qr' ? 'bg-[#881337] text-white border-[#881337] shadow-sm' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-800' }}"
                            >
                                <x-heroicon-s-qr-code class="w-4 h-4" />
                                QR SCAN
                            </button>
                        </div>
                    </div>

                    <!-- Tender Details for Cash -->
                    @if($paymentMethod === 'cash')
                        <div class="p-2.5 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <label class="text-[11px] font-bold text-gray-600 dark:text-gray-300 uppercase">Received:</label>
                                <div class="relative w-36">
                                    <span class="absolute inset-y-0 left-2.5 flex items-center text-xs font-bold text-gray-400">रू</span>
                                    <input 
                                        type="number" 
                                        step="1"
                                        wire:model.live.debounce.150ms="cashReceived"
                                        placeholder="{{ $this->grand_total }}"
                                        class="w-full text-right py-1 pl-6 pr-2.5 text-xs font-black rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <button 
                                    type="button" 
                                    wire:click="setExactCash"
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20"
                                >
                                    Exact
                                </button>
                                @foreach([100, 200, 500, 1000] as $preset)
                                    @if($preset >= $this->grand_total)
                                        <button 
                                            type="button" 
                                            wire:click="setQuickCash({{ $preset }})"
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300"
                                        >
                                            रू {{ $preset }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between text-xs pt-1 border-t border-gray-100 dark:border-gray-800">
                                <span class="font-bold text-gray-500 dark:text-gray-400">Return Change:</span>
                                <span class="text-sm font-black {{ $this->change_due >= 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                    रू {{ number_format(max(0, $this->change_due), 2) }}
                                    @if($this->change_due < 0)
                                        <span class="text-[10px] block font-semibold text-rose-500">Short by रू {{ number_format(abs($this->change_due), 2) }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="p-2.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-[#881337] dark:text-rose-300 text-xs flex items-center gap-2">
                            <x-heroicon-s-qr-code class="w-6 h-6 shrink-0" />
                            <div class="text-[11px] leading-tight">
                                <span class="font-black block">Live QR Displayed to Customer</span>
                                Siddhartha Bank QR active for रू {{ number_format($this->grand_total, 2) }}.
                            </div>
                        </div>
                    @endif

                    <!-- Complete Checkout Action Button -->
                    <button 
                        type="button"
                        wire:click="completeOrder"
                        @disabled(count($cart) === 0 || ($paymentMethod === 'cash' && $this->change_due < 0))
                        class="w-full py-3 px-4 rounded-xl font-black text-sm text-white shadow-lg flex items-center justify-center gap-2 {{ count($cart) === 0 || ($paymentMethod === 'cash' && $this->change_due < 0) ? 'bg-gray-300 dark:bg-gray-800 text-gray-500 cursor-not-allowed shadow-none' : 'bg-[#881337] hover:bg-[#9f1239] shadow-rose-950/20 active:scale-[0.99]' }}"
                    >
                        <x-heroicon-s-check class="w-5 h-5 stroke-[2.5]" />
                        COMPLETE BILL (रू {{ number_format($this->grand_total, 2) }})
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
