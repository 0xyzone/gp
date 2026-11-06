<!DOCTYPE html>
<html lang="en" class="h-full bg-[#120207] text-slate-100 antialiased font-sans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $stallName }} - Live Order Fulfillment Board</title>

    <meta name="reverb-key" content="{{ config('reverb.apps.apps.0.key', env('REVERB_APP_KEY')) }}">
    <meta name="reverb-host" content="{{ env('REVERB_HOST') }}">
    <meta name="reverb-port" content="{{ env('REVERB_PORT', 8080) }}">
    <meta name="reverb-scheme" content="{{ env('REVERB_SCHEME', 'http') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Ambient Burgundy Glassmorphic Background */
        body {
            background: radial-gradient(circle at 10% 10%, #2a0311 0%, #170209 50%, #0d0105 100%);
            min-height: 100vh;
            min-height: 100dvh;
            touch-action: manipulation;
        }

        .glass-card {
            background: rgba(36, 4, 15, 0.78);
            border: 1px solid rgba(251, 113, 133, 0.24);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.75), inset 0 1px 0 rgba(255, 255, 255, 0.12);
        }

        .glass-card-served {
            background: rgba(18, 2, 8, 0.7);
            border: 1px solid rgba(16, 185, 129, 0.25);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .glass-header {
            background: rgba(26, 3, 11, 0.88);
            border-bottom: 1px solid rgba(251, 113, 133, 0.2);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }

        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }

        /* Mobile touch button feedback */
        .touch-action-btn:active {
            transform: scale(0.97);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between" x-data="orderFulfillmentBoard()" x-init="init()" x-cloak>

    <!-- Top Navigation & Live Header (Optimized for Mobile single row + expandable stats) -->
    <header class="glass-header sticky top-0 z-30 px-3.5 sm:px-8 py-3 flex items-center justify-between gap-2 shadow-lg shadow-black/40">
        <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
            <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-linear-to-br from-rose-600 to-rose-900 border border-rose-300/30 flex items-center justify-center font-black text-base sm:text-xl text-white shadow-md shadow-rose-950/60 shrink-0">
                घा
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2 truncate">
                    <h1 class="text-base sm:text-xl font-black text-white tracking-tight truncate">{{ $stallName }}</h1>
                    <span class="hidden xs:inline-block px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[9px] sm:text-[10px] font-black tracking-wider uppercase shrink-0">
                        Kitchen
                    </span>
                </div>
                <div class="flex items-center gap-2 text-[11px] text-rose-200/60">
                    <span class="truncate">Live Queue</span>
                    <span class="text-rose-500/60">•</span>
                    <!-- Realtime sync pill on mobile -->
                    <span class="flex items-center gap-1.5 font-semibold" :class="connected ? 'text-emerald-400' : 'text-rose-400'">
                        <span class="w-1.5 h-1.5 rounded-full" :class="connected ? 'bg-emerald-500 shadow-[0_0_6px_#10b981]' : 'bg-rose-500 animate-ping'"></span>
                        <span x-text="connected ? 'Live' : 'Connecting'"></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <!-- Digital Clock (Visible on tablet/desktop) -->
            <div class="hidden md:block px-3 py-1 rounded-xl bg-white/5 border border-white/10 text-rose-100 font-mono text-xs font-bold" x-text="currentTime"></div>

            <!-- Active Tickets Count Badge (Prominent on Mobile) -->
            <div class="flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-xl bg-rose-600/30 border border-rose-500/40 text-rose-200">
                <span class="text-[10px] sm:text-xs uppercase font-bold tracking-wider">Queue:</span>
                <span class="text-xs sm:text-sm font-black text-white" x-text="activeOrders.length"></span>
            </div>

            <!-- Staff Mode Badge / Login Link -->
            @if($isStaff)
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 text-xs font-bold" title="Staff Authenticated">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="hidden sm:inline">Staff</span>
                </div>
            @else
                <a href="{{ url('/login') }}" class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-rose-900/40 hover:bg-rose-800/60 border border-rose-500/30 text-rose-200 text-xs font-bold transition">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span class="hidden sm:inline">Staff Login</span>
                </a>
            @endif
        </div>
    </header>

    <!-- Main Live Queue Area (Mobile first padding & responsive grid) -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-3 py-4 sm:px-6 sm:py-8 lg:px-8 space-y-5 sm:space-y-8">

        <!-- Active Orders Header & Fast Filter Tabs for Mobile -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center justify-between sm:justify-start gap-3">
                <h2 class="text-lg sm:text-2xl font-black text-white tracking-tight flex items-center gap-2">
                    <span>Tickets to Prepare</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-600 text-white font-black text-xs sm:text-sm shadow-md" x-text="activeOrders.length"></span>
                </h2>

                <!-- Sound Alert Test / Toggle Button -->
                <button 
                    @click="playChime()" 
                    class="touch-action-btn flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 text-rose-200 text-[11px] font-semibold transition"
                    title="Test audio chime"
                >
                    <span>🔔</span>
                    <span class="hidden xs:inline">Test Sound</span>
                </button>
            </div>
            
            <div class="flex items-center justify-between sm:justify-end gap-3 text-[11px] text-rose-300/70">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Paid Ready</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                    <span>Unpaid Tab</span>
                </span>
                <span class="hidden md:inline font-mono">Auto-syncs instantly</span>
            </div>
        </div>

        <!-- Orders Grid: 1 col on mobile, 2 cols on tablet, 3 cols on desktop -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-6">
            
            <!-- Empty State -->
            <div x-show="activeOrders.length === 0" class="col-span-full py-12 sm:py-16 text-center glass-card rounded-2xl sm:rounded-3xl p-6 sm:p-8 border border-dashed border-rose-500/30">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl sm:rounded-3xl bg-rose-950/60 text-rose-300 mx-auto flex items-center justify-center mb-3 sm:mb-4 border border-rose-500/30">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-white">All Caught Up!</h3>
                <p class="text-xs sm:text-sm text-rose-200/60 mt-1 max-w-md mx-auto">There are no pending orders right now. New customer tickets will instantly pop up here with live sound notification.</p>
            </div>

            <!-- Active Order Cards (Mobile-first ergonomically spaced cards) -->
            <template x-for="order in activeOrders" :key="order.id">
                <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 flex flex-col justify-between transition-all duration-200 border-l-4"
                     :class="order.payment_status === 'paid' ? 'border-l-emerald-500' : 'border-l-rose-500'">
                    
                    <div>
                        <!-- Ticket Header (Clean mobile hierarchy) -->
                        <div class="flex items-start justify-between pb-3 sm:pb-4 border-b border-rose-900/60 gap-2">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-rose-300/60 block">Ticket No.</span>
                                <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight" x-text="'#' + order.order_number"></h3>
                                <p class="text-[11px] text-rose-200/50 font-mono mt-0.5" x-text="order.created_at_human || order.created_at"></p>
                            </div>
                            <div class="text-right flex flex-col items-end gap-1.5 shrink-0">
                                <!-- Fulfillment Status Badge -->
                                <span class="px-2 py-0.5 rounded-full text-[9px] sm:text-[10px] font-black tracking-wider uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    Preparing
                                </span>

                                <!-- Payment Status Badge (Paid vs Unpaid Alert) -->
                                <template x-if="order.payment_status === 'paid'">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        ✓ Paid
                                    </span>
                                </template>
                                <template x-if="order.payment_status !== 'paid'">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider uppercase bg-rose-500/30 text-rose-100 border border-rose-500/50 animate-pulse">
                                        ⚠ Collect रू <span x-text="Number(order.grand_total).toFixed(2)"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Ticket Items List (Mobile-friendly high readability touch rows) -->
                        <div class="py-3 sm:py-4 space-y-2">
                            <template x-for="item in order.items" :key="item.id || item.product_name">
                                <div class="flex items-center justify-between text-sm py-1.5 border-b border-rose-950/40 last:border-none gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-6 h-6 rounded-lg bg-rose-600/35 text-rose-100 border border-rose-400/30 flex items-center justify-center font-black text-xs shrink-0" x-text="item.quantity + 'x'"></span>
                                        <span class="font-bold text-white text-sm sm:text-base truncate" x-text="item.product_name"></span>
                                    </div>
                                    <span class="text-xs font-semibold text-rose-200/70 shrink-0 font-mono" x-text="'रू ' + Number(item.total_price || (item.unit_price * item.quantity)).toFixed(2)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Bottom Bar: Total & Large Touch Target Action Buttons -->
                    <div class="pt-3 sm:pt-4 border-t border-rose-900/60 space-y-3">
                        <div class="flex items-center justify-between text-xs text-rose-200/70">
                            <span class="font-medium">Total Bill:</span>
                            <span class="text-base sm:text-lg font-black text-white" x-text="'रू ' + Number(order.grand_total).toFixed(2)"></span>
                        </div>

                        <!-- Staff Action Buttons (Thumb-friendly on mobile) -->
                        <div class="space-y-2 pt-1">
                            @if($isStaff)
                                <!-- Quick Collect Payment button if order is unpaid -->
                                <template x-if="order.payment_status !== 'paid'">
                                    <div class="flex items-center gap-2 pb-1">
                                        <button 
                                            @click="settlePayment(order, 'cash')"
                                            :disabled="order._loading"
                                            class="touch-action-btn flex-1 min-h-[40px] py-2 px-2.5 rounded-xl bg-emerald-600/35 hover:bg-emerald-600/50 active:bg-emerald-600/70 border border-emerald-500/40 text-emerald-200 text-xs font-bold flex items-center justify-center gap-1.5 transition"
                                        >
                                            <span class="text-sm">💵</span>
                                            <span>Settle Cash</span>
                                        </button>
                                        <button 
                                            @click="settlePayment(order, 'qr')"
                                            :disabled="order._loading"
                                            class="touch-action-btn flex-1 min-h-[40px] py-2 px-2.5 rounded-xl bg-rose-600/35 hover:bg-rose-600/50 active:bg-rose-600/70 border border-rose-500/40 text-rose-200 text-xs font-bold flex items-center justify-center gap-1.5 transition"
                                        >
                                            <span class="text-sm">📱</span>
                                            <span>Settle QR</span>
                                        </button>
                                    </div>
                                </template>

                                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                    <!-- Cancel & Restock Button -->
                                    <button 
                                        @click="cancelOrder(order)"
                                        :disabled="order._loading"
                                        class="touch-action-btn min-h-[44px] w-full py-2.5 px-2 rounded-xl bg-red-950/40 hover:bg-red-900/60 active:bg-red-900/80 border border-red-500/40 text-red-200 hover:text-white font-bold text-xs tracking-wider transition flex items-center justify-center gap-1.5 disabled:opacity-50"
                                    >
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span class="truncate">Cancel</span>
                                    </button>

                                    <!-- Mark as Served Button (Primary Touch Target) -->
                                    <button 
                                        @click="markServed(order)"
                                        :disabled="order._loading"
                                        class="touch-action-btn min-h-[44px] w-full py-2.5 px-2.5 rounded-xl bg-linear-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 active:from-emerald-700 active:to-emerald-800 border border-emerald-400/50 text-white font-black text-xs sm:text-sm tracking-wider shadow-lg shadow-emerald-950/80 transition flex items-center justify-center gap-1.5 disabled:opacity-50"
                                    >
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Mark Served</span>
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-2.5 px-3 rounded-xl bg-black/40 border border-white/5 text-[11px] text-rose-300/50 font-medium">
                                    Login with staff account to fulfill or cancel tickets
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </template>

        </div>

        <!-- Recently Served Orders Section (Responsive horizontal drawer / collapsible on mobile) -->
        <div class="pt-6 sm:pt-8" x-show="servedOrders.length > 0">
            <h3 class="text-base sm:text-lg font-bold text-rose-200/80 mb-3 sm:mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Recently Served Orders</span>
            </h3>

            <!-- 2 cols on mobile, 4 cols on desktop -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4">
                <template x-for="sOrder in servedOrders" :key="sOrder.id">
                    <div class="glass-card-served rounded-xl sm:rounded-2xl p-3 sm:p-4 flex items-center justify-between gap-1">
                        <div class="min-w-0">
                            <span class="text-xs font-mono font-bold text-emerald-300 block truncate" x-text="'#' + sOrder.order_number"></span>
                            <span class="text-[10px] sm:text-[11px] text-rose-200/50 block font-mono" x-text="'रू ' + Number(sOrder.grand_total).toFixed(2)"></span>
                        </div>
                        <span class="px-1.5 sm:px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[9px] sm:text-[10px] font-black uppercase shrink-0">
                            Served ✓
                        </span>
                    </div>
                </template>
            </div>
        </div>

    </main>

    <!-- Board Footer -->
    <footer class="glass-header px-4 py-2.5 sm:px-8 sm:py-3 text-center text-[11px] sm:text-xs text-rose-300/40 font-medium">
        {{ $stallName }} • Live Stall Kitchen Fulfillment Display
    </footer>

    <!-- Alpine State & Realtime Echo Integration -->
    <script>
        function orderFulfillmentBoard() {
            return {
                connected: false,
                currentTime: '',
                activeOrders: @json($activeOrders),
                servedOrders: @json($recentServedOrders),

                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);
                    this.setupEcho();
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                },

                playChime() {
                    try {
                        const AudioCtxClass = window.AudioContext || window.webkitAudioContext;
                        if (!AudioCtxClass) return;
                        const ctx = new AudioCtxClass();
                        if (ctx.state === 'suspended') {
                            ctx.resume();
                        }
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                        osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15); // A5
                        gain.gain.setValueAtTime(0.08, ctx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.start();
                        osc.stop(ctx.currentTime + 0.35);
                    } catch (e) {
                        // Suppress browser autoplay restrictions if user gesture hasn't occurred yet
                    }
                },

                setupEcho() {
                    const connect = () => {
                        if (!window.Echo) {
                            setTimeout(connect, 100);
                            return;
                        }

                        const echo = window.Echo;

                        echo.connector.pusher.connection.bind('connected', () => {
                            this.connected = true;
                        });

                        echo.connector.pusher.connection.bind('disconnected', () => {
                            this.connected = false;
                        });

                        const channel = echo.channel('pos-display');

                        // When a new order is completed in POS terminal
                        channel.listen('.order.completed', (data) => {
                            this.handleNewOrder(data);
                        });

                        // When an order status changes (e.g. served or cancelled)
                        channel.listen('.order.status_changed', (data) => {
                            this.handleOrderStatusChanged(data);
                        });
                    };

                    connect();
                },

                handleNewOrder(data) {
                    const orderData = data.order || data;
                    
                    // Prevent duplicates
                    if (this.activeOrders.some(o => o.order_number === orderData.order_number || o.id === orderData.order_id)) {
                        return;
                    }

                    const newTicket = {
                        id: orderData.order_id || Date.now(),
                        order_number: orderData.order_number,
                        payment_status: orderData.payment_status || 'paid',
                        grand_total: orderData.grand_total,
                        items: (orderData.items || []).map(i => ({
                            id: i.id,
                            product_name: i.name || i.product_name,
                            quantity: i.quantity,
                            total_price: i.total || (i.price * i.quantity),
                        })),
                        created_at: orderData.created_at || 'Just now',
                        created_at_human: 'Just now',
                        status: 'completed',
                        _loading: false
                    };

                    this.activeOrders.unshift(newTicket);
                    this.playChime();
                },

                handleOrderStatusChanged(data) {
                    const payload = data.order || data;
                    const orderId = payload.order_id;
                    const orderNum = payload.order_number;
                    const status = payload.status;

                    if (status === 'served') {
                        const target = this.activeOrders.find(o => o.id === orderId || o.order_number === orderNum);
                        if (target) {
                            this.activeOrders = this.activeOrders.filter(o => o !== target);
                            this.servedOrders.unshift(target);
                            if (this.servedOrders.length > 8) {
                                this.servedOrders.pop();
                            }
                        }
                    } else if (status === 'cancelled') {
                        this.activeOrders = this.activeOrders.filter(o => o.id !== orderId && o.order_number !== orderNum);
                    }
                },

                async settlePayment(order, method) {
                    if (order._loading) return;
                    order._loading = true;

                    try {
                        const res = await fetch(`/orders-board/${order.id}/pay`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ payment_method: method })
                        });

                        const data = await res.json();
                        if (data.success) {
                            order.payment_status = 'paid';
                        } else {
                            alert(data.message || 'Could not record payment.');
                        }
                    } catch (e) {
                        alert('Could not record payment. Please check connection.');
                    } finally {
                        order._loading = false;
                    }
                },

                async markServed(order) {
                    if (order._loading) return;
                    order._loading = true;

                    try {
                        const res = await fetch(`/orders-board/${order.id}/serve`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.handleOrderStatusChanged({ order_id: order.id, order_number: order.order_number, status: 'served' });
                        }
                    } catch (e) {
                        alert('Could not update order status. Please check connection.');
                    } finally {
                        order._loading = false;
                    }
                },

                async cancelOrder(order) {
                    if (!confirm(`Cancel order #${order.order_number} and return items to physical stock?`)) {
                        return;
                    }

                    if (order._loading) return;
                    order._loading = true;

                    try {
                        const res = await fetch(`/orders-board/${order.id}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();
                        if (data.success) {
                            this.handleOrderStatusChanged({ order_id: order.id, order_number: order.order_number, status: 'cancelled' });
                        }
                    } catch (e) {
                        alert('Could not cancel order. Please check connection.');
                    } finally {
                        order._loading = false;
                    }
                }
            };
        }
    </script>
</body>
</html>

