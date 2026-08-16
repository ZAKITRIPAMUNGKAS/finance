<div id="register-container" 
     x-data="{ 
         showPass: false,
         showConfirmPass: false,
         isShaking: false,
         isExiting: false,
         exitDirection: 'right',
         
         init() {
             @if ($errors->any())
                 this.triggerShake();
             @endif
         },

         triggerShake() {
             this.isShaking = true;
             setTimeout(() => {
                 this.isShaking = false;
             }, 500);
         },

         navigate(url, direction = 'right') {
             this.isExiting = true;
             this.exitDirection = direction;
             const container = document.getElementById('register-container');
             if (container) {
                 container.classList.remove('anim-page-enter');
                 container.classList.add(direction === 'right' ? 'anim-page-exit-right' : 'anim-page-exit-left');
             }
             setTimeout(() => {
                 window.location.href = url;
             }, 220);
         }
     }"
     class="w-full flex-1 flex flex-col justify-between max-w-sm sm:max-w-md mx-auto relative px-2 sm:px-0 anim-page-enter">
    
    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  1. FLOATING BRAND ORNAMENTS (MATCHING ONBOARDING THEME)   -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden -z-10 select-none" aria-hidden="true">
        <!-- Floating 1: Sparkles Top Left -->
        <div class="absolute -top-1 -left-2 px-2.5 py-1 rounded-2xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] flex items-center gap-1 font-black font-mono text-xs rotate-[-10deg] anim-float-1">
            <x-icon name="sparkles" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[9px] font-black tracking-wider">NEW</span>
        </div>

        <!-- Floating 2: Safe & Encrypted Top Right -->
        <div class="absolute -top-1 -right-2 px-2.5 py-1 rounded-2xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[8deg] flex items-center gap-1.5 anim-float-2">
            <x-icon name="shield-check" class="w-3.5 h-3.5 text-emerald-600" strokeWidth="2.5" />
            <span class="text-[9px] font-black font-mono text-slate-900 tracking-wider">SAFE</span>
        </div>

        <!-- Floating 3: Checkmark Coin Middle Left -->
        <div class="absolute top-1/2 -left-4 w-7 h-7 rounded-full bg-[#C6F24D] border-2 border-slate-950 shadow-[2px_2px_0px_#000] flex items-center justify-center rotate-[15deg] anim-float-3">
            <x-icon name="check" class="w-3.5 h-3.5 text-slate-950" strokeWidth="3" />
        </div>

        <!-- Floating 4: Card Tag Middle Right -->
        <div class="absolute top-1/3 -right-3 px-2 py-1 rounded-xl bg-white border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[-10deg] flex items-center gap-1 anim-float-1">
            <x-icon name="credit-card" class="w-3 h-3 text-slate-950" strokeWidth="2.5" />
            <span class="text-[8px] font-black font-mono text-slate-900">CARD</span>
        </div>
    </div>

    <!-- ── TOP HEADER / NAV ──────────────────────────────────── -->
    <header class="flex items-center justify-between pt-2 pb-3 shrink-0">
        <!-- Back to Login Button -->
        <button type="button" 
                @click="navigate('{{ route('login') }}', 'right')"
                class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 active:scale-95 flex items-center justify-center text-slate-700 transition-all cursor-pointer shadow-2xs">
            <x-icon name="arrow-left" class="w-4 h-4 text-slate-900" strokeWidth="2.5" />
        </button>

        <!-- Brand Center (Clean Transparent Logo) -->
        <a href="{{ url('/') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.svg') }}" class="w-8 h-8 object-contain" alt="PortoFinance Logo">
            <div class="leading-tight text-left">
                <span class="font-black text-sm text-slate-950 tracking-tight block">Porto<span class="text-teal-700">Finance</span></span>
                <span class="text-[8px] font-mono font-bold uppercase tracking-wider text-slate-400 block -mt-0.5">Freelancer OS</span>
            </div>
        </a>

        <!-- Spacer for Symmetry -->
        <div class="w-10"></div>
    </header>

    <!-- ── MAIN GREETING & FORM ──────────────────────────────── -->
    <main class="space-y-4 my-auto py-1">
        
        <!-- Header Greeting (1:1 High Fidelity) -->
        <div class="text-center space-y-1 anim-fade-up">
            <h1 class="text-3xl sm:text-4xl font-black text-slate-950 tracking-tight leading-tight">
                Create Account
            </h1>
            <p class="text-xs font-semibold text-slate-500 max-w-xs mx-auto leading-relaxed">
                Start managing your freelance finances and cash flow in minutes.
            </p>
        </div>

        <!-- Global Auth Error Feedback Banner with Shake Trigger -->
        @if ($errors->any())
        <div class="p-3 bg-rose-50 border-2 border-rose-200 rounded-2xl flex items-center gap-2.5 text-xs text-rose-700 font-bold anim-shake shadow-xs">
            <x-icon name="alert-circle" class="w-4 h-4 text-rose-500 shrink-0" strokeWidth="2.5" />
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <!-- Register Form -->
        <form wire:submit="register" @submit="sessionStorage.setItem('pf_just_logged_in', 'true')" class="space-y-3.5" :class="isShaking ? 'anim-shake' : ''">
            
            <!-- Full Name Input -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 ml-1">Full Name</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-slate-400">
                        <x-icon name="users" class="w-4 h-4" strokeWidth="2" />
                    </span>
                    <input type="text" 
                           wire:model="name" 
                           placeholder="Nama Lengkap" 
                           autocomplete="name"
                           class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white border @error('name') border-rose-400 bg-rose-50/20 @else border-slate-200 @enderror text-sm font-bold text-slate-950 focus:outline-none focus:border-slate-950 shadow-xs transition-all placeholder:text-slate-400 placeholder:font-medium">
                </div>
                @error('name') 
                    <span class="text-[11px] text-rose-500 font-bold px-2 block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Email Address Input -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-slate-700 ml-1">Email Address</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </span>
                    <input type="email" 
                           wire:model="email" 
                           placeholder="nama@email.com" 
                           autocomplete="email"
                           class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white border @error('email') border-rose-400 bg-rose-50/20 @else border-slate-200 @enderror text-sm font-bold text-slate-950 focus:outline-none focus:border-slate-950 shadow-xs transition-all placeholder:text-slate-400 placeholder:font-medium">
                </div>
                @error('email') 
                    <span class="text-[11px] text-rose-500 font-bold px-2 block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Password Inputs (Grid layout) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Create Password -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 ml-1">Password</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-slate-400">
                            <x-icon name="lock" class="w-4 h-4" strokeWidth="2" />
                        </span>
                        <input :type="showPass ? 'text' : 'password'" 
                               wire:model="password" 
                               placeholder="Kata sandi" 
                               autocomplete="new-password"
                               class="w-full pl-10 pr-10 py-3 rounded-2xl bg-white border @error('password') border-rose-400 bg-rose-50/20 @else border-slate-200 @enderror text-sm font-bold text-slate-950 focus:outline-none focus:border-slate-950 shadow-xs transition-all placeholder:text-slate-400 placeholder:font-medium">
                        
                        <button type="button" 
                                @click="showPass = !showPass" 
                                class="absolute right-3 top-3 text-slate-400 hover:text-slate-700 cursor-pointer p-0.5">
                            <template x-if="!showPass">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </template>
                            <template x-if="showPass">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700 ml-1">Konfirmasi</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-3 text-slate-400">
                            <x-icon name="lock" class="w-4 h-4" strokeWidth="2" />
                        </span>
                        <input :type="showConfirmPass ? 'text' : 'password'" 
                               wire:model="password_confirmation" 
                               placeholder="Ulangi sandi" 
                               autocomplete="new-password"
                               class="w-full pl-10 pr-10 py-3 rounded-2xl bg-white border border-slate-200 text-sm font-bold text-slate-950 focus:outline-none focus:border-slate-950 shadow-xs transition-all placeholder:text-slate-400 placeholder:font-medium">
                        
                        <button type="button" 
                                @click="showConfirmPass = !showConfirmPass" 
                                class="absolute right-3 top-3 text-slate-400 hover:text-slate-700 cursor-pointer p-0.5">
                            <template x-if="!showConfirmPass">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </template>
                            <template x-if="showConfirmPass">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
            @error('password') 
                <span class="text-[11px] text-rose-500 font-bold px-2 block">{{ $message }}</span> 
            @enderror

            <!-- Primary Sign Up Button (Signature Neo-Fintech Lime) -->
            <div class="pt-2">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full py-4 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-[0.98] text-slate-950 font-black text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border-2 border-slate-950">
                    <span wire:loading.remove>Sign Up</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mendaftarkan akun...</span>
                    </span>
                    <x-icon name="arrow-right" class="w-4 h-4" strokeWidth="2.5" />
                </button>
            </div>
        </form>

    </main>

    <!-- ── FOOTER SIGN IN LINK ───────────────────────────────── -->
    <footer class="pt-4 pb-2 text-center border-t border-slate-100 shrink-0">
        <p class="text-xs text-slate-500 font-medium">
            Already have an account? 
            <button type="button"
                    @click="navigate('{{ route('login') }}', 'right')"
                    class="font-black text-slate-950 hover:text-teal-700 underline underline-offset-2 ml-1 cursor-pointer transition-colors">
                Sign in &rarr;
            </button>
        </p>
    </footer>

</div>
