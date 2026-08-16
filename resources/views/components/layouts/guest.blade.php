<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'PortoFinance • Personal & Freelance Hub' }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full bg-white text-slate-900 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-[#C6F24D] selection:text-slate-950">
    
    <!-- Full-screen Main Container -->
    <div class="w-full max-w-md mx-auto min-h-screen flex flex-col justify-between p-6 sm:p-8 relative">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
