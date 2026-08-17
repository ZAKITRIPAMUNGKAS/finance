<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>PortoFinance — Personal Finance OS</title>
    
    <!-- PWA Web App Manifest & Mobile Theme -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FFFFFF">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="PortoFinance">

    <!-- Favicon & PWA Icons (Strictly logo.png) -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=5">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo.png') }}?v=5">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}?v=5">
    
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

            <!-- Perfectly Proportioned Logo (Strict Inline Constraints) -->
            <div class="relative mb-4 flex items-center justify-center mx-auto" style="width: 80px; height: 80px;">
                <div class="absolute inset-0 rounded-full bg-[#C6F24D]/35 blur-xl"></div>
                <img src="{{ asset('images/logo.svg') }}" style="width: 64px; height: 64px; max-width: 64px; max-height: 64px; object-fit: contain; display: block;" class="relative" alt="PortoFinance Logo">
            </div>

            <!-- Brand Typography -->
            <div class="space-y-1">
                <h1 class="text-xl sm:text-2xl leading-none font-black tracking-tight text-slate-950">
                    Porto<span class="text-teal-700">Finance</span>
                </h1>
                <p class="text-[10px] sm:text-xs font-mono font-bold tracking-[0.18em] uppercase text-slate-400">
                    Freelancer Financial OS
                </p>
            </div>

            <!-- Animated Dot Wave Loading Indicator -->
            <div class="mt-6 flex items-center justify-center gap-2">
                <span class="w-2 h-2 rounded-full bg-slate-950 anim-dot-1"></span>
                <span class="w-2 h-2 rounded-full bg-teal-700 anim-dot-2"></span>
                <span class="w-2 h-2 rounded-full bg-[#C6F24D] anim-dot-3"></span>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  2. FULL SCREEN ONBOARDING (ALPINE.JS COMPONENT)           -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div id="onboarding-root" 
         x-data="onboardingApp()" 
         class="w-full flex-1 flex flex-col justify-between min-h-[100dvh] max-w-6xl mx-auto px-4 sm:px-6 lg:px-12">
        
        <!-- ── TOP HEADER ──────────────────────────────────────── -->
        <header class="w-full pt-4 sm:pt-6 md:pt-8 pb-2 flex items-center justify-between z-20 shrink-0">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-2.5 group">
                <img src="{{ asset('images/logo.svg') }}" class="w-8 h-8 sm:w-9 sm:h-9 object-contain group-hover:scale-105 transition-transform" alt="Logo">
                <div class="leading-tight">
                    <span class="font-black text-sm sm:text-base text-slate-950 tracking-tight block">Porto<span class="text-teal-700">Finance</span></span>
                    <span class="text-[8px] sm:text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block -mt-0.5">Freelancer Financial OS</span>
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

        <!-- ── SLIDER VIEWPORT (RESPONSIVE 2-COL DESKTOP / 1-COL MOBILE) ────── -->
        <main class="w-full flex-1 overflow-hidden relative flex items-center py-2 sm:py-6 my-auto"
              @touchstart="handleTouchStart($event)"
              @touchend="handleTouchEnd($event)">
            
            <!-- Slide Track with 100% Step per Slide -->
            <div id="slide-track"
                 class="w-full flex flex-row items-center transition-transform duration-500 ease-[cubic-bezier(0.22,1,0.36,1)] will-change-transform"
                 style="transform: translate3d(0%, 0, 0);"
                 :style="'transform: translate3d(-' + ((currentSlide - 1) * 100) + '%, 0, 0)'">

                <!-- ════════ SLIDE 1: FREELANCE FINANCIAL OS ════════ -->
                <div class="w-full min-w-full shrink-0 px-2 sm:px-6 md:px-10 flex items-center justify-center">
                    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-10 lg:gap-16 items-center">
                        
                        <!-- Left Column (Desktop): Text & Copy -->
                        <div class="md:col-span-7 order-2 md:order-1 space-y-3 sm:space-y-4 text-left">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-900 text-[#D4F66C] text-[10px] sm:text-xs font-mono font-extrabold uppercase tracking-wider shadow-xs">
                                <span>⚡ The #1 Freelancer Financial Hub</span>
                            </div>

                            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-950 tracking-tight leading-[1.12]">
                                Finance OS<br>
                                Made for 
                                <span class="inline-flex items-center justify-center px-2.5 sm:px-3.5 py-0.5 rounded-full bg-slate-950 text-[#D4F66C] text-xs sm:text-base align-middle mx-1 shadow-xs">
                                    &rarr;
                                </span><br>
                                Freelancers
                            </h1>

                            <p class="text-xs sm:text-sm md:text-base font-semibold text-slate-600 leading-relaxed max-w-lg">
                                Catat transaksi kilat via <strong>Suara (Voice) & Scan Struk</strong>, kendalikan <strong>Uang Bebas (Available Money)</strong>, amankan budget, dan capai <strong>Wishlist</strong> impianmu.
                            </p>

                            <!-- Feature Badges on Desktop -->
                            <div class="hidden sm:flex flex-wrap items-center gap-2 pt-2">
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5">
                                    <x-icon name="mic" class="w-3.5 h-3.5 text-rose-500" />
                                    <span>Voice & OCR Scan</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5">
                                    <x-icon name="shield-check" class="w-3.5 h-3.5 text-emerald-600" />
                                    <span>Anti-Boncos Buffer</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5">
                                    <x-icon name="shopping-bag" class="w-3.5 h-3.5 text-teal-600" />
                                    <span>Goal Sinking Fund</span>
                                </span>
                            </div>
                        </div>

                        <!-- Right Column (Desktop): Illustration -->
                        <div class="md:col-span-5 order-1 md:order-2 flex justify-center items-center">
                            <div class="relative w-full max-w-[220px] sm:max-w-[280px] md:max-w-[320px] lg:max-w-[360px] aspect-square flex items-center justify-center shrink-0">
                                <div class="w-48 h-48 sm:w-64 sm:h-64 md:w-72 md:h-72 rounded-full bg-slate-50 border border-slate-100 absolute z-0 anim-glow"></div>

                                <!-- Floating 1: Coin Top Left -->
                                <div class="absolute top-2 left-0 sm:left-2 px-2.5 py-1 rounded-xl sm:rounded-2xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] flex items-center gap-1 font-black font-mono text-[10px] sm:text-xs rotate-[-12deg] z-20 anim-float-1">
                                    <x-icon name="dollar-sign" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
                                    <span>Rp</span>
                                </div>

                                <!-- Floating 2: Voice Mic Badge Top Center -->
                                <div class="absolute -top-1 right-1/4 px-2.5 py-1 rounded-xl sm:rounded-2xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[6deg] z-20 flex items-center gap-1 anim-float-2">
                                    <x-icon name="mic" class="w-3.5 h-3.5 text-rose-500" strokeWidth="2.5" />
                                    <span class="text-[8px] sm:text-[9px] font-black font-mono text-slate-900 tracking-wider">VOICE</span>
                                </div>

                                <!-- Floating 3: Credit Card Top Right -->
                                <div class="absolute top-3 right-0 sm:right-1 px-2.5 py-1 rounded-lg sm:rounded-xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[12deg] z-20 flex items-center gap-1 anim-float-3">
                                    <x-icon name="credit-card" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
                                    <span class="text-[8px] sm:text-[9px] font-black font-mono text-slate-900">CARD</span>
                                </div>

                                <!-- Floating 4: Invoice Receipt Bottom Left -->
                                <div class="absolute bottom-3 left-0 sm:left-1 px-2.5 py-1 rounded-lg sm:rounded-xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[-12deg] z-20 flex items-center gap-1 anim-float-2">
                                    <x-icon name="receipt" class="w-3.5 h-3.5 text-emerald-600" strokeWidth="2.5" />
                                    <span class="text-[8px] sm:text-[9px] font-black font-mono text-slate-900">STRUK</span>
                                </div>

                                <!-- Floating 5: Checkmark Coin Bottom Right -->
                                <div class="absolute bottom-3 right-1 sm:right-3 w-7 h-7 sm:w-9 sm:h-9 rounded-full bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] flex items-center justify-center rotate-[10deg] z-20 anim-float-1">
                                    <x-icon name="check" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-slate-950" strokeWidth="3" />
                                </div>

                                <!-- Main Character & Wallet Vector -->
                                <div class="relative z-10 flex flex-col items-center justify-center">
                                    <svg viewBox="0 0 260 220" style="width: 220px; height: 186px; display: block;" xmlns="http://www.w3.org/2000/svg">
                                        <!-- Character Head & Body -->
                                        <path d="M90 100 Q75 65 100 40 Q115 25 130 35 Q145 25 155 45 Q165 70 145 100 Z" fill="#FFFFFF" stroke="#090D16" stroke-width="3.5" stroke-linejoin="round"/>
                                        <circle cx="125" cy="45" r="18" fill="#FFFFFF" stroke="#090D16" stroke-width="3.5"/>
                                        <path d="M128 44 Q138 46 130 52" stroke="#090D16" stroke-width="3" stroke-linecap="round"/>
                                        <circle cx="124" cy="40" r="2.5" fill="#090D16"/>
                                        <path d="M110 36 Q125 22 145 32 L152 38" stroke="#090D16" stroke-width="3.5" stroke-linecap="round"/>
                                        <!-- Waving Arm -->
                                        <path d="M100 70 Q70 60 62 35 Q60 28 65 25 Q70 23 75 32 L88 65" fill="#FFFFFF" stroke="#090D16" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="63" cy="26" r="3" fill="#090D16"/>
                                        <!-- Right Arm Resting on Wallet -->
                                        <path d="M140 75 Q165 80 180 100 Q185 110 175 115 L150 115" fill="#FFFFFF" stroke="#090D16" stroke-width="3.5" stroke-linecap="round"/>
                                        <!-- Wallet Body -->
                                        <rect x="50" y="95" width="160" height="95" rx="20" fill="#FFFFFF" stroke="#090D16" stroke-width="4"/>
                                        <path d="M50 120 C90 130 170 130 210 120" stroke="#090D16" stroke-width="3" stroke-dasharray="6 4"/>
                                        <!-- Contactless Payment Waves -->
                                        <path d="M130 145 A10 10 0 0 1 130 165" stroke="#090D16" stroke-width="3.5" stroke-linecap="round" fill="none"/>
                                        <path d="M138 140 A18 18 0 0 1 138 170" stroke="#090D16" stroke-width="3.5" stroke-linecap="round" fill="none"/>
                                        <path d="M146 135 A26 26 0 0 1 146 175" stroke="#090D16" stroke-width="3.5" stroke-linecap="round" fill="none"/>
                                        <!-- Wallet Clasp in Lime -->
                                        <rect x="50" y="130" width="30" height="24" rx="8" fill="#C6F24D" stroke="#090D16" stroke-width="3.5"/>
                                        <circle cx="65" cy="142" r="3.5" fill="#090D16"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ════════ SLIDE 2: AVAILABLE MONEY FORMULA ═══════ -->
                <div class="w-full min-w-full shrink-0 px-2 sm:px-6 md:px-10 flex items-center justify-center">
                    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-10 lg:gap-16 items-center">
                        
                        <!-- Left Column (Desktop): Text & Copy -->
                        <div class="md:col-span-7 order-2 md:order-1 space-y-3 sm:space-y-4 text-left">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-[10px] sm:text-xs font-mono font-extrabold uppercase tracking-wider shadow-xs">
                                <x-icon name="lock" class="w-3.5 h-3.5 text-emerald-800" strokeWidth="2.5" />
                                <span>Smart Liquidity Formula</span>
                            </div>

                            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-950 tracking-tight leading-[1.12]">
                                Kendalikan<br>
                                Uang Bebas 
                                <span class="inline-flex items-center justify-center px-2.5 sm:px-3.5 py-0.5 rounded-full bg-slate-950 text-[#D4F66C] text-xs sm:text-base align-middle mx-1 shadow-xs">
                                    &rarr;
                                </span><br>
                                Anti-Boncos
                            </h1>

                            <p class="text-xs sm:text-sm md:text-base font-semibold text-slate-600 leading-relaxed max-w-lg">
                                Bukan sekadar total saldo! Formula pintar yang otomatis memisahkan saldo belanja harian dari komitmen tabungan impian Anda.
                            </p>

                            <!-- Feature Badges on Desktop -->
                            <div class="hidden sm:flex flex-wrap items-center gap-2 pt-2">
                                <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-900 text-[11px] font-bold border border-emerald-200 flex items-center gap-1.5">
                                    <x-icon name="sparkles" class="w-3.5 h-3.5 text-emerald-600" />
                                    <span>Real-Time Calculation</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5">
                                    <x-icon name="lock" class="w-3.5 h-3.5 text-slate-700" />
                                    <span>Locked Sinking Funds</span>
                                </span>
                            </div>
                        </div>

                        <!-- Right Column (Desktop): Illustration -->
                        <div class="md:col-span-5 order-1 md:order-2 flex justify-center items-center">
                            <div class="relative w-full max-w-[220px] sm:max-w-[280px] md:max-w-[320px] lg:max-w-[360px] aspect-square flex items-center justify-center shrink-0">
                                <div class="w-48 h-48 sm:w-64 sm:h-64 md:w-72 md:h-72 rounded-full bg-emerald-50 border border-emerald-100 absolute z-0 anim-glow"></div>

                                <!-- Floating Safe Pill -->
                                <div class="absolute top-2 left-0 sm:left-2 px-3 py-1 sm:py-1.5 rounded-xl sm:rounded-2xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] z-20 flex items-center gap-1.5 anim-float-1">
                                    <x-icon name="shield-check" class="w-3.5 h-3.5 text-emerald-600" strokeWidth="2.5" />
                                    <span class="text-[9px] sm:text-[11px] font-black font-mono text-emerald-950">Safe To Spend</span>
                                </div>

                                <!-- Smart Wallet Illustration -->
                                <div class="relative z-10 flex flex-col items-center justify-center">
                                    <svg viewBox="0 0 240 200" style="width: 200px; height: 165px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="40" y="55" width="160" height="105" rx="24" fill="#FFFFFF" stroke="#090D16" stroke-width="4"/>
                                        <path d="M40 85 C90 98 150 98 200 85" stroke="#090D16" stroke-width="3.5" stroke-dasharray="6 4"/>
                                        <rect x="40" y="95" width="35" height="28" rx="10" fill="#C6F24D" stroke="#090D16" stroke-width="3.5"/>
                                        <circle cx="58" cy="109" r="4" fill="#090D16"/>
                                        <path d="M125 115 A10 10 0 0 1 125 135" stroke="#090D16" stroke-width="3.5" stroke-linecap="round"/>
                                        <path d="M135 110 A18 18 0 0 1 135 140" stroke="#090D16" stroke-width="3.5" stroke-linecap="round"/>
                                        <path d="M145 105 A26 26 0 0 1 145 145" stroke="#090D16" stroke-width="3.5" stroke-linecap="round"/>
                                    </svg>
                                </div>

                                <!-- Floating Badge Bottom -->
                                <div class="absolute bottom-3 right-0 sm:right-2 px-3 py-1 sm:py-1.5 rounded-xl bg-[#C6F24D] border-2 border-slate-950 shadow-[2px_2px_0px_#000] z-20 flex items-center gap-1.5 text-xs sm:text-sm font-mono font-black anim-float-2">
                                    <x-icon name="sparkles" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
                                    <span>Rp 8.400.000</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ════════ SLIDE 3: FREELANCE PROJECTS & WISHLIST ══ -->
                <div class="w-full min-w-full shrink-0 px-2 sm:px-6 md:px-10 flex items-center justify-center">
                    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-10 lg:gap-16 items-center">
                        
                        <!-- Left Column (Desktop): Text & Copy -->
                        <div class="md:col-span-7 order-2 md:order-1 space-y-3 sm:space-y-4 text-left">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-100 text-indigo-900 text-[10px] sm:text-xs font-mono font-extrabold uppercase tracking-wider shadow-xs">
                                <x-icon name="target" class="w-3.5 h-3.5 text-indigo-800" strokeWidth="2.5" />
                                <span>Goal & Project Tracking</span>
                            </div>

                            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-950 tracking-tight leading-[1.12]">
                                Wujudkan<br>
                                Wishlist 
                                <span class="inline-flex items-center justify-center px-2.5 sm:px-3.5 py-0.5 rounded-full bg-slate-950 text-[#D4F66C] text-xs sm:text-base align-middle mx-1 shadow-xs">
                                    &rarr;
                                </span><br>
                                Impianmu
                            </h1>

                            <p class="text-xs sm:text-sm md:text-base font-semibold text-slate-600 leading-relaxed max-w-lg">
                                Pantau margin laba tiap project freelance secara transparan dan gunakan simulasi kalkulator <em>"Can I Afford This?"</em> sebelum belanja alat kerja baru.
                            </p>

                            <!-- Feature Badges on Desktop -->
                            <div class="hidden sm:flex flex-wrap items-center gap-2 pt-2">
                                <span class="px-2.5 py-1 rounded-xl bg-indigo-50 text-indigo-900 text-[11px] font-bold border border-indigo-200 flex items-center gap-1.5">
                                    <x-icon name="briefcase" class="w-3.5 h-3.5 text-indigo-600" />
                                    <span>Project Net Margin</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5">
                                    <x-icon name="calculator" class="w-3.5 h-3.5 text-slate-700" />
                                    <span>"Can I Afford This?" Simulator</span>
                                </span>
                            </div>
                        </div>

                        <!-- Right Column (Desktop): Illustration -->
                        <div class="md:col-span-5 order-1 md:order-2 flex justify-center items-center">
                            <div class="relative w-full max-w-[220px] sm:max-w-[280px] md:max-w-[320px] lg:max-w-[360px] aspect-square flex items-center justify-center shrink-0">
                                <div class="w-48 h-48 sm:w-64 sm:h-64 md:w-72 md:h-72 rounded-full bg-indigo-50 border border-indigo-100 absolute z-0 anim-glow"></div>

                                <!-- Floating Target Wishlist Tag -->
                                <div class="absolute top-2 right-0 sm:right-2 px-3 py-1 sm:py-1.5 rounded-xl sm:rounded-2xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] z-20 flex items-center gap-1.5 anim-float-2">
                                    <x-icon name="target" class="w-3.5 h-3.5 text-rose-500" strokeWidth="2.5" />
                                    <span class="text-[9px] sm:text-[11px] font-black font-mono text-indigo-950">Sony A7 IV • 85%</span>
                                </div>

                                <!-- Project Folder Vector -->
                                <div class="relative z-10 flex flex-col items-center justify-center">
                                    <svg viewBox="0 0 240 200" style="width: 200px; height: 165px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="50" y="60" width="140" height="95" rx="16" fill="#FFFFFF" stroke="#090D16" stroke-width="3.5"/>
                                        <rect x="65" y="75" width="50" height="8" rx="4" fill="#090D16"/>
                                        <rect x="65" y="90" width="110" height="5" rx="2.5" fill="#E2E8F0"/>
                                        <rect x="65" y="100" width="85" height="5" rx="2.5" fill="#E2E8F0"/>
                                        <rect x="65" y="115" width="110" height="12" rx="6" fill="#F1F5F9" stroke="#090D16" stroke-width="2"/>
                                        <rect x="67" y="117" width="80" height="8" rx="4" fill="#C6F24D"/>
                                    </svg>
                                </div>

                                <!-- Floating Margin Badge Bottom -->
                                <div class="absolute bottom-3 left-0 sm:left-2 px-3 py-1 sm:py-1.5 rounded-xl bg-[#C6F24D] border-2 border-slate-950 shadow-[2px_2px_0px_#000] z-20 flex items-center gap-1.5 text-xs sm:text-sm font-mono font-black anim-float-1">
                                    <x-icon name="briefcase" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
                                    <span>Margin: 79%</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ════════ SLIDE 4: SOLID FINANCIAL THEORY FOUNDATION ════ -->
                <div class="w-full min-w-full shrink-0 px-2 sm:px-6 md:px-10 flex items-center justify-center">
                    <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-10 lg:gap-16 items-center">
                        
                        <!-- Left Column (Desktop): Text & Copy -->
                        <div class="md:col-span-7 order-2 md:order-1 space-y-3 sm:space-y-4 text-left">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#EBFAD2] text-slate-900 border border-[#D4F66C] text-[10px] sm:text-xs font-mono font-extrabold uppercase tracking-wider shadow-xs">
                                <x-icon name="calculator" class="w-3.5 h-3.5 text-slate-900" strokeWidth="2.5" />
                                <span>Applied Financial Engineering</span>
                            </div>

                            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-950 tracking-tight leading-[1.12]">
                                Berlandaskan<br>
                                Teori Finansial 
                                <span class="inline-flex items-center justify-center px-2.5 sm:px-3.5 py-0.5 rounded-full bg-slate-950 text-[#D4F66C] text-xs sm:text-base align-middle mx-1 shadow-xs">
                                    &rarr;
                                </span><br>
                                Terpercaya
                            </h1>

                            <p class="text-xs sm:text-sm md:text-base font-semibold text-slate-600 leading-relaxed max-w-lg">
                                Bukan sekadar catatan biasa. Ditenagai <strong>5 pilar teori keuangan</strong> untuk memutus siklus ketidakpastian omset freelance dan menjaga kelangsungan hidup Anda secara terukur.
                            </p>

                            <!-- Feature Badges on Desktop -->
                            <div class="hidden sm:grid grid-cols-2 gap-2 pt-2">
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5">
                                    <x-icon name="activity" class="w-3.5 h-3.5 text-teal-700" />
                                    <span>Income Smoothing</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5">
                                    <x-icon name="pie-chart" class="w-3.5 h-3.5 text-indigo-600" />
                                    <span>Kas Terpisah</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5">
                                    <x-icon name="shield-check" class="w-3.5 h-3.5 text-emerald-600" />
                                    <span>Sinking Fund Goal</span>
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200/80 flex items-center gap-1.5">
                                    <x-icon name="pie-chart" class="w-3.5 h-3.5 text-slate-900" />
                                    <span>50/30/20 Adaptive</span>
                                </span>
                            </div>
                        </div>

                        <!-- Right Column (Desktop): Illustration -->
                        <div class="md:col-span-5 order-1 md:order-2 flex justify-center items-center">
                            <div class="relative w-full max-w-[220px] sm:max-w-[280px] md:max-w-[320px] lg:max-w-[360px] aspect-square flex items-center justify-center shrink-0">
                                <div class="w-48 h-48 sm:w-64 sm:h-64 md:w-72 md:h-72 rounded-full bg-[#EBFAD2] border border-[#D4F66C] absolute z-0 anim-glow"></div>

                                <!-- Floating Badge 1: Smoothing Theory Top Left -->
                                <div class="absolute top-2 left-0 sm:left-1 px-2.5 py-1 rounded-xl sm:rounded-2xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] z-20 flex items-center gap-1 anim-float-1 rotate-[-6deg]">
                                    <x-icon name="activity" class="w-3.5 h-3.5 text-teal-700" strokeWidth="2.5" />
                                    <span class="text-[8px] sm:text-[10px] font-black font-mono text-slate-900">Income Smoothing</span>
                                </div>

                                <!-- Floating Badge 2: Dual Entity Top Right -->
                                <div class="absolute top-2 right-0 sm:right-1 px-2.5 py-1 rounded-xl sm:rounded-2xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] z-20 flex items-center gap-1 anim-float-3 rotate-[8deg]">
                                    <x-icon name="pie-chart" class="w-3.5 h-3.5 text-indigo-600" strokeWidth="2.5" />
                                    <span class="text-[8px] sm:text-[10px] font-black font-mono text-slate-900">Kas Terpisah</span>
                                </div>

                                <!-- Center Shield & Theoretical Formula Chart Vector -->
                                <div class="relative z-10 flex flex-col items-center justify-center">
                                    <svg viewBox="0 0 240 200" style="width: 200px; height: 165px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="45" y="45" width="150" height="115" rx="20" fill="#FFFFFF" stroke="#090D16" stroke-width="3.5"/>
                                        <path d="M45 75 L195 75" stroke="#090D16" stroke-width="3"/>
                                        <circle cx="65" cy="60" r="4" fill="#090D16"/>
                                        <circle cx="80" cy="60" r="4" fill="#090D16"/>
                                        <path d="M65 130 C85 130 95 105 120 110 C145 115 155 90 175 88" stroke="#84CC16" stroke-width="4" stroke-linecap="round" fill="none"/>
                                        <circle cx="175" cy="88" r="5" fill="#C6F24D" stroke="#090D16" stroke-width="2.5"/>
                                        <rect x="65" y="140" width="110" height="8" rx="4" fill="#F1F5F9" stroke="#090D16" stroke-width="2"/>
                                        <rect x="67" y="142" width="75" height="4" rx="2" fill="#059669"/>
                                    </svg>
                                </div>

                                <!-- Floating Badge 3: Sinking Fund Bottom Left -->
                                <div class="absolute bottom-3 left-0 sm:left-1 px-2.5 py-1 rounded-lg sm:rounded-xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] z-20 flex items-center gap-1 anim-float-2 rotate-[-4deg]">
                                    <x-icon name="shield-check" class="w-3.5 h-3.5 text-emerald-600" strokeWidth="2.5" />
                                    <span class="text-[8px] sm:text-[10px] font-black font-mono text-slate-900">Sinking Fund</span>
                                </div>

                                <!-- Floating Badge 4: Dynamic Budgeting Bottom Right -->
                                <div class="absolute bottom-3 right-0 sm:right-1 px-2.5 py-1 rounded-lg sm:rounded-xl bg-[#C6F24D] border-2 border-slate-950 shadow-[2px_2px_0px_#000] z-20 flex items-center gap-1 text-xs font-mono font-black anim-float-1 rotate-[6deg]">
                                    <x-icon name="pie-chart" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
                                    <span class="text-[8px] sm:text-[10px] font-black font-mono text-slate-950">50/30/20 Adaptive</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </main>

        <!-- ── BOTTOM NAVIGATION ───────────────────────────────── -->
        <footer class="w-full pt-3 pb-6 sm:pt-4 sm:pb-8 border-t border-slate-100 flex items-center justify-between z-20 shrink-0 bg-white">
            
            <!-- Left: Back Button -->
            <div class="w-20 flex items-center">
                <button x-show="currentSlide > 1" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 -translate-x-2"
                        @click="prevSlide()" 
                        id="btn-prev"
                        type="button" 
                        class="text-xs sm:text-sm font-bold text-slate-400 hover:text-slate-950 transition-colors duration-200 cursor-pointer flex items-center gap-1"
                        style="display: none;">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    <span>Kembali</span>
                </button>
            </div>

            <!-- Center: Step Indicator Dots (Morphing Width) -->
            <div class="flex items-center gap-2">
                <button @click="goToSlide(1)" id="dot-1" type="button" 
                        class="h-1.5 sm:h-2 rounded-full transition-all duration-400 ease-[cubic-bezier(0.22,1,0.36,1)] cursor-pointer"
                        :class="currentSlide === 1 ? 'w-6 sm:w-8 bg-slate-950' : 'w-1.5 sm:w-2 bg-slate-200 hover:bg-slate-300'"></button>
                <button @click="goToSlide(2)" id="dot-2" type="button" 
                        class="h-1.5 sm:h-2 rounded-full transition-all duration-400 ease-[cubic-bezier(0.22,1,0.36,1)] cursor-pointer"
                        :class="currentSlide === 2 ? 'w-6 sm:w-8 bg-slate-950' : 'w-1.5 sm:w-2 bg-slate-200 hover:bg-slate-300'"></button>
                <button @click="goToSlide(3)" id="dot-3" type="button" 
                        class="h-1.5 sm:h-2 rounded-full transition-all duration-400 ease-[cubic-bezier(0.22,1,0.36,1)] cursor-pointer"
                        :class="currentSlide === 3 ? 'w-6 sm:w-8 bg-slate-950' : 'w-1.5 sm:w-2 bg-slate-200 hover:bg-slate-300'"></button>
                <button @click="goToSlide(4)" id="dot-4" type="button" 
                        class="h-1.5 sm:h-2 rounded-full transition-all duration-400 ease-[cubic-bezier(0.22,1,0.36,1)] cursor-pointer"
                        :class="currentSlide === 4 ? 'w-6 sm:w-8 bg-slate-950' : 'w-1.5 sm:w-2 bg-slate-200 hover:bg-slate-300'"></button>
            </div>

            <!-- Right: Next / Start Button with Smooth Morphing -->
            <div class="w-20 flex justify-end">
                <button @click="nextSlide()" 
                        id="btn-next"
                        type="button" 
                        class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-slate-950 hover:bg-slate-800 text-white flex items-center justify-center font-bold transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] active:scale-95 shadow-md cursor-pointer z-30 group">
                    <template x-if="currentSlide < totalSlides">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-[#C6F24D] group-hover:translate-x-0.5 transition-transform duration-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </template>
                    <template x-if="currentSlide === totalSlides">
                        <span class="text-xs sm:text-sm font-black text-[#C6F24D]">GO</span>
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
            }, 1500);
        });

        // ── 2. Alpine Onboarding Engine ──────────────────────────────
        function onboardingApp() {
            return {
                currentSlide: 1,
                totalSlides: 4,
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
                goToSlide(num) {
                    if (this.isNavigating || num === this.currentSlide) return;
                    this.isNavigating = true;
                    this.currentSlide = num;
                    this.syncTrack();
                    setTimeout(() => { this.isNavigating = false; }, 450);
                },
                skip() {
                    this.exitToLogin();
                },
                exitToLogin() {
                    const root = document.getElementById('onboarding-root');
                    if (root) {
                        root.classList.add('anim-page-exit-left');
                    }
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}";
                    }, 240);
                },
                handleTouchStart(e) {
                    this.touchStartX = e.changedTouches[0].screenX;
                },
                handleTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].screenX;
                    const diff = this.touchStartX - this.touchEndX;
                    if (diff > 45) {
                        this.nextSlide();
                    } else if (diff < -45) {
                        this.prevSlide();
                    }
                },
                syncTrack() {
                    const track = document.getElementById('slide-track');
                    if (track) {
                        const offset = (this.currentSlide - 1) * 100;
                        track.style.transform = `translate3d(-${offset}%, 0, 0)`;
                    }
                    for (let i = 1; i <= 4; i++) {
                        const dot = document.getElementById('dot-' + i);
                        if (dot) {
                            if (this.currentSlide === i) {
                                dot.className = "h-1.5 sm:h-2 rounded-full transition-all duration-400 ease-[cubic-bezier(0.22,1,0.36,1)] cursor-pointer w-6 sm:w-8 bg-slate-950";
                            } else {
                                dot.className = "h-1.5 sm:h-2 rounded-full transition-all duration-400 ease-[cubic-bezier(0.22,1,0.36,1)] cursor-pointer w-1.5 sm:w-2 bg-slate-200 hover:bg-slate-300";
                            }
                        }
                    }
                    const btnPrev = document.getElementById('btn-prev');
                    if (btnPrev) btnPrev.style.display = (this.currentSlide > 1) ? 'inline-flex' : 'none';
                }
            };
        }
    </script>

    @livewireScripts
</body>
</html>
