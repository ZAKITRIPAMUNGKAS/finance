<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>PortoFinance — Personal & Freelancer Financial OS</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-white text-slate-950 font-sans antialiased selection:bg-slate-950 selection:text-[#C6F24D] flex flex-col justify-between min-h-[100dvh] overflow-x-hidden select-none relative">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  1. PRODUCTION SPLASH SCREEN (CLEAN LIGHT THEME)           -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div id="splash-screen"
         class="fixed inset-0 z-[9999] bg-white flex items-center justify-center select-none"
         aria-label="Loading PortoFinance"
         role="status">
        <div id="splash-content" class="flex flex-col items-center text-center px-6">

            <!-- Large Logo (Borderless with Soft Lime Aura) -->
            <div class="relative mb-6">
                <div class="absolute -inset-4 rounded-full bg-[#C6F24D]/35 blur-2xl"></div>
                <img src="{{ asset('images/logo.svg') }}" class="relative w-24 h-24 sm:w-32 sm:h-32 object-contain drop-shadow-md" alt="PortoFinance Logo">
            </div>

            <!-- Brand Typography -->
            <div class="space-y-1.5">
                <h1 class="text-2xl sm:text-4xl leading-none font-black tracking-tight text-slate-950">
                    Porto<span class="text-teal-700">Finance</span>
                </h1>
                <p class="text-[10px] sm:text-xs font-mono font-bold tracking-[0.2em] uppercase text-slate-400">
                    Personal & Freelancer Financial OS
                </p>
            </div>

            <!-- Animated Dot Wave Loading Indicator -->
            <div class="mt-8 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-950 anim-dot-1"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-teal-700 anim-dot-2"></span>
                <span class="w-2.5 h-2.5 rounded-full bg-[#C6F24D] anim-dot-3"></span>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  2. FULL SCREEN ONBOARDING (ALPINE.JS COMPONENT)           -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div id="onboarding-root" 
         x-data="onboardingApp()" 
         class="w-full flex-1 flex flex-col justify-between min-h-[100dvh] max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-6">
        
        <!-- ── TOP HEADER ──────────────────────────────────────── -->
        <header class="w-full pb-2 flex items-center justify-between z-20 shrink-0">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-2.5 group">
                <img src="{{ asset('images/logo.svg') }}" class="w-8 h-8 sm:w-9 sm:h-9 object-contain group-hover:scale-105 transition-transform" alt="Logo">
                <div class="leading-tight">
                    <span class="font-black text-sm sm:text-base text-slate-950 tracking-tight block">Porto<span class="text-teal-700">Finance</span></span>
                    <span class="text-[8px] sm:text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block -mt-0.5">Financial OS</span>
                </div>
            </a>

            <!-- Skip Button -->
            <button @click="skip()" 
                    id="btn-skip"
                    type="button" 
                    class="text-xs sm:text-sm font-extrabold text-slate-400 hover:text-slate-950 px-3.5 py-1.5 rounded-full hover:bg-slate-100 transition-colors duration-200 cursor-pointer">
                Skip
            </button>
        </header>

        <!-- ── SLIDER VIEWPORT ─────────────────────────────────── -->
        <main class="w-full flex-1 overflow-hidden relative flex items-center py-2 sm:py-4 my-auto"
              @touchstart="handleTouchStart($event)"
              @touchend="handleTouchEnd($event)">
            
            <!-- Slide Track with 100% Step per Slide -->
            <div id="slide-track"
                 class="w-full flex flex-row items-center transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform"
                 style="transform: translate3d(0%, 0, 0);"
                 :style="'transform: translate3d(-' + ((currentSlide - 1) * 100) + '%, 0, 0)'">

                <!-- ════════ SLIDE 1: OFFICIAL BRAND & MODERN FINANCIAL OS ════════ -->
                <div class="w-full min-w-full shrink-0 px-2 sm:px-6 flex items-center justify-center">
                    <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-10 items-center">
                        
                        <!-- Left Column: Copy -->
                        <div class="md:col-span-7 order-2 md:order-1 space-y-3 sm:space-y-4 text-left">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-950 text-[#C6F24D] text-[10px] sm:text-xs font-mono font-extrabold uppercase tracking-wider shadow-xs">
                                <span>⚡ #1 Modern Financial OS</span>
                            </div>

                            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-950 tracking-tight leading-[1.12]">
                                Finance OS<br>
                                Made for 
                                <span class="inline-flex items-center justify-center px-2.5 sm:px-3 py-0.5 rounded-full bg-slate-950 text-[#C6F24D] text-xs sm:text-sm align-middle mx-1 shadow-xs">
                                    &rarr;
                                </span><br>
                                Smart Creators
                            </h1>

                            <p class="text-xs sm:text-sm md:text-base font-semibold text-slate-600 leading-relaxed max-w-lg">
                                Catat transaksi kilat via <strong>Suara (Voice) & Scan Struk</strong>, kendalikan <strong>Uang Bebas (Available Money)</strong>, amankan budget harian, dan capai <strong>Wishlist</strong> impianmu.
                            </p>

                            <!-- Feature Badges -->
                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5 shadow-2xs">
                                    <x-icon name="mic" class="w-3.5 h-3.5 text-rose-500" />
                                    <span>Voice & OCR Scan</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5 shadow-2xs">
                                    <x-icon name="shield-check" class="w-3.5 h-3.5 text-teal-600" />
                                    <span>Anti-Boncos Buffer</span>
                                </span>
                            </div>
                        </div>

                        <!-- Right Column: Official 3D Logo Artwork -->
                        <div class="md:col-span-5 order-1 md:order-2 flex justify-center items-center">
                            <div class="relative w-full max-w-[240px] sm:max-w-[300px] aspect-square flex items-center justify-center shrink-0">
                                
                                <!-- Soft Aura Glow -->
                                <div class="w-48 h-48 sm:w-60 sm:h-60 rounded-full bg-teal-500/10 blur-2xl absolute z-0"></div>
                                <div class="w-36 h-36 sm:w-44 sm:h-44 rounded-full bg-[#C6F24D]/25 blur-xl absolute z-0"></div>

                                <!-- Floating Badge 1: Voice -->
                                <div class="absolute top-2 right-2 px-2.5 py-1 rounded-xl bg-white border border-slate-200 shadow-md flex items-center gap-1 text-[10px] font-bold font-mono text-slate-900 z-20 anim-float-1">
                                    <x-icon name="mic" class="w-3 h-3 text-rose-500" />
                                    <span>VOICE</span>
                                </div>

                                <!-- Floating Badge 2: Struk OCR -->
                                <div class="absolute bottom-4 left-2 px-2.5 py-1 rounded-xl bg-white border border-slate-200 shadow-md flex items-center gap-1 text-[10px] font-bold font-mono text-slate-900 z-20 anim-float-2">
                                    <x-icon name="receipt" class="w-3 h-3 text-teal-600" />
                                    <span>STRUK</span>
                                </div>

                                <!-- Center Official 3D Graphic -->
                                <div class="relative z-10 p-4 rounded-3xl bg-white/60 backdrop-blur-xs border border-slate-100 shadow-xl">
                                    <img src="{{ asset('images/logo.svg') }}" class="w-36 h-36 sm:w-48 sm:h-48 object-contain drop-shadow-xl" alt="PortoFinance Official Logo">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ════════ SLIDE 2: AVAILABLE MONEY FORMULA ═══════ -->
                <div class="w-full min-w-full shrink-0 px-2 sm:px-6 flex items-center justify-center">
                    <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-10 items-center">
                        
                        <!-- Left Column: Copy -->
                        <div class="md:col-span-7 order-2 md:order-1 space-y-3 sm:space-y-4 text-left">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-[10px] sm:text-xs font-mono font-extrabold uppercase tracking-wider shadow-xs">
                                <span>🔒 Smart Liquidity Formula</span>
                            </div>

                            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-950 tracking-tight leading-[1.12]">
                                Kendalikan<br>
                                Uang Bebas 
                                <span class="inline-flex items-center justify-center px-2.5 sm:px-3 py-0.5 rounded-full bg-slate-950 text-[#C6F24D] text-xs sm:text-sm align-middle mx-1 shadow-xs">
                                    &rarr;
                                </span><br>
                                Anti-Boncos
                            </h1>

                            <p class="text-xs sm:text-sm md:text-base font-semibold text-slate-600 leading-relaxed max-w-lg">
                                Bukan sekadar total saldo! Formula pintar yang otomatis memisahkan saldo belanja harian dari komitmen tabungan dan tagihan rutin Anda.
                            </p>

                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-900 text-[11px] font-bold border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                                    <x-icon name="sparkles" class="w-3.5 h-3.5 text-emerald-600" />
                                    <span>Real-Time Available Money</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5 shadow-2xs">
                                    <x-icon name="shield-check" class="w-3.5 h-3.5 text-slate-700" />
                                    <span>Safe To Spend Indicator</span>
                                </span>
                            </div>
                        </div>

                        <!-- Right Column: Smart Liquidity Card -->
                        <div class="md:col-span-5 order-1 md:order-2 flex justify-center items-center">
                            <div class="relative w-full max-w-[240px] sm:max-w-[300px] aspect-square flex items-center justify-center shrink-0">
                                
                                <div class="w-48 h-48 sm:w-60 sm:h-60 rounded-full bg-emerald-500/10 blur-2xl absolute z-0"></div>

                                <!-- Floating Safe Pill -->
                                <div class="absolute top-2 left-2 px-3 py-1 rounded-xl bg-white border border-slate-200 shadow-md z-20 flex items-center gap-1.5 text-[10px] font-mono font-black text-emerald-950 anim-float-1">
                                    <x-icon name="shield-check" class="w-3.5 h-3.5 text-emerald-600" strokeWidth="2.5" />
                                    <span>Safe To Spend</span>
                                </div>

                                <!-- Card Graphic -->
                                <div class="relative z-10 w-48 sm:w-56 p-5 rounded-3xl bg-white border-2 border-slate-900 shadow-xl space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Uang Bebas</span>
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    </div>
                                    <div>
                                        <div class="text-xl sm:text-2xl font-black font-mono text-slate-950">Rp 5.250.000</div>
                                        <div class="text-[10px] font-bold text-emerald-700 mt-0.5">100% Aman Dibelanjakan</div>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500 font-medium">
                                        <span>Tabungan Terkunci</span>
                                        <span class="font-mono font-bold text-slate-900">Rp 3.150.000</span>
                                    </div>
                                </div>

                                <!-- Floating Badge Bottom -->
                                <div class="absolute bottom-2 right-2 px-3 py-1 rounded-xl bg-[#C6F24D] border border-slate-950 shadow-md z-20 flex items-center gap-1 text-[11px] font-mono font-black text-slate-950 anim-float-2">
                                    <x-icon name="sparkles" class="w-3.5 h-3.5 text-slate-950" />
                                    <span>Terkendali</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ════════ SLIDE 3: WISHLIST & GOALS ═══════════════════ -->
                <div class="w-full min-w-full shrink-0 px-2 sm:px-6 flex items-center justify-center">
                    <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-10 items-center">
                        
                        <!-- Left Column: Copy -->
                        <div class="md:col-span-7 order-2 md:order-1 space-y-3 sm:space-y-4 text-left">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-100 text-indigo-900 text-[10px] sm:text-xs font-mono font-extrabold uppercase tracking-wider shadow-xs">
                                <span>🎯 Goal & Wishlist Tracking</span>
                            </div>

                            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-950 tracking-tight leading-[1.12]">
                                Wujudkan<br>
                                Wishlist 
                                <span class="inline-flex items-center justify-center px-2.5 sm:px-3 py-0.5 rounded-full bg-slate-950 text-[#C6F24D] text-xs sm:text-sm align-middle mx-1 shadow-xs">
                                    &rarr;
                                </span><br>
                                Impianmu
                            </h1>

                            <p class="text-xs sm:text-sm md:text-base font-semibold text-slate-600 leading-relaxed max-w-lg">
                                Pantau progres tabungan barang impian Anda dan gunakan simulasi kalkulator <em>"Can I Afford This?"</em> sebelum memutuskan membeli barang baru.
                            </p>

                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <span class="px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-900 text-[11px] font-bold border border-indigo-200 flex items-center gap-1.5 shadow-2xs">
                                    <x-icon name="target" class="w-3.5 h-3.5 text-indigo-600" />
                                    <span>Goal Sinking Funds</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5 shadow-2xs">
                                    <x-icon name="calculator" class="w-3.5 h-3.5 text-slate-700" />
                                    <span>Affordability Simulator</span>
                                </span>
                            </div>
                        </div>

                        <!-- Right Column: Wishlist Card -->
                        <div class="md:col-span-5 order-1 md:order-2 flex justify-center items-center">
                            <div class="relative w-full max-w-[240px] sm:max-w-[300px] aspect-square flex items-center justify-center shrink-0">
                                
                                <div class="w-48 h-48 sm:w-60 sm:h-60 rounded-full bg-indigo-500/10 blur-2xl absolute z-0"></div>

                                <!-- Wishlist Card Graphic -->
                                <div class="relative z-10 w-48 sm:w-56 p-5 rounded-3xl bg-white border-2 border-slate-900 shadow-xl space-y-3.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Wishlist Impian</span>
                                        <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-bold">85%</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-extrabold text-slate-950">MacBook Pro M3</div>
                                        <div class="text-xs font-mono font-bold text-slate-500 mt-0.5">Rp 21.250.000 / 25jt</div>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden p-0.5">
                                        <div class="bg-[#C6F24D] h-full rounded-full w-[85%]"></div>
                                    </div>
                                    <div class="text-[10px] text-teal-700 font-bold flex items-center gap-1">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                        <span>Siap terbeli 1 bulan lagi!</span>
                                    </div>
                                </div>

                                <!-- Floating Badge Bottom -->
                                <div class="absolute bottom-2 left-2 px-3 py-1 rounded-xl bg-slate-950 text-[#C6F24D] shadow-md z-20 flex items-center gap-1 text-[11px] font-mono font-black anim-float-1">
                                    <x-icon name="sparkles" class="w-3.5 h-3.5" />
                                    <span>Target Tercapai</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </main>

        <!-- ── BOTTOM NAVIGATION ───────────────────────────────── -->
        <footer class="w-full pt-3 pb-2 border-t border-slate-100 flex items-center justify-between z-20 shrink-0 bg-white">
            
            <!-- Left: Back Button -->
            <div class="w-24 flex items-center">
                <button x-show="currentSlide > 1" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        @click="prevSlide()" 
                        id="btn-prev"
                        type="button" 
                        class="text-xs sm:text-sm font-bold text-slate-500 hover:text-slate-950 transition-colors duration-150 cursor-pointer flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-slate-100"
                        style="display: none;">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    <span>Kembali</span>
                </button>
            </div>

            <!-- Center: Step Indicator Dots (3 Clean Dots) -->
            <div class="flex items-center gap-2">
                <button @click="goToSlide(1)" id="dot-1" type="button" 
                        class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                        :class="currentSlide === 1 ? 'w-7 bg-slate-950' : 'w-2 bg-slate-200 hover:bg-slate-300'"></button>
                <button @click="goToSlide(2)" id="dot-2" type="button" 
                        class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                        :class="currentSlide === 2 ? 'w-7 bg-slate-950' : 'w-2 bg-slate-200 hover:bg-slate-300'"></button>
                <button @click="goToSlide(3)" id="dot-3" type="button" 
                        class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                        :class="currentSlide === 3 ? 'w-7 bg-slate-950' : 'w-2 bg-slate-200 hover:bg-slate-300'"></button>
            </div>

            <!-- Right: Next / Start Button -->
            <div class="w-24 flex justify-end">
                <button @click="nextSlide()" 
                        id="btn-next"
                        type="button" 
                        class="h-10 sm:h-11 px-4 sm:px-5 rounded-2xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] flex items-center justify-center font-black text-xs sm:text-sm transition-all duration-200 active:scale-95 shadow-md cursor-pointer z-30 gap-1.5">
                    <template x-if="currentSlide < totalSlides">
                        <span class="flex items-center gap-1">
                            <span>Lanjut</span>
                            <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                        </span>
                    </template>
                    <template x-if="currentSlide === totalSlides">
                        <span class="flex items-center gap-1">
                            <span>Mulai</span>
                            <x-icon name="sparkles" class="w-3.5 h-3.5" />
                        </span>
                    </template>
                </button>
            </div>

        </footer>

    </div>

    <!-- Onboarding Controller Engine & Pure Vanilla Splash Handler -->
    <script>
        // ── 1. Pure Vanilla JS Splash Handler (1500ms Relaxed Timing) ─
        document.addEventListener('DOMContentLoaded', () => {
            const splash = document.getElementById('splash-screen');
            if (!splash) return;

            setTimeout(() => {
                splash.classList.add('is-hidden');
                setTimeout(() => {
                    splash.remove();
                }, 600);
            }, 1200);
        });

        // ── 2. Alpine Onboarding Engine ──────────────────────────────
        function onboardingApp() {
            return {
                currentSlide: 1,
                totalSlides: 3,
                touchStartX: 0,
                touchEndX: 0,
                isNavigating: false,
                nextSlide() {
                    if (this.isNavigating) return;
                    if (this.currentSlide < this.totalSlides) {
                        this.isNavigating = true;
                        this.currentSlide++;
                        this.syncTrack();
                        setTimeout(() => { this.isNavigating = false; }, 450);
                    } else {
                        this.exitToLogin();
                    }
                },
                prevSlide() {
                    if (this.isNavigating) return;
                    if (this.currentSlide > 1) {
                        this.isNavigating = true;
                        this.currentSlide--;
                        this.syncTrack();
                        setTimeout(() => { this.isNavigating = false; }, 450);
                    }
                },
                goToSlide(index) {
                    if (this.isNavigating || index === this.currentSlide) return;
                    this.isNavigating = true;
                    this.currentSlide = index;
                    this.syncTrack();
                    setTimeout(() => { this.isNavigating = false; }, 450);
                },
                syncTrack() {
                    const track = document.getElementById('slide-track');
                    if (track) {
                        track.style.transform = `translate3d(-${(this.currentSlide - 1) * 100}%, 0, 0)`;
                    }
                },
                handleTouchStart(e) {
                    this.touchStartX = e.changedTouches[0].screenX;
                },
                handleTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].screenX;
                    this.handleSwipe();
                },
                handleSwipe() {
                    const threshold = 40;
                    const diff = this.touchStartX - this.touchEndX;
                    if (Math.abs(diff) > threshold) {
                        if (diff > 0) {
                            this.nextSlide();
                        } else {
                            this.prevSlide();
                        }
                    }
                },
                skip() {
                    this.exitToLogin();
                },
                exitToLogin() {
                    window.location.href = "{{ route('login') }}";
                }
            };
        }
    </script>
</body>
</html>
