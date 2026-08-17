<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>{{ $title ?? 'PortoFinance • Personal & Freelance Hub' }}</title>
    
    <!-- Favicon & PWA Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logofinance.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/logofinance.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/logofinance.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logofinance.png') }}">
    
    <!-- Inline critical x-cloak prevention to guarantee zero layout bleeding before JS loads -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-slate-50 text-slate-900 font-sans antialiased min-h-[100dvh] flex flex-col justify-between selection:bg-[#C6F24D] selection:text-slate-950 overflow-x-hidden relative">
    
    <!-- Ambient Background Glow Gradients (Soft & Aesthetic) -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10 select-none" aria-hidden="true">
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[#C6F24D]/15 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-teal-400/10 blur-3xl"></div>
    </div>

    <!-- Full-Screen Responsive Clean Container -->
    <div class="w-full flex-1 flex flex-col justify-between min-h-[100dvh] max-w-md mx-auto px-5 sm:px-6 py-6 relative z-0">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
