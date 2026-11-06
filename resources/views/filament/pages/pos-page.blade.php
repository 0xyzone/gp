<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 -mt-3">
        <!-- Products Section (Left 7-8 Cols) -->
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-4">
            <!-- Search & Category Filters -->
            <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col md:flex-row gap-3 items-center justify-between">
                <!-- Search input -->
                <div class="relative w-full md:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <x-heroicon-m-magnifying-glass class="w-5 h-5" />
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.250ms="search" 
                        placeholder="Search product name or code..." 
                        class="fi-input block w-full rounded-lg border-none py-1.5 pl-10 pr-3 text-sm leading-6 text-gray-950 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:focus:ring-primary-500"
                    />
                </div>

                <!-- Category Pills -->
                <div class="flex items-center gap-2 overflow-x-auto w-full pb-1 scrollbar-thin">
                    <button 
                        type="button"
                        wire:click="filterCategory(null)"
                        class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all shadow-sm {{ is_null($selectedCategory) ? 'bg-amber-500 text-white shadow-amber-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                    >
                        All Items
                    </button>
                    @foreach($this->categories as $category)
                        <button 
                            type="button"
                            wire:click="filterCategory({{ $category->id }})"
                            class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all shadow-sm {{ $selectedCategory === $category->id ? 'bg-amber-500 text-white shadow-amber-500/20' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 overflow-y-auto max-h-[calc(100vh-210px)] pr-1 scrollbar-thin">
                @forelse($this->products as $product)
                    @php
                        $inCart = isset($cart[$product->id]);
                        $qty = $inCart ? $cart[$product->id]['quantity'] : 0;
                    @endphp
                    <div 
                        class="group relative rounded-xl p-3.5 transition-all duration-150 flex flex-col justify-between shadow-sm hover:shadow-md fi-section bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 {{ $inCart ? '!ring-2 !ring-amber-500' : 'hover:ring-gray-300 dark:hover:ring-white/20' }}"
                    >
                        <div>
                            <!-- Image or Placeholder -->
                            <div 
                                wire:click="addToCart({{ $product->id }})"
                                class="w-full h-28 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-950 flex items-center justify-center cursor-pointer mb-2.5 relative group-hover:scale-[1.02] transition-transform"
                            >
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                                        <x-heroicon-o-shopping-bag class="w-6 h-6" />
                                    </div>
                                @endif

                                @if($inCart)
                                    <div class="absolute top-1.5 right-1.5 bg-amber-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full shadow-md">
                                        {{ $qty }} in cart
                                    </div>
                                @endif
                            </div>

                            <!-- Title & Code -->
                            <div wire:click="addToCart({{ $product->id }})" class="cursor-pointer">
                                @if($product->code)
                                    <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 tracking-wider">
                                        {{ $product->code }}
                                    </span>
                                @endif
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-snug line-clamp-2">
                                    {{ $product->name }}
                                </h3>
                            </div>
                        </div>

                        <!-- Price & Action controls -->
                        <div class="mt-3 pt-2.5 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                            <span class="text-sm font-bold text-amber-600 dark:text-amber-400">
                                Rs. {{ number_format($product->price, 2) }}
                            </span>

                            @if($inCart)
                                <div class="flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 p-0.5 rounded-lg">
                                    <button 
                                        type="button" 
                                        wire:click.stop="decrementQuantity({{ $product->id }})"
                                        class="w-6 h-6 flex items-center justify-center rounded text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 font-bold text-xs"
                                    >
                                        -
                                    </button>
                                    <span class="text-xs font-bold text-gray-900 dark:text-gray-100 px-1 min-w-[18px] text-center">
                                        {{ $qty }}
                                    </span>
                                    <button 
                                        type="button" 
                                        wire:click.stop="incrementQuantity({{ $product->id }})"
                                        class="w-6 h-6 flex items-center justify-center rounded text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 font-bold text-xs"
                                    >
                                        +
                                    </button>
                                </div>
                            @else
                                <button 
                                    type="button"
                                    wire:click="addToCart({{ $product->id }})"
                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-amber-500 hover:text-white transition-colors"
                                >
                                    + Add
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-gray-400 dark:text-gray-500">
                        <x-heroicon-o-archive-box-x-mark class="w-12 h-12 mx-auto mb-2 opacity-50" />
                        <p class="text-sm">No products found matching your search.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Order Summary & Checkout Sidebar (Right 4-5 Cols) -->
        <div class="lg:col-span-5 xl:col-span-4 flex flex-col">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 flex flex-col h-[calc(100vh-140px)] sticky top-4">
                <!-- Sidebar Header -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/70 dark:bg-gray-950/40 rounded-t-xl">
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-shopping-cart class="w-5 h-5 text-amber-500" />
                        <h2 class="font-bold text-base text-gray-950 dark:text-white">Active Order</h2>
                        <span class="bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ collect($cart)->sum('quantity') }} items
                        </span>
                    </div>

                    @if(count($cart) > 0)
                        <button 
                            type="button"
                            wire:click="clearCart"
                            wire:confirm="Are you sure you want to clear the entire cart?"
                            class="text-xs text-rose-500 hover:text-rose-600 font-medium flex items-center gap-1 transition-colors"
                        >
                            <x-heroicon-m-trash class="w-3.5 h-3.5" />
                            Clear
                        </button>
                    @endif
                </div>

                <!-- Cart Items List (Scrollable) -->
                <div class="flex-1 overflow-y-auto p-4 divide-y divide-gray-100 dark:divide-gray-800 scrollbar-thin">
                    @forelse($cart as $item)
                        <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-semibold text-gray-900 dark:text-gray-100 truncate">
                                    {{ $item['name'] }}
                                </h4>
                                <span class="text-[11px] text-gray-400">
                                    Rs. {{ number_format($item['price'], 2) }} each
                                </span>
                            </div>

                            <!-- Quantity Selector -->
                            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 p-0.5 rounded-md">
                                <button 
                                    type="button"
                                    wire:click="decrementQuantity({{ $item['id'] }})"
                                    class="w-5 h-5 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded"
                                >
                                    -
                                </button>
                                <span class="text-xs font-bold px-1.5 text-gray-900 dark:text-gray-100 min-w-[20px] text-center">
                                    {{ $item['quantity'] }}
                                </span>
                                <button 
                                    type="button"
                                    wire:click="incrementQuantity({{ $item['id'] }})"
                                    class="w-5 h-5 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-700 rounded"
                                >
                                    +
                                </button>
                            </div>

                            <!-- Row Total -->
                            <div class="text-right min-w-[65px]">
                                <span class="text-xs font-bold text-gray-950 dark:text-white">
                                    Rs. {{ number_format($item['total'], 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 py-12">
                            <x-heroicon-o-shopping-cart class="w-10 h-10 mb-2 opacity-40" />
                            <p class="text-xs font-medium">Cart is currently empty</p>
                            <p class="text-[11px] text-gray-400">Click on product cards to add</p>
                        </div>
                    @endforelse
                </div>

                <!-- Live Calculations & Tender Section -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-950/40 rounded-b-xl flex flex-col gap-3">
                    <!-- Calculations -->
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between text-gray-500 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-200">Rs. {{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-gray-500 dark:text-gray-400">
                            <span>Discount</span>
                            <div class="flex items-center gap-1 w-24">
                                <span class="text-[10px]">Rs.</span>
                                <input 
                                    type="number" 
                                    step="1" 
                                    min="0"
                                    wire:model.live.debounce.250ms="discount" 
                                    class="w-full px-2 py-0.5 text-xs text-right rounded border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                />
                            </div>
                        </div>
                        <div class="flex justify-between text-sm font-extrabold text-gray-950 dark:text-white pt-1.5 border-t border-gray-200 dark:border-gray-800">
                            <span>Grand Total</span>
                            <span class="text-amber-600 dark:text-amber-400 text-base">Rs. {{ number_format($this->grand_total, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment Mode Switcher -->
                    <div class="pt-1">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5 block">
                            Payment Method
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button 
                                type="button"
                                wire:click="$set('paymentMethod', 'cash')"
                                class="py-2 px-3 rounded-lg border text-xs font-bold flex items-center justify-center gap-1.5 transition-all {{ $paymentMethod === 'cash' ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-800 hover:bg-gray-50' }}"
                            >
                                <x-heroicon-m-banknotes class="w-4 h-4" />
                                Cash
                            </button>
                            <button 
                                type="button"
                                wire:click="$set('paymentMethod', 'qr')"
                                class="py-2 px-3 rounded-lg border text-xs font-bold flex items-center justify-center gap-1.5 transition-all {{ $paymentMethod === 'qr' ? 'bg-amber-500 text-white border-amber-500 shadow-sm' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-800 hover:bg-gray-50' }}"
                            >
                                <x-heroicon-m-qr-code class="w-4 h-4" />
                                QR Pay
                            </button>
                        </div>
                    </div>

                    <!-- Cash Calculation Box -->
                    @if($paymentMethod === 'cash')
                        <div class="bg-white dark:bg-gray-900 p-3 rounded-lg border border-gray-200 dark:border-gray-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-semibold text-gray-700 dark:text-gray-300">
                                    Cash Received:
                                </label>
                                <div class="relative w-32">
                                    <span class="absolute inset-y-0 left-2 flex items-center text-xs text-gray-400">Rs.</span>
                                    <input 
                                        type="number" 
                                        step="1"
                                        wire:model.live.debounce.150ms="cashReceived"
                                        placeholder="{{ $this->grand_total }}"
                                        class="w-full pl-8 pr-2 py-1 text-xs text-right font-bold rounded border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-white focus:ring-1 focus:ring-amber-500"
                                    />
                                </div>
                            </div>

                            <!-- Quick Cash Tenders -->
                            <div class="flex items-center gap-1.5 pt-1 overflow-x-auto">
                                <button 
                                    type="button" 
                                    wire:click="setExactCash"
                                    class="px-2 py-1 text-[10px] font-bold rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200"
                                >
                                    Exact
                                </button>
                                @foreach([100, 500, 1000] as $preset)
                                    @if($preset >= $this->grand_total)
                                        <button 
                                            type="button" 
                                            wire:click="setQuickCash({{ $preset }})"
                                            class="px-2 py-1 text-[10px] font-bold rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-200"
                                        >
                                            {{ $preset }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Change to Return -->
                            <div class="pt-2 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Change Due:</span>
                                <span class="text-sm font-extrabold {{ $this->change_due >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500' }}">
                                    Rs. {{ number_format(max(0, $this->change_due), 2) }}
                                    @if($this->change_due < 0)
                                        <span class="text-[10px] font-normal text-rose-500 block">Short by Rs. {{ number_format(abs($this->change_due), 2) }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @else
                        <!-- QR Notice -->
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-lg border border-amber-200 dark:border-amber-800/50 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-600 dark:text-amber-300 flex items-center justify-center shrink-0">
                                <x-heroicon-o-qr-code class="w-5 h-5" />
                            </div>
                            <div class="text-[11px] text-amber-950 dark:text-amber-200">
                                <strong>Stall QR Displayed</strong>
                                <p class="text-[10px] opacity-80">QR Code is being shown on customer display with total Rs. {{ number_format($this->grand_total, 2) }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Complete Order Button -->
                    <button 
                        type="button"
                        wire:click="completeOrder"
                        @disabled(count($cart) === 0 || ($paymentMethod === 'cash' && $this->change_due < 0))
                        class="w-full py-3 px-4 rounded-xl font-bold text-sm text-white bg-amber-500 hover:bg-amber-600 transition-all shadow-md shadow-amber-500/25 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <x-heroicon-m-check-badge class="w-5 h-5" />
                        Complete Order (Rs. {{ number_format($this->grand_total, 2) }})
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
