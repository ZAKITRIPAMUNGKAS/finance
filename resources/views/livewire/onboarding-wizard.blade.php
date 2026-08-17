<div>
    @if($isOpen)
    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  MODAL BACKDROP (ISOLATED & CENTERED)                       -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="fixed inset-0 z-50 overflow-hidden bg-[#0F172A]/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5 select-none animate-fade-in"
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
             monthlyIncome: 5000000,
             rawIncomeInput: '5.000.000',
             isSubmitting: false,

             // Toggle account active status
             toggleAcc(key) {
                 this.activeAccounts[key] = !this.activeAccounts[key];
                 if (!this.activeAccounts[key]) {
                     this.balances[key] = '0';
                 }
             },

             // Preset Income Chips
             setIncome(val) {
                 this.monthlyIncome = Number(val);
                 this.rawIncomeInput = Number(val).toLocaleString('id-ID');
             },

             // Raw Income Input Formatter
             onIncomeInput(e) {
                 let num = parseInt(e.target.value.replace(/\D/g, ''), 10) || 0;
                 this.monthlyIncome = num;
                 this.rawIncomeInput = num > 0 ? num.toLocaleString('id-ID') : '';
             },

             // Balance input formatter
             formatBalance(key, e) {
                 let num = parseInt(e.target.value.replace(/\D/g, ''), 10) || 0;
                 this.balances[key] = num > 0 ? num.toLocaleString('id-ID') : '0';
             },

             // 50/30/20 Smart Formula
             get needsAmount() {
                 return Math.round(this.monthlyIncome * 0.5);
             },
             get wantsAmount() {
                 return Math.round(this.monthlyIncome * 0.3);
             },
             get savingsAmount() {
                 return Math.round(this.monthlyIncome * 0.2);
             },

             // Navigation
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

             // Submit to Livewire
             submit() {
                 this.isSubmitting = true;
                 let cleanBalances = {};
                 for (let k in this.balances) {
                     cleanBalances[k] = String(this.balances[k]).replace(/\./g, '');
                 }

                 $wire.saveOnboarding({
                     persona: this.persona,
                     activeAccounts: this.activeAccounts,
                     accountBalances: cleanBalances,
                     monthlyIncome: String(this.monthlyIncome)
                 });
             }
         }">
        
        <!-- ═══════════════════════════════════════════════════════════ -->
        <!--  MAIN MODAL CARD (SOLID 3-PART FLEX COLUMN ARCHITECTURE)    -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div class="bg-white rounded-[24px] shadow-2xl border border-slate-200/90 w-full max-w-[640px] max-h-[90vh] flex flex-col overflow-hidden transition-all transform animate-scale-up">
            
            <!-- ── PART 1: COMPACT HEADER (STABLE & FIXED TOP) ──────── -->
            <header class="px-5 py-4 sm:px-6 sm:py-4.5 border-b border-slate-100 bg-[#F8FAFC] shrink-0">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <!-- Brand Eyebrow -->
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('images/logo.svg') }}" style="width: 26px; height: 26px;" class="object-contain shrink-0" alt="PortoFinance Logo">
                        <div class="leading-none">
                            <span class="font-black text-sm text-[#0F172A] tracking-tight block">Porto<span class="text-teal-700">Finance</span></span>
                            <span class="text-[8px] font-mono font-bold uppercase tracking-wider text-slate-400 block mt-0.5">Financial Setup</span>
                        </div>
                    </div>

                    <!-- Step Badge -->
                    <div class="px-2.5 py-0.5 rounded-full bg-white border border-slate-200 text-[11px] font-mono font-bold text-slate-600 shadow-2xs">
                        <span x-text="step"></span> <span class="text-slate-300">/</span> <span>3</span>
                    </div>
                </div>

                <!-- Horizontal Stepper Bar -->
                <div class="flex items-center justify-between gap-2 pt-1 text-[11px] font-mono font-bold">
                    
                    <!-- 01 Profil -->
                    <div class="flex-1 flex flex-col gap-1.5">
                        <div class="h-1.5 rounded-full transition-all duration-300"
                             :class="step === 1 ? 'bg-[#0F172A]' : (step > 1 ? 'bg-[#008F83]' : 'bg-slate-200')"></div>
                        <div class="flex items-center gap-1 transition-colors"
                             :class="step === 1 ? 'text-[#0F172A]' : (step > 1 ? 'text-[#008F83]' : 'text-slate-400')">
                            <span class="text-[9px]">01</span>
                            <span class="truncate">Profil</span>
                        </div>
                    </div>

                    <span class="text-slate-300 text-[9px] mb-3">&bull;</span>

                    <!-- 02 Rekening -->
                    <div class="flex-1 flex flex-col gap-1.5">
                        <div class="h-1.5 rounded-full transition-all duration-300"
                             :class="step === 2 ? 'bg-[#0F172A]' : (step > 2 ? 'bg-[#008F83]' : 'bg-slate-200')"></div>
                        <div class="flex items-center gap-1 transition-colors"
                             :class="step === 2 ? 'text-[#0F172A]' : (step > 2 ? 'text-[#008F83]' : 'text-slate-400')">
                            <span class="text-[9px]">02</span>
                            <span class="truncate">Rekening</span>
                        </div>
                    </div>

                    <span class="text-slate-300 text-[9px] mb-3">&bull;</span>

                    <!-- 03 Anggaran -->
                    <div class="flex-1 flex flex-col gap-1.5">
                        <div class="h-1.5 rounded-full transition-all duration-300"
                             :class="step === 3 ? 'bg-[#0F172A]' : 'bg-slate-200'"></div>
                        <div class="flex items-center gap-1 transition-colors"
                             :class="step === 3 ? 'text-[#0F172A]' : 'text-slate-400'">
                            <span class="text-[9px]">03</span>
                            <span class="truncate">Anggaran</span>
                        </div>
                    </div>

                </div>
            </header>

            <!-- ── PART 2: SCROLLABLE CONTENT AREA ──────────────────── -->
            <main class="p-5 sm:p-6 overflow-y-auto flex-1 min-h-0 space-y-5 text-[#0F172A] custom-scrollbar">

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- STEP 1: PROFIL KEUANGAN                                 -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <div x-show="step === 1" 
                     x-transition:enter="transition ease-out duration-250 transform"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-4">
                    
                    <div class="space-y-1">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#008F83] block">Langkah 01</span>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-[#0F172A] tracking-tight leading-snug">
                            Mari kenalan. Bagaimana cara kamu mendapatkan penghasilan?
                        </h2>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Pilih profil yang paling sesuai agar PortoFinance dapat menyesuaikan pengaturan keuanganmu.
                        </p>
                    </div>

                    <!-- 4 Persona Cards (Grid 2 col) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        
                        <!-- 1. Pekerja Tetap -->
                        <button type="button" @click="persona = 'employee_salary'"
                                class="p-4 rounded-2xl text-left transition-all cursor-pointer flex flex-col justify-between group h-full"
                                :class="persona === 'employee_salary' ? 'border-2 border-[#0F172A] bg-[#F7FFD9] shadow-2xs' : 'border border-[#E2E8F0] bg-white hover:border-slate-300'">
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-lg shadow-2xs">
                                        💼
                                    </div>
                                    <span x-show="persona === 'employee_salary'" class="w-5 h-5 rounded-full bg-[#0F172A] text-[#C6F24D] flex items-center justify-center text-[10px] font-black">
                                        ✓
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-[#0F172A] mb-1">Pekerja Tetap</h3>
                                <p class="text-xs text-[#64748B] leading-relaxed">
                                    Menerima gaji rutin bulanan dengan pengeluaran yang terstruktur.
                                </p>
                            </div>
                            <div class="mt-3.5 pt-2.5 border-t border-slate-200/70 text-[10px] font-bold text-slate-700">
                                Alokasi · Standard 50/30/20
                            </div>
                        </button>

                        <!-- 2. Freelancer / Kreator -->
                        <button type="button" @click="persona = 'freelancer_project'"
                                class="p-4 rounded-2xl text-left transition-all cursor-pointer flex flex-col justify-between group h-full"
                                :class="persona === 'freelancer_project' ? 'border-2 border-[#0F172A] bg-[#F7FFD9] shadow-2xs' : 'border border-[#E2E8F0] bg-white hover:border-slate-300'">
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-lg shadow-2xs">
                                        💻
                                    </div>
                                    <span x-show="persona === 'freelancer_project'" class="w-5 h-5 rounded-full bg-[#0F172A] text-[#C6F24D] flex items-center justify-center text-[10px] font-black">
                                        ✓
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-[#0F172A] mb-1">Freelancer / Kreator</h3>
                                <p class="text-xs text-[#64748B] leading-relaxed">
                                    Pendapatan berbasis proyek dengan nominal yang fluktuatif.
                                </p>
                            </div>
                            <div class="mt-3.5 pt-2.5 border-t border-slate-200/70 text-[10px] font-bold text-[#008F83]">
                                Fitur · Income Floor Guard
                            </div>
                        </button>

                        <!-- 3. Pebisnis / Usaha -->
                        <button type="button" @click="persona = 'merchant_business'"
                                class="p-4 rounded-2xl text-left transition-all cursor-pointer flex flex-col justify-between group h-full"
                                :class="persona === 'merchant_business' ? 'border-2 border-[#0F172A] bg-[#F7FFD9] shadow-2xs' : 'border border-[#E2E8F0] bg-white hover:border-slate-300'">
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-lg shadow-2xs">
                                        🏪
                                    </div>
                                    <span x-show="persona === 'merchant_business'" class="w-5 h-5 rounded-full bg-[#0F172A] text-[#C6F24D] flex items-center justify-center text-[10px] font-black">
                                        ✓
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-[#0F172A] mb-1">Pebisnis / Usaha</h3>
                                <p class="text-xs text-[#64748B] leading-relaxed">
                                    Memisahkan cash flow operasional bisnis dengan pengeluaran pribadi.
                                </p>
                            </div>
                            <div class="mt-3.5 pt-2.5 border-t border-slate-200/70 text-[10px] font-bold text-blue-700">
                                Fitur · Multi-Account Split
                            </div>
                        </button>

                        <!-- 4. Mahasiswa / Pelajar -->
                        <button type="button" @click="persona = 'student_creator'"
                                class="p-4 rounded-2xl text-left transition-all cursor-pointer flex flex-col justify-between group h-full"
                                :class="persona === 'student_creator' ? 'border-2 border-[#0F172A] bg-[#F7FFD9] shadow-2xs' : 'border border-[#E2E8F0] bg-white hover:border-slate-300'">
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-lg shadow-2xs">
                                        🎓
                                    </div>
                                    <span x-show="persona === 'student_creator'" class="w-5 h-5 rounded-full bg-[#0F172A] text-[#C6F24D] flex items-center justify-center text-[10px] font-black">
                                        ✓
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-[#0F172A] mb-1">Mahasiswa / Pemula</h3>
                                <p class="text-xs text-[#64748B] leading-relaxed">
                                    Mengelola uang saku bulanan dan melatih kebiasaan menabung rutin.
                                </p>
                            </div>
                            <div class="mt-3.5 pt-2.5 border-t border-slate-200/70 text-[10px] font-bold text-amber-700">
                                Alokasi · Pocket Control
                            </div>
                        </button>

                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- STEP 2: REKENING & DOMPET                               -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <div x-show="step === 2" 
                     x-transition:enter="transition ease-out duration-250 transform"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-4">
                    
                    <div class="space-y-1">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#008F83] block">Langkah 02</span>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-[#0F172A] tracking-tight leading-snug">
                            Rekening & dompetmu
                        </h2>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Pilih akun yang ingin kamu gunakan. Saldo awal bersifat opsional.
                        </p>
                    </div>

                    <!-- All 11 Accounts Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                        
                        <!-- 1. BCA -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.bca ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('bca')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="BCA" type="bank" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">BCA Utama</h4>
                                        <span class="text-[10px] text-slate-400">Bank Transfer</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.bca ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.bca" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.bca"
                                           @input="formatBalance('bca', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 2. Mandiri -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.mandiri ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('mandiri')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="Mandiri" type="bank" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">Bank Mandiri</h4>
                                        <span class="text-[10px] text-slate-400">Livin' by Mandiri</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.mandiri ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.mandiri" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.mandiri"
                                           @input="formatBalance('mandiri', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 3. BRI -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.bri ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('bri')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="BRI" type="bank" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">Bank BRI</h4>
                                        <span class="text-[10px] text-slate-400">BRImo</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.bri ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.bri" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.bri"
                                           @input="formatBalance('bri', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 4. BNI -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.bni ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('bni')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="BNI" type="bank" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">Bank BNI</h4>
                                        <span class="text-[10px] text-slate-400">BNI Mobile</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.bni ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.bni" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.bni"
                                           @input="formatBalance('bni', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 5. Bank Jago -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.jago ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('jago')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="Bank Jago" type="bank" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">Bank Jago</h4>
                                        <span class="text-[10px] text-slate-400">Digital Banking</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.jago ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.jago" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.jago"
                                           @input="formatBalance('jago', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 6. SeaBank -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.seabank ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('seabank')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="SeaBank" type="bank" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">SeaBank</h4>
                                        <span class="text-[10px] text-slate-400">Digital Banking</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.seabank ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.seabank" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.seabank"
                                           @input="formatBalance('seabank', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 7. GoPay -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.gopay ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('gopay')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="GoPay" type="ewallet" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">GoPay</h4>
                                        <span class="text-[10px] text-slate-400">E-Wallet</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.gopay ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.gopay" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.gopay"
                                           @input="formatBalance('gopay', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 8. OVO -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.ovo ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('ovo')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="OVO" type="ewallet" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">OVO</h4>
                                        <span class="text-[10px] text-slate-400">E-Wallet</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.ovo ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.ovo" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.ovo"
                                           @input="formatBalance('ovo', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 9. DANA -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.dana ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('dana')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="DANA" type="ewallet" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">DANA</h4>
                                        <span class="text-[10px] text-slate-400">E-Wallet</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.dana ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.dana" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.dana"
                                           @input="formatBalance('dana', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 10. ShopeePay -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.shopeepay ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('shopeepay')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="ShopeePay" type="ewallet" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">ShopeePay</h4>
                                        <span class="text-[10px] text-slate-400">E-Wallet</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.shopeepay ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.shopeepay" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.shopeepay"
                                           @input="formatBalance('shopeepay', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                        <!-- 11. Dompet Tunai -->
                        <div class="p-3.5 rounded-2xl transition-all"
                             :class="activeAccounts.cash ? 'border-2 border-[#0F172A] bg-white shadow-2xs' : 'border border-[#E2E8F0] bg-white opacity-70 hover:opacity-100'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('cash')">
                                <div class="flex items-center gap-2.5">
                                    <x-account-logo name="Dompet Tunai" type="cash" class="w-8 h-8 rounded-xl shrink-0" />
                                    <div>
                                        <h4 class="text-xs font-bold text-[#0F172A]">Dompet Tunai</h4>
                                        <span class="text-[10px] text-slate-400">Cash Fisik</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs transition-colors"
                                      :class="activeAccounts.cash ? 'bg-[#0F172A] border-[#0F172A] text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.cash" class="mt-3 pt-2 border-t border-slate-100 space-y-1">
                                <label class="block text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400">Saldo Awal</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold font-mono text-slate-400 select-none">
                                        Rp
                                    </span>
                                    <input type="text" 
                                           :value="balances.cash"
                                           @input="formatBalance('cash', $event)"
                                           placeholder="0"
                                           class="w-full h-10 rounded-xl border border-slate-300 pl-12 pr-4 text-xs font-mono font-bold text-slate-900 focus:border-slate-950 focus:ring-0 bg-white shadow-2xs">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- STEP 3: ANGGARAN & ALOKASI 50/30/20                     -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <div x-show="step === 3" 
                     x-transition:enter="transition ease-out duration-250 transform"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-4">
                    
                    <div class="space-y-1">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#008F83] block">Langkah 03</span>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-[#0F172A] tracking-tight leading-snug">
                            Atur pemasukanmu
                        </h2>
                        <p class="text-xs sm:text-sm text-[#64748B] leading-relaxed">
                            Masukkan estimasi pemasukan bulanan untuk melihat pembagian anggaran idealmu.
                        </p>
                    </div>

                    <!-- Income Input Card -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-3">
                        <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-500">
                            ESTIMASI PENDAPATAN BULANAN
                        </label>
                        
                        <!-- Strictly Non-Overlapping Currency Input -->
                        <div class="relative">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm sm:text-base font-bold font-mono text-slate-400 select-none">
                                Rp
                            </span>
                            <input type="text" 
                                   :value="rawIncomeInput"
                                   @input="onIncomeInput($event)"
                                   placeholder="0"
                                   class="w-full h-12 sm:h-14 rounded-xl border-2 border-slate-900 bg-white pl-14 pr-4 text-base sm:text-lg font-black font-mono text-[#0F172A] focus:outline-none focus:border-slate-950 focus:ring-0 shadow-2xs">
                        </div>

                        <!-- Quick Chips -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            <button type="button" @click="setIncome(3000000)"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono transition-all border cursor-pointer"
                                    :class="monthlyIncome === 3000000 ? 'bg-[#0F172A] text-[#C6F24D] border-[#0F172A]' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'">
                                Rp 3 Jt
                            </button>
                            <button type="button" @click="setIncome(5000000)"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono transition-all border cursor-pointer"
                                    :class="monthlyIncome === 5000000 ? 'bg-[#0F172A] text-[#C6F24D] border-[#0F172A]' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'">
                                Rp 5 Jt
                            </button>
                            <button type="button" @click="setIncome(10000000)"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono transition-all border cursor-pointer"
                                    :class="monthlyIncome === 10000000 ? 'bg-[#0F172A] text-[#C6F24D] border-[#0F172A]' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'">
                                Rp 10 Jt
                            </button>
                            <button type="button" @click="setIncome(20000000)"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono transition-all border cursor-pointer"
                                    :class="monthlyIncome === 20000000 ? 'bg-[#0F172A] text-[#C6F24D] border-[#0F172A]' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300'">
                                Rp 20 Jt
                            </button>
                        </div>
                    </div>

                    <!-- Budget Visual Breakdown Section -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-500">
                            <span>Pembagian Anggaran</span>
                            <span class="font-mono text-[#0F172A] font-extrabold" x-text="'Rp ' + Number(monthlyIncome).toLocaleString('id-ID')"></span>
                        </div>

                        <!-- Segmented Progress Bar -->
                        <div class="w-full h-2.5 rounded-full overflow-hidden flex bg-slate-100 border border-slate-200">
                            <div class="bg-blue-600 h-full transition-all duration-300" style="width: 50%;"></div>
                            <div class="bg-purple-600 h-full transition-all duration-300" style="width: 30%;"></div>
                            <div class="bg-[#A4D928] h-full transition-all duration-300" style="width: 20%;"></div>
                        </div>

                        <!-- 3 Detail Allocation Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            
                            <!-- 50% Kebutuhan -->
                            <div class="p-3 rounded-xl bg-blue-50/60 border border-blue-200/80">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold uppercase text-blue-700">Kebutuhan</span>
                                    <span class="text-[10px] font-mono font-black text-blue-900 bg-blue-100 px-1.5 py-0.5 rounded">50%</span>
                                </div>
                                <div class="text-sm font-black font-mono text-[#0F172A]"
                                     x-text="'Rp ' + needsAmount.toLocaleString('id-ID')"></div>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Makan, listrik, sewa</span>
                            </div>

                            <!-- 30% Keinginan -->
                            <div class="p-3 rounded-xl bg-purple-50/60 border border-purple-200/80">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold uppercase text-purple-700">Keinginan</span>
                                    <span class="text-[10px] font-mono font-black text-purple-900 bg-purple-100 px-1.5 py-0.5 rounded">30%</span>
                                </div>
                                <div class="text-sm font-black font-mono text-[#0F172A]"
                                     x-text="'Rp ' + wantsAmount.toLocaleString('id-ID')"></div>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Hiburan, belanja & hobi</span>
                            </div>

                            <!-- 20% Tabungan -->
                            <div class="p-3 rounded-xl bg-[#F7FFD9] border border-lime-300">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold uppercase text-emerald-800">Tabungan</span>
                                    <span class="text-[10px] font-mono font-black text-emerald-950 bg-lime-200 px-1.5 py-0.5 rounded">20%</span>
                                </div>
                                <div class="text-sm font-black font-mono text-[#0F172A]"
                                     x-text="'Rp ' + savingsAmount.toLocaleString('id-ID')"></div>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Dana darurat & investasi</span>
                            </div>

                        </div>
                    </div>

                    <!-- Smart Recommendation Box -->
                    <div class="p-3.5 rounded-xl bg-[#0F172A] text-white space-y-1 shadow-sm">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs">✨</span>
                            <h4 class="text-[10px] font-mono font-black text-[#C6F24D] uppercase tracking-wider">Rekomendasi PortoFinance</h4>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Dengan pemasukan <span class="font-bold text-white font-mono" x-text="'Rp ' + Number(monthlyIncome).toLocaleString('id-ID')"></span>/bulan, sistem otomatis mengatur alokasi idealmu. Kamu bisa menyesuaikannya kapan saja.
                        </p>
                    </div>

                </div>

            </main>

            <!-- ── PART 3: STABLE FOOTER (NEVER OVERLAPPING CONTENT) ── -->
            <footer class="px-5 py-3.5 sm:px-6 sm:py-4 border-t border-slate-100 bg-[#F8FAFC] shrink-0 flex items-center justify-between gap-3">
                
                <!-- Back Button -->
                <div>
                    <button type="button" x-show="step > 1" @click="prev()"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white font-bold text-xs text-slate-700 hover:border-slate-300 hover:bg-slate-50 cursor-pointer transition-all active:scale-95 shadow-2xs">
                        &larr; Kembali
                    </button>
                </div>

                <!-- Next / Submit Button -->
                <div>
                    <!-- Step 1 & 2: Lanjutkan -->
                    <button type="button" x-show="step < 3" @click="next()"
                            class="px-5 py-2.5 rounded-xl bg-[#0F172A] hover:bg-slate-800 text-[#C6F24D] font-black text-xs sm:text-sm cursor-pointer shadow-sm transition-all active:scale-95 flex items-center gap-1.5">
                        <span>Lanjutkan</span>
                        <span>&rarr;</span>
                    </button>

                    <!-- Step 3: Mulai PortoFinance -->
                    <button type="button" x-show="step === 3" @click="submit()" :disabled="isSubmitting"
                            class="px-6 py-2.5 rounded-xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-95 border-2 border-[#0F172A] text-[#0F172A] font-black text-xs sm:text-sm cursor-pointer shadow-sm transition-all flex items-center gap-2">
                        <span x-show="!isSubmitting" class="flex items-center gap-1.5">
                            <span>Mulai PortoFinance</span>
                            <span>&rarr;</span>
                        </span>
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-[#0F172A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menyiapkan keuanganmu...</span>
                        </span>
                    </button>
                </div>

            </footer>

        </div>
    </div>
    @endif
</div>
