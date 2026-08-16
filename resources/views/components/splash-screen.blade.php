<!-- PortoFinance Unified Splash Screen (Identical to Welcome Splash) -->
<div id="app-splash-screen"
     x-data="{ 
         showSplash: true,
         init() {
             // Check if splash was already shown in this session (unless forced via ?splash=1 or just logged in)
             const urlParams = new URLSearchParams(window.location.search);
             const forceSplash = urlParams.has('splash');
             const justLoggedIn = sessionStorage.getItem('pf_just_logged_in');

             if (!forceSplash && sessionStorage.getItem('pf_app_splash_shown') && !justLoggedIn) {
                 this.showSplash = false;
                 setTimeout(() => window.dispatchEvent(new CustomEvent('splash-completed')), 200);
                 return;
             }

             // Clear temporary flag
             sessionStorage.removeItem('pf_just_logged_in');
             sessionStorage.setItem('pf_app_splash_shown', 'true');

             // Smooth reveal for 1.2s then fade out
             setTimeout(() => {
                 this.showSplash = false;
                 setTimeout(() => window.dispatchEvent(new CustomEvent('splash-completed')), 450);
             }, 1200);
         }
      }" 
     x-show="showSplash"
     x-transition:leave="transition-all ease-[cubic-bezier(0.22,1,0.36,1)] duration-500"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-98 pointer-events-none"
     class="fixed inset-0 z-[99999] bg-white flex flex-col items-center justify-center p-6 text-slate-950 select-none overflow-hidden"
     x-cloak>

    <div class="flex flex-col items-center text-center px-6">

        <!-- Compact Logo (Borderless with Soft Lime Aura) -->
        <div class="relative mb-4">
            <div class="absolute -inset-3 rounded-full bg-[#C6F24D]/30 blur-xl"></div>
            <img src="{{ asset('images/logo.svg') }}" class="relative w-14 h-14 sm:w-16 sm:h-16 object-contain" alt="PortoFinance Logo">
        </div>

        <!-- Brand Typography -->
        <div class="space-y-1">
            <h1 class="text-xl sm:text-2xl leading-none font-black tracking-tight text-slate-950">
                Porto<span class="text-teal-700">Finance</span>
            </h1>
            <p class="text-[9px] sm:text-[10px] font-mono font-bold tracking-[0.2em] uppercase text-slate-400">
                Freelancer Financial OS
            </p>
        </div>

        <!-- Animated Dot Wave Loading Indicator -->
        <div class="mt-6 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-slate-950 anim-dot-1"></span>
            <span class="w-2 h-2 rounded-full bg-teal-700 anim-dot-2"></span>
            <span class="w-2 h-2 rounded-full bg-[#C6F24D] anim-dot-3"></span>
        </div>

    </div>

</div>
