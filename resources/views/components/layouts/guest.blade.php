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
<body class="h-full bg-slate-50/50 sm:bg-slate-100/60 text-slate-900 font-sans antialiased min-h-[100dvh] flex flex-col justify-center items-center selection:bg-[#C6F24D] selection:text-slate-950 overflow-x-hidden p-0 sm:p-4 md:p-6">
    
    <!-- Responsive Dynamic Card Container -->
    <div class="w-full max-w-sm sm:max-w-md md:max-w-lg min-h-[100dvh] sm:min-h-0 bg-white sm:border sm:border-slate-200/90 sm:rounded-[32px] sm:shadow-xl flex flex-col justify-between p-4 sm:p-7 md:p-8 relative overflow-hidden">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
