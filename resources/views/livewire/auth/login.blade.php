<div id="login-container" 
     x-data="{ 
         showPass: false,
         isShaking: false,
         
         init() {
             window.addEventListener('login-failed', () => {
                 this.triggerShake();
             });
         },

         triggerShake() {
             this.isShaking = true;
             setTimeout(() => {
                 this.isShaking = false;
             }, 500);
         }
     }"
     class="w-full flex-1 flex flex-col justify-between max-w-md mx-auto relative anim-page-enter">
    
    <!-- ── TOP HEADER / NAV ──────────────────────────────────── -->
    <header class="flex items-center justify-between pb-3 sm:pb-5 shrink-0">
        <!-- Back to Welcome Page -->
        <a href="{{ url('/') }}" 
           class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-slate-100 hover:bg-slate-200 active:scale-95 flex items-center justify-center text-slate-700 transition-all shadow-2xs">
            <x-icon name="arrow-left" class="w-4 h-4 text-slate-900" strokeWidth="2.5" />
        </a>

        <!-- Brand Center -->
        <a href="{{ url('/') }}" class="flex items-center gap-2 group">
            <img src="{{ asset('images/logo.svg') }}" class="w-7 h-7 sm:w-8 sm:h-8 object-contain group-hover:scale-105 transition-transform" alt="PortoFinance Logo">
            <div class="leading-tight text-left">
                <span class="font-black text-xs sm:text-sm text-slate-950 tracking-tight block">Porto<span class="text-teal-700">Finance</span></span>
                <span class="text-[8px] font-mono font-bold uppercase tracking-wider text-slate-400 block -mt-0.5">Financial OS</span>
            </div>
        </a>

        <!-- Spacer for Symmetry -->
        <div class="w-9 sm:w-10"></div>
    </header>

    <!-- ── MAIN GREETING & FORM ──────────────────────────────── -->
    <main class="space-y-4 my-auto py-1">
        
        <!-- Header Greeting -->
        <div class="text-center space-y-1">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight leading-tight">
                Selamat Datang! 👋
            </h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 max-w-xs mx-auto leading-relaxed">
                Masuk ke workspace keuangan Anda melalui Google atau Email.
            </p>
        </div>

        <!-- Global Auth Error Feedback Banner -->
        @if ($errors->any())
        <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-2.5 text-xs text-rose-700 font-bold anim-shake shadow-xs">
            <x-icon name="alert-circle" class="w-4 h-4 text-rose-500 shrink-0" strokeWidth="2.5" />
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <!-- Google OAuth Instant Login Button -->
        <div class="pt-1">
            <a href="{{ route('google.redirect') }}" 
               @click="sessionStorage.setItem('pf_just_logged_in', 'true')"
               class="w-full py-3 px-4 rounded-2xl bg-white hover:bg-slate-50 active:scale-[0.98] text-slate-900 font-bold text-xs sm:text-sm border-2 border-slate-200 hover:border-slate-400 shadow-2xs transition-all flex items-center justify-center gap-3 cursor-pointer">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Lanjutkan dengan Google</span>
            </a>
        </div>

        <!-- Divider -->
        <div class="relative flex items-center justify-center py-1">
            <div class="w-full border-t border-slate-200"></div>
            <span class="absolute bg-white px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                atau email & kata sandi
            </span>
        </div>

        <!-- Login Form -->
        <form wire:submit="login" @submit="sessionStorage.setItem('pf_just_logged_in', 'true')" class="space-y-3.5">
            
            <!-- Email Input Field -->
            <div class="space-y-1">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 ml-1">Email</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400">
                        <x-icon name="users" class="w-4 h-4" strokeWidth="2" />
                    </span>
                    <input type="email" 
                           wire:model="email" 
                           placeholder="nama@email.com" 
                           autocomplete="email"
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-2xl bg-white border @error('email') border-rose-400 bg-rose-50/20 @else border-slate-200 @enderror text-xs sm:text-sm font-bold text-slate-950 focus:outline-none focus:border-slate-950 focus:ring-1 focus:ring-slate-950 shadow-2xs transition-all placeholder:text-slate-400 placeholder:font-normal">
                </div>
                @error('email') 
                    <span class="text-[11px] text-rose-500 font-bold px-2 block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Password Input Field -->
            <div class="space-y-1" :class="isShaking ? 'anim-shake' : ''">
                <div class="flex items-center justify-between ml-1">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">Kata Sandi</label>
                </div>
                <div class="relative">
                    <span class="absolute left-3.5 top-3 text-slate-400">
                        <x-icon name="lock" class="w-4 h-4" strokeWidth="2" />
                    </span>
                    <input :type="showPass ? 'text' : 'password'" 
                           wire:model="password" 
                           placeholder="Masukkan kata sandi" 
                           autocomplete="current-password"
                           class="w-full pl-10 pr-11 py-2.5 sm:py-3 rounded-2xl bg-white border @error('password') border-rose-400 bg-rose-50/20 @else border-slate-200 @enderror text-xs sm:text-sm font-bold text-slate-950 focus:outline-none focus:border-slate-950 focus:ring-1 focus:ring-slate-950 shadow-2xs transition-all placeholder:text-slate-400 placeholder:font-normal">
                    
                    <button type="button" 
                            @click="showPass = !showPass" 
                            class="absolute right-3.5 top-2.5 text-slate-400 hover:text-slate-700 cursor-pointer transition-colors p-1">
                        <template x-if="!showPass">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </template>
                        <template x-if="showPass">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                        </template>
                    </button>
                </div>
                @error('password') 
                    <span class="text-[11px] text-rose-500 font-bold px-2 block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Primary Log In Button -->
            <div class="pt-1.5">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full py-3.5 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-[0.98] text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border-2 border-slate-950">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <span>Masuk ke Akun</span>
                        <x-icon name="arrow-right" class="w-4 h-4" strokeWidth="2.5" />
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memverifikasi...</span>
                    </span>
                </button>
            </div>
        </form>

    </main>

    <!-- ── FOOTER REGISTER LINK ──────────────────────────────── -->
    <footer class="pt-3 pb-2 text-center border-t border-slate-100 shrink-0">
        <p class="text-xs text-slate-500 font-medium">
            Belum punya akun? 
            <a href="{{ route('register') }}"
               class="font-black text-slate-950 hover:text-teal-700 underline underline-offset-2 ml-1 cursor-pointer transition-colors">
                Daftar Sekarang &rarr;
            </a>
        </p>
    </footer>

</div>
