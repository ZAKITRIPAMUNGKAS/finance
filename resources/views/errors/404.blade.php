@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ANIMATED 404 ILLUSTRATION (LOTTIE + NEO-FINTECH SVG)     -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="relative w-64 h-64 sm:w-72 sm:h-72 mx-auto flex items-center justify-center select-none">
        
        <!-- Ambient Backing Glow -->
        <div class="absolute inset-4 rounded-full bg-[#C6F24D]/30 blur-2xl anim-glow"></div>

        <!-- Floating 404 Error Badge Top Right -->
        <div class="absolute top-2 right-2 px-3 py-1 rounded-2xl bg-white border-2 border-slate-950 shadow-[3px_3px_0px_#000] rotate-[8deg] flex items-center gap-1.5 z-20 anim-float-2">
            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
            <span class="text-xs font-mono font-black text-slate-950">ERR_404</span>
        </div>

        <!-- Floating Lost Coin Bottom Left -->
        <div class="absolute bottom-4 left-2 px-2.5 py-1 rounded-xl bg-[#C6F24D] border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[-10deg] flex items-center gap-1 z-20 anim-float-1">
            <x-icon name="dollar-sign" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[10px] font-black font-mono text-slate-950">NYASAR?</span>
        </div>

        <!-- Lottie Player with Smart Fallback Animated SVG -->
        <div class="relative z-10 w-full h-full flex items-center justify-center">
            <!-- Modern Animated 404 Vector Illustration -->
            <svg viewBox="0 0 280 240" class="w-56 h-48 sm:w-64 sm:h-56 drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Large "4 0 4" Background Numbers -->
                <text x="35" y="105" font-family="Plus Jakarta Sans, sans-serif" font-weight="900" font-size="72" fill="#E2E8F0" letter-spacing="-2">4</text>
                <text x="185" y="105" font-family="Plus Jakarta Sans, sans-serif" font-weight="900" font-size="72" fill="#E2E8F0" letter-spacing="-2">4</text>
                
                <!-- Radar / Search Circle (Middle '0') -->
                <circle cx="140" cy="80" r="38" fill="#FFFFFF" stroke="#090D16" stroke-width="4"/>
                <circle cx="140" cy="80" r="26" stroke="#CBD5E1" stroke-width="2" stroke-dasharray="4 4"/>
                <circle cx="140" cy="80" r="14" stroke="#CBD5E1" stroke-width="2" stroke-dasharray="2 2"/>
                
                <!-- Radar Scanning Needle Animated -->
                <g class="anim-float-3" style="transform-origin: 140px 80px;">
                    <line x1="140" y1="80" x2="160" y2="60" stroke="#C6F24D" stroke-width="4" stroke-linecap="round"/>
                    <circle cx="160" cy="60" r="4" fill="#090D16"/>
                </g>

                <!-- Floating Magnifying Glass -->
                <g class="anim-float-1" style="transform-origin: 140px 140px;">
                    <!-- Lens Ring -->
                    <circle cx="130" cy="140" r="28" fill="#FFFFFF" stroke="#090D16" stroke-width="4"/>
                    <circle cx="130" cy="140" r="20" fill="#F1F5F9" fill-opacity="0.6"/>
                    <!-- Lens Reflection -->
                    <path d="M120 125 A16 16 0 0 1 142 135" stroke="#94A3B8" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                    <!-- Question Mark Inside Lens -->
                    <text x="124" y="148" font-family="Plus Jakarta Sans, sans-serif" font-weight="900" font-size="20" fill="#090D16">?</text>
                    <!-- Handle -->
                    <path d="M150 160 L175 185" stroke="#090D16" stroke-width="7" stroke-linecap="round"/>
                    <path d="M150 160 L175 185" stroke="#C6F24D" stroke-width="3" stroke-linecap="round"/>
                </g>

                <!-- Ground Shadow Ellipse -->
                <ellipse cx="140" cy="205" rx="80" ry="8" fill="#090D16" fill-opacity="0.08"/>
            </svg>
        </div>
    </div>

    <!-- ── TEXT CONTENT ──────────────────────────────────────── -->
    <div class="space-y-2 max-w-md mx-auto">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-xs font-mono font-bold uppercase tracking-wider">
            <x-icon name="alert-triangle" class="w-3.5 h-3.5 text-rose-600" strokeWidth="2.5" />
            <span>Halaman Tidak Ditemukan</span>
        </div>

        <h1 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight leading-tight">
            Oops! Jalur Ini Buntu 🗺️
        </h1>

        <p class="text-xs sm:text-sm font-medium text-slate-600 leading-relaxed max-w-sm mx-auto">
            Halaman atau catatan keuangan yang Anda tuju sepertinya telah dipindahkan, dihapus, atau tautan yang dimasukkan kurang tepat.
        </p>
    </div>

    <!-- ── ACTION BUTTONS ────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-xs sm:max-w-sm mx-auto pt-2">
        <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" 
           class="w-full py-3.5 px-5 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-[0.98] text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border-2 border-slate-950">
            <x-icon name="arrow-left" class="w-4 h-4" strokeWidth="2.5" />
            <span>{{ auth()->check() ? 'Kembali ke Dashboard' : 'Kembali ke Beranda' }}</span>
        </a>

        <button onclick="window.history.back()" 
                type="button"
                class="w-full py-3.5 px-4 rounded-2xl bg-white hover:bg-slate-100 active:scale-[0.98] text-slate-800 font-bold text-xs sm:text-sm border-2 border-slate-200 shadow-2xs transition-all flex items-center justify-center gap-2 cursor-pointer">
            <x-icon name="repeat" class="w-4 h-4 text-slate-600" strokeWidth="2" />
            <span>Halaman Sebelumnya</span>
        </button>
    </div>
@endsection
