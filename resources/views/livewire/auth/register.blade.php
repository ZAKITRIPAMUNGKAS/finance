<div id="register-container" 
     x-data="{ 
         showPass: false,
         showConfirmPass: false,
         isShaking: false,
         
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
         }
     }"
     class="w-full flex-1 flex flex-col justify-between h-full relative anim-page-enter">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  HARMONIOUS FLOATING ORNAMENTS (AESTHETIC & NON-COLLIDING) -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="absolute inset-0 pointer-events-none overflow-visible -z-10 select-none" aria-hidden="true">
        <!-- Floating Ornament 1: Sparkles (Left Margin, Level with Subtitle) -->
        <div class="absolute top-28 -left-4 sm:-left-8 px-2.5 py-1 rounded-xl bg-white/95 backdrop-blur-xs border-2 border-slate-950 shadow-[2px_2px_0px_#000] flex items-center gap-1 font-black font-mono text-[11px] rotate-[-10deg] anim-float-1">
            <x-icon name="sparkles" class="w-3.5 h-3.5 text-slate-950" strokeWidth="2.5" />
            <span class="text-[9px] font-black tracking-wider">NEW</span>
        </div>

        <!-- Floating Ornament 2: SAFE Shield (Right Margin, Level with Subtitle) -->
        <div class="absolute top-32 -right-4 sm:-right-8 px-2.5 py-1 rounded-xl bg-white/95 backdrop-blur-xs border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[8deg] flex items-center gap-1.5 anim-float-2">
            <x-icon name="shield-check" class="w-3.5 h-3.5 text-emerald-600" strokeWidth="2.5" />
            <span class="text-[9px] font-black font-mono text-slate-900 tracking-wider">SAFE</span>
        </div>

        <!-- Floating Ornament 3: Checkmark Coin (Left Margin, Beside Submit) -->
        <div class="absolute bottom-28 -left-4 sm:-left-7 w-7 h-7 rounded-full bg-[#C6F24D] border-2 border-slate-950 shadow-[2px_2px_0px_#000] flex items-center justify-center rotate-[15deg] anim-float-3">
            <x-icon name="check" class="w-3.5 h-3.5 text-slate-950" strokeWidth="3" />
        </div>

        <!-- Floating Ornament 4: Cash Badge (Right Margin, Beside Submit) -->
        <div class="absolute bottom-24 -right-4 sm:-right-7 px-2 py-0.5 rounded-lg bg-[#C6F24D] border-2 border-slate-950 shadow-[2px_2px_0px_#000] rotate-[-6deg] flex items-center gap-1 anim-float-1">
            <x-icon name="credit-card" class="w-3 h-3 text-slate-950" strokeWidth="2.5" />
            <span class="text-[8px] font-black font-mono text-slate-900 tracking-wider">CASH</span>
        </div>
    </div>

    <!-- ── TOP HEADER / NAV ──────────────────────────────────── -->
    <header class="flex items-center justify-between pt-1 pb-3 shrink-0">
        <!-- Back to Login Button -->
        <a href="{{ route('login') }}" 
           class="w-10 h-10 rounded-full bg-slate-100 hover:bg-slate-200 active:scale-95 flex items-center justify-center text-slate-700 transition-all shadow-2xs">
            <x-icon name="arrow-left" class="w-4 h-4 text-slate-900" strokeWidth="2.5" />
        </a>

        <!-- Brand Center -->
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
            <img src="{{ asset('images/logo.svg') }}" class="w-8 h-8 object-contain group-hover:scale-105 transition-transform" alt="PortoFinance Logo">
            <div class="leading-tight text-left">
                <span class="font-black text-sm text-slate-950 tracking-tight block">Porto<span class="text-teal-700">Finance</span></span>
                <span class="text-[8px] font-mono font-bold uppercase tracking-wider text-slate-400 block -mt-0.5">Financial OS</span>
            </div>
        </a>

        <!-- Spacer for Symmetry -->
        <div class="w-10"></div>
    </header>

    <!-- ── MAIN GREETING & FORM ──────────────────────────────── -->
    <main class="space-y-4 sm:space-y-5 my-auto py-2">
        
        <!-- Header Greeting with Aesthetic Pill Badge -->
        <div class="text-center space-y-1.5">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#EBFAD2] text-slate-900 border border-[#D4F66C] text-[10px] font-mono font-extrabold uppercase tracking-wider shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-600 animate-pulse"></span>
                <span>Pro Financial Engine</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-950 tracking-tight leading-tight">
                Buat Akun Baru
            </h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 max-w-xs sm:max-w-sm mx-auto leading-relaxed">
                Mulai kelola keuangan & cash flow Anda secara rapi dan profesional.
            </p>
        </div>

        <!-- Global Auth Error Feedback Banner -->
        @if ($errors->any())
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-2.5 text-xs text-rose-700 font-bold anim-shake shadow-xs">
            <x-icon name="alert-circle" class="w-4 h-4 text-rose-500 shrink-0" strokeWidth="2.5" />
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <!-- Google OAuth Instant Register Button -->
        <div>
            <a href="{{ route('google.redirect') }}" 
               @click="sessionStorage.setItem('pf_just_logged_in', 'true')"
               class="w-full py-3 sm:py-3.5 px-4 rounded-2xl bg-white hover:bg-slate-50 active:scale-[0.98] text-slate-900 font-bold text-xs sm:text-sm border-2 border-slate-200 hover:border-slate-400 shadow-2xs transition-all flex items-center justify-center gap-3 cursor-pointer">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Daftar dengan Google</span>
            </a>
        </div>

        <!-- Divider -->
        <div class="relative flex items-center justify-center py-0.5">
            <div class="w-full border-t border-slate-200"></div>
            <span class="absolute bg-slate-50 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                atau daftar dengan email
            </span>
        </div>

        <!-- Register Form -->
        <form wire:submit="register" @submit="sessionStorage.setItem('pf_just_logged_in', 'true')" class="space-y-3.5" :class="isShaking ? 'anim-shake' : ''">
            
            <!-- Full Name Input -->
            <div class="space-y-1.5">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 ml-1">Nama Lengkap</label>
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="user" class="w-4 h-4" strokeWidth="2" />
                    </span>
                    <input type="text" 
                           wire:model="name" 
                           placeholder="Nama Anda" 
                           autocomplete="name"
                           class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-2xl bg-white border @error('name') border-rose-400 bg-rose-50/20 @else border-slate-200 @enderror text-xs sm:text-sm font-bold text-slate-950 focus:outline-none focus:border-slate-950 focus:ring-1 focus:ring-slate-950 shadow-2xs transition-all placeholder:text-slate-400 placeholder:font-normal">
                </div>
                @error('name') 
                    <span class="text-[11px] text-rose-500 font-bold px-2 block">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Email Address Input -->
            <div class="space-y-1.5">
                <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 ml-1">Email</label>
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="mail" class="w-4 h-4" strokeWidth="2" />
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

            <!-- Password Inputs (Responsive Grid layout) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3">
                <!-- Create Password -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 ml-1">Kata Sandi</label>
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <x-icon name="lock" class="w-4 h-4" strokeWidth="2" />
                        </span>
                        <input :type="showPass ? 'text' : 'password'" 
                               wire:model="password" 
                               placeholder="Min. 6 karakter" 
                               autocomplete="new-password"
                               class="w-full pl-10 pr-10 py-2.5 sm:py-3 rounded-2xl bg-white border @error('password') border-rose-400 bg-rose-50/20 @else border-slate-200 @enderror text-xs sm:text-sm font-bold text-slate-950 focus:outline-none focus:border-slate-950 focus:ring-1 focus:ring-slate-950 shadow-2xs transition-all placeholder:text-slate-400 placeholder:font-normal">
                        
                        <button type="button" 
                                @click="showPass = !showPass" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer p-1"
                                title="Tampilkan / Sembunyikan Kata Sandi">
                            <span x-show="!showPass" class="flex items-center">
                                <x-icon name="eye" class="w-4 h-4" strokeWidth="2" />
                            </span>
                            <span x-show="showPass" x-cloak class="flex items-center">
                                <x-icon name="eye-off" class="w-4 h-4" strokeWidth="2" />
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 ml-1">Ulangi Sandi</label>
                    <div class="relative w-full">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <x-icon name="shield-check" class="w-4 h-4" strokeWidth="2" />
                        </span>
                        <input :type="showConfirmPass ? 'text' : 'password'" 
                               wire:model="password_confirmation" 
                               placeholder="Ulangi sandi" 
                               autocomplete="new-password"
                               class="w-full pl-10 pr-10 py-2.5 sm:py-3 rounded-2xl bg-white border border-slate-200 text-xs sm:text-sm font-bold text-slate-950 focus:outline-none focus:border-slate-950 focus:ring-1 focus:ring-slate-950 shadow-2xs transition-all placeholder:text-slate-400 placeholder:font-normal">
                        
                        <button type="button" 
                                @click="showConfirmPass = !showConfirmPass" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer p-1"
                                title="Tampilkan / Sembunyikan Konfirmasi Sandi">
                            <span x-show="!showConfirmPass" class="flex items-center">
                                <x-icon name="eye" class="w-4 h-4" strokeWidth="2" />
                            </span>
                            <span x-show="showConfirmPass" x-cloak class="flex items-center">
                                <x-icon name="eye-off" class="w-4 h-4" strokeWidth="2" />
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            @error('password') 
                <span class="text-[11px] text-rose-500 font-bold px-2 block">{{ $message }}</span> 
            @enderror

            <!-- Submit Registration Button -->
            <div class="pt-2">
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full py-4 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-[0.98] text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border-2 border-slate-950">
                    <span wire:loading.remove class="flex items-center gap-2">
                        <span>Daftar Akun Baru</span>
                        <x-icon name="arrow-right" class="w-4 h-4" strokeWidth="2.5" />
                    </span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mendaftarkan akun...</span>
                    </span>
                </button>
            </div>
        </form>

        <!-- Security & Trust Micro-Strip -->
        <div class="flex items-center justify-center gap-3 pt-1 text-[10px] font-bold text-slate-400">
            <span class="flex items-center gap-1">
                <x-icon name="shield-check" class="w-3 h-3 text-emerald-600" />
                <span>256-Bit SSL</span>
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <x-icon name="sparkles" class="w-3 h-3 text-teal-600" />
                <span>Auto Rp 0 Starter</span>
            </span>
        </div>

    </main>

    <!-- ── FOOTER LOGIN LINK ─────────────────────────────────── -->
    <footer class="pt-3 pb-2 text-center border-t border-slate-200/60 shrink-0">
        <p class="text-xs text-slate-500 font-medium">
            Sudah punya akun? 
            <a href="{{ route('login') }}"
               class="font-black text-slate-950 hover:text-teal-700 underline underline-offset-2 ml-1 cursor-pointer transition-colors">
                Masuk di sini &rarr;
            </a>
        </p>
    </footer>

</div>
