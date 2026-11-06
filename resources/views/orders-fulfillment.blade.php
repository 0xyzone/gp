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
        }

        .glass-card {
            background: rgba(36, 4, 15, 0.75);
            border: 1px solid rgba(251, 113, 133, 0.22);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.7), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .glass-card-served {
            background: rgba(18, 2, 8, 0.65);
            border: 1px solid rgba(16, 185, 129, 0.25);
            backdrop-filter: blur(16px);
        }

        .glass-header {
            background: rgba(26, 3, 11, 0.85);
            border-bottom: 1px solid rgba(251, 113, 133, 0.2);
            backdrop-filter: blur(24px);
        }

        .scrollbar-none::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between" x-data="orderFulfillmentBoard()" x-init="init()" x-cloak>

    <!-- Top Navigation & Live Header -->
    <header class="glass-header sticky top-0 z-30 px-6 sm:px-10 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-600 to-rose-900 border border-rose-300/30 flex items-center justify-center font-black text-xl text-white shadow-lg shadow-rose-950/60">
                घा
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ $stallName }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-black tracking-wider uppercase">
                        Kitchen & Counter Board
                    </span>
                </div>
                <p class="text-xs text-rose-200/60 font-medium">Live Order Queue • Instant Real-time Broadcast</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- WebSocket Connection Status Indicator -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-black/40 border border-rose-500/20 text-xs font-semibold">
                <span class="w-2.5 h-2.5 rounded-full" :class="connected ? 'bg-emerald-500 shadow-[0_0_8px_#10b981]' : 'bg-rose-500 animate-ping'"></span>
                <span x-text="connected ? 'Live Sync Active' : 'Connecting...'" :class="connected ? 'text-emerald-400' : 'text-rose-400'"></span>
            </div>

            <!-- Digital Clock -->
            <div class="hidden sm:block px-4 py-1.5 rounded-xl bg-white/5 border border-white/10 text-rose-100 font-mono text-sm font-bold" x-text="currentTime"></div>

            <!-- Staff Mode Badge / Login Link -->
            @if($isStaff)
                <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Staff Authenticated</span>
                </div>
            @else
                <a href="{{ url('/login') }}" class="flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-rose-900/40 hover:bg-rose-800/60 border border-rose-500/30 text-rose-200 text-xs font-bold transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>Staff Login to Process</span>
                </a>
            @endif
        </div>
    </header>

    <!-- Main Live Queue Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6 sm:p-10 space-y-8">

        <!-- Active Orders Stats & Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-black text-white tracking-tight flex items-center gap-2.5">
                    <span>Active Orders to Prepare</span>
                    <span class="px-3 py-1 rounded-full bg-rose-600 text-white font-black text-sm shadow-md" x-text="activeOrders.length"></span>
                </h2>
            </div>
            
            <div class="text-xs text-rose-300/70 font-semibold">
                Auto-updates in real-time as cashier completes bill
            </div>
        </div>

        <!-- Orders Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Empty State -->
            <div x-show="activeOrders.length === 0" class="col-span-full py-16 text-center glass-card rounded-3xl p-8 border border-dashed border-rose-500/30">
                <div class="w-16 h-16 rounded-3xl bg-rose-950/60 text-rose-300 mx-auto flex items-center justify-center mb-4 border border-rose-500/30">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white">All Caught Up!</h3>
                <p class="text-sm text-rose-200/60 mt-1 max-w-md mx-auto">There are no pending orders right now. New customer tickets will instantly pop up here with live sound notification.</p>
            </div>

            <!-- Active Order Cards -->
            <template x-for="order in activeOrders" :key="order.id">
                <div class="glass-card rounded-3xl p-6 flex flex-col justify-between transition-all duration-300 hover:border-rose-400/50 hover:shadow-rose-950/80">
                    
                    <div>
                        <!-- Ticket Header -->
                        <div class="flex items-start justify-between pb-4 border-b border-rose-900/60">
                            <div>
                                <span class="text-[11px] font-black uppercase tracking-wider text-rose-300/60 block">Ticket No.</span>
                                <h3 class="text-2xl font-black text-white tracking-tight" x-text="'#' + order.order_number"></h3>
                            </div>
                            <div class="text-right flex flex-col items-end gap-1">
                                <!-- Fulfillment Status Badge -->
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    Preparing
                                </span>

                                <!-- Payment Status Badge (Paid vs Unpaid Alert) -->
                                <template x-if="order.payment_status === 'paid'">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        ✓ Paid (Ready for handoff)
                                    </span>
                                </template>
                                <template x-if="order.payment_status !== 'paid'">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider uppercase bg-rose-500/25 text-rose-200 border border-rose-500/40 animate-pulse">
                                        ⚠ Unpaid • Collect रू <span x-text="Number(order.grand_total).toFixed(2)"></span>
                                    </span>
                                </template>

                                <p class="text-[11px] text-rose-200/50 font-mono" x-text="order.created_at_human || order.created_at"></p>
                            </div>
                        </div>

                        <!-- Ticket Items List -->
                        <div class="py-4 space-y-2.5">
                            <template x-for="item in order.items" :key="item.id || item.product_name">
                                <div class="flex items-center justify-between text-sm py-1 border-b border-rose-950/40 last:border-none">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-6 h-6 rounded-lg bg-rose-600/30 text-rose-200 border border-rose-500/30 flex items-center justify-center font-black text-xs shrink-0" x-text="item.quantity + 'x'"></span>
                                        <span class="font-bold text-white" x-text="item.product_name"></span>
                                    </div>
                                    <span class="text-xs font-semibold text-rose-200/60" x-text="'रू ' + Number(item.total_price || (item.unit_price * item.quantity)).toFixed(2)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Bottom Bar: Total & Action Buttons -->
                    <div class="pt-4 border-t border-rose-900/60 space-y-4">
                        <div class="flex items-center justify-between text-xs text-rose-200/70">
                            <span>Total Bill:</span>
                            <span class="text-lg font-black text-white" x-text="'रू ' + Number(order.grand_total).toFixed(2)"></span>
                        </div>

                        <!-- Staff Action Buttons -->
                        <div class="space-y-2 pt-1">
                            @if($isStaff)
                                <!-- Quick Collect Payment button if order is unpaid -->
                                <template x-if="order.payment_status !== 'paid'">
                                    <div class="flex items-center gap-2 pb-1">
                                        <button 
                                            @click="settlePayment(order, 'cash')"
                                            :disabled="order._loading"
                                            class="flex-1 py-1.5 px-2 rounded-lg bg-emerald-600/30 hover:bg-emerald-600/50 border border-emerald-500/40 text-emerald-200 text-[11px] font-bold flex items-center justify-center gap-1 transition"
                                        >
                                            💵 Settle Cash
                                        </button>
                                        <button 
                                            @click="settlePayment(order, 'qr')"
                                            :disabled="order._loading"
                                            class="flex-1 py-1.5 px-2 rounded-lg bg-rose-600/30 hover:bg-rose-600/50 border border-rose-500/40 text-rose-200 text-[11px] font-bold flex items-center justify-center gap-1 transition"
                                        >
                                            📱 Settle QR
                                        </button>
                                    </div>
                                </template>

                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Cancel & Restock Button -->
                                    <button 
                                        @click="cancelOrder(order)"
                                        :disabled="order._loading"
                                        class="w-full py-2.5 px-3 rounded-xl bg-red-950/40 hover:bg-red-900/60 border border-red-500/40 text-red-200 hover:text-white font-bold text-xs tracking-wider transition flex items-center justify-center gap-1.5 disabled:opacity-50"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Cancel & Restock</span>
                                    </button>

                                    <!-- Mark as Served Button -->
                                    <button 
                                        @click="markServed(order)"
                                        :disabled="order._loading"
                                        class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 border border-emerald-400/40 text-white font-black text-xs tracking-wider shadow-lg shadow-emerald-950/60 transition flex items-center justify-center gap-1.5 disabled:opacity-50"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Mark Served</span>
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-2 px-3 rounded-xl bg-black/40 border border-white/5 text-[11px] text-rose-300/50 font-medium">
                                    Login with staff account to fulfill or cancel tickets
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </template>

        </div>

        <!-- Recently Served Orders Section -->
        <div class="pt-8" x-show="servedOrders.length > 0">
            <h3 class="text-lg font-bold text-rose-200/80 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Recently Served Orders (Completed for Customers)</span>
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <template x-for="sOrder in servedOrders" :key="sOrder.id">
                    <div class="glass-card-served rounded-2xl p-4 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-mono font-bold text-emerald-300 block" x-text="'#' + sOrder.order_number"></span>
                            <span class="text-[11px] text-rose-200/50" x-text="'रू ' + Number(sOrder.grand_total).toFixed(2)"></span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-black uppercase">
                            Served ✓
                        </span>
                    </div>
                </template>
            </div>
        </div>

    </main>

    <!-- Board Footer -->
    <footer class="glass-header px-8 py-3 text-center text-xs text-rose-300/40 font-medium">
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
                    this.playChime();
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                },

                playChime() {
                    try {
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
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
                        // Audio context restriction before user interaction
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

