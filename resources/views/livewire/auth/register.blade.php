<div id="register-container" 
     x-data="{ 
         showPass: false,
         showConfirmPass: false,
         isShaking: false,
         isAuthenticating: false,
         
         init() {
             @if ($errors->any())
                 this.isAuthenticating = false;
                 this.triggerShake();
             @endif
         },

         startAuth() {
             this.isAuthenticating = true;
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
    <!--  FULLSCREEN REGISTRATION & INITIALIZATION LOADING OVERLAY   -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div x-show="isAuthenticating" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-lg flex flex-col items-center justify-center p-6 text-center select-none">
        
        <!-- Ambient Glowing Aura -->
        <div class="absolute w-72 h-72 rounded-full bg-[#C6F24D]/25 blur-3xl anim-glow pointer-events-none"></div>

        <div class="relative z-10 max-w-sm w-full space-y-6 flex flex-col items-center">
            
            <!-- Animated Icon Core Matrix -->
            <div class="relative w-24 h-24 flex items-center justify-center">
                <!-- Outer Pulsing Ring -->
                <div class="absolute inset-0 rounded-3xl border-2 border-[#C6F24D] animate-ping opacity-30"></div>
                
                <!-- Rotating Border Halo -->
                <div class="absolute -inset-2 rounded-3xl border-2 border-dashed border-[#C6F24D]/60 animate-spin" style="animation-duration: 8s;"></div>

                <!-- Central Solid Hub -->
                <div class="relative w-20 h-20 rounded-3xl bg-slate-900 border-2 border-[#C6F24D] shadow-[0_0_30px_rgba(198,242,77,0.4)] flex items-center justify-center">
                    <img src="{{ asset('images/logo.svg') }}" class="w-10 h-10 object-contain animate-pulse" alt="PortoFinance">
                </div>

                <!-- Floating Sparkle Badge -->
                <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-[#C6F24D] border-2 border-slate-950 flex items-center justify-center shadow-md anim-float-1">
                    <x-icon name="sparkles" class="w-4 h-4 text-slate-950" strokeWidth="2.5" />
                </div>
            </div>

            <!-- Dynamic Status Headers -->
            <div class="space-y-2 text-center">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6F24D]/15 border border-[#C6F24D]/40 text-[#C6F24D] text-[10px] font-mono font-bold tracking-widest uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#C6F24D] animate-ping"></span>
                    <span>PROVISIONING WORKSPACE</span>
                </div>
                <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                    Mempersiapkan Workspace Anda...
                </h3>
                <p class="text-xs text-slate-300 max-w-xs mx-auto leading-relaxed">
                    Membuat akun awal Rp 0 dan mengaktifkan modul alokasi pintar.
                </p>
            </div>

            <!-- Neon Progress Bar Indicator -->
            <div class="w-full max-w-xs space-y-2">
                <div class="w-full h-2 rounded-full bg-slate-800 border border-slate-700 overflow-hidden relative">
                    <div class="h-full bg-gradient-to-r from-teal-400 via-[#C6F24D] to-[#A4D928] rounded-full animate-pulse w-full"></div>
                </div>
                <div class="flex items-center justify-between text-[9px] font-mono text-slate-400">
                    <span>STATUS: ALLOCATING</span>
                    <span class="text-[#C6F24D] font-bold">READY IN MOMENTS</span>
                </div>
            </div>

        </div>
    </div>

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
               @click="startAuth(); sessionStorage.setItem('pf_just_logged_in', 'true')"
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
        <form wire:submit="register" @submit="startAuth(); sessionStorage.setItem('pf_just_logged_in', 'true')" class="space-y-3.5" :class="isShaking ? 'anim-shake' : ''">
            
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
                    <span class="flex items-center gap-2">
                        <span>Daftar Akun Baru</span>
                        <x-icon name="arrow-right" class="w-4 h-4" strokeWidth="2.5" />
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
