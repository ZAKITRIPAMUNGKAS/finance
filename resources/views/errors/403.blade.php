@extends('errors.layout')

@section('title', '403 - Akses Ditolak')

@section('content')
    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ANIMATED 403 ILLUSTRATION (LOCKED VAULT)                  -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="relative w-64 h-64 sm:w-72 sm:h-72 mx-auto flex items-center justify-center select-none">
        
        <!-- Ambient Glow -->
        <div class="absolute inset-4 rounded-full bg-amber-400/20 blur-2xl anim-glow"></div>

        <!-- Floating 403 Badge Top Right -->
        <div class="absolute top-2 right-2 px-3 py-1 rounded-2xl bg-white border-2 border-slate-950 shadow-[3px_3px_0px_#000] rotate-[8deg] flex items-center gap-1.5 z-20 anim-float-2">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <span class="text-xs font-mono font-black text-slate-950">ERR_403</span>
        </div>

        <!-- Floating Lock Badge Bottom Left -->
        <div class="absolute bottom-4 left-2 px-2.5 py-1 rounded-xl bg-amber-300 border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[-10deg] flex items-center gap-1 z-20 anim-float-1">
            <x-icon name="lock" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[10px] font-black font-mono text-slate-950">RESTRICTED</span>
        </div>

        <!-- Modern Animated 403 Vector Illustration -->
        <div class="relative z-10 w-full h-full flex items-center justify-center">
            <svg viewBox="0 0 280 240" class="w-56 h-48 sm:w-64 sm:h-56 drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Vault Outer Door -->
                <rect x="55" y="45" width="170" height="150" rx="28" fill="#FFFFFF" stroke="#090D16" stroke-width="4"/>
                
                <!-- Vault Inner Circle Wheel -->
                <circle cx="140" cy="120" r="46" fill="#F8FAFC" stroke="#090D16" stroke-width="3.5"/>
                <circle cx="140" cy="120" r="32" stroke="#CBD5E1" stroke-width="2" stroke-dasharray="6 4"/>
                
                <!-- Heavy Padlock Body (Animated) -->
                <g class="anim-float-2" style="transform-origin: 140px 120px;">
                    <!-- Shackle -->
                    <path d="M125 110 V90 A15 15 0 0 1 155 90 V110" stroke="#090D16" stroke-width="5" stroke-linecap="round" fill="none"/>
                    <!-- Lock Body -->
                    <rect x="118" y="105" width="44" height="34" rx="10" fill="#C6F24D" stroke="#090D16" stroke-width="3.5"/>
                    <!-- Keyhole -->
                    <circle cx="140" cy="118" r="3.5" fill="#090D16"/>
                    <path d="M138 118 L142 118 L141 127 L139 127 Z" fill="#090D16"/>
                </g>

                <!-- Ground Shadow -->
                <ellipse cx="140" cy="205" rx="75" ry="7" fill="#090D16" fill-opacity="0.08"/>
            </svg>
        </div>
    </div>

    <!-- ── TEXT CONTENT ──────────────────────────────────────── -->
    <div class="space-y-2 max-w-md mx-auto">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-mono font-bold uppercase tracking-wider">
            <x-icon name="shield-check" class="w-3.5 h-3.5 text-amber-600" strokeWidth="2.5" />
            <span>Akses Ditolak</span>
        </div>

        <h1 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight leading-tight">
            Area Brankas Terkunci 🔐
        </h1>

        <p class="text-xs sm:text-sm font-medium text-slate-600 leading-relaxed max-w-sm mx-auto">
            Anda tidak memiliki izin otorisasi untuk mengakses berkas keuangan atau halaman ini.
        </p>
    </div>

    <!-- ── ACTION BUTTONS ────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-xs sm:max-w-sm mx-auto pt-2">
        <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" 
           class="w-full py-3.5 px-5 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-[0.98] text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border-2 border-slate-950">
            <x-icon name="arrow-left" class="w-4 h-4" strokeWidth="2.5" />
            <span>{{ auth()->check() ? 'Kembali ke Dashboard' : 'Kembali ke Beranda' }}</span>
        </a>
    </div>
@endsection
