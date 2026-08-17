@extends('errors.layout')

@section('title', '503 - Pemeliharaan Sistem')

@section('content')
    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  ANIMATED 503 ILLUSTRATION (MAINTENANCE)                   -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="relative w-64 h-64 sm:w-72 sm:h-72 mx-auto flex items-center justify-center select-none">
        
        <!-- Ambient Glow -->
        <div class="absolute inset-4 rounded-full bg-teal-400/20 blur-2xl anim-glow"></div>

        <!-- Floating 503 Badge Top Right -->
        <div class="absolute top-2 right-2 px-3 py-1 rounded-2xl bg-white border-2 border-slate-950 shadow-[3px_3px_0px_#000] rotate-[8deg] flex items-center gap-1.5 z-20 anim-float-2">
            <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
            <span class="text-xs font-mono font-black text-slate-950">ERR_503</span>
        </div>

        <!-- Floating Upgrade Badge Bottom Left -->
        <div class="absolute bottom-4 left-2 px-2.5 py-1 rounded-xl bg-teal-200 border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[-10deg] flex items-center gap-1 z-20 anim-float-1">
            <x-icon name="sparkles" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[10px] font-black font-mono text-slate-950">UPGRADING</span>
        </div>

        <!-- Modern Animated Maintenance Illustration -->
        <div class="relative z-10 w-full h-full flex items-center justify-center">
            <svg viewBox="0 0 280 240" class="w-56 h-48 sm:w-64 sm:h-56 drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Large Gear (Rotating Slow) -->
                <g class="anim-float-3" style="transform-origin: 120px 115px;">
                    <circle cx="120" cy="115" r="42" fill="#FFFFFF" stroke="#090D16" stroke-width="4"/>
                    <circle cx="120" cy="115" r="20" fill="#F1F5F9" stroke="#090D16" stroke-width="3"/>
                    <path d="M120 68 V76 M120 154 V162 M73 115 H81 M159 115 H167 M87 82 L93 88 M147 142 L153 148 M87 148 L93 142 M147 88 L153 82" stroke="#090D16" stroke-width="6" stroke-linecap="round"/>
                </g>

                <!-- Small Lime Gear -->
                <g class="anim-float-1" style="transform-origin: 175px 145px;">
                    <circle cx="175" cy="145" r="26" fill="#C6F24D" stroke="#090D16" stroke-width="3.5"/>
                    <circle cx="175" cy="145" r="10" fill="#FFFFFF" stroke="#090D16" stroke-width="2.5"/>
                </g>

                <!-- Ground Shadow -->
                <ellipse cx="140" cy="195" rx="75" ry="7" fill="#090D16" fill-opacity="0.08"/>
            </svg>
        </div>
    </div>

    <!-- ── TEXT CONTENT ──────────────────────────────────────── -->
    <div class="space-y-2 max-w-md mx-auto">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 border border-teal-200 text-teal-800 text-xs font-mono font-bold uppercase tracking-wider">
            <x-icon name="settings" class="w-3.5 h-3.5 text-teal-600" strokeWidth="2.5" />
            <span>Sedang Pemeliharaan</span>
        </div>

        <h1 class="text-2xl sm:text-4xl font-black text-slate-950 tracking-tight leading-tight">
            Peningkatan Sistem Rutin 🛠️
        </h1>

        <p class="text-xs sm:text-sm font-medium text-slate-600 leading-relaxed max-w-sm mx-auto">
            PortoFinance sedang dalam peningkatan infrastruktur berkala untuk menghadirkan performa yang lebih cepat dan aman. Kami akan segera kembali!
        </p>
    </div>

    <!-- ── ACTION BUTTONS ────────────────────────────────────── -->
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-xs sm:max-w-sm mx-auto pt-2">
        <button onclick="window.location.reload()" 
                type="button"
                class="w-full py-3.5 px-5 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-[0.98] text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border-2 border-slate-950">
            <x-icon name="repeat" class="w-4 h-4" strokeWidth="2.5" />
            <span>Cek Status / Muat Ulang</span>
        </button>
    </div>
@endsection
