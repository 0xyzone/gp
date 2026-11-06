<!DOCTYPE html>
<html lang="en" class="h-full bg-[#07090e] text-slate-100 antialiased overflow-hidden select-none">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('stall_name', 'Ghasa Products') }} - Customer Display</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* 3D background subtle mesh */
        .bg-mesh {
            background-color: #06080d;
            background-image: 
                radial-gradient(at 0% 0%, rgba(217, 119, 6, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(59, 130, 246, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(245, 158, 11, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.9) 0px, transparent 50%);
        }

        .tilt-card {
            transform: perspective(1000px) rotateX(2deg) rotateY(-1deg);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
        }
        .tilt-card:hover {
            transform: perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(-6px) scale(1.02);
        }

        .shadow-neon-amber {
            box-shadow: 0 0 50px -10px rgba(245, 158, 11, 0.4), 0 20px 40px -15px rgba(0, 0, 0, 0.7);
        }
        
        .shadow-3d-subtle {
            box-shadow: 
                0 1px 1px rgba(0, 0, 0, 0.08),
                0 2px 2px rgba(0, 0, 0, 0.12),
                0 4px 4px rgba(0, 0, 0, 0.16),
                0 8px 8px rgba(0, 0, 0, 0.2),
                0 16px 16px rgba(0, 0, 0, 0.24),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body class="h-full bg-mesh text-slate-100 flex flex-col justify-between" x-data="customerDisplay()" x-init="init()" x-cloak>
    
    <!-- Atmospheric Background 3D Glow Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-32 left-1/4 w-[600px] h-[600px] bg-amber-500/10 rounded-full blur-[140px] animate-pulse-glow"></div>
        <div class="absolute -bottom-40 right-10 w-[650px] h-[650px] bg-amber-600/10 rounded-full blur-[150px] animate-pulse-glow" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-[-10%] w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-[130px]"></div>
    </div>

    <!-- ========================================================================= -->
    <!-- MINIMAL FLOATING BRAND HEADER (Clean, Premium, Non-Cluttered) -->
    <!-- ========================================================================= -->
    <header class="relative z-20 px-10 pt-6 pb-2 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- 3D Brand Medallion -->
            <div class="relative group">
                <div class="absolute -inset-1 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 opacity-60 blur-md group-hover:opacity-100 transition duration-500"></div>
                <div class="relative w-12 h-12 rounded-2xl bg-gradient-to-br from-[#1e2430] to-[#0c1017] border border-amber-400/40 flex items-center justify-center text-amber-400 font-black text-2xl shadow-3d-subtle">
                    G
                </div>
            </div>

            <div>
                <h1 class="text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    {{ \App\Models\Setting::get('stall_name', 'Ghasa Products') }}
                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-500/15 border border-amber-400/30 text-amber-300 uppercase tracking-widest">
                        Expo 2026
                    </span>
                </h1>
                <p class="text-xs text-slate-400 font-medium tracking-wide">Authentic Himalayan Delicacies & Organic Produce</p>
            </div>
        </div>

        <!-- Right Side: Status Badge & Dynamic Mode Indicator -->
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-white/[0.04] backdrop-blur-xl border border-white/[0.08] shadow-3d-subtle">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" :class="connected ? 'bg-emerald-400' : 'bg-rose-400'"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5" :class="connected ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                </span>
                <span class="text-xs font-bold tracking-wider uppercase text-slate-300" x-text="connected ? 'Live Sync' : 'Reconnecting...'"></span>
            </div>

            <div class="px-4 py-2 rounded-2xl bg-white/[0.04] backdrop-blur-xl border border-white/[0.08] text-xs font-mono font-bold text-slate-400 shadow-3d-subtle" x-text="currentTime"></div>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- MAIN INTERACTIVE DISPLAY AREA -->
    <!-- ========================================================================= -->
    <main class="relative z-10 flex-1 px-10 py-4 flex flex-col min-h-0 overflow-hidden">

        <!-- ===================================================================== -->
        <!-- SCENARIO A: STANDBY SCREEN (NO ACTIVE ORDER) -->
        <!-- Cinematic Hero Feature + Infinite Smooth Scrolling 3D Product Carousel -->
        <!-- ===================================================================== -->
        <template x-if="screenState === 'idle'">
            <div class="w-full h-full flex flex-col justify-between py-2">
                
                <!-- Top Centerpiece: Visual Hero Banner with 3D Depth -->
                <div class="relative rounded-3xl p-8 overflow-hidden glass-card shadow-3d-subtle border border-amber-500/20 flex flex-col md:flex-row items-center justify-between gap-8 mb-4">
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="space-y-2 max-w-2xl text-left">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gradient-to-r from-amber-500/20 to-transparent border-l-2 border-amber-400 text-amber-300 text-xs font-extrabold uppercase tracking-widest">
                            <span>✦</span> Freshly Crafted For You
                        </div>
                        <h2 class="text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                            Experience True Himalayan Flavors
                        </h2>
                        <p class="text-sm lg:text-base text-slate-300 leading-relaxed font-normal">
                            Explore our handpicked organic range below. When you're ready, place your order with our cashier and watch your bill calculate live on this screen.
                        </p>
                    </div>

                    <!-- Right feature highlight badge -->
                    <div class="flex items-center gap-5 bg-gradient-to-br from-amber-500/20 via-slate-900/60 to-slate-950/80 p-5 rounded-3xl border border-amber-400/30 shadow-neon-amber shrink-0 animate-float-slow">
                        <div class="w-16 h-16 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black shadow-lg">
                            <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <span class="text-xs uppercase tracking-widest text-amber-400 font-extrabold block">Instant Digital Pay</span>
                            <span class="text-lg font-black text-white">Siddhartha Bank QR</span>
                            <span class="text-xs text-slate-400 block mt-0.5">Fonepay • eSewa • Mobile Banking</span>
                        </div>
                    </div>
                </div>

                <!-- Products Showcase: Infinite Smooth Marquee 3D Carousel -->
                <div class="relative w-full flex-1 flex flex-col justify-center overflow-hidden py-2">
                    <div class="flex items-center justify-between mb-3 px-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span class="text-xs font-black uppercase tracking-widest text-slate-300">Featured Stall Products</span>
                        </div>
                        <span class="text-xs text-slate-500 font-medium">Automatic Showcase • {{ count($allProducts) }} Available Items</span>
                    </div>

                    <!-- Marquee Track Wrapper with Gradient Fades at edges -->
                    <div class="relative w-full overflow-hidden">
                        <!-- Left Fade -->
                        <div class="absolute left-0 inset-y-0 w-24 bg-gradient-to-r from-[#06080d] to-transparent z-10 pointer-events-none"></div>
                        <!-- Right Fade -->
                        <div class="absolute right-0 inset-y-0 w-24 bg-gradient-to-l from-[#06080d] to-transparent z-10 pointer-events-none"></div>

                        <!-- Infinite Animated Marquee Track -->
                        <div class="flex gap-6 animate-marquee-slow w-max py-4">
                            <!-- Duplicate products array to ensure seamless infinite looping -->
                            @foreach([$allProducts, $allProducts] as $productList)
                                @foreach($productList as $product)
                                    @php
                                        $img = $product->image ? asset('storage/' . $product->image) : asset('images/placeholder-product.svg');
                                    @endphp
                                    <div class="w-72 shrink-0 rounded-3xl p-4 glass-card tilt-card border border-white/[0.08] hover:border-amber-400/60 shadow-3d-subtle flex flex-col justify-between select-none">
                                        <div>
                                            <!-- Product Image container with 3D elevation -->
                                            <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-slate-950/80 mb-3.5 border border-white/[0.06] shadow-inner group">
                                                <img 
                                                    src="{{ $img }}" 
                                                    alt="{{ $product->name }}" 
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                                    loading="lazy"
                                                />
                                                @if($product->category)
                                                    <span class="absolute top-2.5 left-2.5 px-3 py-1 rounded-xl bg-slate-950/80 backdrop-blur-md text-[10px] font-black uppercase tracking-wider text-amber-300 border border-amber-400/30">
                                                        {{ $product->category->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            <h3 class="text-base font-black text-white line-clamp-1">
                                                {{ $product->name }}
                                            </h3>
                                            @if($product->description)
                                                <p class="text-xs text-slate-400 line-clamp-2 mt-1 leading-snug">
                                                    {{ $product->description }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="mt-4 pt-3 border-t border-white/[0.08] flex items-center justify-between">
                                            <div>
                                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Price</span>
                                                <span class="text-xl font-black text-amber-400">
                                                    रू {{ number_format($product->price, 2) }}
                                                </span>
                                            </div>
                                            <div class="px-3 py-1 rounded-xl bg-amber-500/10 border border-amber-400/30 text-[11px] font-extrabold text-amber-300">
                                                Available
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
        <!-- SCENARIO B: ACTIVE ORDER IN PROGRESS (LIVE 3D SPLIT VIEW) -->
        <!-- Huge Siddhartha QR Display + Real-Time Itemized Cart Calculation -->
        <!-- ===================================================================== -->
        <template x-if="screenState === 'active'">
            <div class="w-full h-full grid grid-cols-12 gap-8 py-2">
                
                <!-- Left 7 Cols: Live Itemized Cart List with 3D Depth -->
                <div class="col-span-7 flex flex-col rounded-3xl p-7 glass-card shadow-3d-subtle border border-white/[0.1] overflow-hidden">
                    <div class="flex items-center justify-between pb-5 border-b border-white/[0.08]">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-500/20 border border-amber-400/30 text-amber-400 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-white tracking-wide uppercase">Your Selected Items</h3>
                                <p class="text-xs text-slate-400 font-medium">Verify your items with cashier</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/15 border border-amber-400/30 text-amber-300 text-xs font-black">
                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                            <span x-text="itemCount + ' total items'"></span>
                        </div>
                    </div>

                    <!-- Items Scrollable View -->
                    <div class="flex-1 overflow-y-auto py-4 space-y-3 pr-2 scrollbar-none">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white/[0.03] hover:bg-white/[0.06] border border-white/[0.06] transition-all duration-200">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 text-slate-950 flex items-center justify-center font-black text-xl shadow-lg shrink-0" x-text="item.quantity + 'x'"></div>
                                    <div>
                                        <h4 class="font-black text-white text-lg tracking-wide" x-text="item.name"></h4>
                                        <p class="text-xs text-slate-400 mt-0.5" x-text="'रू ' + Number(item.price).toFixed(2) + ' per item'"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-amber-400" x-text="'रू ' + Number(item.total).toFixed(2)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Order Subtotal Footnote -->
                    <div class="pt-4 border-t border-white/[0.08] flex items-center justify-between text-sm text-slate-400">
                        <span>Items Subtotal:</span>
                        <span class="font-bold text-white text-base" x-text="'रू ' + Number(subtotal).toFixed(2)"></span>
                    </div>
                </div>

                <!-- Right 5 Cols: Massive Payment Area & Siddhartha Bank QR Artwork -->
                <div class="col-span-5 flex flex-col gap-6">
                    
                    <!-- Grand Total 3D Highlight Card -->
                    <div class="p-6 rounded-3xl glass-card-amber shadow-neon-amber flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-amber-300 uppercase tracking-widest block">GRAND TOTAL PAYABLE</span>
                            <template x-if="discount > 0">
                                <span class="text-xs font-black px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30" x-text="'Special Discount - रू ' + Number(discount).toFixed(2)"></span>
                            </template>
                        </div>

                        <div class="mt-2 text-6xl font-black text-white tracking-tight leading-none" x-text="'रू ' + Number(grandTotal).toFixed(2)"></div>
                    </div>

                    <!-- Interactive Tender or Big QR Scan Container -->
                    <div class="flex-1 rounded-3xl p-6 glass-card shadow-3d-subtle border border-white/[0.1] flex flex-col justify-between overflow-hidden">
                        
                        <!-- CASH MODE -->
                        <template x-if="paymentMethod === 'cash'">
                            <div class="h-full flex flex-col justify-between py-2">
                                <div>
                                    <div class="flex items-center gap-3 text-slate-300 mb-6 pb-4 border-b border-white/[0.08]">
                                        <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-black text-base uppercase tracking-wider text-white">Cash Tender</span>
                                            <p class="text-xs text-slate-400">Hand cash to cashier counter</p>
                                        </div>
                                    </div>

                                    <div class="space-y-5">
                                        <div class="p-5 rounded-2xl bg-black/40 border border-white/[0.08]">
                                            <span class="text-xs text-slate-400 block mb-1 font-bold uppercase tracking-wider">Cash Received</span>
                                            <span class="text-3xl font-black text-slate-100" x-text="cashReceived > 0 ? 'रू ' + Number(cashReceived).toFixed(2) : 'Awaiting Tender...'"></span>
                                        </div>

                                        <div class="p-5 rounded-2xl border transition-all duration-300" :class="changeDue >= 0 && cashReceived > 0 ? 'bg-emerald-950/40 border-emerald-500/50 text-emerald-400 shadow-lg' : 'bg-black/40 border-white/[0.08] text-slate-400'">
                                            <span class="text-xs block mb-1 font-black uppercase tracking-wider" :class="changeDue >= 0 && cashReceived > 0 ? 'text-emerald-300' : 'text-slate-400'">Your Return Change</span>
                                            <span class="text-5xl font-black tracking-tight" x-text="cashReceived > 0 ? 'रू ' + Number(Math.max(0, changeDue)).toFixed(2) : 'रू 0.00'"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs text-slate-400 text-center font-medium bg-white/[0.03] p-3 rounded-2xl border border-white/[0.05]">
                                    Please collect your receipt and exact return change
                                </div>
                            </div>
                        </template>

                        <!-- QR PAYMENT MODE: GIGANTIC PROMINENT SIDDHARTHA BANK ARTWORK -->
                        <template x-if="paymentMethod === 'qr'">
                            <div class="h-full flex flex-col items-center justify-between text-center">
                                
                                <div class="flex items-center gap-2 text-xs font-black text-amber-400 uppercase tracking-widest bg-amber-500/10 px-4 py-1.5 rounded-full border border-amber-400/20">
                                    <svg class="w-4 h-4 animate-spin text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                    Scan with Siddhartha Bank / Fonepay
                                </div>

                                <!-- Massive 3D Framed QR Display -->
                                <div class="my-auto w-full max-w-[340px] bg-white rounded-3xl p-3 shadow-2xl border-[6px] border-amber-400/90 shadow-neon-amber transform hover:scale-105 transition-all duration-300">
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
                                    <div class="text-2xl font-black text-white" x-text="'Pay रू ' + Number(grandTotal).toFixed(2)"></div>
                                    <p class="text-xs text-amber-300 font-extrabold uppercase tracking-wide">
                                        {{ \App\Models\Setting::get('upi_bank_name', 'Siddhartha Bank') }} • All Mobile Banking Apps
                                    </p>
                                </div>

                            </div>
                        </template>

                    </div>
                </div>

            </div>
        </template>


        <!-- ===================================================================== -->
        <!-- SCENARIO C: ORDER COMPLETED SCREEN -->
        <!-- ===================================================================== -->
        <template x-if="screenState === 'completed'">
            <div class="w-full h-full flex flex-col items-center justify-center text-center max-w-2xl mx-auto py-6">
                
                <div class="relative mb-6">
                    <div class="w-28 h-28 rounded-full bg-emerald-500/20 border-4 border-emerald-400 flex items-center justify-center animate-bounce shadow-neon-amber">
                        <svg class="w-14 h-14 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <h2 class="text-5xl font-black text-white mb-2 tracking-tight">Payment Received!</h2>
                <p class="text-slate-300 text-lg mb-8" x-text="'Thank you! Order #' + (lastOrder.order_number || '') + ' is complete.'"></p>

                <div class="w-full glass-card border border-white/[0.1] rounded-3xl p-8 text-left space-y-4 mb-8 shadow-3d-subtle">
                    <div class="flex justify-between text-lg">
                        <span class="text-slate-400">Total Settled:</span>
                        <span class="text-white font-black text-2xl" x-text="'रू ' + Number(lastOrder.grand_total || 0).toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between text-base">
                        <span class="text-slate-400">Payment Option:</span>
                        <span class="text-amber-400 font-black uppercase tracking-wider" x-text="lastOrder.payment_method"></span>
                    </div>
                    <template x-if="lastOrder.payment_method === 'cash'">
                        <div class="flex justify-between text-base pt-3 border-t border-white/[0.08] text-emerald-400 font-black">
                            <span>Change Returned:</span>
                            <span class="text-xl" x-text="'रू ' + Number(lastOrder.change_returned || 0).toFixed(2)"></span>
                        </div>
                    </template>
                </div>

                <div class="text-xs text-slate-400 font-semibold uppercase tracking-widest bg-white/[0.04] px-5 py-2.5 rounded-full border border-white/[0.08]">
                    Screen resetting for the next customer...
                </div>
            </div>
        </template>

    </main>

    <!-- Bottom Stall Footnote -->
    <footer class="relative z-10 px-10 py-3 border-t border-white/[0.06] flex items-center justify-between text-xs text-slate-500 font-medium">
        <div class="flex items-center gap-4">
            <span>{{ \App\Models\Setting::get('account_holder', 'Ghasa Products Nepal') }}</span>
            <span>•</span>
            <span>Stall Helpline: {{ \App\Models\Setting::get('contact_phone', '+977-9800000000') }}</span>
        </div>
        <div class="flex items-center gap-2 text-amber-500/80 font-bold">
            <span>Thank you for visiting Ghasa Products</span>
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
