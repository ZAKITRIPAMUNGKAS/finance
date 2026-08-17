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
<body class="h-full bg-slate-100/70 text-slate-900 font-sans antialiased min-h-[100dvh] flex flex-col justify-center items-center selection:bg-[#C6F24D] selection:text-slate-950 overflow-x-hidden p-0 sm:p-4 md:p-6 relative">
    
    <!-- Ambient Background Lighting Orbs -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10 select-none" aria-hidden="true">
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[#C6F24D]/25 blur-3xl anim-glow"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-teal-400/20 blur-3xl anim-glow" style="animation-delay: 1.5s;"></div>
    </div>

    <!-- Responsive Dynamic Card Container -->
    <div class="w-full max-w-sm sm:max-w-md md:max-w-lg min-h-[100dvh] sm:min-h-0 bg-white sm:border-2 sm:border-slate-900/10 sm:rounded-[32px] sm:shadow-2xl flex flex-col justify-between p-4 sm:p-7 md:p-8 relative overflow-hidden">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
