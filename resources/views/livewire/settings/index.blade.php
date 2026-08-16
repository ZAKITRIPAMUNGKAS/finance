<div class="max-w-3xl mx-auto space-y-6" x-data="{ activeTab: 'profile' }">

    <!-- TOP PROFILE COMPACT CARD -->
    <div class="bg-white border border-slate-200/70 rounded-3xl p-5 sm:p-6 shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-13 h-13 rounded-2xl bg-slate-950 text-[#C6F24D] flex items-center justify-center font-black text-xl shadow-sm shrink-0">
                {{ strtoupper(substr($name ?: 'U', 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900 tracking-tight leading-tight">{{ $name }}</h2>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-[#EBFAD2] text-slate-900 border border-[#D4F66C] text-[9px] font-bold">
                        <span>⚡ Pro</span>
                    </span>
                </div>
                <span class="text-xs text-slate-400 font-mono block mt-0.5">{{ $email }}</span>
            </div>
        </div>

        <button wire:click="logout" 
            class="px-3.5 py-2 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 text-xs font-extrabold transition-all flex items-center gap-1.5 cursor-pointer shrink-0 shadow-sm">
            <x-icon name="log-out" class="w-4 h-4" />
            <span class="hidden sm:inline">Keluar</span>
        </button>
    </div>

    <!-- SEGMENTED TABS (Clean Mobile-First Navigation) -->
    <div class="flex items-center p-1.5 bg-white border border-slate-200/80 rounded-2xl shadow-sm gap-1">
        <button @click="activeTab = 'profile'"
            :class="activeTab === 'profile' ? 'bg-slate-950 text-[#C6F24D] shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/60'"
            class="flex-1 py-2.5 px-3 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 cursor-pointer">
            <x-icon name="users" class="w-4 h-4" />
            <span>Profil Akun</span>
        </button>

        <button @click="activeTab = 'security'"
            :class="activeTab === 'security' ? 'bg-slate-950 text-[#C6F24D] shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/60'"
            class="flex-1 py-2.5 px-3 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 cursor-pointer">
            <x-icon name="lock" class="w-4 h-4" />
            <span>Kata Sandi</span>
        </button>

        <button @click="activeTab = 'preferences'"
            :class="activeTab === 'preferences' ? 'bg-slate-950 text-[#C6F24D] shadow-sm' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/60'"
            class="flex-1 py-2.5 px-3 rounded-xl text-xs font-extrabold transition-all flex items-center justify-center gap-2 cursor-pointer">
            <x-icon name="settings" class="w-4 h-4" />
            <span>Preferensi</span>
        </button>
    </div>

    <!-- TAB 1: INFORMASI PROFIL -->
    <div x-show="activeTab === 'profile'" x-transition class="bg-white border border-slate-200/70 rounded-3xl p-6 sm:p-7 shadow-sm space-y-5">
        <div>
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Informasi Akun</h3>
            <p class="text-xs text-slate-400">Kelola identitas personal dan email penagihan</p>
        </div>

        @if (session()->has('profile_success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-800 flex items-center gap-2">
                <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                <span>{{ session('profile_success') }}</span>
            </div>
        @endif

        <form wire:submit="updateProfile" class="space-y-4">
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Nama Lengkap</label>
                <input type="text" wire:model="name"
                    class="w-full px-4 py-3 rounded-2xl bg-[#F8F9FA] border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                @error('name') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Alamat Email</label>
                <input type="email" wire:model="email"
                    class="w-full px-4 py-3 rounded-2xl bg-[#F8F9FA] border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                @error('email') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] text-slate-950 font-extrabold text-xs shadow-md active-tap transition-all cursor-pointer">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: KEAMANAN KATA SANDI -->
    <div x-show="activeTab === 'security'" x-transition class="bg-white border border-slate-200/70 rounded-3xl p-6 sm:p-7 shadow-sm space-y-5" x-cloak>
        <div>
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Keamanan Kata Sandi</h3>
            <p class="text-xs text-slate-400">Pastikan akun Anda terlindungi dengan password yang kuat</p>
        </div>

        @if (session()->has('password_success'))
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-800 flex items-center gap-2">
                <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
                <span>{{ session('password_success') }}</span>
            </div>
        @endif

        <form wire:submit="updatePassword" class="space-y-4">
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Password Saat Ini</label>
                <input type="password" wire:model="current_password" placeholder="••••••••" autocomplete="current-password"
                    class="w-full px-4 py-3 rounded-2xl bg-[#F8F9FA] border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                @error('current_password') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Password Baru</label>
                    <input type="password" wire:model="new_password" placeholder="Minimal 6 karakter" autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-2xl bg-[#F8F9FA] border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                    @error('new_password') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1.5">Ulangi Password Baru</label>
                    <input type="password" wire:model="new_password_confirmation" placeholder="••••••••" autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-2xl bg-[#F8F9FA] border border-slate-200 text-sm font-semibold text-slate-900 focus:outline-none focus:border-slate-900 focus:bg-white transition-all">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-950 text-[#C6F24D] font-extrabold text-xs hover:bg-slate-800 shadow-md active-tap transition-all cursor-pointer">
                    Perbarui Kata Sandi
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 3: PREFERENSI FINANSIAL -->
    <div x-show="activeTab === 'preferences'" x-transition class="bg-white border border-slate-200/70 rounded-3xl p-6 sm:p-7 shadow-sm space-y-5" x-cloak>
        <div>
            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Preferensi Finansial & Kalkulator</h3>
            <p class="text-xs text-slate-400">Parameter acuan perhitungan Available Money dan Dana Darurat</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
            <div class="p-4 bg-[#F8F9FA] rounded-2xl border border-slate-100 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Target Dana Darurat</span>
                <span class="text-base font-extrabold text-slate-900 font-mono">6 Bulan Pengeluaran</span>
                <span class="text-[10px] text-slate-500 block">CFPB Gold Standard</span>
            </div>

            <div class="p-4 bg-[#F8F9FA] rounded-2xl border border-slate-100 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Mata Uang</span>
                <span class="text-base font-extrabold text-slate-900 font-mono">IDR (Rupiah • Rp)</span>
                <span class="text-[10px] text-slate-500 block">Format FinTech Indonesia</span>
            </div>

            <div class="p-4 bg-[#F8F9FA] rounded-2xl border border-slate-100 space-y-1">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Tagging Transaksi</span>
                <span class="text-base font-extrabold text-slate-900 font-mono">Personal & Freelance</span>
                <span class="text-[10px] text-slate-500 block">Pemisahan Pos Otomatis</span>
            </div>
        </div>
    </div>

</div>
