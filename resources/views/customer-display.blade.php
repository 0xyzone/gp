<!DOCTYPE html>
<html lang="en" class="h-full bg-[#fcf9f9] text-slate-800 antialiased overflow-hidden select-none">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1920, height=1080, initial-scale=1">
    <title>{{ \App\Models\Setting::get('stall_name', 'Ghasa Products') }} - Customer Display</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Fixed 1920x1080 Viewport Enclosure */
        body {
            width: 1920px;
            height: 1080px;
            margin: 0 auto;
            position: relative;
            background: radial-gradient(circle at 10% 10%, #fff1f2 0%, #fdf8f8 45%, #f7f1f2 100%);
        }

        .scrollbar-none::-webkit-scrollbar { display: none; }

        /* Soft Burgundy Drop Shadows */
        .shadow-burgundy-soft {
            box-shadow: 0 20px 40px -15px rgba(136, 19, 55, 0.12), 0 0 1px 1px rgba(136, 19, 55, 0.05);
        }

        .shadow-burgundy-glow {
            box-shadow: 0 25px 60px -10px rgba(136, 19, 55, 0.35);
        }
    </style>
</head>
<body class="flex flex-col justify-between p-10 font-sans" x-data="customerDisplay()" x-init="init()" x-cloak>
    
    <!-- Background Elegant Burgundy Blobs -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 -left-32 w-[650px] h-[650px] bg-rose-200/40 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 -right-20 w-[700px] h-[700px] bg-red-100/50 rounded-full blur-[150px]"></div>
        <div class="absolute top-1/3 right-1/4 w-[450px] h-[450px] bg-amber-100/40 rounded-full blur-[120px]"></div>
    </div>

    <!-- ========================================================================= -->
    <!-- HEADER BAR: CLEAN MOBILE/TABLET APP HEADER IN BRIGHT BURGUNDY THEME -->
    <!-- ========================================================================= -->
    <header class="relative z-20 flex items-center justify-between px-8 py-5 rounded-3xl glass-panel-light shadow-burgundy-soft">
        <div class="flex items-center gap-4">
            <!-- App Icon / Badge -->
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#881337] to-[#4c0519] flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-rose-950/20">
                घ
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-black tracking-tight text-[#4c0519]">
                        घासा • Ghasa Products
                    </h1>
                    <span class="px-3 py-1 rounded-full bg-[#881337]/10 text-[#881337] font-extrabold text-xs tracking-wider uppercase">
                        Authentic Nepali Achar & Pani Puri Stall
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">
                    100% Traditional Homemade Pickles, Organics & Live Crunchy Pani Puri
                </p>
            </div>
        </div>

        <!-- Connection Badge & Real-Time Clock -->
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2.5 px-5 py-2.5 rounded-2xl bg-white border border-rose-100 shadow-sm text-xs font-bold text-slate-700">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="connected ? 'bg-emerald-400' : 'bg-rose-400'"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3" :class="connected ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                </span>
                <span x-text="connected ? 'Counter Connected' : 'Connecting...'"></span>
            </div>

            <div class="px-5 py-2.5 rounded-2xl bg-[#881337] text-white font-mono text-xs font-bold shadow-md shadow-rose-950/15" x-text="currentTime"></div>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- MAIN INTERFACE: 1920x1080 FULL SCREEN WITH CLEAN PROPORTIONS & BREATHING SPACE -->
    <!-- ========================================================================= -->
    <main class="relative z-10 flex-1 my-7 flex flex-col min-h-0 overflow-hidden">

        <!-- ===================================================================== -->
        <!-- VIEW 1: STANDBY / IDLE SCREEN (BURGUNDY CLEAN MOBILE APP CARD SHOWCASE) -->
        <!-- Clean Hero Banner + Infinite Product Ribbon with Crisp Cards -->
        <!-- ===================================================================== -->
        <template x-if="screenState === 'idle'">
            <div class="w-full h-full flex flex-col justify-between">
                
                <!-- Hero Banner Card -->
                <div class="relative rounded-[32px] p-8 glass-panel-burgundy text-white flex items-center justify-between overflow-hidden shrink-0">
                    <!-- Subtle pattern accent -->
                    <div class="absolute -right-20 -bottom-20 w-80 h-80 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>

                    <div class="space-y-2 max-w-3xl">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/15 text-rose-100 text-xs font-bold uppercase tracking-widest backdrop-blur-md">
                            <span>✨</span> Welcome to our Expo Stall
                        </div>
                        <h2 class="text-4xl font-black tracking-tight leading-tight">
                            स्वादिलो घरको अचार र क्रिस्पी पानी पुरी!
                        </h2>
                        <p class="text-sm text-rose-100/90 leading-relaxed font-normal max-w-2xl">
                            Taste our signature Akabare, Gundruk, Buff & Chicken Pickles, or enjoy freshly served hygienic Pani Puri with tingling jaljeera and sweet lapsi chutney.
                        </p>
                    </div>

                    <!-- Right Quick QR Scan Pill -->
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-3xl border border-white/20 flex items-center gap-4 shrink-0 shadow-lg animate-float-gentle">
                        <div class="w-14 h-14 rounded-2xl bg-white text-[#881337] flex items-center justify-center font-black shadow-md">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <span class="text-[11px] font-black uppercase tracking-widest text-rose-200 block">Digital Stall Pay</span>
                            <span class="text-base font-black text-white">Siddhartha Bank QR</span>
                            <span class="text-[11px] text-rose-100/80 block mt-0.5">Fonepay • eSewa • Mobile Banking</span>
                        </div>
                    </div>
                </div>

                <!-- Products Infinite Carousel Section with Proper Breathing Space -->
                <div class="relative w-full flex-1 flex flex-col justify-center overflow-hidden py-3">
                    <div class="flex items-center justify-between mb-4 px-2">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#881337]"></span>
                            <span class="text-xs font-black uppercase tracking-widest text-[#881337]">
                                Fresh Stall Catalog • Live Menu
                            </span>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">
                            Order at the counter • {{ count($allProducts) }} Items Available
                        </span>
                    </div>

                    <!-- Marquee Track Wrapper with Soft Gradient Edges -->
                    <div class="relative w-full overflow-hidden">
                        <div class="absolute left-0 inset-y-0 w-28 bg-gradient-to-r from-[#fcf9f9] to-transparent z-10 pointer-events-none"></div>
                        <div class="absolute right-0 inset-y-0 w-28 bg-gradient-to-l from-[#fcf9f9] to-transparent z-10 pointer-events-none"></div>

                        <!-- Infinite Looping Track -->
                        <div class="flex gap-7 animate-marquee-slow w-max py-3">
                            @foreach([$allProducts, $allProducts] as $productsBatch)
                                @foreach($productsBatch as $product)
                                    @php
                                        $img = $product->image ? asset('storage/' . $product->image) : asset('images/placeholder-product.svg');
                                    @endphp
                                    <!-- App Card Style with Glassmorphic Clean Look -->
                                    <div class="w-[300px] shrink-0 rounded-[28px] p-5 glass-card-clean flex flex-col justify-between select-none">
                                        <div>
                                            <!-- Image Box -->
                                            <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-rose-50/50 mb-4 border border-rose-100/60 flex items-center justify-center p-3">
                                                <img 
                                                    src="{{ $img }}" 
                                                    alt="{{ $product->name }}" 
                                                    class="w-full h-full object-contain"
                                                    loading="lazy"
                                                />
                                                @if($product->category)
                                                    <span class="absolute top-3 left-3 px-3 py-1 rounded-full bg-[#881337] text-white text-[10px] font-black uppercase tracking-wider shadow-sm">
                                                        {{ $product->category->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            <h3 class="text-base font-black text-slate-900 line-clamp-1 leading-snug">
                                                {{ $product->name }}
                                            </h3>
                                            @if($product->description)
                                                <p class="text-xs text-slate-500 line-clamp-2 mt-1.5 leading-relaxed font-normal">
                                                    {{ $product->description }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="mt-5 pt-3.5 border-t border-rose-100 flex items-center justify-between">
                                            <div>
                                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Price</span>
                                                <span class="text-xl font-black text-[#881337]">
                                                    रू {{ number_format($product->price, 2) }}
                                                </span>
                                            </div>
                                            <div class="px-3.5 py-1.5 rounded-xl bg-rose-50 text-[#881337] font-black text-xs border border-rose-200/60">
                                                Freshly Prepared
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </template>


        <!-- ===================================================================== -->
        <!-- VIEW 2: ACTIVE ORDER PLACEMENT (ELEGANT BURGUNDY LIVE BILLING) -->
        <!-- Left: Itemized Order | Right: Total & Big Siddhartha Bank QR Frame -->
        <!-- ===================================================================== -->
        <template x-if="screenState === 'active'">
            <div class="w-full h-full grid grid-cols-12 gap-8">
                
                <!-- Left 7 Columns: Order Items List Card -->
                <div class="col-span-7 flex flex-col rounded-[32px] p-8 glass-panel-light shadow-burgundy-soft overflow-hidden">
                    <div class="flex items-center justify-between pb-5 border-b border-rose-100">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-[#881337] text-white flex items-center justify-center font-bold shadow-md shadow-rose-950/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Your Order Items</h3>
                                <p class="text-xs text-slate-500 font-medium">Prepared freshly at Ghasa Products counter</p>
                            </div>
                        </div>

                        <div class="px-4 py-1.5 rounded-full bg-rose-100 text-[#881337] font-black text-xs">
                            <span x-text="itemCount + ' items'"></span>
                        </div>
                    </div>

                    <!-- Scrollable items -->
                    <div class="flex-1 overflow-y-auto py-5 space-y-3.5 pr-2 scrollbar-none">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white border border-rose-100/80 shadow-sm hover:border-[#881337]/30 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-[#881337] font-black text-xl shadow-inner shrink-0" x-text="item.quantity + 'x'"></div>
                                    <div>
                                        <h4 class="font-black text-slate-900 text-lg leading-snug" x-text="item.name"></h4>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="'रू ' + Number(item.price).toFixed(2) + ' each'"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-[#881337]" x-text="'रू ' + Number(item.total).toFixed(2)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Subtotal bar -->
                    <div class="pt-4 border-t border-rose-100 flex items-center justify-between text-sm text-slate-600">
                        <span class="font-medium">Items Subtotal:</span>
                        <span class="font-extrabold text-slate-900 text-lg" x-text="'रू ' + Number(subtotal).toFixed(2)"></span>
                    </div>
                </div>

                <!-- Right 5 Columns: Grand Total & Massive Siddhartha Bank QR Frame -->
                <div class="col-span-5 flex flex-col gap-6">
                    
                    <!-- Grand Total Card in Burgundy Gradient -->
                    <div class="p-7 rounded-[32px] glass-panel-burgundy text-white shadow-burgundy-glow flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black uppercase tracking-widest text-rose-200">TOTAL BILL AMOUNT</span>
                            <template x-if="discount > 0">
                                <span class="text-xs font-black px-3 py-1 rounded-full bg-emerald-500 text-white shadow-sm" x-text="'Stall Discount: - रू ' + Number(discount).toFixed(2)"></span>
                            </template>
                        </div>

                        <div class="mt-2 text-6xl font-black tracking-tight leading-none text-white" x-text="'रू ' + Number(grandTotal).toFixed(2)"></div>
                    </div>

                    <!-- Interactive Tender or Big QR Box -->
                    <div class="flex-1 rounded-[32px] p-7 glass-panel-light shadow-burgundy-soft border border-white flex flex-col justify-between overflow-hidden">
                        
                        <!-- CASH MODE -->
                        <template x-if="paymentMethod === 'cash'">
                            <div class="h-full flex flex-col justify-between py-2">
                                <div>
                                    <div class="flex items-center gap-3 text-slate-800 mb-6 pb-4 border-b border-rose-100">
                                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-black text-lg uppercase tracking-wider text-slate-900">Cash Payment</span>
                                            <p class="text-xs text-slate-500">Please hand over the exact cash at counter</p>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="p-5 rounded-2xl bg-white border border-rose-100 shadow-sm">
                                            <span class="text-xs text-slate-400 block mb-1 font-bold uppercase tracking-wider">Amount Tendered</span>
                                            <span class="text-3xl font-black text-slate-800" x-text="cashReceived > 0 ? 'रू ' + Number(cashReceived).toFixed(2) : 'Awaiting cash...'"></span>
                                        </div>

                                        <div class="p-5 rounded-2xl border transition-all" :class="changeDue >= 0 && cashReceived > 0 ? 'bg-emerald-50 border-emerald-300 text-emerald-900 shadow-sm' : 'bg-white border-rose-100 text-slate-400'">
                                            <span class="text-xs block mb-1 font-black uppercase tracking-wider" :class="changeDue >= 0 && cashReceived > 0 ? 'text-emerald-700' : 'text-slate-400'">Return Change</span>
                                            <span class="text-5xl font-black tracking-tight" x-text="cashReceived > 0 ? 'रू ' + Number(Math.max(0, changeDue)).toFixed(2) : 'रू 0.00'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs text-slate-500 text-center font-medium bg-rose-50/60 p-3.5 rounded-2xl border border-rose-100">
                                    Please collect your receipt & verified return change before leaving the counter
                                </div>
                            </div>
                        </template>

                        <!-- QR SCAN MODE: BIG HIGH RESOLUTION SIDDHARTHA BANK QR DESIGN -->
                        <template x-if="paymentMethod === 'qr'">
                            <div class="h-full flex flex-col items-center justify-between text-center py-1">
                                
                                <div class="px-5 py-2 rounded-full bg-[#881337]/10 text-[#881337] text-xs font-black uppercase tracking-widest flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    SCAN TO PAY WITH ANY BANK APP
                                </div>

                                <!-- Big Prominent QR Art in Clean White Card -->
                                <div class="my-auto w-full max-w-[340px] bg-white rounded-3xl p-3 shadow-xl border-4 border-[#881337] transform hover:scale-105 transition-all">
                                    @php
                                        $qrImage = \App\Models\Setting::get('payment_qr_image', 'images/siddhartha-bank-qr.png');
                                        $qrUrl = str_starts_with($qrImage, 'http') ? $qrImage : (str_starts_with($qrImage, 'images/') ? asset($qrImage) : asset('storage/' . $qrImage));
                                    @endphp
                                    <img 
                                        src="{{ $qrUrl }}" 
                                        alt="Scan QR to Pay" 
                                        class="w-full h-auto object-contain rounded-2xl block"
                                    />
                                </div>

                                <div class="space-y-1">
                                    <div class="text-2xl font-black text-slate-900" x-text="'Please Pay रू ' + Number(grandTotal).toFixed(2)"></div>
                                    <p class="text-xs text-[#881337] font-extrabold uppercase tracking-wide">
                                        {{ \App\Models\Setting::get('upi_bank_name', 'Siddhartha Bank') }} • Any Banking App / Fonepay
                                    </p>
                                </div>

                            </div>
                        </template>

                    </div>
                </div>

            </div>
        </template>


        <!-- ===================================================================== -->
        <!-- VIEW 3: ORDER COMPLETED SCREEN -->
        <!-- ===================================================================== -->
        <template x-if="screenState === 'completed'">
            <div class="w-full h-full flex flex-col items-center justify-center text-center max-w-2xl mx-auto py-8">
                
                <div class="w-28 h-28 rounded-full bg-emerald-100 border-4 border-emerald-500 text-emerald-600 flex items-center justify-center mb-6 shadow-xl animate-bounce">
                    <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h2 class="text-4xl font-black text-slate-900 mb-2 tracking-tight">धन्यवाद! भुक्तानी सम्पन्न भयो</h2>
                <p class="text-slate-600 text-base mb-8" x-text="'Order #' + (lastOrder.order_number || '') + ' registered successfully.'"></p>

                <div class="w-full glass-panel-light rounded-3xl p-8 text-left space-y-4 mb-8 shadow-burgundy-soft border border-rose-100">
                    <div class="flex justify-between text-lg">
                        <span class="text-slate-500">Total Settled:</span>
                        <span class="text-[#881337] font-black text-3xl" x-text="'रू ' + Number(lastOrder.grand_total || 0).toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-base">
                        <span class="text-slate-500">Payment Option:</span>
                        <span class="text-slate-900 font-black uppercase tracking-wider" x-text="lastOrder.payment_method"></span>
                    </div>
                    <template x-if="lastOrder.payment_method === 'cash'">
                        <div class="flex justify-between text-base pt-3 border-t border-rose-100 text-emerald-700 font-black">
                            <span>Change Returned:</span>
                            <span class="text-xl" x-text="'रू ' + Number(lastOrder.change_returned || 0).toFixed(2)"></span>
                        </div>
                    </template>
                </div>

                <div class="text-xs text-[#881337] font-bold uppercase tracking-widest bg-rose-50 px-6 py-3 rounded-full border border-rose-200">
                    Returning to stall menu in a few moments...
                </div>
            </div>
        </template>

    </main>

    <!-- Clean Bottom Footer Bar -->
    <footer class="relative z-10 px-8 py-3 rounded-2xl glass-panel-light flex items-center justify-between text-xs text-slate-500 font-medium">
        <div class="flex items-center gap-3">
            <span class="font-bold text-[#881337]">{{ \App\Models\Setting::get('account_holder', 'Ghasa Products Nepal') }}</span>
            <span>•</span>
            <span>Customer Helpline: {{ \App\Models\Setting::get('contact_phone', '+977-9800000000') }}</span>
        </div>
        <div class="flex items-center gap-2 font-bold text-[#881337]">
            <span>घासा अचार तथा पानी पुरी स्टलमा स्वागत छ!</span>
        </div>
    </footer>

    <!-- Customer Display Echo Connection & State Handler -->
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
