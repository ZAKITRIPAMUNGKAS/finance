<div class="max-w-3xl mx-auto space-y-6" x-data="{ activeTab: 'profile' }">

    <!-- TOP PROFILE COMPACT CARD -->
    <div class="bg-white border border-slate-200/70 rounded-3xl p-5 sm:p-6 shadow-xs flex items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-[#C6F24D] border border-slate-700 flex items-center justify-center font-mono font-black text-base shrink-0 shadow-xs">
                {{ strtoupper(substr($name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-tight">{{ $name }}</h2>
                    
                    @if($user->isAdmin())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-950 text-[#C6F24D] text-[10px] font-mono font-black border border-slate-800">
                            🛡️ Superadmin
                        </span>
                    @elseif($user->isLifetime())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 text-[10px] font-mono font-bold">
                            👑 Lifetime VIP
                        </span>
                    @elseif($user->isPro())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 text-[10px] font-mono font-bold">
                            ⭐ PRO Member
                        </span>
                    @elseif($user->isTrial())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 text-[10px] font-mono font-bold">
                            🎁 Trial ({{ $user->remaining_trial_days }}h)
                        </span>
                    @else
                        <a href="{{ route('pricing') }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-[#C6F24D] hover:bg-[#b8e640] text-slate-950 text-[10px] font-black transition-colors">
                            <span>Upgrade ke PRO 🚀</span>
                        </a>
                    @endif
                </div>
                <span class="text-xs text-slate-400 font-mono block mt-0.5">{{ $email }}</span>
            </div>
        </div>

        <button wire:click="logout" 
            class="px-3.5 py-2 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 text-xs font-extrabold transition-all flex items-center gap-1.5 cursor-pointer shrink-0 shadow-xs">
            <x-icon name="log-out" class="w-4 h-4" />
            <span class="hidden sm:inline">Keluar</span>
        </button>
    </div>

    <!-- SEGMENTED TABS (Clean Mobile-First Navigation) -->
    <div class="flex items-center p-1.5 bg-white border border-slate-200/80 rounded-2xl shadow-xs gap-1 overflow-x-auto scrollbar-none">
        <button @click="activeTab = 'profile'"
            :class="activeTab === 'profile' ? 'bg-slate-950 text-[#C6F24D] shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/60'"
            class="flex-1 min-w-[100px] py-2.5 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
            <x-icon name="user" class="w-4 h-4" />
            <span>Profil</span>
        </button>

        <button @click="activeTab = 'security'"
            :class="activeTab === 'security' ? 'bg-slate-950 text-[#C6F24D] shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/60'"
            class="flex-1 min-w-[100px] py-2.5 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
            <x-icon name="lock" class="w-4 h-4" />
            <span>Kata Sandi</span>
        </button>

        <button @click="activeTab = 'preferences'"
            :class="activeTab === 'preferences' ? 'bg-slate-950 text-[#C6F24D] shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/60'"
            class="flex-1 min-w-[100px] py-2.5 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
            <x-icon name="settings" class="w-4 h-4" />
            <span>Preferensi</span>
        </button>

        <button @click="activeTab = 'privacy'"
            :class="activeTab === 'privacy' ? 'bg-slate-950 text-[#C6F24D] shadow-xs' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100/60'"
            class="flex-1 min-w-[100px] py-2.5 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0">
            <x-icon name="shield" class="w-4 h-4" />
            <span>Data & Privasi</span>
        </button>
    </div>

    <!-- TAB 1: INFORMASI PROFIL -->
    <div x-show="activeTab === 'profile'" x-transition class="bg-white border border-slate-200/70 rounded-3xl p-6 sm:p-7 shadow-xs space-y-5">
        <div>
            <h3 class="text-base font-black text-slate-900 tracking-tight">Informasi Akun</h3>
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
    <div x-show="activeTab === 'security'" x-transition class="bg-white border border-slate-200/70 rounded-3xl p-6 sm:p-7 shadow-xs space-y-5" x-cloak>
        <div>
            <h3 class="text-base font-black text-slate-900 tracking-tight">Keamanan Kata Sandi</h3>
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
    <div x-show="activeTab === 'preferences'" x-transition class="bg-white border border-slate-200/70 rounded-3xl p-6 sm:p-7 shadow-xs space-y-5" x-cloak>
        <div>
            <h3 class="text-base font-black text-slate-900 tracking-tight">Preferensi Finansial & Kalkulator</h3>
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

    <!-- TAB 4: DATA & PRIVASI (PORTABILITY & GDPR) -->
    <div x-show="activeTab === 'privacy'" x-transition class="bg-white border border-slate-200/70 rounded-3xl p-6 sm:p-7 shadow-xs space-y-6" x-cloak>
        <div>
            <h3 class="text-base font-black text-slate-900 tracking-tight">Portabilitas Data & Privasi Akun</h3>
            <p class="text-xs text-slate-400">Unduh salinan seluruh data finansial Anda atau lakukan penghapusan akun mandiri.</p>
        </div>

        @if(session('data_error'))
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-xs font-bold text-rose-800 flex items-center gap-2">
            <x-icon name="alert-circle" class="w-4 h-4 text-rose-600 shrink-0" />
            <span>{{ session('data_error') }}</span>
        </div>
        @endif

        <!-- 1. BACKUP DATA SECTION -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <h4 class="text-xs font-black text-slate-950 flex items-center gap-2">
                    <x-icon name="download" class="w-4 h-4 text-emerald-600" />
                    <span>Download Arsip Cadangan Data (JSON Backup)</span>
                </h4>
                <p class="text-[11px] text-slate-500">
                    Ekspor seluruh mutasi transaksi, pos rekening, proyek, tagihan invoice, dan klien dalam format JSON terstruktur.
                </p>
            </div>

            <button type="button" 
                    wire:click="exportAllData" 
                    class="px-4 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-white font-black text-xs transition-all shadow-xs flex items-center gap-2 shrink-0 cursor-pointer active-tap">
                <x-icon name="download-cloud" class="w-4 h-4 text-[#C6F24D]" />
                <span>Unduh Data (.JSON)</span>
            </button>
        </div>

        <!-- 2. DELETE ACCOUNT SECTION (DANGER ZONE) -->
        <div class="p-5 rounded-2xl bg-rose-50/50 border border-rose-200/80 space-y-3">
            <div class="space-y-1">
                <h4 class="text-xs font-black text-rose-950 flex items-center gap-2">
                    <x-icon name="alert-triangle" class="w-4 h-4 text-rose-600" />
                    <span>Zona Berbahaya: Hapus Akun Permanen</span>
                </h4>
                <p class="text-[11px] text-rose-700 leading-relaxed">
                    Tindakan ini akan menghapus akun dan seluruh catatan keuangan Anda secara permanen dari server. Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <button type="button" 
                    wire:click="confirmDeleteAccount" 
                    class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs transition-all shadow-xs flex items-center gap-2 cursor-pointer active-tap">
                <x-icon name="trash-2" class="w-4 h-4 text-white" />
                <span>Hapus Akun Saya Permanen</span>
            </button>
        </div>
    </div>

    <!-- DELETE ACCOUNT MODAL -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div class="bg-white border border-rose-200 rounded-3xl p-6 shadow-2xl max-w-md w-full space-y-4 animate-in fade-in zoom-in-95 duration-150">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2 text-rose-600">
                    <x-icon name="alert-triangle" class="w-5 h-5 shrink-0" />
                    <h3 class="text-sm font-black text-rose-950">Konfirmasi Penghapusan Akun</h3>
                </div>
                <button type="button" wire:click="$set('showDeleteModal', false)" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin menghapus akun Anda (<strong>{{ $email }}</strong>)? Seluruh catatan transaksi, invoice, dan rekening akan dihapus secara permanen.
            </p>

            <form wire:submit.prevent="deleteAccount" class="space-y-3 pt-2">
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1">Ketik Password Anda untuk Konfirmasi:</label>
                    <input type="password" 
                           wire:model="delete_password" 
                           placeholder="••••••••" 
                           class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:border-rose-600">
                    @error('delete_password') <span class="text-xs text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-black transition-all shadow-xs">
                        Ya, Hapus Akun Permanen
                    </button>
                </div>
            </form>

        </div>
    </div>
    @endif

</div>
