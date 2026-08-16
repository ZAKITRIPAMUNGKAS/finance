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
<body class="h-full bg-white text-slate-900 font-sans antialiased min-h-[100dvh] flex flex-col justify-between selection:bg-[#C6F24D] selection:text-slate-950 overflow-x-hidden">
    
    <!-- Full-screen Responsive Main Container -->
    <div class="w-full max-w-sm sm:max-w-md md:max-w-lg mx-auto min-h-[100dvh] flex flex-col justify-between p-4 sm:p-6 md:p-8 relative overflow-x-hidden">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
