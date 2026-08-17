@extends('errors.layout')

@section('title', '419 - Sesi Kedaluwarsa')

@section('content')
    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ANIMATED 419 ILLUSTRATION (HOURGLASS)                     -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="relative w-64 h-64 sm:w-72 sm:h-72 mx-auto flex items-center justify-center select-none">
        
        <!-- Ambient Glow -->
        <div class="absolute inset-4 rounded-full bg-sky-400/20 blur-2xl anim-glow"></div>

        <!-- Floating 419 Badge Top Right -->
        <div class="absolute top-2 right-2 px-3 py-1 rounded-2xl bg-white border-2 border-slate-950 shadow-[3px_3px_0px_#000] rotate-[8deg] flex items-center gap-1.5 z-20 anim-float-2">
            <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
            <span class="text-xs font-mono font-black text-slate-950">ERR_419</span>
        </div>

        <!-- Floating Clock Badge Bottom Left -->
        <div class="absolute bottom-4 left-2 px-2.5 py-1 rounded-xl bg-sky-200 border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[-10deg] flex items-center gap-1 z-20 anim-float-1">
            <x-icon name="clock" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[10px] font-black font-mono text-slate-950">TIMEOUT</span>
        </div>

        <!-- Modern Animated Hourglass Illustration -->
        <div class="relative z-10 w-full h-full flex items-center justify-center">
            <svg viewBox="0 0 280 240" class="w-56 h-48 sm:w-64 sm:h-56 drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Outer Glass Circle Background -->
                <circle cx="140" cy="120" r="55" fill="#FFFFFF" stroke="#090D16" stroke-width="4"/>
                
                <!-- Animated Hourglass -->
                <g class="anim-float-3" style="transform-origin: 140px 120px;">
                    <!-- Top Frame -->
                    <rect x="110" y="80" width="60" height="8" rx="4" fill="#090D16"/>
                    <!-- Bottom Frame -->
                    <rect x="110" y="152" width="60" height="8" rx="4" fill="#090D16"/>
                    
                    <!-- Glass Body -->
                    <path d="M115 88 L136 116 C138 119 138 121 136 124 L115 152 H165 L144 124 C142 121 142 119 144 116 L165 88 Z" fill="#F8FAFC" stroke="#090D16" stroke-width="3"/>
                    
                    <!-- Sand Top -->
                    <path d="M120 92 H160 L140 115 Z" fill="#C6F24D"/>
                    <!-- Sand Bottom -->
                    <path d="M122 150 H158 L140 132 Z" fill="#C6F24D"/>
                    <!-- Sand Trickle Line -->
                    <line x1="140" y1="115" x2="140" y2="135" stroke="#C6F24D" stroke-width="2" stroke-dasharray="2 2"/>
                </g>

                <!-- Ground Shadow -->
                <ellipse cx="140" cy="205" rx="75" ry="7" fill="#090D16" fill-opacity="0.08"/>
            </svg>
        </div>
    </div>

    <!-- ── TEXT CONTENT ──────────────────────────────────────── -->
    <div class="space-y-2 max-w-md mx-auto">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 border border-sky-200 text-sky-800 text-xs font-mono font-bold uppercase tracking-wider">
            <x-icon name="clock" class="w-3.5 h-3.5 text-sky-600" strokeWidth="2.5" />
            <span>Sesi Kedaluwarsa</span>
        </div>

        <h1 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight leading-tight">
            Waktu Sesi Berakhir ⏳
        </h1>

        <p class="text-xs sm:text-sm font-medium text-slate-600 leading-relaxed max-w-sm mx-auto">
            Halaman ini tidak aktif dalam beberapa waktu. Demi keamanan transaksi, token sesi telah diperbarui.
        </p>
    </div>

    <!-- ── ACTION BUTTONS ────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-xs sm:max-w-sm mx-auto pt-2">
        <button onclick="window.location.reload()" 
                type="button"
                class="w-full py-3.5 px-5 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-[0.98] text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border-2 border-slate-950">
            <x-icon name="repeat" class="w-4 h-4" strokeWidth="2.5" />
            <span>Muat Ulang Halaman</span>
        </button>

        <a href="{{ route('login') }}" 
           class="w-full py-3.5 px-4 rounded-2xl bg-white hover:bg-slate-100 active:scale-[0.98] text-slate-800 font-bold text-xs sm:text-sm border-2 border-slate-200 shadow-2xs transition-all flex items-center justify-center gap-2 cursor-pointer">
            <span>Masuk Kembali &rarr;</span>
        </a>
    </div>
@endsection
