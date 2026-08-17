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
    
    <!-- Ambient Background Glow Gradients -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10 select-none" aria-hidden="true">
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[#C6F24D]/20 blur-3xl anim-glow"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-teal-400/15 blur-3xl anim-glow" style="animation-delay: 1.5s;"></div>
    </div>

    <!-- Desktop Floating Orbital Badges (Visible on Medium & Large Screens in Outer Screen Margins) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10 select-none hidden md:block" aria-hidden="true">
        <!-- Top Left Orbital: Rp Currency Pill -->
        <div class="absolute top-24 left-8 lg:left-16 px-3.5 py-1.5 rounded-2xl bg-white border-2 border-slate-950 shadow-[3px_3px_0px_#000] flex items-center gap-1.5 font-black font-mono text-xs rotate-[-10deg] anim-float-1">
            <x-icon name="dollar-sign" class="w-4 h-4 text-slate-950" strokeWidth="2.5" />
            <span>Rp IDR</span>
        </div>

        <!-- Top Right Orbital: SAFE Vault Shield -->
        <div class="absolute top-24 right-8 lg:right-16 px-3.5 py-1.5 rounded-2xl bg-white border-2 border-slate-950 shadow-[3px_3px_0px_#000] rotate-[8deg] flex items-center gap-1.5 anim-float-2">
            <x-icon name="shield-check" class="w-4 h-4 text-emerald-600" strokeWidth="2.5" />
            <span class="text-[10px] font-black font-mono text-slate-900 tracking-wider">BANK GRADE</span>
        </div>

        <!-- Bottom Left Orbital: Smart Allocation Coin -->
        <div class="absolute bottom-24 left-8 lg:left-16 px-3 py-1.5 rounded-2xl bg-[#C6F24D] border-2 border-slate-950 shadow-[3px_3px_0px_#000] flex items-center gap-1.5 rotate-[12deg] anim-float-3">
            <x-icon name="sparkles" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[10px] font-black font-mono text-slate-950 tracking-wider">AUTO SYNC</span>
        </div>

        <!-- Bottom Right Orbital: Zero Fee Badge -->
        <div class="absolute bottom-24 right-8 lg:right-16 px-3 py-1.5 rounded-2xl bg-[#C6F24D] border-2 border-slate-950 shadow-[3px_3px_0px_#000] rotate-[-8deg] flex items-center gap-1.5 anim-float-1">
            <x-icon name="zap" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[10px] font-black font-mono text-slate-900 tracking-wider">INSTANT</span>
        </div>
    </div>

    <!-- Full-Screen Responsive Container -->
    <div class="w-full flex-1 flex flex-col justify-between min-h-[100dvh] max-w-md sm:max-w-lg mx-auto px-5 sm:px-8 py-5 sm:py-8 relative z-0">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
