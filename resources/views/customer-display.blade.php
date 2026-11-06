<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Order Display | Ghasa Products</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <!-- Pusher & Laravel Echo -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.17.1/dist/echo.iife.js"></script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        .glow { box-shadow: 0 0 50px -10px rgba(245, 158, 11, 0.25); }
    </style>
</head>
<body class="h-full overflow-hidden select-none bg-slate-950" x-data="customerDisplay()" x-init="init()" x-cloak>
    
    <!-- Top Floating Header -->
    <header class="fixed top-0 left-0 right-0 z-20 flex items-center justify-between px-8 py-4 bg-slate-950/80 backdrop-blur-md border-b border-slate-800/80">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center shadow-lg shadow-amber-500/20 text-slate-950 font-black text-xl">
                G
            </div>
            <div>
                <h1 class="text-lg font-bold tracking-tight text-white">{{ \App\Models\Setting::get('stall_name', 'Ghasa Products') }}</h1>
                <p class="text-xs text-amber-400 font-semibold uppercase tracking-wider">Customer Live Counter</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Connection indicator -->
            <div class="flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold"
                 :class="connected ? 'bg-emerald-950/80 text-emerald-400 border border-emerald-800' : 'bg-rose-950/80 text-rose-400 border border-rose-800'">
                <span class="w-2 h-2 rounded-full" :class="connected ? 'bg-emerald-400 animate-pulse' : 'bg-rose-400'"></span>
                <span x-text="connected ? 'Live Sync Active' : 'Connecting...'"></span>
            </div>
            
            <div class="text-xs text-slate-400 font-mono" x-text="currentTime"></div>
        </div>
    </header>

    <!-- Main Screen Content Area -->
    <main class="h-full pt-20 pb-6 px-8 flex gap-8">

        <!-- STATE 1: Empty / Idle Welcome Screen -->
        <template x-if="screenState === 'idle'">
            <div class="w-full h-full flex flex-col items-center justify-center text-center">
                <div class="relative mb-6">
                    <div class="w-24 h-24 rounded-3xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 glow">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>

                <h2 class="text-4xl font-extrabold text-white tracking-tight mb-2">
                    Welcome to {{ \App\Models\Setting::get('stall_name', 'Ghasa Products') }}
                </h2>
                <p class="text-slate-400 text-base max-w-md">
                    Please place your order at the counter. Your items and live bill calculations will appear right here.
                </p>

                <div class="mt-8 flex items-center gap-3 px-5 py-2.5 rounded-full bg-slate-900/80 border border-slate-800 text-xs font-medium text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    Terminal Ready for Next Customer
                </div>
            </div>
        </template>

        <!-- STATE 2: Active Order Live Calculation Screen -->
        <template x-if="screenState === 'active'">
            <div class="w-full h-full grid grid-cols-12 gap-8">
                
                <!-- Left: Order Items List (8 cols) -->
                <div class="col-span-7 flex flex-col bg-slate-900/70 border border-slate-800/80 rounded-3xl p-6 backdrop-blur-sm overflow-hidden">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-slate-300 uppercase tracking-wider">Your Order Items</span>
                            <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold" x-text="itemCount + ' items'"></span>
                        </div>
                        <span class="text-xs text-slate-500">Live Counter</span>
                    </div>

                    <!-- Items Scrollable -->
                    <div class="flex-1 overflow-y-auto py-4 space-y-3 pr-2">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center font-bold text-amber-400 text-sm" x-text="item.quantity + 'x'"></div>
                                    <div>
                                        <h4 class="font-bold text-white text-base" x-text="item.name"></h4>
                                        <p class="text-xs text-slate-400" x-text="'रू ' + Number(item.price).toFixed(2) + ' each'"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-amber-400" x-text="'रू ' + Number(item.total).toFixed(2)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Right: Bill Calculations & Payment Status (5 cols) -->
                <div class="col-span-5 flex flex-col gap-6">
                    <!-- Grand Total Card -->
                    <div class="p-6 rounded-3xl bg-gradient-to-br from-amber-600/20 via-slate-900 to-slate-900 border border-amber-500/30 flex flex-col justify-between glow">
                        <div>
                            <span class="text-xs font-bold text-amber-400 uppercase tracking-wider block mb-1">Total Amount Payable</span>
                            <div class="text-5xl font-black text-white tracking-tight" x-text="'रू ' + Number(grandTotal).toFixed(2)"></div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-800 space-y-2 text-sm">
                            <div class="flex justify-between text-slate-400">
                                <span>Subtotal</span>
                                <span class="font-semibold text-slate-200" x-text="'रू ' + Number(subtotal).toFixed(2)"></span>
                            </div>
                            <template x-if="discount > 0">
                                <div class="flex justify-between text-emerald-400">
                                    <span>Expo Special Discount</span>
                                    <span class="font-bold" x-text="'- रू ' + Number(discount).toFixed(2)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Payment Mode Section -->
                    <div class="flex-1 p-6 rounded-3xl bg-slate-900/70 border border-slate-800/80 flex flex-col justify-between">
                        <!-- CASH MODE -->
                        <template x-if="paymentMethod === 'cash'">
                            <div class="h-full flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 text-slate-300 mb-4">
                                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span class="font-bold text-sm uppercase tracking-wider">Cash Payment</span>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800">
                                            <span class="text-xs text-slate-400 block mb-1">Amount Tendered</span>
                                            <span class="text-2xl font-bold text-slate-100" x-text="cashReceived > 0 ? 'रू ' + Number(cashReceived).toFixed(2) : 'Awaiting cash...'"></span>
                                        </div>

                                        <div class="p-3.5 rounded-2xl border" :class="changeDue >= 0 && cashReceived > 0 ? 'bg-emerald-950/30 border-emerald-800/60 text-emerald-400' : 'bg-slate-950/60 border-slate-800 text-slate-400'">
                                            <span class="text-xs block mb-1" :class="changeDue >= 0 && cashReceived > 0 ? 'text-emerald-400' : 'text-slate-400'">Change Return</span>
                                            <span class="text-3xl font-black" x-text="cashReceived > 0 ? 'रू ' + Number(Math.max(0, changeDue)).toFixed(2) : 'रू 0.00'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-[11px] text-slate-500 text-center">
                                    Please verify change before leaving the counter
                                </div>
                            </div>
                        </template>

                        <!-- QR MODE -->
                        <template x-if="paymentMethod === 'qr'">
                            <div class="h-full flex flex-col items-center justify-between text-center py-1">
                                <div class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    Scan with Any Bank App / Fonepay
                                </div>

                                <!-- Official Bank QR Artwork from Settings -->
                                <div class="my-auto w-full max-w-[260px] bg-white rounded-2xl p-2 shadow-2xl border-2 border-amber-400 overflow-hidden">
                                    @php
                                        $qrImage = \App\Models\Setting::get('payment_qr_image', 'images/siddhartha-bank-qr.png');
                                        $qrUrl = str_starts_with($qrImage, 'http') ? $qrImage : (str_starts_with($qrImage, 'images/') ? asset($qrImage) : asset('storage/' . $qrImage));
                                    @endphp
                                    <img 
                                        src="{{ $qrUrl }}" 
                                        alt="Scan To Pay" 
                                        class="w-full h-auto object-contain rounded-xl"
                                    />
                                </div>

                                <div>
                                    <div class="text-base font-extrabold text-white" x-text="'Pay रू ' + Number(grandTotal).toFixed(2)"></div>
                                    <p class="text-[11px] text-amber-400 font-medium mt-0.5">{{ \App\Models\Setting::get('upi_bank_name', 'Siddhartha Bank') }} • Any Banking App</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- STATE 3: Order Completed / Thank You Screen -->
        <template x-if="screenState === 'completed'">
            <div class="w-full h-full flex flex-col items-center justify-center text-center max-w-xl mx-auto">
                <div class="w-20 h-20 rounded-full bg-emerald-500/20 border-2 border-emerald-400 flex items-center justify-center mb-5 animate-pulse">
                    <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-3xl font-black text-white mb-2">Payment Successful!</h2>
                <p class="text-slate-400 text-sm mb-6" x-text="'Order #' + (lastOrder.order_number || '') + ' has been registered.'"></p>

                <div class="w-full bg-slate-900 border border-slate-800 rounded-3xl p-6 text-left space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Total Paid</span>
                        <span class="text-white font-bold" x-text="'रू ' + Number(lastOrder.grand_total || 0).toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Payment Method</span>
                        <span class="text-amber-400 font-bold uppercase" x-text="lastOrder.payment_method"></span>
                    </div>
                    <template x-if="lastOrder.payment_method === 'cash'">
                        <div class="flex justify-between text-sm pt-2 border-t border-slate-800 text-emerald-400 font-bold">
                            <span>Change Returned</span>
                            <span x-text="'रू ' + Number(lastOrder.change_returned || 0).toFixed(2)"></span>
                        </div>
                    </template>
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    Screen will automatically reset for the next customer in a few moments...
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
                    const reverbKey = "{{ config('reverb.apps.apps.0.key', env('REVERB_APP_KEY')) }}";
                    const reverbHost = "{{ env('REVERB_HOST', 'localhost') }}";
                    const reverbPort = {{ env('REVERB_PORT', 8080) }};
                    const reverbScheme = "{{ env('REVERB_SCHEME', 'http') }}";

                    const echo = new window.Echo({
                        broadcaster: 'reverb',
                        key: reverbKey,
                        wsHost: reverbHost === '0.0.0.0' ? window.location.hostname : reverbHost,
                        wsPort: reverbPort,
                        wssPort: reverbPort,
                        forceTLS: reverbScheme === 'https',
                        enabledTransports: ['ws', 'wss'],
                    });

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
