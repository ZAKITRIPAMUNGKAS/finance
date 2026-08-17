<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>@yield('title') • PortoFinance</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    
    <!-- Lottie Animation Players (CDN) -->
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-900 font-sans antialiased min-h-[100dvh] flex flex-col justify-between selection:bg-[#C6F24D] selection:text-slate-950 overflow-x-hidden relative p-4 sm:p-6 md:p-8">
    
    <!-- Ambient Background Glow Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10 select-none" aria-hidden="true">
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[#C6F24D]/25 blur-3xl anim-glow"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-teal-400/20 blur-3xl anim-glow" style="animation-delay: 1.5s;"></div>
    </div>

    <!-- Header Logo Navigation -->
    <header class="w-full max-w-4xl mx-auto flex items-center justify-between shrink-0">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
            <img src="{{ asset('images/logo.svg') }}" class="w-8 h-8 object-contain group-hover:scale-105 transition-transform" alt="PortoFinance Logo">
            <div class="leading-tight text-left">
                <span class="font-black text-sm text-slate-950 tracking-tight block">Porto<span class="text-teal-700">Finance</span></span>
                <span class="text-[8px] font-mono font-bold uppercase tracking-wider text-slate-400 block -mt-0.5">Financial OS</span>
            </div>
        </a>

        <a href="{{ url('/') }}" class="text-xs font-bold text-slate-600 hover:text-slate-950 transition-colors px-3 py-1.5 rounded-xl bg-white border border-slate-200 shadow-2xs">
            &larr; Beranda
        </a>
    </header>

    <!-- Main Dynamic Error Body -->
    <main class="w-full max-w-lg mx-auto my-auto py-6 text-center space-y-6 flex flex-col items-center justify-center anim-page-enter">
        @yield('content')
    </main>

    <!-- Footer Copyright -->
    <footer class="w-full max-w-4xl mx-auto text-center pt-4 pb-2 text-[11px] font-medium text-slate-400 border-t border-slate-200/60 shrink-0">
        &copy; {{ date('Y') }} PortoFinance OS • Secure Financial Management Platform
    </footer>

</body>
</html>
