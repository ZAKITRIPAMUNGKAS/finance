<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/75 backdrop-blur-xl flex items-center justify-center p-3 sm:p-6 transition-all duration-300 animate-fade-in"
         x-data="{ activeStep: @entangle('step') }">
        
        <!-- MAIN MODAL CONTAINER -->
        <div class="bg-white rounded-3xl sm:rounded-4xl shadow-2xl border border-slate-200/90 w-full max-w-2xl overflow-hidden flex flex-col max-h-[92vh] transition-all transform animate-scale-up">
            
            <!-- HEADER WITH PROGRESS BAR -->
            <div class="p-5 sm:p-7 border-b border-slate-100 bg-linear-to-b from-slate-50/90 to-white flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shadow-sm shrink-0">
                            <x-icon name="sparkles" class="w-5 h-5" />
                        </div>
                        <div>
                            <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wider text-teal-600 block">Setup Awal Keuangan</span>
                            <h2 class="text-base sm:text-xl font-extrabold text-slate-900 tracking-tight">Selamat Datang di PortoFinance! 👋</h2>
                        </div>
                    </div>
                    
                    <span class="text-xs font-mono font-bold px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200/60 shadow-2xs">
                        Langkah {{ $step }} dari 3
                    </span>
                </div>

                <!-- 3-STEP PROGRESS TRACK -->
                <div class="grid grid-cols-3 gap-2.5 pt-1">
                    <div class="h-2 rounded-full transition-all duration-500 ease-out {{ $step >= 1 ? 'bg-slate-950' : 'bg-slate-100' }}"></div>
                    <div class="h-2 rounded-full transition-all duration-500 ease-out {{ $step >= 2 ? 'bg-slate-950' : 'bg-slate-100' }}"></div>
                    <div class="h-2 rounded-full transition-all duration-500 ease-out {{ $step >= 3 ? 'bg-slate-950' : 'bg-slate-100' }}"></div>
                </div>
            </div>

            <!-- MODAL BODY (STEP CONTENT) -->
            <div class="p-5 sm:p-8 overflow-y-auto space-y-6 flex-1 text-slate-800 custom-scrollbar">

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- STEP 1: PILIH PROFIL / AKTIVITAS UTAMA                       -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                @if($step === 1)
                <div class="space-y-4 animate-fade-in">
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">Siapa Anda & apa aktivitas utama Anda?</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Pilih satu peran yang paling menggambarkan Anda. Sistem akan otomatis menyiapkan pos pengeluaran & anggaran yang paling pas!
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        
                        <!-- 1. Karyawan -->
                        <button type="button" 
                                wire:click="setPersona('employee_salary')"
                                class="p-4 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex items-start gap-3.5 relative hover:-translate-y-0.5 {{ $persona === 'employee_salary' ? 'bg-teal-50/60 border-slate-950 ring-2 ring-slate-950 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300 hover:bg-slate-50/50' }}">
                            <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center shrink-0 shadow-2xs">
                                <x-icon name="building-2" class="w-5 h-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-950">Karyawan & Pegawai</h4>
                                    @if($persona === 'employee_salary')
                                        <x-icon name="check-circle" class="w-4 h-4 text-slate-950" />
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Gaji bulanan tetap, fokus metode 50/30/20, makan & tagihan rutin.</p>
                            </div>
                        </button>

                        <!-- 2. UMKM -->
                        <button type="button" 
                                wire:click="setPersona('umkm_business')"
                                class="p-4 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex items-start gap-3.5 relative hover:-translate-y-0.5 {{ $persona === 'umkm_business' ? 'bg-amber-50/60 border-slate-950 ring-2 ring-slate-950 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300 hover:bg-slate-50/50' }}">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 shadow-2xs">
                                <x-icon name="shopping-bag" class="w-5 h-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-950">Wirausaha & UMKM</h4>
                                    @if($persona === 'umkm_business')
                                        <x-icon name="check-circle" class="w-4 h-4 text-slate-950" />
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Toko online / dagang, pisah modal HPP, iklan medsos & gaji owner.</p>
                            </div>
                        </button>

                        <!-- 3. Freelancer Kreatif / IT -->
                        <button type="button" 
                                wire:click="setPersona('creative_media')"
                                class="p-4 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex items-start gap-3.5 relative hover:-translate-y-0.5 {{ $persona === 'creative_media' ? 'bg-indigo-50/60 border-slate-950 ring-2 ring-slate-950 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300 hover:bg-slate-50/50' }}">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center shrink-0 shadow-2xs">
                                <x-icon name="camera" class="w-5 h-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-950">Freelancer & Kreator</h4>
                                    @if($persona === 'creative_media')
                                        <x-icon name="check-circle" class="w-4 h-4 text-slate-950" />
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Pemasukan per project, sewa alat, invoice klien & tabungan upgrade gear.</p>
                            </div>
                        </button>

                        <!-- 4. Pelajar / Mahasiswa -->
                        <button type="button" 
                                wire:click="setPersona('pelajar_mahasiswa')"
                                class="p-4 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex items-start gap-3.5 relative hover:-translate-y-0.5 {{ $persona === 'pelajar_mahasiswa' ? 'bg-sky-50/60 border-slate-950 ring-2 ring-slate-950 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300 hover:bg-slate-50/50' }}">
                            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center shrink-0 shadow-2xs">
                                <x-icon name="sparkles" class="w-5 h-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-950">Pelajar & Mahasiswa</h4>
                                    @if($persona === 'pelajar_mahasiswa')
                                        <x-icon name="check-circle" class="w-4 h-4 text-slate-950" />
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Uang saku kiriman, sewa kos, tugas kuliah, makan warteg & anti-boncos.</p>
                            </div>
                        </button>

                        <!-- 5. Keluarga -->
                        <button type="button" 
                                wire:click="setPersona('keluarga_rumahtangga')"
                                class="p-4 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex items-start gap-3.5 relative hover:-translate-y-0.5 {{ $persona === 'keluarga_rumahtangga' ? 'bg-rose-50/60 border-slate-950 ring-2 ring-slate-950 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300 hover:bg-slate-50/50' }}">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center shrink-0 shadow-2xs">
                                <x-icon name="users" class="w-5 h-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-950">Rumah Tangga & Keluarga</h4>
                                    @if($persona === 'keluarga_rumahtangga')
                                        <x-icon name="check-circle" class="w-4 h-4 text-slate-950" />
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Belanja dapur keluarga, SPP anak, utilitas rumah & proteksi darurat.</p>
                            </div>
                        </button>

                        <!-- 6. Hybrid -->
                        <button type="button" 
                                wire:click="setPersona('hybrid_sidehustle')"
                                class="p-4 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex items-start gap-3.5 relative hover:-translate-y-0.5 {{ $persona === 'hybrid_sidehustle' ? 'bg-emerald-50/60 border-slate-950 ring-2 ring-slate-950 shadow-xs' : 'bg-white border-slate-200/80 hover:border-slate-300 hover:bg-slate-50/50' }}">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 shadow-2xs">
                                <x-icon name="trending-up" class="w-5 h-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs sm:text-sm font-extrabold text-slate-950">Karyawan + Sampingan</h4>
                                    @if($persona === 'hybrid_sidehustle')
                                        <x-icon name="check-circle" class="w-4 h-4 text-slate-950" />
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">Gaji kantor untuk biaya hidup, hasil sampingan 100% untuk akselerasi.</p>
                            </div>
                        </button>

                    </div>
                </div>
                @endif

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- STEP 2: REKENING & SALDO AWAL RIIL                          -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                @if($step === 2)
                <div class="space-y-4 animate-fade-in">
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">Rekening & dompet yang aktif Anda gunakan?</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Centang akun yang Anda miliki dan masukkan saldo awalnya (boleh diisi <strong>Rp 0</strong> jika ingin mulai dari nol).
                        </p>
                    </div>

                    <div class="space-y-2.5 pt-2 max-h-88 overflow-y-auto pr-1.5 custom-scrollbar">
                        
                        @php
                        $availableAccounts = [
                            'bca' => 'BCA Utama',
                            'mandiri' => 'Bank Mandiri',
                            'bri' => 'Bank BRI',
                            'bni' => 'Bank BNI',
                            'jago' => 'Bank Jago',
                            'seabank' => 'SeaBank',
                            'gopay' => 'GoPay',
                            'ovo' => 'OVO',
                            'dana' => 'DANA',
                            'shopeepay' => 'ShopeePay',
                            'cash' => 'Dompet Tunai',
                        ];
                        @endphp

                        @foreach($availableAccounts as $accKey => $accName)
                        <div class="p-3.5 sm:p-4 rounded-2xl border transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ !empty($activeAccounts[$accKey]) ? 'bg-white border-slate-900 ring-2 ring-slate-950/10 shadow-xs' : 'bg-slate-50/50 border-slate-200/80 opacity-65 hover:opacity-100 hover:bg-slate-50' }}">
                            
                            <!-- Toggle & Logo -->
                            <div class="flex items-center gap-3 cursor-pointer min-w-0 flex-1 select-none" wire:click="toggleAccount('{{ $accKey }}')">
                                <div class="w-5 h-5 rounded-lg flex items-center justify-center transition-colors {{ !empty($activeAccounts[$accKey]) ? 'bg-slate-950 text-white shadow-2xs' : 'border-2 border-slate-300 bg-white' }}">
                                    @if(!empty($activeAccounts[$accKey]))
                                        <x-icon name="check" class="w-3.5 h-3.5 text-white" strokeWidth="3" />
                                    @endif
                                </div>
                                
                                <x-account-logo :name="$accName" class="w-9 h-9 rounded-xl shrink-0 shadow-2xs" />
                                
                                <div class="min-w-0">
                                    <span class="font-extrabold text-xs sm:text-sm text-slate-950 block truncate">{{ $accName }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono block">{{ $accKey === 'cash' ? 'Uang Fisik' : ($accKey === 'gopay' || $accKey === 'ovo' || $accKey === 'dana' || $accKey === 'shopeepay' ? 'E-Wallet' : 'Rekening Bank') }}</span>
                                </div>
                            </div>

                            <!-- Input Saldo Awal (Unified Centered Inline Flex Group) -->
                            @if(!empty($activeAccounts[$accKey]))
                            <div class="flex items-center justify-between sm:justify-end gap-2.5 pl-8 sm:pl-0 shrink-0">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Saldo Awal:</span>
                                <div style="display: flex; align-items: center;" class="h-9.5 px-3 rounded-xl bg-slate-50 border border-slate-300 focus-within:border-slate-950 focus-within:bg-white focus-within:ring-2 focus-within:ring-slate-950/15 transition-all shadow-2xs">
                                    <span class="text-xs font-mono font-black text-slate-400 select-none mr-1.5 leading-none">Rp</span>
                                    <input type="number" 
                                           wire:model.defer="accountBalances.{{ $accKey }}"
                                           placeholder="0"
                                           class="w-24 sm:w-28 text-xs font-mono font-bold text-slate-950 bg-transparent border-0 p-0 focus:outline-none focus:ring-0 text-right leading-none">
                                </div>
                            </div>
                            @else
                            <button type="button" 
                                    wire:click="toggleAccount('{{ $accKey }}')"
                                    class="hidden sm:inline-flex text-[11px] font-bold text-slate-400 hover:text-slate-900 transition-colors px-2 py-1 cursor-pointer">
                                + Aktifkan
                            </button>
                            @endif

                        </div>
                        @endforeach

                    </div>
                </div>
                @endif

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- STEP 3: ESTIMASI UANG MASUK BULANAN                         -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                @if($step === 3)
                <div class="space-y-5 animate-fade-in">
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-950 tracking-tight">Perkiraan pemasukan / uang masuk Anda per bulan?</h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Angka ini menjadi dasar perhitungan anggaran otomatis agar pos kebutuhan harian, tabungan, dan hiburan terbagi secara proporsional.
                        </p>
                    </div>

                    <!-- Input Nominal Bulanan -->
                    <div class="p-6 bg-slate-50/80 rounded-3xl border border-slate-200/80 space-y-4">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-500">Estimasi Pendapatan Rata-Rata Bulanan</label>
                        
                        <div style="display: flex; align-items: center;" class="rounded-2xl bg-white border-2 border-slate-900 px-4 py-3 shadow-xs focus-within:ring-4 focus-within:ring-[#C6F24D]/30 transition-all">
                            <span class="text-lg sm:text-xl font-mono font-black text-slate-400 select-none mr-2.5 leading-none">Rp</span>
                            <input type="number" 
                                   wire:model.live="monthlyIncome"
                                   placeholder="5000000" 
                                   class="w-full text-xl sm:text-2xl font-mono font-black text-slate-950 bg-transparent border-0 p-0 focus:outline-none focus:ring-0 leading-none">
                        </div>

                        <!-- Pilihan Cepat (Chips) -->
                        <div class="space-y-1.5 pt-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pilihan Cepat:</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" wire:click="setMonthlyIncomeChip('2500000')" class="px-3.5 py-2 rounded-xl border text-xs font-bold font-mono transition-all duration-150 cursor-pointer active:scale-95 {{ $monthlyIncome == '2500000' ? 'bg-slate-950 text-[#C6F24D] border-slate-950 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50' }}">
                                    Rp 2.500.000
                                </button>
                                <button type="button" wire:click="setMonthlyIncomeChip('5000000')" class="px-3.5 py-2 rounded-xl border text-xs font-bold font-mono transition-all duration-150 cursor-pointer active:scale-95 {{ $monthlyIncome == '5000000' ? 'bg-slate-950 text-[#C6F24D] border-slate-950 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50' }}">
                                    Rp 5.000.000
                                </button>
                                <button type="button" wire:click="setMonthlyIncomeChip('10000000')" class="px-3.5 py-2 rounded-xl border text-xs font-bold font-mono transition-all duration-150 cursor-pointer active:scale-95 {{ $monthlyIncome == '10000000' ? 'bg-slate-950 text-[#C6F24D] border-slate-950 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50' }}">
                                    Rp 10.000.000
                                </button>
                                <button type="button" wire:click="setMonthlyIncomeChip('15000000')" class="px-3.5 py-2 rounded-xl border text-xs font-bold font-mono transition-all duration-150 cursor-pointer active:scale-95 {{ $monthlyIncome == '15000000' ? 'bg-slate-950 text-[#C6F24D] border-slate-950 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50' }}">
                                    Rp 15.000.000+
                                </button>
                            </div>
                        </div>

                        <!-- Friendly Badge Note -->
                        <div class="p-3.5 rounded-2xl bg-teal-50/80 border border-teal-200 text-xs text-teal-900 flex items-start gap-2.5">
                            <x-icon name="shield-check" class="w-4 h-4 text-teal-700 shrink-0 mt-0.5" />
                            <p class="text-[11px] leading-relaxed text-teal-800 font-medium">
                                <strong>Tenang!</strong> Angka ini bisa Anda sesuaikan kapan saja nanti di menu <em>Budgets</em> atau melalui pencatatan transaksi harian.
                            </p>
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <!-- MODAL FOOTER NAVIGATION -->
            <div class="p-5 sm:p-7 border-t border-slate-100 bg-slate-50/60 flex items-center justify-between gap-3">
                
                @if($step > 1)
                <button type="button" 
                        wire:click="prevStep"
                        class="px-4 sm:px-5 py-2.5 rounded-2xl bg-white border border-slate-200 hover:bg-slate-100 text-xs font-extrabold text-slate-700 transition-all duration-150 flex items-center gap-1.5 cursor-pointer shadow-2xs active:scale-95">
                    <x-icon name="arrow-left" class="w-3.5 h-3.5" />
                    <span>Kembali</span>
                </button>
                @else
                <div class="text-[11px] text-slate-400 font-semibold">Langkah 1/3</div>
                @endif

                @if($step < 3)
                <button type="button" 
                        wire:click="nextStep"
                        class="px-6 sm:px-8 py-3 rounded-2xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs sm:text-sm font-black transition-all duration-150 flex items-center gap-2 cursor-pointer shadow-md active:scale-95">
                    <span>Lanjutkan</span>
                    <x-icon name="arrow-right" class="w-4 h-4" />
                </button>
                @else
                <button type="button" 
                        wire:click="completeOnboarding"
                        class="px-6 sm:px-8 py-3.5 rounded-2xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs sm:text-sm font-black transition-all duration-150 flex items-center gap-2 cursor-pointer shadow-lg shadow-slate-950/20 active:scale-95">
                    <x-icon name="sparkles" class="w-4 h-4" />
                    <span>Mulai Kelola Keuangan Saya &rarr;</span>
                </button>
                @endif

            </div>

        </div>

    </div>
    @endif
</div>
