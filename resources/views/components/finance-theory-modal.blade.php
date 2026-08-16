<script>
    window.financeTheoryModalComponent = function() {
        return {
            showTheory: false,
            activeTab: 0,
            dontShowAgain: false,
            theories: [
                {
                    id: 'smoothing',
                    title: 'Variable Income Smoothing',
                    subtitle: 'Teori Perataan Pendapatan Fluktuatif',
                    badge: 'Cashflow Liquidity',
                    formula: 'Available Money = Total Saldo Likuid - Buffer Minimum - Komitmen Proyek',
                    problem: 'Freelancer rentan mengalami ilusi saldo rekening besar sehabis gajian proyek, lalu terjebak defisit di bulan sepi klien (feast or famine cycle).',
                    solution: 'PortoFinance tidak hanya menampilkan saldo total, melainkan menghitung Uang Bebas Belanja (Available Money) real-time agar Anda hanya membelanjakan porsi aman tanpa mengorbankan dana cadangan hidup.',
                    benefit: 'Bebas dari rasa panik di akhir bulan atau saat jeda antar proyek.'
                },
                {
                    id: 'dual_entity',
                    title: 'Dual-Entity Net Profit Margin',
                    subtitle: 'Pemisahan Kas Bisnis vs Kas Pribadi',
                    badge: 'Business Accounting',
                    formula: 'Net Margin = ((Revenue - Biaya OpEx & Tools) / Revenue) x 100%',
                    problem: 'Mencampuradukkan uang muka (DP) klien dengan uang saku pribadi sehingga biaya operasional, langganan software, dan pajak terpakai tanpa sadar.',
                    solution: 'Setiap project freelance memiliki alokasi anggaran sendiri. Margin profit dihitung otomatis agar Anda tahu persis berapa gaji bersih riil yang layak Anda tarik ke rekening pribadi.',
                    benefit: 'Ketahui efisiensi setiap klien dan pastikan setiap proyek menghasilkan laba nyata.'
                },
                {
                    id: 'sinking_fund',
                    title: 'Sinking Fund & Anti-Impulse',
                    subtitle: 'Teori Pembelian Terukur & Bebas Sesal',
                    badge: 'Behavioral Finance',
                    formula: 'Feasibility = Saldo Tabungan Barang >= Harga + (Safety Runway 3x)',
                    problem: 'Pembelian impulsif barang mahal (laptop, kamera, gadget) yang menguras saldo harian dan memicu penyesalan (buyer remorse).',
                    solution: 'Modul Purchase Wishlist & simulator Can I Afford This? mengevaluasi kesiapan finansial secara objektif berdasarkan trajectory tabungan dan cadangan dana darurat Anda.',
                    benefit: 'Beli barang impian dengan rasa percaya diri 100% tanpa rasa bersalah.'
                },
                {
                    id: 'adaptive_budget',
                    title: 'Adaptive Percentage Budgeting',
                    subtitle: 'Budgeting Berbasis Rasio Proporsional',
                    badge: 'Dynamic Allocation',
                    formula: 'Alokasi Dinamis: Needs (50%) + Wants (30%) + Sinking & Buffer (20%)',
                    problem: 'Budgeting fixed nominal rupiah (contoh: makan harus pas Rp 2jt) selalu gagal untuk pekerja lepas karena penghasilan tiap bulan berubah-ubah.',
                    solution: 'Anggaran dirancang berbasis persentase dinamis yang otomatis mengecil di bulan sepi dan fleksibel saat panen omset, menjaga rasio kesehatan finansial tetap seimbang.',
                    benefit: 'Disiplin anggaran yang realistis dan fleksibel tanpa beban mental.'
                },
                {
                    id: 'runway_index',
                    title: 'Survival Runway & Health Ratio',
                    subtitle: 'Indeks Ketahanan Finansial Independen',
                    badge: 'Risk Management',
                    formula: 'Runway (Bulan) = Dana Likuid Darurat / Rata-rata Pengeluaran Bulanan',
                    problem: 'Ketidakpastian kapan proyek berikutnya datang menimbulkan kecemasan dan stres kerja berkepanjangan.',
                    solution: 'Indikator Health Index secara transparan memberitahu berapa bulan Anda bisa bertahan hidup dengan gaya hidup normal jika hari ini Anda memutuskan libur bekerja.',
                    benefit: 'Ketenangan pikiran (peace of mind) dan daya tawar negosiasi harga klien yang lebih tinggi.'
                }
            ],

            init() {
                window.addEventListener('splash-completed', () => {
                    const hasSeen = localStorage.getItem('pf_theory_intro_seen');
                    if (!hasSeen) {
                        setTimeout(() => {
                            this.open();
                        }, 350);
                    }
                });
            },

            open() {
                this.activeTab = 0;
                this.showTheory = true;
            },

            close() {
                if (this.dontShowAgain) {
                    localStorage.setItem('pf_theory_intro_seen', 'true');
                }
                this.showTheory = false;
            },

            startTour() {
                this.close();
                localStorage.setItem('pf_theory_intro_seen', 'true');
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('open-interactive-tour'));
                }, 250);
            }
        };
    };
</script>

