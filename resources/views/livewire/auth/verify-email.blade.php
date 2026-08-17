<div id="verify-email-container" class="w-full flex-1 flex flex-col justify-between max-w-md mx-auto relative anim-page-enter">
    
    <!-- ── TOP HEADER / NAV ──────────────────────────────────── -->
    <header class="flex items-center justify-between pb-4 sm:pb-6 shrink-0">
        <!-- Spacer -->
        <div class="w-9 sm:w-10"></div>

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

    <!-- ── MAIN CONTENT ──────────────────────────────────────── -->
    <main class="space-y-5 my-auto py-2 text-center">
        
        <!-- Mailbox Icon Badge -->
        <div class="relative w-20 h-20 mx-auto flex items-center justify-center">
            <div class="absolute inset-0 rounded-full bg-[#C6F24D]/35 blur-xl"></div>
            <div class="relative w-16 h-16 rounded-3xl bg-slate-950 border-2 border-slate-950 text-[#C6F24D] flex items-center justify-center shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
            </div>
            <!-- Floating Checkmark -->
            <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-[#C6F24D] border-2 border-slate-950 flex items-center justify-center text-slate-950 shadow-xs">
                <x-icon name="check" class="w-3.5 h-3.5" strokeWidth="3" />
            </div>
        </div>

        <!-- Header Greeting -->
        <div class="space-y-2">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight leading-tight">
                Verifikasi Email Anda 📬
            </h1>
            <p class="text-xs sm:text-sm font-medium text-slate-500 max-w-sm mx-auto leading-relaxed">
                Kami telah mengirimkan tautan verifikasi ke:
            </p>
            <div class="inline-block px-3.5 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs sm:text-sm font-mono font-bold text-slate-900">
                {{ $user->email ?? 'email@anda.com' }}
            </div>
            <p class="text-[11px] sm:text-xs text-slate-400 max-w-xs mx-auto leading-relaxed pt-1">
                Silakan buka kotak masuk (inbox atau folder spam) email Anda dan klik tombol verifikasi untuk mulai mengelola keuangan.
            </p>
        </div>

        <!-- Feedback Alert when Resent -->
        @if ($sent)
        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-center gap-2 text-xs text-emerald-800 font-bold anim-fade-up">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" strokeWidth="2.5" />
            <span>Tautan verifikasi baru telah berhasil dikirim!</span>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="space-y-3 pt-2">
            <!-- Resend Verification Button -->
            <button type="button" 
                    wire:click="resendVerification"
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-[0.98] text-slate-950 font-black text-xs sm:text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border-2 border-slate-950">
                <span wire:loading.remove class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                    <span>Kirim Ulang Email Verifikasi</span>
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Mengirim ulang...</span>
                </span>
            </button>
        </div>

    </main>

    <!-- ── FOOTER LOGOUT LINK ─────────────────────────────────── -->
    <footer class="pt-4 pb-2 text-center border-t border-slate-100 shrink-0">
        <p class="text-xs text-slate-500 font-medium">
            Salah memasukkan email? 
            <button type="button" 
                    wire:click="logout"
                    class="font-black text-rose-600 hover:text-rose-700 underline underline-offset-2 ml-1 cursor-pointer transition-colors">
                Keluar & Ganti Akun &rarr;
            </button>
        </p>
    </footer>

</div>
