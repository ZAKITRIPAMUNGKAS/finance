<div x-data="{
        showTutorial: false,
        currentStep: 1,
        totalSteps: 6,
        setStep(step) {
            this.currentStep = Math.max(1, Math.min(this.totalSteps, step));
        },
        nextStep() {
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
            } else {
                this.close();
            }
        },
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },
        open() {
            this.currentStep = 1;
            this.showTutorial = true;
        },
        close() {
            this.showTutorial = false;
        }
    }"
    @open-tutorial.window="open()"
    @keydown.window.escape="if (showTutorial) close()"
    @keydown.window.arrow-right="if (showTutorial) nextStep()"
    @keydown.window.arrow-left="if (showTutorial) prevStep()"
    x-cloak>

    <!-- Modal Backdrop -->
    <div x-show="showTutorial" 
         x-transition:enter="transition-opacity ease-out duration-250"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[120] overflow-y-auto bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6">

        <!-- Modal Box with Smooth Scale-In -->
        <div @click.outside="close()"
             x-show="showTutorial"
             x-transition:enter="transition-all ease-out duration-250"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="relative w-full max-w-xl max-h-[92vh] bg-white border border-slate-200 rounded-3xl shadow-2xl overflow-hidden flex flex-col my-auto">

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!--  1. MODAL HEADER: Logo & Step Progression Tracker            -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <div class="px-4 sm:px-6 pt-4 sm:pt-5 pb-3 sm:pb-4 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0 shadow-2xs">
                        <img src="{{ asset('images/logo.svg') }}" class="w-5 h-5 sm:w-6 sm:h-6 object-contain" alt="PortoFinance Logo">
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <h3 class="text-xs sm:text-base font-extrabold text-slate-900 tracking-tight">Panduan PortoFinance</h3>
                            <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[9px] sm:text-[10px] font-mono font-bold">
                                <span x-text="currentStep"></span>/6
                            </span>
                        </div>
                        <p class="text-[10px] sm:text-[11px] text-slate-400 font-medium truncate max-w-[200px] sm:max-w-none">Panduan ringkas sistem keuangan</p>
                    </div>
                </div>

                <button @click="close()" 
                        type="button"
                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer"
                        title="Tutup (Esc)">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!--  2. STEP NAV PILLS                                          -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <div class="px-4 sm:px-6 py-2 bg-[#F8F9FA] border-b border-slate-100 flex items-center justify-between gap-1 overflow-x-auto no-scrollbar shrink-0">
                <template x-for="(stepName, idx) in ['Voice & OCR', 'Uang Bebas', 'Budget Tier', 'Proyek & Margin', 'Simulator Beli', 'Health Index']" :key="idx">
                    <button @click="setStep(idx + 1)"
                            type="button"
                            class="px-2 sm:px-2.5 py-1 rounded-lg text-[9px] sm:text-[10px] font-mono font-bold transition-all cursor-pointer whitespace-nowrap"
                            :class="currentStep === (idx + 1) ? 'bg-slate-950 text-[#C6F24D] shadow-2xs' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-200/60'">
                        <span x-text="'0' + (idx + 1)"></span>
                        <span class="hidden sm:inline ml-1" x-text="stepName"></span>
                    </button>
                </template>
            </div>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!--  3. SLIDING HORIZONTAL CAROUSEL (Buttery Smooth 60FPS)       -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <div class="overflow-y-auto overflow-x-hidden w-full relative max-h-[62vh]">
                <div class="flex transition-transform duration-300 ease-out will-change-transform"
                     :style="`transform: translateX(-${(currentStep - 1) * 100}%);`">

                    <!-- SLIDE 1: VOICE & OCR SCAN -->
                    <div class="w-full shrink-0 p-5 sm:p-6 space-y-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0">
                                <x-icon name="mic" class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 font-mono block">01 &bull; Pencatatan Cepat</span>
                                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Perintah Suara & Scan Struk Otomatis</h4>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            Tekan tombol <kbd class="px-1.5 py-0.5 bg-slate-100 rounded text-[10px] font-mono text-slate-800 border border-slate-200">Ctrl+K</kbd> untuk membuka quick modal. Anda bisa berbicara santai dalam bahasa Indonesia atau unggah foto struk kasir untuk mengisi form secara otomatis.
                        </p>

                        <div class="p-3.5 bg-[#F8F9FA] rounded-2xl border border-slate-200/80 space-y-2.5">
                            <div class="flex items-center justify-between text-[10px] font-mono font-bold text-slate-400">
                                <span>Input Suara Natural:</span>
                                <span class="text-emerald-600 font-sans font-bold">NLP Smart Extractor</span>
                            </div>
                            <div class="p-2.5 bg-white rounded-xl border border-slate-200 text-xs font-mono text-slate-800">
                                <span class="text-slate-400">&ldquo;</span>Makan mie ayam 20 ribu bayar pakai cash<span class="text-slate-400">&rdquo;</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-xs font-mono">
                                <div class="p-2 bg-white rounded-lg border border-slate-200">
                                    <span class="text-[9px] text-slate-400 block font-sans">Deskripsi</span>
                                    <span class="font-bold text-slate-900 truncate block">Makan Mie Ayam</span>
                                </div>
                                <div class="p-2 bg-white rounded-lg border border-slate-200">
                                    <span class="text-[9px] text-slate-400 block font-sans">Nominal</span>
                                    <span class="font-bold text-emerald-600 block">Rp 20.000</span>
                                </div>
                                <div class="p-2 bg-white rounded-lg border border-slate-200">
                                    <span class="text-[9px] text-slate-400 block font-sans">Akun</span>
                                    <span class="font-bold text-slate-900 block">Cash Dompet</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SLIDE 2: AVAILABLE MONEY -->
                    <div class="w-full shrink-0 p-5 sm:p-6 space-y-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0">
                                <x-icon name="wallet" class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 font-mono block">02 &bull; Likuiditas Riil</span>
                                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Konsep Uang Bebas (Available Money)</h4>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            Total saldo di rekening bank bukan uang bebas belanja. PortoFinance mengisolasi tabungan wishlist impian agar tidak terpakai secara tidak sengaja.
                        </p>

                        <div class="p-3.5 bg-[#F8F9FA] rounded-2xl border border-slate-200/80 space-y-2.5">
                            <div class="flex items-center justify-between text-[10px] font-mono font-bold text-slate-400">
                                <span>Kalkulasi Arus Kas:</span>
                                <span class="text-slate-900 font-bold">Real-Time Sync</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-center text-xs font-mono">
                                <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                                    <span class="text-[9px] text-slate-400 block font-sans">Total Saldo Bank</span>
                                    <span class="font-bold text-slate-900">Rp 10.000.000</span>
                                </div>
                                <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                                    <span class="text-[9px] text-rose-500 block font-sans">Terkunci Wishlist</span>
                                    <span class="font-bold text-rose-600">- Rp 3.500.000</span>
                                </div>
                                <div class="p-2.5 bg-slate-950 text-[#C6F24D] rounded-xl">
                                    <span class="text-[9px] text-slate-400 block font-sans">Available Money</span>
                                    <span class="font-bold text-sm leading-tight block">Rp 6.500.000</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SLIDE 3: TIER BUDGETING -->
                    <div class="w-full shrink-0 p-5 sm:p-6 space-y-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0">
                                <x-icon name="pie-chart" class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 font-mono block">03 &bull; Anti-Gaji Tetap</span>
                                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Adaptive 3-Tier Waterfall Budgeting</h4>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            Pendapatan freelance fluktuatif. Sistem memprioritaskan biaya hidup bertahan sebelum mengizinkan alokasi ke operasional dan gaya hidup.
                        </p>

                        <div class="space-y-2 text-xs">
                            <div class="p-2.5 bg-[#F8F9FA] rounded-xl border border-slate-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-900 text-white font-mono text-[9px] font-bold">Tier 1</span>
                                    <span class="font-bold text-slate-900">Survival Floor</span>
                                </div>
                                <span class="text-slate-500 text-[11px]">Makan, sewa, listrik, & kebutuhan pokok</span>
                            </div>
                            <div class="p-2.5 bg-[#F8F9FA] rounded-xl border border-slate-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-200 text-slate-800 font-mono text-[9px] font-bold">Tier 2</span>
                                    <span class="font-bold text-slate-900">Operasional Kerja</span>
                                </div>
                                <span class="text-slate-500 text-[11px]">Software subscription, internet, & meeting</span>
                            </div>
                            <div class="p-2.5 bg-[#F8F9FA] rounded-xl border border-slate-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 rounded bg-[#C6F24D] text-slate-950 font-mono text-[9px] font-bold">Tier 3</span>
                                    <span class="font-bold text-slate-900">Lifestyle & Surplus</span>
                                </div>
                                <span class="text-slate-500 text-[11px]">Dialokasikan hanya jika pemasukan surplus</span>
                            </div>
                        </div>
                    </div>

                    <!-- SLIDE 4: PROJECTS & INVOICES -->
                    <div class="w-full shrink-0 p-5 sm:p-6 space-y-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0">
                                <x-icon name="briefcase" class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 font-mono block">04 &bull; Bisnis Freelance</span>
                                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Manajemen Proyek, Invoice & Margin Laba</h4>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            Pantau pembayaran DP klien, tagihan invoice belum lunas, dan modal pengeluaran proyek untuk melihat margin laba bersih tiap pekerjaan.
                        </p>

                        <div class="p-3.5 bg-[#F8F9FA] rounded-2xl border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-900">Redesign Aplikasi Mobile</span>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-mono text-[10px] font-bold">85% Profit Margin</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-xs font-mono">
                                <div class="p-2 bg-white rounded-lg border border-slate-200">
                                    <span class="text-[9px] text-slate-400 font-sans block">Nilai Proyek</span>
                                    <span class="font-bold text-slate-900">Rp 15.000.000</span>
                                </div>
                                <div class="p-2 bg-white rounded-lg border border-slate-200">
                                    <span class="text-[9px] text-slate-400 font-sans block">Biaya Modal</span>
                                    <span class="font-bold text-rose-600">Rp 2.250.000</span>
                                </div>
                                <div class="p-2 bg-white rounded-lg border border-slate-200">
                                    <span class="text-[9px] text-slate-400 font-sans block">Laba Bersih</span>
                                    <span class="font-bold text-emerald-600">+Rp 12.750.000</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SLIDE 5: PURCHASE SIMULATOR -->
                    <div class="w-full shrink-0 p-5 sm:p-6 space-y-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0">
                                <x-icon name="calculator" class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 font-mono block">05 &bull; Evaluasi Belanja</span>
                                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Simulator "Can I Afford This?"</h4>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            Sebelum membeli peralatan kerja baru (kamera, laptop, lisensi), uji dampaknya terhadap runway kas dan dana darurat Anda.
                        </p>

                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-1">
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px] block">Aman (Safe)</span>
                                <span class="text-[10px] text-slate-500 font-mono block">Sisa runway &gt; 3 bulan</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-1">
                                <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold text-[10px] block">Hati-Hati</span>
                                <span class="text-[10px] text-slate-500 font-mono block">Sisa runway 1-3 bulan</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-1">
                                <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold text-[10px] block">Risiko Tinggi</span>
                                <span class="text-[10px] text-slate-500 font-mono block">Menguras dana darurat</span>
                            </div>
                        </div>
                    </div>

                    <!-- SLIDE 6: FINANCIAL HEALTH INDEX -->
                    <div class="w-full shrink-0 p-5 sm:p-6 space-y-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0">
                                <x-icon name="activity" class="w-4 h-4" />
                            </div>
                            <div>
                                <span class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 font-mono block">06 &bull; Diagnosa Menyeluruh</span>
                                <h4 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Financial Health Index (5 Pilar)</h4>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            Evaluasi komprehensif mengukur ketahanan keuangan Anda dari 5 dimensi: Cash Runway, Volatility Buffer, Savings Rate, Piutang Invoice, dan Rasio Bisnis.
                        </p>

                        <div class="p-3.5 bg-[#F8F9FA] rounded-2xl border border-slate-200/80 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0 shadow-2xs">
                                    <x-icon name="shield-check" class="w-6 h-6" strokeWidth="2.2" />
                                </div>
                                <div>
                                    <span class="font-bold text-xs text-slate-900 block">Kondisi Finansial Prima</span>
                                    <span class="text-[10px] text-slate-500 font-mono">Skor 85/100 &bull; Runway 6+ Bulan</span>
                                </div>
                            </div>
                            <a href="{{ route('analytics') }}" @click="close()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-900 hover:bg-slate-50 transition-colors shrink-0 shadow-2xs">
                                Buka Analytics &rarr;
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!--  4. MODAL FOOTER                                            -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <div class="px-5 sm:px-6 py-3.5 bg-[#F8F9FA] border-t border-slate-100 flex items-center justify-between shrink-0">
                <!-- Dots Indicator & Theory Link -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <template x-for="step in totalSteps" :key="step">
                            <button @click="setStep(step)" 
                                    type="button"
                                    class="h-2 rounded-full transition-all duration-200 cursor-pointer"
                                    :class="currentStep === step ? 'w-6 bg-slate-950' : 'w-2 bg-slate-300 hover:bg-slate-400'">
                            </button>
                        </template>
                    </div>
                    <button type="button" 
                            @click="close(); setTimeout(() => $dispatch('open-finance-theory'), 120)" 
                            class="hidden sm:inline-flex items-center gap-1 text-[11px] font-bold text-slate-500 hover:text-slate-950 transition-colors">
                        <span>🏛️ Teori Finansial</span>
                    </button>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center gap-2">
                    <button type="button" 
                            x-show="currentStep > 1" 
                            @click="prevStep()" 
                            class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">
                        &larr; Kembali
                    </button>
                    
                    <button type="button" 
                            @click="nextStep()" 
                            class="px-5 py-2 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-extrabold shadow-sm active-tap transition-all flex items-center gap-1.5 cursor-pointer">
                        <span x-text="currentStep === totalSteps ? 'Selesai & Mulai 🚀' : 'Lanjut →'"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