<!-- PortoFinance Financial Engineering & Theory Foundation Modal -->
<div x-data="window.financeTheoryModalComponent()"
     @open-finance-theory.window="open()"
     @keydown.window.escape="if (showTheory) close()"
     x-cloak>

    <!-- Modal Backdrop Blur -->
    <div x-show="showTheory" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[125] overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-6">

        <!-- Theory Modal Box -->
        <div @click.outside="close()"
             x-show="showTheory"
             x-transition:enter="transition-all ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             class="relative w-full max-w-2xl max-h-[92vh] bg-white border border-slate-200 rounded-3xl shadow-2xl overflow-hidden flex flex-col my-auto">

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!-- 1. HERO BANNER HEADER                                       -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <div class="relative px-5 sm:px-7 pt-6 pb-5 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white shrink-0 overflow-hidden border-b border-slate-800">
                <!-- Background Glow Accents -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#C6F24D]/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-1/3 w-48 h-48 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#C6F24D]/15 border border-[#C6F24D]/30 text-[#C6F24D] text-[10px] font-mono font-bold uppercase tracking-wider mb-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#C6F24D] animate-pulse"></span>
                            <span>Applied Financial Engineering</span>
                        </div>
                        <h2 class="text-lg sm:text-2xl font-black tracking-tight text-white leading-tight">
                            Fondasi Teori Keuangan <span class="text-[#C6F24D]">PortoFinance</span>
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-lg leading-relaxed">
                            Bukan sekadar buku kas biasa. Dirancang dengan formula finansial akademis & modern untuk mengatasi siklus ketidakpastian pendapatan freelancer.
                        </p>
                    </div>

                    <button @click="close()" 
                            type="button"
                            class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition-colors shrink-0 cursor-pointer"
                            title="Tutup (Esc)">
                        <x-icon name="x" class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!-- 2. THEORY TAB NAVIGATION                                    -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <div class="px-4 sm:px-6 py-2.5 bg-[#F8F9FA] border-b border-slate-200/80 flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0">
                <template x-for="(theory, idx) in theories" :key="theory.id">
                    <button @click="activeTab = idx"
                            type="button"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition-all cursor-pointer whitespace-nowrap flex items-center gap-1.5"
                            :class="activeTab === idx ? 'bg-slate-950 text-white shadow-sm font-extrabold' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-200/70'">
                        <span class="font-mono text-[9px] px-1.5 py-0.2 rounded" :class="activeTab === idx ? 'bg-[#C6F24D] text-slate-950 font-black' : 'bg-slate-200 text-slate-600'" x-text="'0' + (idx + 1)"></span>
                        <span x-text="theory.title"></span>
                    </button>
                </template>
            </div>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!-- 3. DETAILED THEORY CONTENT BODY                             -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <div class="p-5 sm:p-7 overflow-y-auto flex-1 space-y-5 bg-white text-slate-900">
                <template x-for="(theory, idx) in theories" :key="theory.id">
                    <div x-show="activeTab === idx" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-4">
                        
                        <!-- Header Pill & Subtitle -->
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block" x-text="theory.badge"></span>
                                <h3 class="text-base sm:text-lg font-black text-slate-950 tracking-tight" x-text="theory.subtitle"></h3>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-[#EBFAD2] text-slate-900 border border-[#D4F66C] text-[10px] font-mono font-bold">
                                Pilar Finansial #<span x-text="idx + 1"></span>
                            </span>
                        </div>

                        <!-- Mathematical Formula Card (JetBrains Mono) -->
                        <div class="p-4 bg-slate-950 rounded-2xl text-slate-100 border border-slate-800 space-y-1.5 shadow-inner">
                            <div class="flex items-center justify-between text-[10px] font-mono text-slate-400">
                                <span class="uppercase tracking-wider font-bold">📐 Mathematical Model & Formula</span>
                                <span class="text-[#C6F24D] font-bold">Porto Algorithm</span>
                            </div>
                            <div class="font-mono text-xs sm:text-sm font-bold text-[#C6F24D] break-words" x-text="theory.formula"></div>
                        </div>

                        <!-- Problem vs Solution 2-Column Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                            <!-- Masalah Freelancer Tradisional -->
                            <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-200/80 space-y-1.5">
                                <div class="flex items-center gap-1.5 text-xs font-extrabold text-rose-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    <span>Tantangan / Jebakan Klasik</span>
                                </div>
                                <p class="text-xs text-rose-800 leading-relaxed font-medium" x-text="theory.problem"></p>
                            </div>

                            <!-- Solusi Ilmiah PortoFinance -->
                            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 space-y-1.5">
                                <div class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <span>Solusi Sistem PortoFinance</span>
                                </div>
                                <p class="text-xs text-emerald-900 leading-relaxed font-medium" x-text="theory.solution"></p>
                            </div>
                        </div>

                        <!-- Real-World Impact Benefit Card -->
                        <div class="p-3.5 rounded-2xl bg-[#F8F9FA] border border-slate-200 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-slate-900 text-[#C6F24D] flex items-center justify-center shrink-0 font-bold text-xs">
                                💡
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Dampak Nyata Untuk Anda</span>
                                <p class="text-xs font-bold text-slate-900" x-text="theory.benefit"></p>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- ═══════════════════════════════════════════════════════════ -->
            <!-- 4. FOOTER CONTROLS & CALL TO ACTION                         -->
            <!-- ═══════════════════════════════════════════════════════════ -->
            <div class="px-5 sm:px-7 py-4 bg-[#F8F9FA] border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <label class="flex items-center gap-2 text-xs text-slate-500 cursor-pointer select-none">
                    <input type="checkbox" x-model="dontShowAgain" class="w-4 h-4 rounded border-slate-300 text-slate-950 focus:ring-0">
                    <span>Jangan tampilkan otomatis lagi</span>
                </label>

                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <button @click="close()" 
                            type="button"
                            class="flex-1 sm:flex-initial px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 transition-colors cursor-pointer text-center">
                        Jelajahi Sendiri
                    </button>

                    <button @click="startTour()" 
                            type="button"
                            class="flex-1 sm:flex-initial px-5 py-2 rounded-xl text-xs font-extrabold text-slate-950 bg-[#C6F24D] hover:bg-[#b8e640] transition-all shadow-sm flex items-center justify-center gap-2 cursor-pointer active-tap">
                        <span>Mulai Tur Interaktif</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
