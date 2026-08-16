<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/75 backdrop-blur-md flex items-center justify-center p-3 sm:p-5 transition-all duration-300 animate-fade-in"
         x-data="{
             step: 1,
             persona: 'employee_salary',
             activeAccounts: {
                 bca: true,
                 gopay: true,
                 cash: true
             },
             balances: {
                 bca: '0',
                 mandiri: '0',
                 bri: '0',
                 bni: '0',
                 jago: '0',
                 seabank: '0',
                 gopay: '0',
                 ovo: '0',
                 dana: '0',
                 shopeepay: '0',
                 cash: '0'
             },
             monthlyIncome: '5000000',
             isSubmitting: false,

             toggleAcc(key) {
                 this.activeAccounts[key] = !this.activeAccounts[key];
             },

             setIncome(val) {
                 this.monthlyIncome = String(val);
             },

             next() {
                 if (this.step === 2) {
                     let hasActive = Object.values(this.activeAccounts).some(Boolean);
                     if (!hasActive) {
                         this.activeAccounts['cash'] = true;
                     }
                 }
                 if (this.step < 3) {
                     this.step++;
                 }
             },

             prev() {
                 if (this.step > 1) {
                     this.step--;
                 }
             },

             submit() {
                 this.isSubmitting = true;
                 $wire.saveOnboarding({
                     persona: this.persona,
                     activeAccounts: this.activeAccounts,
                     accountBalances: this.balances,
                     monthlyIncome: this.monthlyIncome
                 });
             }
         }">
        
        <!-- MAIN MODAL CARD (Sleek, Compact, Clean) -->
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 w-full max-w-xl overflow-hidden flex flex-col max-h-[90vh] transition-all transform animate-scale-up">
            
            <!-- HEADER WITH CLEAN BREADCRUMB INDICATOR -->
            <div class="px-5 py-4.5 sm:px-6 sm:py-5 border-b border-slate-100 bg-slate-50/60 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shadow-2xs shrink-0">
                        <x-icon name="sparkles" class="w-4.5 h-4.5" />
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-teal-600 block leading-tight">Setup Awal</span>
                        <h2 class="text-sm sm:text-base font-extrabold text-slate-950 tracking-tight leading-tight">Selamat Datang di PortoFinance! 👋</h2>
                    </div>
                </div>
                
                <!-- Step Dots Indicator -->
                <div class="flex items-center gap-1.5 bg-white border border-slate-200/80 px-2.5 py-1 rounded-full shadow-2xs">
                    <span class="w-2 h-2 rounded-full transition-all duration-300" :class="step === 1 ? 'bg-slate-950 scale-125' : (step > 1 ? 'bg-teal-600' : 'bg-slate-200')"></span>
                    <span class="w-2 h-2 rounded-full transition-all duration-300" :class="step === 2 ? 'bg-slate-950 scale-125' : (step > 2 ? 'bg-teal-600' : 'bg-slate-200')"></span>
                    <span class="w-2 h-2 rounded-full transition-all duration-300" :class="step === 3 ? 'bg-slate-950 scale-125' : 'bg-slate-200'"></span>
                    <span class="text-[10px] font-mono font-bold text-slate-600 ml-1" x-text="step + '/3'"></span>
                </div>
            </div>

            <!-- MODAL BODY (INSTANT ZERO-LATENCY TRANSITIONS) -->
            <div class="p-5 sm:p-6 overflow-y-auto space-y-4 flex-1 text-slate-800 custom-scrollbar">

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- STEP 1: PILIH PROFIL (Grid Compact 2-Col)                   -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div x-show="step === 1" 
                     x-transition:enter="transition ease-out duration-200 transform"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-3.5">
                    
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-950 tracking-tight">Siapa Anda & apa aktivitas utama Anda?</h3>
                        <p class="text-xs text-slate-500 mt-0.5 leading-normal">
                            Pilih profil Anda untuk menyesuaikan kategori pengeluaran dan anggaran otomatis.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                        
                        <!-- 1. Karyawan -->
                        <div @click="persona = 'employee_salary'"
                             :class="persona === 'employee_salary' ? 'bg-teal-50/70 border-slate-950 ring-1.5 ring-slate-950 shadow-2xs' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50'"
                             class="p-3 rounded-2xl border text-left transition-all duration-150 cursor-pointer flex items-start gap-3 relative select-none">
                            <div class="w-8.5 h-8.5 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                <x-icon name="building-2" class="w-4.5 h-4.5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-extrabold text-slate-950">Karyawan / Pegawai</h4>
                                    <template x-if="persona === 'employee_salary'">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-slate-950 shrink-0" />
                                    </template>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">Gaji tetap bulanan, metode 50/30/20, makan & tagihan rutin.</p>
                            </div>
                        </div>

                        <!-- 2. UMKM -->
                        <div @click="persona = 'umkm_business'"
                             :class="persona === 'umkm_business' ? 'bg-amber-50/70 border-slate-950 ring-1.5 ring-slate-950 shadow-2xs' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50'"
                             class="p-3 rounded-2xl border text-left transition-all duration-150 cursor-pointer flex items-start gap-3 relative select-none">
                            <div class="w-8.5 h-8.5 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                <x-icon name="shopping-bag" class="w-4.5 h-4.5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-extrabold text-slate-950">Wirausaha / UMKM</h4>
                                    <template x-if="persona === 'umkm_business'">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-slate-950 shrink-0" />
                                    </template>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">Toko online, pisah modal HPP, iklan medsos & gaji owner.</p>
                            </div>
                        </div>

                        <!-- 3. Freelancer -->
                        <div @click="persona = 'creative_media'"
                             :class="persona === 'creative_media' ? 'bg-indigo-50/70 border-slate-950 ring-1.5 ring-slate-950 shadow-2xs' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50'"
                             class="p-3 rounded-2xl border text-left transition-all duration-150 cursor-pointer flex items-start gap-3 relative select-none">
                            <div class="w-8.5 h-8.5 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                <x-icon name="camera" class="w-4.5 h-4.5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-extrabold text-slate-950">Freelancer & Kreator</h4>
                                    <template x-if="persona === 'creative_media'">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-slate-950 shrink-0" />
                                    </template>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">Uang per project, sewa gear, invoice klien & upgrade alat.</p>
                            </div>
                        </div>

                        <!-- 4. Pelajar / Mahasiswa -->
                        <div @click="persona = 'pelajar_mahasiswa'"
                             :class="persona === 'pelajar_mahasiswa' ? 'bg-sky-50/70 border-slate-950 ring-1.5 ring-slate-950 shadow-2xs' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50'"
                             class="p-3 rounded-2xl border text-left transition-all duration-150 cursor-pointer flex items-start gap-3 relative select-none">
                            <div class="w-8.5 h-8.5 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                <x-icon name="graduation-cap" class="w-4.5 h-4.5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-extrabold text-slate-950">Pelajar & Mahasiswa</h4>
                                    <template x-if="persona === 'pelajar_mahasiswa'">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-slate-950 shrink-0" />
                                    </template>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">Uang saku kiriman, sewa kos, tugas kuliah & anti-boncos.</p>
                            </div>
                        </div>

                        <!-- 5. Keluarga -->
                        <div @click="persona = 'keluarga_rumahtangga'"
                             :class="persona === 'keluarga_rumahtangga' ? 'bg-rose-50/70 border-slate-950 ring-1.5 ring-slate-950 shadow-2xs' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50'"
                             class="p-3 rounded-2xl border text-left transition-all duration-150 cursor-pointer flex items-start gap-3 relative select-none">
                            <div class="w-8.5 h-8.5 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                <x-icon name="heart" class="w-4.5 h-4.5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-extrabold text-slate-950">Rumah Tangga & Keluarga</h4>
                                    <template x-if="persona === 'keluarga_rumahtangga'">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-slate-950 shrink-0" />
                                    </template>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">Belanja dapur, SPP anak, utilitas rumah & proteksi darurat.</p>
                            </div>
                        </div>

                        <!-- 6. Hybrid -->
                        <div @click="persona = 'hybrid_sidehustle'"
                             :class="persona === 'hybrid_sidehustle' ? 'bg-emerald-50/70 border-slate-950 ring-1.5 ring-slate-950 shadow-2xs' : 'bg-white border-slate-200 hover:border-slate-300 hover:bg-slate-50/50'"
                             class="p-3 rounded-2xl border text-left transition-all duration-150 cursor-pointer flex items-start gap-3 relative select-none">
                            <div class="w-8.5 h-8.5 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                <x-icon name="rocket" class="w-4.5 h-4.5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-extrabold text-slate-950">Karyawan + Sampingan</h4>
                                    <template x-if="persona === 'hybrid_sidehustle'">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-slate-950 shrink-0" />
                                    </template>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-0.5 leading-snug">Gaji kantor untuk hidup, hasil sampingan untuk investasi.</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- STEP 2: REKENING & SALDO AWAL (Compact, No Glitch)          -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div x-show="step === 2" 
                     x-transition:enter="transition ease-out duration-200 transform"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-3.5">
                    
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-950 tracking-tight">Rekening & dompet yang Anda gunakan?</h3>
                        <p class="text-xs text-slate-500 mt-0.5 leading-normal">
                            Centang akun yang Anda miliki. Saldo awal boleh dibiarkan <strong>0</strong> jika ingin mulai dari nol.
                        </p>
                    </div>

                    <div class="space-y-2 max-h-75 overflow-y-auto pr-1 custom-scrollbar">
                        
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
                        <div :class="activeAccounts['{{ $accKey }}'] ? 'bg-white border-slate-950 ring-1 ring-slate-950/20 shadow-2xs' : 'bg-slate-50/50 border-slate-200 opacity-60 hover:opacity-100'"
                             class="p-2.5 sm:p-3 rounded-2xl border transition-all duration-150 flex items-center justify-between gap-3">
                            
                            <!-- Toggle Checkbox + Logo + Name -->
                            <div class="flex items-center gap-2.5 cursor-pointer min-w-0 flex-1 select-none" @click="toggleAcc('{{ $accKey }}')">
                                <div class="w-4.5 h-4.5 rounded-md flex items-center justify-center transition-colors"
                                     :class="activeAccounts['{{ $accKey }}'] ? 'bg-slate-950 text-white' : 'border border-slate-300 bg-white'">
                                    <template x-if="activeAccounts['{{ $accKey }}']">
                                        <x-icon name="check" class="w-3 h-3 text-white" strokeWidth="3" />
                                    </template>
                                </div>
                                
                                <x-account-logo :name="$accName" class="w-7.5 h-7.5 rounded-lg shrink-0 shadow-2xs" />
                                
                                <div class="min-w-0">
                                    <span class="font-extrabold text-xs text-slate-950 block truncate">{{ $accName }}</span>
                                    <span class="text-[9px] text-slate-400 font-mono block leading-tight">{{ $accKey === 'cash' ? 'Uang Fisik' : ($accKey === 'gopay' || $accKey === 'ovo' || $accKey === 'dana' || $accKey === 'shopeepay' ? 'E-Wallet' : 'Bank') }}</span>
                                </div>
                            </div>

                            <!-- Input Saldo Awal (Unified Horizontal Inline Flex) -->
                            <template x-if="activeAccounts['{{ $accKey }}']">
                                <div class="flex items-center gap-1.5 shrink-0" @click.stop>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Saldo:</span>
                                    <div style="display: inline-flex; align-items: center; justify-content: flex-end;"
                                         class="h-8 px-2 rounded-xl bg-slate-50 border border-slate-300 focus-within:border-slate-950 focus-within:bg-white focus-within:ring-1 focus-within:ring-slate-950 transition-all">
                                        <span class="text-[10px] font-mono font-bold text-slate-400 select-none mr-1">Rp</span>
                                        <input type="number" 
                                               x-model="balances['{{ $accKey }}']"
                                               placeholder="0"
                                               class="w-20 sm:w-24 text-xs font-mono font-bold text-slate-950 bg-transparent border-0 p-0 focus:outline-none focus:ring-0 text-right leading-none">
                                    </div>
                                </div>
                            </template>
                            
                            <template x-if="!activeAccounts['{{ $accKey }}']">
                                <button type="button" 
                                        @click="toggleAcc('{{ $accKey }}')"
                                        class="text-[10px] font-bold text-slate-400 hover:text-slate-900 transition-colors px-2 py-1 cursor-pointer select-none">
                                    + Aktifkan
                                </button>
                            </template>

                        </div>
                        @endforeach

                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- STEP 3: ESTIMASI UANG MASUK BULANAN                         -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div x-show="step === 3" 
                     x-transition:enter="transition ease-out duration-200 transform"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-4">
                    
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-950 tracking-tight">Perkiraan uang masuk Anda per bulan?</h3>
                        <p class="text-xs text-slate-500 mt-0.5 leading-normal">
                            Angka ini menjadi dasar perhitungan batas anggaran otomatis pos kebutuhan dan tabungan.
                        </p>
                    </div>

                    <!-- Input Nominal Bulanan -->
                    <div class="p-4 sm:p-5 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-3.5">
                        <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Estimasi Pendapatan Bulanan</label>
                        
                        <div style="display: flex; align-items: center;" class="rounded-2xl bg-white border-2 border-slate-950 px-3.5 py-2.5 shadow-2xs focus-within:ring-2 focus-within:ring-[#C6F24D] transition-all">
                            <span class="text-base sm:text-lg font-mono font-black text-slate-400 select-none mr-2">Rp</span>
                            <input type="number" 
                                   x-model="monthlyIncome"
                                   placeholder="5000000" 
                                   class="w-full text-lg sm:text-xl font-mono font-black text-slate-950 bg-transparent border-0 p-0 focus:outline-none focus:ring-0 leading-none">
                        </div>

                        <!-- Pilihan Cepat (Chips) -->
                        <div class="space-y-1 pt-0.5">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Pilihan Cepat:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" @click="setIncome('2500000')" 
                                        :class="monthlyIncome == '2500000' ? 'bg-slate-950 text-[#C6F24D] border-slate-950 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50'"
                                        class="px-2.5 py-1 rounded-xl border text-[11px] font-bold font-mono transition-all duration-100 cursor-pointer">
                                    Rp 2.500.000
                                </button>
                                <button type="button" @click="setIncome('5000000')" 
                                        :class="monthlyIncome == '5000000' ? 'bg-slate-950 text-[#C6F24D] border-slate-950 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50'"
                                        class="px-2.5 py-1 rounded-xl border text-[11px] font-bold font-mono transition-all duration-100 cursor-pointer">
                                    Rp 5.000.000
                                </button>
                                <button type="button" @click="setIncome('10000000')" 
                                        :class="monthlyIncome == '10000000' ? 'bg-slate-950 text-[#C6F24D] border-slate-950 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50'"
                                        class="px-2.5 py-1 rounded-xl border text-[11px] font-bold font-mono transition-all duration-100 cursor-pointer">
                                    Rp 10.000.000
                                </button>
                                <button type="button" @click="setIncome('15000000')" 
                                        :class="monthlyIncome == '15000000' ? 'bg-slate-950 text-[#C6F24D] border-slate-950 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400 hover:bg-slate-50'"
                                        class="px-2.5 py-1 rounded-xl border text-[11px] font-bold font-mono transition-all duration-100 cursor-pointer">
                                    Rp 15.000.000+
                                </button>
                            </div>
                        </div>

                        <!-- Friendly Note -->
                        <div class="p-2.5 rounded-xl bg-teal-50 border border-teal-200/80 text-xs text-teal-900 flex items-start gap-2">
                            <x-icon name="shield-check" class="w-3.5 h-3.5 text-teal-700 shrink-0 mt-0.5" />
                            <p class="text-[10px] leading-relaxed text-teal-800">
                                Angka ini dapat Anda ubah kapan saja nanti di menu <strong>Budgets</strong>.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- MODAL FOOTER (Clean & Smooth Action Bar) -->
            <div class="px-5 py-4 sm:px-6 sm:py-4.5 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between gap-3 shrink-0">
                
                <button type="button" 
                        x-show="step > 1"
                        @click="prev()"
                        class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-xs font-bold text-slate-700 transition-all flex items-center gap-1 cursor-pointer shadow-2xs active:scale-95">
                    <x-icon name="arrow-left" class="w-3 h-3" />
                    <span>Kembali</span>
                </button>
                <div x-show="step === 1" class="text-[10px] font-medium text-slate-400">Pilih salah satu untuk lanjut</div>

                <!-- Next or Complete Button -->
                <template x-if="step < 3">
                    <button type="button" 
                            @click="next()"
                            class="px-5 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-black transition-all flex items-center gap-1.5 cursor-pointer shadow-xs active:scale-95">
                        <span>Lanjutkan</span>
                        <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                    </button>
                </template>

                <template x-if="step === 3">
                    <button type="button" 
                            @click="submit()"
                            :disabled="isSubmitting"
                            class="px-5 py-2.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] text-xs font-black transition-all flex items-center gap-1.5 cursor-pointer shadow-md active:scale-95 disabled:opacity-50">
                        <template x-if="!isSubmitting">
                            <span class="flex items-center gap-1.5">
                                <x-icon name="sparkles" class="w-3.5 h-3.5" />
                                <span>Mulai Kelola Keuangan &rarr;</span>
                            </span>
                        </template>
                        <template x-if="isSubmitting">
                            <span class="flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-[#C6F24D]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Menyimpan setup...</span>
                            </span>
                        </template>
                    </button>
                </template>

            </div>

        </div>

    </div>
    @endif
</div>
