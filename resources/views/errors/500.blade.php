@extends('errors.layout')

@section('title', '500 - Gangguan Server')

@section('content')
    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ANIMATED 500 ILLUSTRATION (SERVER SPARK)                  -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="relative w-64 h-64 sm:w-72 sm:h-72 mx-auto flex items-center justify-center select-none">
        
        <!-- Ambient Glow -->
        <div class="absolute inset-4 rounded-full bg-rose-500/20 blur-2xl anim-glow"></div>

        <!-- Floating 500 Badge Top Right -->
        <div class="absolute top-2 right-2 px-3 py-1 rounded-2xl bg-white border-2 border-slate-950 shadow-[3px_3px_0px_#000] rotate-[8deg] flex items-center gap-1.5 z-20 anim-float-2">
            <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
            <span class="text-xs font-mono font-black text-slate-950">ERR_500</span>
        </div>

        <!-- Floating Zap Badge Bottom Left -->
        <div class="absolute bottom-4 left-2 px-2.5 py-1 rounded-xl bg-rose-200 border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[-10deg] flex items-center gap-1 z-20 anim-float-1">
            <x-icon name="zap" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[10px] font-black font-mono text-slate-950">SERVER ERROR</span>
        </div>

        <!-- Modern Animated Server Vector Illustration -->
        <div class="relative z-10 w-full h-full flex items-center justify-center">
            <svg viewBox="0 0 280 240" class="w-56 h-48 sm:w-64 sm:h-56 drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Server Rack Box 1 (Top) -->
                <rect x="60" y="60" width="160" height="42" rx="14" fill="#FFFFFF" stroke="#090D16" stroke-width="4"/>
                <circle cx="85" cy="81" r="5" fill="#C6F24D" stroke="#090D16" stroke-width="2"/>
                <circle cx="102" cy="81" r="5" fill="#38BDF8" stroke="#090D16" stroke-width="2"/>
                <line x1="130" x2="200" y1="81" y2="81" stroke="#E2E8F0" stroke-width="4" stroke-linecap="round"/>

                <!-- Server Rack Box 2 (Bottom - Glitching) -->
                <rect x="60" y="115" width="160" height="42" rx="14" fill="#FFFFFF" stroke="#090D16" stroke-width="4"/>
                <circle cx="85" cy="136" r="5" fill="#F43F5E" stroke="#090D16" stroke-width="2" class="animate-ping"/>
                <circle cx="102" cy="136" r="5" fill="#CBD5E1" stroke="#090D16" stroke-width="2"/>
                <line x1="130" x2="180" y1="136" y2="136" stroke="#E2E8F0" stroke-width="4" stroke-linecap="round"/>

                <!-- Spark Lightning Animation Floating -->
                <g class="anim-float-3" style="transform-origin: 195px 120px;">
                    <polygon points="205,100 190,125 200,125 188,148 215,118 200,118" fill="#F43F5E" stroke="#090D16" stroke-width="2.5" stroke-linejoin="round"/>
                </g>

                <!-- Ground Shadow -->
                <ellipse cx="140" cy="195" rx="75" ry="7" fill="#090D16" fill-opacity="0.08"/>
            </svg>
        </div>
    </div>

    <!-- ── TEXT CONTENT ──────────────────────────────────────── -->
    <div class="space-y-2 max-w-md mx-auto">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-800 text-xs font-mono font-bold uppercase tracking-wider">
            <x-icon name="alert-triangle" class="w-3.5 h-3.5 text-rose-600" strokeWidth="2.5" />
            <span>Gangguan Server</span>
        </div>

        <h1 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight leading-tight">
            Terjadi Masalah Teknis ⚡
        </h1>

        <p class="text-xs sm:text-sm font-medium text-slate-600 leading-relaxed max-w-sm mx-auto">
            Sistem kami sedang mengalami kendala pemrosesan data sesaat. Tim teknis kami telah mencatat log kejadian ini.
        </p>
    </div>

    <!-- ── ACTION BUTTONS ────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-xs sm:max-w-sm mx-auto pt-2">
        <button onclick="window.location.reload()" 
                type="button"
                class="w-full py-3.5 px-5 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-[0.98] text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border-2 border-slate-950">
            <x-icon name="repeat" class="w-4 h-4" strokeWidth="2.5" />
            <span>Coba Lagi / Muat Ulang</span>
        </button>

        <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" 
           class="w-full py-3.5 px-4 rounded-2xl bg-white hover:bg-slate-100 active:scale-[0.98] text-slate-800 font-bold text-xs sm:text-sm border-2 border-slate-200 shadow-2xs transition-all flex items-center justify-center gap-2 cursor-pointer">
            <span>Kembali ke Beranda</span>
        </a>
    </div>
@endsection
