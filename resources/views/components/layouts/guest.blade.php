<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>{{ $title ?? 'PortoFinance • Personal & Freelance Hub' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-slate-50 text-slate-900 font-sans antialiased min-h-[100dvh] flex flex-col justify-between selection:bg-[#C6F24D] selection:text-slate-950 overflow-x-hidden relative">
    
    <!-- Ambient Background Lighting Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10 select-none" aria-hidden="true">
        <div class="absolute -top-24 -left-24 w-80 h-80 sm:w-96 sm:h-96 rounded-full bg-[#C6F24D]/20 blur-3xl anim-glow"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 sm:w-96 sm:h-96 rounded-full bg-teal-400/15 blur-3xl anim-glow" style="animation-delay: 1.5s;"></div>
    </div>

    <!-- Floating Background Badges (Only on Large Screens - Outer Margins, Never Crowding Mobile) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10 select-none hidden lg:block" aria-hidden="true">
        <!-- Top Left: Rp Coin -->
        <div class="absolute top-12 left-12 px-3 py-1.5 rounded-2xl bg-white border-2 border-slate-950 shadow-[3px_3px_0px_#000] flex items-center gap-1.5 font-black font-mono text-xs rotate-[-12deg] anim-float-1">
            <x-icon name="dollar-sign" class="w-4 h-4 text-slate-950" strokeWidth="2.5" />
            <span>Rp</span>
        </div>

        <!-- Top Right: SAFE Shield -->
        <div class="absolute top-14 right-12 px-3 py-1.5 rounded-2xl bg-white border-2 border-slate-950 shadow-[3px_3px_0px_#000] rotate-[10deg] flex items-center gap-1.5 anim-float-2">
            <x-icon name="shield-check" class="w-4 h-4 text-emerald-600" strokeWidth="2.5" />
            <span class="text-[10px] font-black font-mono text-slate-900 tracking-wider">SAFE</span>
        </div>

        <!-- Bottom Left: Checkmark Coin -->
        <div class="absolute bottom-16 left-14 w-9 h-9 rounded-full bg-[#C6F24D] border-2 border-slate-950 shadow-[3px_3px_0px_#000] flex items-center justify-center rotate-[15deg] anim-float-3">
            <x-icon name="check" class="w-4 h-4 text-slate-950" strokeWidth="3" />
        </div>

        <!-- Bottom Right: FAST Badge -->
        <div class="absolute bottom-16 right-14 px-3 py-1 rounded-xl bg-[#C6F24D] border-2 border-slate-950 shadow-[3px_3px_0px_#000] rotate-[-8deg] flex items-center gap-1.5 anim-float-1">
            <x-icon name="zap" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[9px] font-black font-mono text-slate-900 tracking-wider">FAST</span>
        </div>
    </div>

    <!-- Full-Screen Dynamic Container -->
    <div class="w-full flex-1 flex flex-col justify-between min-h-[100dvh] max-w-lg mx-auto px-5 sm:px-8 py-5 sm:py-8 relative">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
