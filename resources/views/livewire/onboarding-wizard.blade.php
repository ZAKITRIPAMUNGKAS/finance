<div>
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6 transition-all duration-300 select-none animate-fade-in"
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

             // Toggle account activation
             toggleAcc(key) {
                 this.activeAccounts[key] = !this.activeAccounts[key];
                 if (!this.activeAccounts[key]) {
                     this.balances[key] = '0';
                 }
             },

             // Quick Income Chips
             setIncome(val) {
                 this.monthlyIncome = Number(val);
                 this.rawIncomeInput = Number(val).toLocaleString('id-ID');
             },

             // Input handler for income
             onIncomeInput(e) {
                 let num = parseInt(e.target.value.replace(/\D/g, ''), 10) || 0;
                 this.monthlyIncome = num;
                 this.rawIncomeInput = num > 0 ? num.toLocaleString('id-ID') : '';
             },

             // Format balance input
             formatBalance(key, e) {
                 let num = parseInt(e.target.value.replace(/\D/g, ''), 10) || 0;
                 this.balances[key] = num > 0 ? num.toLocaleString('id-ID') : '0';
             },

             // Calculated 50/30/20 values
             get needsAmount() {
                 return Math.round(this.monthlyIncome * 0.5);
             },
             get wantsAmount() {
                 return Math.round(this.monthlyIncome * 0.3);
             },
             get savingsAmount() {
                 return Math.round(this.monthlyIncome * 0.2);
             },

             // Stepper Navigation
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

             // Final Submission
             submit() {
                 this.isSubmitting = true;
                 
                 // Clean up balances to raw numeric string
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
        <!--  MAIN FINTECH SETUP CARD CONTAINER                          -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <div class="bg-white rounded-3xl shadow-2xl border-2 border-slate-900 w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh] transition-all transform animate-scale-up">
            
            <!-- ── TOP BRAND & STEPPER HEADER ───────────────────────── -->
            <div class="px-6 py-5 border-b border-slate-100 bg-[#F8FAFC] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                
                <!-- Brand Header -->
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.svg') }}" style="width: 28px; height: 28px;" class="object-contain shrink-0" alt="PortoFinance Logo">
                    <div class="leading-tight">
                        <span class="font-black text-sm text-[#0F172A] tracking-tight block">Porto<span class="text-teal-700">Finance</span></span>
                        <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block -mt-0.5">Financial Setup</span>
                    </div>
                </div>
                
                <!-- Named Stepper Progress Indicator -->
                <div class="flex items-center gap-2 text-xs font-mono font-extrabold text-slate-400 self-start sm:self-auto">
                    <!-- Step 1 Indicator -->
                    <div class="flex items-center gap-1.5 transition-colors" :class="step === 1 ? 'text-[#0F172A]' : (step > 1 ? 'text-teal-600' : 'text-slate-300')">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] border transition-all"
                              :class="step === 1 ? 'border-slate-900 bg-[#C6F24D] text-slate-950 font-black shadow-2xs' : (step > 1 ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-200 text-slate-400')">
                            <template x-if="step > 1">✓</template>
                            <template x-if="step <= 1">1</template>
                        </span>
                        <span class="hidden sm:inline">Profil</span>
                    </div>

                    <span class="w-4 h-0.5 transition-colors" :class="step > 1 ? 'bg-teal-500' : 'bg-slate-200'"></span>

                    <!-- Step 2 Indicator -->
                    <div class="flex items-center gap-1.5 transition-colors" :class="step === 2 ? 'text-[#0F172A]' : (step > 2 ? 'text-teal-600' : 'text-slate-300')">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] border transition-all"
                              :class="step === 2 ? 'border-slate-900 bg-[#C6F24D] text-slate-950 font-black shadow-2xs' : (step > 2 ? 'border-teal-600 bg-teal-600 text-white' : 'border-slate-200 text-slate-400')">
                            <template x-if="step > 2">✓</template>
                            <template x-if="step <= 2">2</template>
                        </span>
                        <span class="hidden sm:inline">Rekening</span>
                    </div>

                    <span class="w-4 h-0.5 transition-colors" :class="step > 2 ? 'bg-teal-500' : 'bg-slate-200'"></span>

                    <!-- Step 3 Indicator -->
                    <div class="flex items-center gap-1.5 transition-colors" :class="step === 3 ? 'text-[#0F172A]' : 'text-slate-300'">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] border transition-all"
                              :class="step === 3 ? 'border-slate-900 bg-[#C6F24D] text-slate-950 font-black shadow-2xs' : 'border-slate-200 text-slate-400'">
                            3
                        </span>
                        <span class="hidden sm:inline">Anggaran</span>
                    </div>
                </div>
            </div>

            <!-- ── MODAL BODY ───────────────────────────────────────── -->
            <div class="p-6 sm:p-7 overflow-y-auto space-y-6 flex-1 text-slate-900 custom-scrollbar">

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- STEP 1: PROFIL KEUANGAN (PERSONA)                           -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div x-show="step === 1" 
                     x-transition:enter="transition ease-out duration-250 transform"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-5">
                    
                    <div class="space-y-1.5 text-center sm:text-left">
                        <span class="text-[10px] font-mono font-extrabold uppercase tracking-widest text-teal-600">Langkah 01</span>
                        <h2 class="text-xl sm:text-2xl font-black text-[#0F172A] tracking-tight leading-snug">
                            Mari kenalan. Bagaimana cara kamu mendapatkan penghasilan?
                        </h2>
                        <p class="text-xs sm:text-sm font-medium text-slate-500">
                            Pilih profil yang paling sesuai agar sistem PortoFinance menyiapkan otomatis alokasi idealmu.
                        </p>
                    </div>

                    <!-- Persona Neo-Fintech Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                        
                        <!-- 1. Pekerja Tetap -->
                        <button type="button" @click="persona = 'employee_salary'"
                                class="p-4.5 rounded-2xl border-2 text-left transition-all cursor-pointer relative flex flex-col justify-between group"
                                :class="persona === 'employee_salary' ? 'border-[#0F172A] bg-[#F4FFD6] shadow-sm scale-[1.01]' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-xl shadow-2xs">
                                        💼
                                    </div>
                                    <template x-if="persona === 'employee_salary'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-900 text-[#C6F24D] text-[10px] font-mono font-black uppercase">
                                            ✓ Terpilih
                                        </span>
                                    </template>
                                </div>
                                <h3 class="text-sm font-black text-[#0F172A] mb-1">Pekerja Tetap</h3>
                                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                    Menerima gaji rutin bulanan dengan pengeluaran yang terstruktur.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center gap-1 text-[11px] font-bold text-slate-700">
                                <span>Alokasi: Standard 50/30/20</span>
                            </div>
                        </button>

                        <!-- 2. Freelancer / Kreator -->
                        <button type="button" @click="persona = 'freelancer_project'"
                                class="p-4.5 rounded-2xl border-2 text-left transition-all cursor-pointer relative flex flex-col justify-between group"
                                :class="persona === 'freelancer_project' ? 'border-[#0F172A] bg-[#F4FFD6] shadow-sm scale-[1.01]' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-xl shadow-2xs">
                                        💻
                                    </div>
                                    <template x-if="persona === 'freelancer_project'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-900 text-[#C6F24D] text-[10px] font-mono font-black uppercase">
                                            ✓ Terpilih
                                        </span>
                                    </template>
                                </div>
                                <h3 class="text-sm font-black text-[#0F172A] mb-1">Freelancer / Kreator</h3>
                                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                    Pendapatan berbasis proyek atau komisi dengan nominal fluktuatif.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center gap-1 text-[11px] font-bold text-teal-700">
                                <span>Fitur: Income Floor P25 Guard</span>
                            </div>
                        </button>

                        <!-- 3. Pebisnis / Merchant -->
                        <button type="button" @click="persona = 'merchant_business'"
                                class="p-4.5 rounded-2xl border-2 text-left transition-all cursor-pointer relative flex flex-col justify-between group"
                                :class="persona === 'merchant_business' ? 'border-[#0F172A] bg-[#F4FFD6] shadow-sm scale-[1.01]' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-xl shadow-2xs">
                                        🏪
                                    </div>
                                    <template x-if="persona === 'merchant_business'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-900 text-[#C6F24D] text-[10px] font-mono font-black uppercase">
                                            ✓ Terpilih
                                        </span>
                                    </template>
                                </div>
                                <h3 class="text-sm font-black text-[#0F172A] mb-1">Pebisnis / Usaha</h3>
                                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                    Memisahkan cash flow operasional bisnis dengan pengeluaran pribadi.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center gap-1 text-[11px] font-bold text-blue-700">
                                <span>Fitur: Multi-Account Separation</span>
                            </div>
                        </button>

                        <!-- 4. Mahasiswa / Pelajar -->
                        <button type="button" @click="persona = 'student_creator'"
                                class="p-4.5 rounded-2xl border-2 text-left transition-all cursor-pointer relative flex flex-col justify-between group"
                                :class="persona === 'student_creator' ? 'border-[#0F172A] bg-[#F4FFD6] shadow-sm scale-[1.01]' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/50'">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-xl shadow-2xs">
                                        🎓
                                    </div>
                                    <template x-if="persona === 'student_creator'">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-900 text-[#C6F24D] text-[10px] font-mono font-black uppercase">
                                            ✓ Terpilih
                                        </span>
                                    </template>
                                </div>
                                <h3 class="text-sm font-black text-[#0F172A] mb-1">Mahasiswa / Pemula</h3>
                                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                    Mengelola uang saku bulanan dan melatih kebiasaan menabung rutin.
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center gap-1 text-[11px] font-bold text-amber-700">
                                <span>Alokasi: Smart Pocket Control</span>
                            </div>
                        </button>

                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- STEP 2: REKENING & DOMPET DIGITAL                           -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div x-show="step === 2" 
                     x-transition:enter="transition ease-out duration-250 transform"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-5">
                    
                    <div class="space-y-1.5 text-center sm:text-left">
                        <span class="text-[10px] font-mono font-extrabold uppercase tracking-widest text-teal-600">Langkah 02</span>
                        <h2 class="text-xl sm:text-2xl font-black text-[#0F172A] tracking-tight leading-snug">
                            Rekening & Dompetmu
                        </h2>
                        <p class="text-xs sm:text-sm font-medium text-slate-500">
                            Pilih akun yang ingin kamu gunakan dan masukkan saldo awal (opsional).
                        </p>
                    </div>

                    <!-- Accounts Neo-Fintech Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        
                        <!-- 1. BCA -->
                        <div class="p-3.5 rounded-2xl border-2 transition-all"
                             :class="activeAccounts.bca ? 'border-[#0F172A] bg-[#F4FFD6]/60 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('bca')">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-[#003B70] text-white font-black text-xs flex items-center justify-center shadow-2xs">
                                        BCA
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-black text-[#0F172A]">BCA Utama</h4>
                                        <span class="text-[10px] font-bold text-slate-400">Bank Transfer</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs"
                                      :class="activeAccounts.bca ? 'bg-slate-900 border-slate-900 text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <!-- Inline Saldo Awal input when active -->
                            <div x-show="activeAccounts.bca" class="mt-3 pt-2.5 border-t border-slate-200/80">
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">Saldo Awal (Rp)</label>
                                <input type="text" 
                                       :value="balances.bca"
                                       @input="formatBalance('bca', $event)"
                                       placeholder="0"
                                       class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-mono font-bold text-slate-950 focus:outline-none focus:border-slate-950">
                            </div>
                        </div>

                        <!-- 2. Mandiri -->
                        <div class="p-3.5 rounded-2xl border-2 transition-all"
                             :class="activeAccounts.mandiri ? 'border-[#0F172A] bg-[#F4FFD6]/60 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('mandiri')">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-[#002D62] text-white font-black text-[10px] flex items-center justify-center shadow-2xs">
                                        MDR
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-black text-[#0F172A]">Bank Mandiri</h4>
                                        <span class="text-[10px] font-bold text-slate-400">Livin' by Mandiri</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs"
                                      :class="activeAccounts.mandiri ? 'bg-slate-900 border-slate-900 text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.mandiri" class="mt-3 pt-2.5 border-t border-slate-200/80">
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">Saldo Awal (Rp)</label>
                                <input type="text" 
                                       :value="balances.mandiri"
                                       @input="formatBalance('mandiri', $event)"
                                       placeholder="0"
                                       class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-mono font-bold text-slate-950 focus:outline-none focus:border-slate-950">
                            </div>
                        </div>

                        <!-- 3. GoPay -->
                        <div class="p-3.5 rounded-2xl border-2 transition-all"
                             :class="activeAccounts.gopay ? 'border-[#0F172A] bg-[#F4FFD6]/60 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('gopay')">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-[#00AA13] text-white font-black text-[10px] flex items-center justify-center shadow-2xs">
                                        GPY
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-black text-[#0F172A]">GoPay</h4>
                                        <span class="text-[10px] font-bold text-slate-400">E-Wallet</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs"
                                      :class="activeAccounts.gopay ? 'bg-slate-900 border-slate-900 text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.gopay" class="mt-3 pt-2.5 border-t border-slate-200/80">
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">Saldo Awal (Rp)</label>
                                <input type="text" 
                                       :value="balances.gopay"
                                       @input="formatBalance('gopay', $event)"
                                       placeholder="0"
                                       class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-mono font-bold text-slate-950 focus:outline-none focus:border-slate-950">
                            </div>
                        </div>

                        <!-- 4. DANA -->
                        <div class="p-3.5 rounded-2xl border-2 transition-all"
                             :class="activeAccounts.dana ? 'border-[#0F172A] bg-[#F4FFD6]/60 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('dana')">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-[#118EEA] text-white font-black text-[10px] flex items-center justify-center shadow-2xs">
                                        DNA
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-black text-[#0F172A]">DANA</h4>
                                        <span class="text-[10px] font-bold text-slate-400">E-Wallet</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs"
                                      :class="activeAccounts.dana ? 'bg-slate-900 border-slate-900 text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.dana" class="mt-3 pt-2.5 border-t border-slate-200/80">
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">Saldo Awal (Rp)</label>
                                <input type="text" 
                                       :value="balances.dana"
                                       @input="formatBalance('dana', $event)"
                                       placeholder="0"
                                       class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-mono font-bold text-slate-950 focus:outline-none focus:border-slate-950">
                            </div>
                        </div>

                        <!-- 5. Bank Jago -->
                        <div class="p-3.5 rounded-2xl border-2 transition-all"
                             :class="activeAccounts.jago ? 'border-[#0F172A] bg-[#F4FFD6]/60 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('jago')">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-[#845EC2] text-white font-black text-[10px] flex items-center justify-center shadow-2xs">
                                        JGO
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-black text-[#0F172A]">Bank Jago</h4>
                                        <span class="text-[10px] font-bold text-slate-400">Digital Banking</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs"
                                      :class="activeAccounts.jago ? 'bg-slate-900 border-slate-900 text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.jago" class="mt-3 pt-2.5 border-t border-slate-200/80">
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">Saldo Awal (Rp)</label>
                                <input type="text" 
                                       :value="balances.jago"
                                       @input="formatBalance('jago', $event)"
                                       placeholder="0"
                                       class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-mono font-bold text-slate-950 focus:outline-none focus:border-slate-950">
                            </div>
                        </div>

                        <!-- 6. Dompet Tunai -->
                        <div class="p-3.5 rounded-2xl border-2 transition-all"
                             :class="activeAccounts.cash ? 'border-[#0F172A] bg-[#F4FFD6]/60 shadow-2xs' : 'border-slate-200 bg-white hover:border-slate-300'">
                            <div class="flex items-center justify-between cursor-pointer" @click="toggleAcc('cash')">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white font-black text-xs flex items-center justify-center shadow-2xs">
                                        💵
                                    </span>
                                    <div>
                                        <h4 class="text-xs font-black text-[#0F172A]">Dompet Tunai</h4>
                                        <span class="text-[10px] font-bold text-slate-400">Cash Fisik</span>
                                    </div>
                                </div>
                                <span class="w-5 h-5 rounded-full border flex items-center justify-center text-xs"
                                      :class="activeAccounts.cash ? 'bg-slate-900 border-slate-900 text-[#C6F24D]' : 'border-slate-300 bg-white text-transparent'">
                                    ✓
                                </span>
                            </div>
                            
                            <div x-show="activeAccounts.cash" class="mt-3 pt-2.5 border-t border-slate-200/80">
                                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-600 mb-1">Saldo Awal (Rp)</label>
                                <input type="text" 
                                       :value="balances.cash"
                                       @input="formatBalance('cash', $event)"
                                       placeholder="0"
                                       class="w-full px-3 py-1.5 rounded-xl bg-white border border-slate-300 text-xs font-mono font-bold text-slate-950 focus:outline-none focus:border-slate-950">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- STEP 3: ESTIMASI PEMASUKAN & SMART 50/30/20 ALLOCATION      -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <div x-show="step === 3" 
                     x-transition:enter="transition ease-out duration-250 transform"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="space-y-5">
                    
                    <div class="space-y-1.5 text-center sm:text-left">
                        <span class="text-[10px] font-mono font-extrabold uppercase tracking-widest text-teal-600">Langkah 03</span>
                        <h2 class="text-xl sm:text-2xl font-black text-[#0F172A] tracking-tight leading-snug">
                            Pendapatan & Alokasi Anggaran
                        </h2>
                        <p class="text-xs sm:text-sm font-medium text-slate-500">
                            Masukkan estimasi pemasukan bulanan untuk melihat pembagian alokasi finansial idealmu.
                        </p>
                    </div>

                    <!-- Monthly Income Input Box -->
                    <div class="p-5 rounded-2xl bg-[#F8FAFC] border-2 border-slate-200 space-y-3">
                        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">
                            Estimasi Pendapatan Bulanan
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-base font-extrabold font-mono text-slate-400">
                                Rp
                            </span>
                            <input type="text" 
                                   :value="rawIncomeInput"
                                   @input="onIncomeInput($event)"
                                   placeholder="5.000.000"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl bg-white border-2 border-slate-900 text-lg sm:text-xl font-black font-mono text-[#0F172A] focus:outline-none shadow-2xs">
                        </div>

                        <!-- Quick Preset Chips -->
                        <div class="flex flex-wrap items-center gap-2 pt-1">
                            <button type="button" @click="setIncome(3000000)"
                                    class="px-3 py-1 rounded-xl text-xs font-bold font-mono transition-all border cursor-pointer"
                                    :class="monthlyIncome === 3000000 ? 'bg-slate-900 text-[#C6F24D] border-slate-900 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300'">
                                Rp 3 Jt
                            </button>
                            <button type="button" @click="setIncome(5000000)"
                                    class="px-3 py-1 rounded-xl text-xs font-bold font-mono transition-all border cursor-pointer"
                                    :class="monthlyIncome === 5000000 ? 'bg-slate-900 text-[#C6F24D] border-slate-900 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300'">
                                Rp 5 Jt
                            </button>
                            <button type="button" @click="setIncome(10000000)"
                                    class="px-3 py-1 rounded-xl text-xs font-bold font-mono transition-all border cursor-pointer"
                                    :class="monthlyIncome === 10000000 ? 'bg-slate-900 text-[#C6F24D] border-slate-900 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300'">
                                Rp 10 Jt
                            </button>
                            <button type="button" @click="setIncome(20000000)"
                                    class="px-3 py-1 rounded-xl text-xs font-bold font-mono transition-all border cursor-pointer"
                                    :class="monthlyIncome === 20000000 ? 'bg-slate-900 text-[#C6F24D] border-slate-900 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300'">
                                Rp 20 Jt
                            </button>
                        </div>
                    </div>

                    <!-- Visual Segmented Allocation Meter -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-500">
                            <span>Breakdown Alokasi 50 / 30 / 20</span>
                            <span class="font-mono text-slate-900">Total: 100%</span>
                        </div>
                        <div class="w-full h-3 rounded-full overflow-hidden flex bg-slate-100 border border-slate-200 shadow-2xs">
                            <div class="bg-blue-600 h-full transition-all duration-300" style="width: 50%;"></div>
                            <div class="bg-purple-600 h-full transition-all duration-300" style="width: 30%;"></div>
                            <div class="bg-[#A4D928] h-full transition-all duration-300" style="width: 20%;"></div>
                        </div>
                    </div>

                    <!-- 3 Cards: Needs, Wants, Savings -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        
                        <!-- 50% Needs -->
                        <div class="p-3.5 rounded-2xl bg-blue-50/70 border-2 border-blue-200 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-black uppercase tracking-wider text-blue-700">Kebutuhan</span>
                                <span class="px-2 py-0.5 rounded-md bg-blue-200/80 text-blue-900 font-mono font-black text-[10px]">50%</span>
                            </div>
                            <div>
                                <span class="text-sm sm:text-base font-black font-mono text-[#0F172A] block"
                                      x-text="'Rp ' + needsAmount.toLocaleString('id-ID')"></span>
                                <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">Makan, sewa, listrik & tagihan</span>
                            </div>
                        </div>

                        <!-- 30% Wants -->
                        <div class="p-3.5 rounded-2xl bg-purple-50/70 border-2 border-purple-200 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-black uppercase tracking-wider text-purple-700">Keinginan</span>
                                <span class="px-2 py-0.5 rounded-md bg-purple-200/80 text-purple-900 font-mono font-black text-[10px]">30%</span>
                            </div>
                            <div>
                                <span class="text-sm sm:text-base font-black font-mono text-[#0F172A] block"
                                      x-text="'Rp ' + wantsAmount.toLocaleString('id-ID')"></span>
                                <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">Hiburan, belanja & hobi</span>
                            </div>
                        </div>

                        <!-- 20% Savings -->
                        <div class="p-3.5 rounded-2xl bg-[#F4FFD6] border-2 border-lime-300 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-800">Tabungan</span>
                                <span class="px-2 py-0.5 rounded-md bg-lime-200 text-emerald-950 font-mono font-black text-[10px]">20%</span>
                            </div>
                            <div>
                                <span class="text-sm sm:text-base font-black font-mono text-[#0F172A] block"
                                      x-text="'Rp ' + savingsAmount.toLocaleString('id-ID')"></span>
                                <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">Dana darurat & investasi</span>
                            </div>
                        </div>

                    </div>

                    <!-- Smart Recommendation Box -->
                    <div class="p-4 rounded-2xl bg-slate-900 text-white space-y-1.5 shadow-md">
                        <div class="flex items-center gap-2">
                            <span class="text-sm">✨</span>
                            <h4 class="text-xs font-black text-[#C6F24D] uppercase tracking-wider">Rekomendasi PortoFinance</h4>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Dengan pemasukan <span class="font-bold text-white font-mono" x-text="'Rp ' + monthlyIncome.toLocaleString('id-ID')"></span>/bulan, sistem otomatis mengatur alokasi idealmu. Kamu bisa menyesuaikan kategori dan budget ini kapan saja di dashboard.
                        </p>
                    </div>

                </div>

            </div>

            <!-- ── MODAL FOOTER ─────────────────────────────────────── -->
            <div class="px-6 py-4.5 border-t border-slate-100 bg-[#F8FAFC] flex items-center justify-between gap-3">
                
                <!-- Back Button -->
                <div>
                    <button type="button" x-show="step > 1" @click="prev()"
                            class="px-4 py-2.5 rounded-xl border-2 border-slate-200 bg-white font-black text-xs text-slate-700 hover:border-slate-300 hover:bg-slate-50 cursor-pointer transition-all active:scale-95 shadow-2xs">
                        &larr; Kembali
                    </button>
                </div>

                <!-- Next / Submit Button -->
                <div>
                    <!-- Step 1 & 2: Lanjutkan -->
                    <button type="button" x-show="step < 3" @click="next()"
                            class="px-6 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-[#C6F24D] font-black text-xs sm:text-sm cursor-pointer shadow-md transition-all active:scale-95 flex items-center gap-2">
                        <span>Lanjutkan</span>
                        <span>&rarr;</span>
                    </button>

                    <!-- Step 3: Mulai PortoFinance -->
                    <button type="button" x-show="step === 3" @click="submit()" :disabled="isSubmitting"
                            class="px-6 py-3.5 rounded-2xl bg-[#C6F24D] hover:bg-[#B5E63B] active:scale-95 border-2 border-slate-900 text-slate-950 font-black text-xs sm:text-sm cursor-pointer shadow-md transition-all flex items-center gap-2">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <span>Mulai PortoFinance</span>
                            <span>&rarr;</span>
                        </span>
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-slate-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menyiapkan keuanganmu...</span>
                        </span>
                    </button>
                </div>

            </div>

        </div>
    </div>
    @endif
</div>
