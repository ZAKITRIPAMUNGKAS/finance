<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F8F9FA] text-slate-900 antialiased selection:bg-lime-300 selection:text-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'PortoFinance' }} — Digital Finance for Freelancers</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans bg-[#F8F9FA] text-slate-900 flex flex-col md:flex-row min-h-screen"
      x-data="{ 
          mobileSidebarOpen: false, 
          quickAddOpen: false
      }"
      @keydown.window.prevent.ctrl.k="quickAddOpen = true"
      @keydown.window.prevent.cmd.k="quickAddOpen = true"
      @open-quick-add.window="quickAddOpen = true">

    <!-- Splash Screen Initial Animation -->
    <x-splash-screen />

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-40 md:hidden"
         @click="mobileSidebarOpen = false"
         x-cloak>
    </div>

    <!-- Desktop Sidebar Navigation (Clean White FinTech Style) -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 flex flex-col transition-transform duration-200 ease-in-out -translate-x-full md:translate-x-0 md:static shrink-0 shadow-[2px_0_20px_rgba(0,0,0,0.02)]"
           :class="mobileSidebarOpen ? '!translate-x-0' : '-translate-x-full md:translate-x-0'">
        
        <!-- App Brand Header with Official Logo -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100 bg-white">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group" @click="mobileSidebarOpen = false">
                <img src="{{ asset('images/logo.svg') }}" class="w-9 h-9 rounded-xl object-contain shadow-sm group-hover:scale-105 transition-transform" alt="PortoFinance Logo">
                <div class="leading-none">
                    <span class="font-extrabold text-base tracking-tight text-slate-900 block">Porto<span class="text-teal-600">Finance</span></span>
                    <span class="text-[9px] uppercase font-extrabold tracking-wider text-slate-400">Freelancer OS</span>
                </div>
            </a>
            <button @click="mobileSidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-3.5 py-5 space-y-6" @click="if ($event.target.closest('a, button')) mobileSidebarOpen = false">
            <!-- Core Section -->
            <div>
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Menu Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <x-icon name="layout-dashboard" class="w-4 h-4" />
                        <span>Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- Personal Finance Section -->
            <div>
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Personal Finance</p>
                <div class="space-y-1">
                    <a href="{{ route('transactions') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('transactions*') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <x-icon name="receipt" class="w-4 h-4" />
                        <span>Transactions</span>
                    </a>
                    <a href="{{ route('accounts') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('accounts*') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <x-icon name="credit-card" class="w-4 h-4" />
                        <span>Accounts & Wallets</span>
                    </a>
                    <a href="{{ route('budgets') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('budgets*') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <x-icon name="pie-chart" class="w-4 h-4" />
                        <span>Percentage Budget</span>
                    </a>
                </div>
            </div>

            <!-- Business Section -->
            <div>
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Freelance Business</p>
                <div class="space-y-1">
                    <a href="{{ route('projects') }}" 
                       id="tour-nav-projects"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('projects*') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <div class="flex items-center gap-3">
                            <x-icon name="briefcase" class="w-4 h-4" />
                            <span>Projects & Profit</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-slate-900 text-white font-bold">Margin</span>
                    </a>
                    <a href="{{ route('clients') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('clients*') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <x-icon name="users" class="w-4 h-4" />
                        <span>Clients & Invoices</span>
                    </a>
                </div>
            </div>

            <!-- Planning v1.1 Section -->
            <div>
                <div class="flex items-center justify-between px-3 mb-2">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Planning (v1.1)</p>
                    <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-lime-100 text-lime-800 font-extrabold uppercase">New</span>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('wishlists') }}" 
                       id="tour-nav-wishlists"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('wishlists*') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <div class="flex items-center gap-3">
                            <x-icon name="shopping-bag" class="w-4 h-4" />
                            <span>Purchase Wishlist</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-bold">Saving</span>
                    </a>
                    <a href="{{ route('purchase-planning') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('purchase-planning*') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <div class="flex items-center gap-3">
                            <x-icon name="calculator" class="w-4 h-4" />
                            <span>Can I Afford This?</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold">Check</span>
                    </a>
                </div>
            </div>

            <!-- System & Account Section -->
            <div>
                <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Akun & Sistem</p>
                <div class="space-y-1">
                    <a href="{{ route('analytics') }}" 
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('analytics*') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <div class="flex items-center gap-3">
                            <x-icon name="activity" class="w-4 h-4" />
                            <span>Financial Health</span>
                        </div>
                        <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-slate-900 text-[#C6F24D] font-bold">Score</span>
                    </a>
                    <a href="{{ route('settings') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('settings*') ? 'bg-[#C6F24D] text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/70' }}">
                        <x-icon name="settings" class="w-4 h-4" />
                        <span>Pengaturan Akun</span>
                    </a>
                    <button @click="mobileSidebarOpen = false; setTimeout(() => $dispatch('open-finance-theory'), 120)" 
                            type="button"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-950 hover:bg-slate-100/70 transition-all cursor-pointer text-left">
                        <x-icon name="book-open" class="w-4 h-4 text-indigo-600" />
                        <span>Fondasi Teori Keuangan</span>
                    </button>
                    <button @click="mobileSidebarOpen = false; setTimeout(() => $dispatch('open-interactive-tour'), 120)" 
                            type="button"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-950 hover:bg-slate-100/70 transition-all cursor-pointer text-left">
                        <x-icon name="compass" class="w-4 h-4 text-teal-600" />
                        <span>Tur Interaktif</span>
                    </button>
                    <button @click="mobileSidebarOpen = false; setTimeout(() => $dispatch('open-tutorial'), 120)" 
                            type="button"
                            class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-950 hover:bg-slate-100/70 transition-all cursor-pointer text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                        <span>Tutorial Lengkap</span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Sidebar Footer Quick CTA -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <button @click="$dispatch('open-quick-add')"
                    id="tour-quick-add"
                    class="w-full flex items-center justify-center gap-2 py-2.5 px-3.5 rounded-2xl bg-slate-950 hover:bg-slate-800 text-white font-bold text-xs shadow-md active-tap transition-all cursor-pointer">
                <span class="w-5 h-5 rounded-full bg-[#C6F24D] text-slate-950 flex items-center justify-center font-black text-xs">+</span>
                <span>Quick Transaction</span>
                <kbd class="hidden xl:inline-block px-1.5 py-0.5 text-[9px] font-mono bg-slate-800 rounded text-slate-400 ml-1">Ctrl+K</kbd>
            </button>
        </div>
    </aside>

    <!-- Main View Area -->
    <div class="flex-1 flex flex-col min-w-0 pb-24 md:pb-0 overflow-hidden bg-[#F8F9FA]">
               <!-- Top Navbar (Clean Premium FinTech Style) -->
        <header class="h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/70 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 shadow-[0_1px_6px_rgba(0,0,0,0.02)]">
            
            @php
                $currentUser = auth()->user() ?? \App\Models\User::first();
                $userName = $currentUser->name ?? 'Zaki';
                $userEmail = $currentUser->email ?? 'zaki@portofinance.test';
                $userInitial = strtoupper(substr($userName, 0, 1));
            @endphp

            <!-- Left: Mobile Toggle & Brand (or Desktop Welcome) -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <button @click="mobileSidebarOpen = true" 
                        type="button"
                        class="md:hidden w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-700 hover:text-slate-950 flex items-center justify-center transition-colors cursor-pointer active-tap shadow-2xs"
                        aria-label="Buka Menu">
                    <x-icon name="menu" class="w-4 h-4" />
                </button>

                <!-- Mobile Brand Header (Logo + PortoFinance) -->
                <a href="{{ route('dashboard') }}" class="flex md:hidden items-center gap-2">
                    <img src="{{ asset('images/logo.svg') }}" class="w-7 h-7 rounded-lg object-contain shadow-2xs" alt="PortoFinance Logo">
                    <span class="font-extrabold text-sm tracking-tight text-slate-900 leading-none">Porto<span class="text-teal-600">Finance</span></span>
                </a>

                <!-- Desktop Greeting / Title -->
                <div class="hidden md:flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-400">Freelancer Workspace &bull;</span>
                    <span class="text-sm font-extrabold text-slate-900">Halo, {{ explode(' ', $userName)[0] }}! 👋</span>
                </div>
            </div>

            <!-- Right: Available Money Hero Pill, Live Notifications, and Far-Right User Profile -->
            <div class="flex items-center gap-2 sm:gap-2.5">
                {{-- Live Available Money Bar (auto-updates on transaction-saved) --}}
                <div id="tour-available-money">
                    <livewire:available-money-bar />
                </div>

                {{-- Live Notification Dropdown Bell --}}
                <livewire:notification-dropdown />

                {{-- Far-Right User Profile Dropdown (Logo Orang / Avatar) --}}
                <div class="relative" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false">
                    <!-- User Avatar Button -->
                    <button @click="userMenuOpen = !userMenuOpen" 
                            id="tour-user-profile"
                            type="button"
                            class="w-9 h-9 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] flex items-center justify-center font-black text-xs shadow-2xs cursor-pointer active-tap transition-transform hover:scale-105"
                            title="Profil & Detail Akun"
                            aria-label="Menu Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#C6F24D]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </button>

                    <!-- User Detail Popover Dropdown -->
                    <div x-show="userMenuOpen" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute right-0 mt-2.5 w-64 bg-white border border-slate-200/80 rounded-2xl shadow-2xl overflow-hidden z-50 p-2"
                         x-cloak>
                        
                        <!-- User Card Header -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 mb-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#C6F24D]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-extrabold text-xs text-slate-900 truncate">{{ $userName }}</h4>
                                    <span class="text-[10px] text-slate-400 truncate block">{{ $userEmail }}</span>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-slate-200/60 flex items-center justify-between">
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400">Role</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-[#C6F24D] text-slate-950">
                                    FREELANCER PRO
                                </span>
                            </div>
                        </div>

                        <!-- Menu Links -->
                        <div class="space-y-1 text-xs">
                            <a href="{{ route('settings') }}" 
                               @click="userMenuOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition-colors">
                                <x-icon name="settings" class="w-4 h-4 text-slate-500" />
                                <span>Pengaturan Akun</span>
                            </a>

                            <a href="{{ route('analytics') }}" 
                               @click="userMenuOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition-colors">
                                <x-icon name="activity" class="w-4 h-4 text-slate-500" />
                                <span>Financial Health Score</span>
                            </a>

                            <button @click="userMenuOpen = false; setTimeout(() => $dispatch('open-finance-theory'), 100)" 
                                    type="button"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition-colors text-left cursor-pointer">
                                <x-icon name="book-open" class="w-4 h-4 text-indigo-600" />
                                <span>Fondasi Teori Keuangan</span>
                            </button>

                            <button @click="userMenuOpen = false; setTimeout(() => $dispatch('open-interactive-tour'), 100)" 
                                    type="button"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition-colors text-left cursor-pointer">
                                <x-icon name="compass" class="w-4 h-4 text-teal-600" />
                                <span>Mulai Tur Interaktif</span>
                            </button>

                            <button @click="userMenuOpen = false; setTimeout(() => $dispatch('open-tutorial'), 100)" 
                                    type="button"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold text-slate-700 hover:text-slate-950 hover:bg-slate-100 transition-colors text-left cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                                <span>Tutorial Lengkap</span>
                            </button>
                        </div>

                        <!-- Logout Button -->
                        <div class="pt-1.5 mt-1.5 border-t border-slate-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl font-bold text-rose-600 hover:bg-rose-50 transition-colors text-left cursor-pointer text-xs">
                                    <x-icon name="log-out" class="w-4 h-4 text-rose-500" />
                                    <span>Keluar / Logout</span>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </header>

        <!-- Main Viewport -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto pb-28 md:pb-10">
            {{ $slot }}
        </main>
    </div>

    <!-- MOBILE FLOATING BOTTOM NAVIGATION BAR (FinTech Style) -->
    <div class="fixed bottom-3 inset-x-3 z-40 md:hidden">
        <nav class="bg-slate-950 text-white rounded-3xl shadow-2xl border border-slate-800/80 px-3 py-2 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 py-1 px-3 rounded-2xl transition-all {{ request()->routeIs('dashboard') ? 'text-[#C6F24D] font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <x-icon name="layout-dashboard" class="w-5 h-5" />
                <span class="text-[9px] font-bold">Home</span>
            </a>
            
            <a href="{{ route('transactions') }}" class="flex flex-col items-center gap-1 py-1 px-3 rounded-2xl transition-all {{ request()->routeIs('transactions*') ? 'text-[#C6F24D] font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <x-icon name="receipt" class="w-5 h-5" />
                <span class="text-[9px] font-bold">Trans</span>
            </a>
            
            <!-- Mobile Floating Center Action Button -->
            <button @click="$dispatch('open-quick-add')" id="tour-quick-add-mobile" class="w-11 h-11 rounded-2xl bg-[#C6F24D] text-slate-950 flex items-center justify-center font-black shadow-lg shadow-[#C6F24D]/30 active-tap transition-transform">
                <x-icon name="plus" class="w-6 h-6 text-slate-950" strokeWidth="2.5" />
            </button>

            <a href="{{ route('wishlists') }}" id="tour-nav-wishlists-mobile" class="flex flex-col items-center gap-1 py-1 px-3 rounded-2xl transition-all {{ request()->routeIs('wishlists*') ? 'text-[#C6F24D] font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <x-icon name="shopping-bag" class="w-5 h-5" />
                <span class="text-[9px] font-bold">Wishlist</span>
            </a>
            
            <a href="{{ route('projects') }}" id="tour-nav-projects-mobile" class="flex flex-col items-center gap-1 py-1 px-3 rounded-2xl transition-all {{ request()->routeIs('projects*') ? 'text-[#C6F24D] font-bold' : 'text-slate-400 hover:text-slate-200' }}">
                <x-icon name="briefcase" class="w-5 h-5" />
                <span class="text-[9px] font-bold">Project</span>
            </a>
        </nav>
    </div>

    <!-- Global Livewire Quick-Add Transaction Modal (< 10 detik) -->
    <livewire:quick-transaction-modal />

    <!-- Global Notification Toast System -->
    <livewire:notification-toast />

    <!-- Global Financial Theory & Foundation Briefing Modal -->
    <x-finance-theory-modal />

    <!-- Global Quick Tutorial Modal -->
    <x-quick-tutorial-modal />

    <!-- Global Financial Onboarding Setup Wizard (Muncul sebelum Tour) -->
    <livewire:onboarding-wizard />

    <!-- Global Custom In-Website Confirmation Dialog (Replaces Native Browser Says Popups) -->
    <x-confirm-dialog />

    <!-- Global Interactive Onboarding Tour (with spotlight & pointer arrow) -->
    <x-interactive-tour />

    @livewireScripts
</body>
</html>
