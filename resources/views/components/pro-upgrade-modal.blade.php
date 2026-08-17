<div x-data="{ 
        open: false,
        feature: 'general',
        featureTitles: {
            'accounts': 'Batas 2 Rekening Kas Tercapai',
            'projects': 'Batas 2 Proyek Aktif Tercapai',
            'ai_voice': 'Kuota AI Voice & Scan Struk Bulanan Habis (5/5)',
            'general': 'Buka Seluruh Potensi Finansial Tanpa Batas'
        },
        featureDescriptions: {
            'accounts': 'Akun Free Starter dibatasi maksimal 2 rekening/e-wallet. Upgrade ke PRO untuk mengelola rekening bank & e-wallet tanpa batas.',
            'projects': 'Akun Free Starter dibatasi maksimal 2 proyek aktif. Upgrade ke PRO untuk mencatat seluruh proyek dan margin klien tanpa batas.',
            'ai_voice': 'Anda telah menggunakan kuota 5x AI Voice & Scan Struk bulan ini. Upgrade ke PRO untuk menikmati kuota AI tanpa batas setiap saat.',
            'general': 'Tingkatkan produktivitas finansial freelance Anda dengan akses penuh ke seluruh fitur canggih PortoFinance PRO.'
        }
     }"
     @open-upgrade-modal.window="
        feature = $event.detail.feature || 'general';
        open = true;
     "
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" 
         @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        
        <div x-show="open" 
             x-transition:enter="ease-out duration-250"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-slate-950 text-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-800">
            
            <!-- Glow Background Accent -->
            <div class="absolute -top-20 -right-20 w-56 h-56 bg-[#C6F24D]/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-56 h-56 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 p-6 sm:p-8 space-y-6">

                <!-- Header: Badge & Close -->
                <div class="flex items-center justify-between">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6F24D]/15 border border-[#C6F24D]/30 text-[#C6F24D] text-[11px] font-mono font-black uppercase tracking-wider">
                        <x-icon name="crown" class="w-3.5 h-3.5 text-[#C6F24D]" strokeWidth="2.5" />
                        <span>PortoFinance PRO Member</span>
                    </div>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-white p-1 rounded-full hover:bg-slate-800 transition-colors cursor-pointer">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>

                <!-- Dynamic Title & Description -->
                <div class="space-y-2">
                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight" x-text="featureTitles[feature] || featureTitles['general']"></h2>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed" x-text="featureDescriptions[feature] || featureDescriptions['general']"></p>
                </div>

                <!-- PRO Benefits Checklist Grid -->
                <div class="bg-slate-900/80 rounded-2xl p-4 sm:p-5 border border-slate-800 space-y-3">
                    <span class="text-[10px] font-mono font-extrabold uppercase tracking-wider text-slate-400">Keuntungan Eksklusif PRO:</span>
                    
                    <div class="grid grid-cols-1 gap-2.5 text-xs text-slate-200">
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-xs shrink-0">✓</div>
                            <span><strong>Unlimited</strong> Rekening Bank & Dompet Digital (E-Wallet)</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-xs shrink-0">✓</div>
                            <span><strong>Unlimited</strong> Proyek Freelance, Client Portal & Margin Profit</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-xs shrink-0">✓</div>
                            <span><strong>Unlimited</strong> AI Voice Command & Scan Foto Struk OCR</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-xs shrink-0">✓</div>
                            <span><strong>Cetak PDF Laporan & Invoice</strong> Resmi Tanpa Watermark</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-xs shrink-0">✓</div>
                            <span>Simulasi Cerdas <em>"Can I Afford This?"</em> & Wishlist Lengkap</span>
                        </div>
                    </div>
                </div>

                <!-- Price & CTA Button -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-baseline justify-between">
                        <div>
                            <span class="text-[10px] font-mono uppercase text-slate-400 font-bold">Investasi Langganan:</span>
                            <div class="text-lg sm:text-xl font-black text-[#C6F24D] font-mono">
                                Mulai Rp 49.000<span class="text-xs text-slate-400 font-sans font-normal"> / bulan</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-[#C6F24D]/20 text-[#C6F24D] font-mono font-bold">Lifetime Pass Available</span>
                        </div>
                    </div>

                    <!-- Direct Upgrade CTA Button -->
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20PortoFinance%2C%20saya%20tertarik%20untuk%20upgrade%20akun%20saya%20ke%20paket%20PRO%20Member." 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="w-full py-3 px-5 rounded-2xl bg-[#C6F24D] hover:bg-[#b8e640] text-slate-950 font-black text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-lg active-tap cursor-pointer">
                        <x-icon name="zap" class="w-4 h-4 text-slate-950" strokeWidth="2.5" />
                        <span>Upgrade ke PRO Sekarang 🚀</span>
                    </a>

                    <p class="text-[10px] text-center text-slate-500 font-medium">
                        Aktivasi instan oleh tim kami &bull; Bebas batasan selamanya &bull; Garansi 100%
                    </p>
                </div>

            </div>

        </div>

    </div>
</div>
