<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('stall_name', 'Ghasa Products') }} - Customer Live Screen</title>

    <!-- Vite compiled Tailwind CSS & app scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .glow-amber { box-shadow: 0 0 60px -10px rgba(245, 158, 11, 0.25); }
        .scrollbar-none::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="h-full overflow-hidden select-none bg-slate-950 font-sans text-slate-100" x-data="customerDisplay()" x-init="init()" x-cloak>
    
    <!-- Top Header Bar -->
    <header class="fixed top-0 left-0 right-0 z-30 flex items-center justify-between px-8 py-4 bg-slate-950/90 backdrop-blur-md border-b border-slate-800/80">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center shadow-lg shadow-amber-500/25 text-slate-950 font-black text-2xl">
                G
            </div>
            <div>
                <h1 class="text-xl font-black tracking-tight text-white">{{ \App\Models\Setting::get('stall_name', 'Ghasa Products') }}</h1>
                <p class="text-xs text-amber-400 font-bold uppercase tracking-wider">Expo Customer Display Counter</p>
            </div>
        </div>

        <div class="flex items-center gap-5">
            <!-- Connection Status -->
            <div class="flex items-center gap-2.5 px-4 py-1.5 rounded-full text-xs font-bold transition-all"
                 :class="connected ? 'bg-emerald-950/80 text-emerald-400 border border-emerald-800/80 shadow-sm' : 'bg-rose-950/80 text-rose-400 border border-rose-800/80 animate-pulse'">
                <span class="w-2.5 h-2.5 rounded-full" :class="connected ? 'bg-emerald-400 animate-ping' : 'bg-rose-400'"></span>
                <span x-text="connected ? 'Live Sync Online' : 'Connecting to Terminal...'"></span>
            </div>
            
            <div class="text-sm text-slate-400 font-mono font-medium" x-text="currentTime"></div>
        </div>
    </header>

    <!-- Main Live Screen Area -->
    <main class="h-full pt-20 pb-6 px-8 flex">

        <!-- ========================================================================= -->
        <!-- STATE 1: IDLE / NO ACTIVE BILL - SHOW PRODUCTS CATALOG & PROMOTIONS -->
        <!-- ========================================================================= -->
        <template x-if="screenState === 'idle'">
            <div class="w-full h-full flex flex-col gap-6">
                <!-- Welcome Banner & Stall Headline -->
                <div class="rounded-3xl bg-gradient-to-r from-amber-950/40 via-slate-900 to-slate-900 border border-amber-500/30 p-6 flex items-center justify-between glow-amber">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                            Welcome to our stall
                        </span>
                        <h2 class="text-3xl font-black text-white tracking-tight">Discover Our Authentic Products</h2>
                        <p class="text-xs text-slate-400">Order at the counter • Cash & Siddhartha Bank QR Payments Accepted</p>
                    </div>

                    <div class="hidden md:flex items-center gap-4 bg-slate-950/70 border border-slate-800/80 rounded-2xl px-5 py-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-extrabold text-white block">Fast Digital Checkout</span>
                            <span class="text-[11px] text-amber-400 font-semibold">Instant Bank QR Payment</span>
                        </div>
                    </div>
                </div>

                <!-- Products Showcase Grid (Displays when no active cart) -->
                <div class="flex-1 overflow-y-auto pr-2 scrollbar-none">
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 pb-8">
                        @forelse($allProducts as $product)
                            @php
                                $img = $product->image ? asset('storage/' . $product->image) : asset('images/placeholder-product.svg');
                            @endphp
                            <div class="rounded-2xl bg-slate-900/80 border border-slate-800 p-3 flex flex-col justify-between hover:border-amber-500/50 transition-all shadow-md group">
                                <div>
                                    <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-slate-950 mb-2.5">
                                        <img 
                                            src="{{ $img }}" 
                                            alt="{{ $product->name }}" 
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy"
                                        />
                                        @if($product->category)
                                            <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-slate-950/80 backdrop-blur text-[10px] text-amber-300 font-bold">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="text-xs sm:text-sm font-bold text-white line-clamp-2 leading-snug">
                                        {{ $product->name }}
                                    </h3>
                                    @if($product->description)
                                        <p class="text-[11px] text-slate-400 line-clamp-1 mt-1">
                                            {{ $product->description }}
                                        </p>
                                    @endif
                                </div>

                                <div class="mt-3 pt-2 border-t border-slate-800 flex items-center justify-between">
                                    <span class="text-xs sm:text-sm font-black text-amber-400">
                                        रू {{ number_format($product->price, 2) }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-950 text-emerald-400 border border-emerald-800">
                                        In Stock
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-20 text-center text-slate-500">
                                <p class="text-base font-bold">Products will appear here momentarily.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </template>

        <!-- ========================================================================= -->
        <!-- STATE 2: ACTIVE ORDER PLACEMENT HAPPENING - LIVE TWO-COLUMN BILLING VIEW -->
        <!-- ========================================================================= -->
        <template x-if="screenState === 'active'">
            <div class="w-full h-full grid grid-cols-12 gap-8">
                <!-- Left: Live Order Breakdown (7 cols) -->
                <div class="col-span-7 flex flex-col bg-slate-900/80 border border-slate-800 rounded-3xl p-6 backdrop-blur-md overflow-hidden shadow-2xl">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <span class="text-base font-black text-white tracking-wide uppercase">Your Items in Order</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold" x-text="itemCount + ' items'"></span>
                        </div>
                        <span class="text-xs text-emerald-400 font-bold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Live Updating
                        </span>
                    </div>

                    <!-- Items List -->
                    <div class="flex-1 overflow-y-auto py-4 space-y-3 pr-2 scrollbar-none">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-950/70 border border-slate-800 hover:border-slate-700 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center font-black text-amber-400 text-base" x-text="item.quantity + 'x'"></div>
                                    <div>
                                        <h4 class="font-extrabold text-white text-base" x-text="item.name"></h4>
                                        <p class="text-xs text-slate-400 mt-0.5" x-text="'रू ' + Number(item.price).toFixed(2) + ' per unit'"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xl font-black text-amber-400" x-text="'रू ' + Number(item.total).toFixed(2)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Right: Payable Totals & Real-Time QR or Cash Counter (5 cols) -->
                <div class="col-span-5 flex flex-col gap-6">
                    <!-- Grand Total Banner -->
                    <div class="p-6 rounded-3xl bg-gradient-to-br from-amber-600/25 via-slate-900 to-slate-900 border border-amber-500/40 flex flex-col justify-between glow-amber shadow-2xl">
                        <div>
                            <span class="text-xs font-bold text-amber-400 uppercase tracking-widest block mb-1">TOTAL PAYABLE AMOUNT</span>
                            <div class="text-6xl font-black text-white tracking-tight" x-text="'रू ' + Number(grandTotal).toFixed(2)"></div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-800 space-y-2 text-sm">
                            <div class="flex justify-between text-slate-400 font-medium">
                                <span>Subtotal</span>
                                <span class="font-bold text-slate-200" x-text="'रू ' + Number(subtotal).toFixed(2)"></span>
                            </div>
                            <template x-if="discount > 0">
                                <div class="flex justify-between text-emerald-400 font-bold">
                                    <span>Expo Special Discount</span>
                                    <span x-text="'- रू ' + Number(discount).toFixed(2)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Payment Mode Interactive Box -->
                    <div class="flex-1 p-6 rounded-3xl bg-slate-900/80 border border-slate-800 shadow-2xl flex flex-col justify-between overflow-hidden">
                        <!-- CASH MODE -->
                        <template x-if="paymentMethod === 'cash'">
                            <div class="h-full flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 text-slate-300 mb-4 pb-3 border-b border-slate-800">
                                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span class="font-extrabold text-sm uppercase tracking-wider text-white">Cash Tender Counter</span>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800">
                                            <span class="text-xs text-slate-400 block mb-1 font-medium">Amount Received</span>
                                            <span class="text-3xl font-black text-slate-100" x-text="cashReceived > 0 ? 'रू ' + Number(cashReceived).toFixed(2) : 'Awaiting cash...'"></span>
                                        </div>

                                        <div class="p-4 rounded-2xl border transition-colors" :class="changeDue >= 0 && cashReceived > 0 ? 'bg-emerald-950/40 border-emerald-800 text-emerald-400' : 'bg-slate-950/80 border-slate-800 text-slate-400'">
                                            <span class="text-xs block mb-1 font-bold" :class="changeDue >= 0 && cashReceived > 0 ? 'text-emerald-400' : 'text-slate-400'">Your Return Change</span>
                                            <span class="text-4xl font-black" x-text="cashReceived > 0 ? 'रू ' + Number(Math.max(0, changeDue)).toFixed(2) : 'रू 0.00'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs text-slate-400 text-center pt-2">
                                    Please verify and collect your return change at the counter
                                </div>
                            </div>
                        </template>

                        <!-- QR SCAN MODE WITH SIDDHARTHA BANK DESIGN -->
                        <template x-if="paymentMethod === 'qr'">
                            <div class="h-full flex flex-col items-center justify-between text-center py-1">
                                <div class="text-xs font-black text-amber-400 uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    SCAN TO PAY WITH ANY BANK APP
                                </div>

                                <!-- Dynamic QR Art from Settings -->
                                <div class="my-auto w-full max-w-[270px] bg-white rounded-3xl p-2.5 shadow-2xl border-4 border-amber-400 overflow-hidden transform hover:scale-[1.02] transition-transform">
                                    @php
                                        $qrImage = \App\Models\Setting::get('payment_qr_image', 'images/siddhartha-bank-qr.png');
                                        $qrUrl = str_starts_with($qrImage, 'http') ? $qrImage : (str_starts_with($qrImage, 'images/') ? asset($qrImage) : asset('storage/' . $qrImage));
                                    @endphp
                                    <img 
                                        src="{{ $qrUrl }}" 
                                        alt="Siddhartha Bank QR Payment" 
                                        class="w-full h-auto object-contain rounded-2xl"
                                    />
                                </div>

                                <div>
                                    <div class="text-lg font-black text-white" x-text="'Please Pay रू ' + Number(grandTotal).toFixed(2)"></div>
                                    <p class="text-xs text-amber-400 font-bold mt-0.5">{{ \App\Models\Setting::get('upi_bank_name', 'Siddhartha Bank') }} • Any Banking App / Fonepay</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- ========================================================================= -->
        <!-- STATE 3: ORDER COMPLETED SCREEN -->
        <!-- ========================================================================= -->
        <template x-if="screenState === 'completed'">
            <div class="w-full h-full flex flex-col items-center justify-center text-center max-w-xl mx-auto">
                <div class="w-24 h-24 rounded-full bg-emerald-500/20 border-2 border-emerald-400 flex items-center justify-center mb-6 animate-pulse shadow-2xl shadow-emerald-500/20">
                    <svg class="w-12 h-12 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-4xl font-black text-white mb-2">Thank You! Order Completed</h2>
                <p class="text-slate-400 text-sm mb-6" x-text="'Order #' + (lastOrder.order_number || '') + ' registered successfully.'"></p>

                <div class="w-full bg-slate-900 border border-slate-800 rounded-3xl p-6 text-left space-y-3 mb-6 shadow-xl">
                    <div class="flex justify-between text-base">
                        <span class="text-slate-400">Total Paid:</span>
                        <span class="text-white font-extrabold" x-text="'रू ' + Number(lastOrder.grand_total || 0).toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-base">
                        <span class="text-slate-400">Payment Mode:</span>
                        <span class="text-amber-400 font-bold uppercase" x-text="lastOrder.payment_method"></span>
                    </div>
                    <template x-if="lastOrder.payment_method === 'cash'">
                        <div class="flex justify-between text-base pt-3 border-t border-slate-800 text-emerald-400 font-bold">
                            <span>Change Given:</span>
                            <span x-text="'रू ' + Number(lastOrder.change_returned || 0).toFixed(2)"></span>
                        </div>
                    </template>
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    Screen will return to product showcase in a few moments...
                </div>
            </div>
        </template>

    </main>

    <script>
        function customerDisplay() {
            return {
                connected: false,
                currentTime: '',
                screenState: 'idle', // 'idle', 'active', 'completed'
                cart: [],
                itemCount: 0,
                subtotal: 0,
                discount: 0,
                grandTotal: 0,
                paymentMethod: 'cash',
                cashReceived: 0,
                changeDue: 0,
                lastOrder: {},

                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    this.setupEcho();
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                },

                setupEcho() {
                    const connectEcho = () => {
                        if (!window.Echo) {
                            setTimeout(connectEcho, 100);
                            return;
                        }

                        const echo = window.Echo;

                        echo.connector.pusher.connection.bind('connected', () => {
                            this.connected = true;
                        });

                        echo.connector.pusher.connection.bind('disconnected', () => {
                            this.connected = false;
                        });

                        echo.connector.pusher.connection.bind('error', () => {
                            this.connected = false;
                        });

                        const channel = echo.channel('pos-display');

                        channel.listen('.cart.updated', (e) => {
                            this.handleCartUpdate(e.payload);
                        });

                        channel.listen('.order.completed', (e) => {
                            this.handleOrderCompleted(e.order);
                        });

                        channel.listen('.cart.cleared', () => {
                            this.resetToIdle();
                        });
                    };

                    connectEcho();
                },

                handleCartUpdate(payload) {
                    this.cart = payload.cart || [];
                    this.itemCount = payload.item_count || 0;
                    this.subtotal = payload.subtotal || 0;
                    this.discount = payload.discount || 0;
                    this.grandTotal = payload.grand_total || 0;
                    this.paymentMethod = payload.payment_method || 'cash';
                    this.cashReceived = payload.cash_received || 0;
                    this.changeDue = payload.change_due || 0;

                    if (this.cart.length > 0) {
                        this.screenState = 'active';
                    } else {
                        this.screenState = 'idle';
                    }
                },

                handleOrderCompleted(order) {
                    this.lastOrder = order;
                    this.screenState = 'completed';

                    setTimeout(() => {
                        if (this.screenState === 'completed') {
                            this.resetToIdle();
                        }
                    }, 8000);
                },

                resetToIdle() {
                    this.screenState = 'idle';
                    this.cart = [];
                    this.itemCount = 0;
                    this.subtotal = 0;
                    this.discount = 0;
                    this.grandTotal = 0;
                    this.cashReceived = 0;
                    this.changeDue = 0;
                }
            }
        }
    </script>
</body>
</html>
