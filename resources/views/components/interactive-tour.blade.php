<div x-data="{
        showTour: false,
        currentStep: 1,
        totalSteps: 5,
        targetRect: { top: 0, left: 0, width: 0, height: 0, bottom: 0, right: 0 },
        isMobile: window.innerWidth < 768,
        steps: [
            {
                id: 'quick-add',
                title: 'Catat Cepat via AI Voice & Struk ⚡',
                badge: 'Langkah 01 / 05 &bull; Pencatatan Cepat',
                desc: 'Klik tombol ini atau tekan shortcut Ctrl+K untuk mencatat transaksi pemasukan atau pengeluaranmu dalam hitungan detik via suara bahasa Indonesia atau foto struk!',
                desktopTarget: '#tour-quick-add',
                mobileTarget: '#tour-quick-add-mobile',
                pointerDir: 'right'
            },
            {
                id: 'available-money',
                title: 'Uang Bebas (Anti-Kecolongan) 🛡️',
                badge: 'Langkah 02 / 05 &bull; Saldo Aman',
                desc: 'Ini nominal aman yang boleh kamu belanjakan. Sistem otomatis memisahkan saldo bank riil dengan uang yang sudah kamu lock untuk target impian.',
                desktopTarget: '#tour-available-money',
                mobileTarget: '#tour-available-money',
                pointerDir: 'top'
            },
            {
                id: 'projects',
                title: 'Proyek Freelance & Invoice 💼',
                badge: 'Langkah 03 / 05 &bull; Manajemen Bisnis',
                desc: 'Kelola DP dan pelunasan invoice klien, pantau modal pengeluaran proyek, dan hitung margin laba bersih tiap pekerjaan freelance-mu.',
                desktopTarget: '#tour-nav-projects',
                mobileTarget: '#tour-nav-projects-mobile',
                pointerDir: 'left'
            },
            {
                id: 'wishlists',
                title: 'Wishlist & Simulator Beli 🎯',
                badge: 'Langkah 04 / 05 &bull; Rencana Belanja',
                desc: 'Kunci tabungan untuk beli gadget baru dan uji dampak pengeluaran dengan simulator \'Can I Afford This?\' sebelum memutuskan belanja.',
                desktopTarget: '#tour-nav-wishlists',
                mobileTarget: '#tour-nav-wishlists-mobile',
                pointerDir: 'left'
            },
            {
                id: 'profile',
                title: 'Pusat Kendali & Notifikasi ⚙️',
                badge: 'Langkah 05 / 05 &bull; Profil & Skor',
                desc: 'Cek skor kesehatan finansial (Health Index), kelola rekening akun bank, dan lihat reminder tagihan klien yang jatuh tempo di sini.',
                desktopTarget: '#tour-user-profile',
                mobileTarget: '#tour-user-profile',
                pointerDir: 'top'
            }
        ],
        init() {
            const hasOnboarding = {{ auth()->check() && !auth()->user()->onboarding_completed ? 'false' : 'true' }};
            const completed = localStorage.getItem('portofinance_guided_tour_done');
            if (hasOnboarding && completed !== 'true') {
                setTimeout(() => {
                    this.startTour();
                }, 600);
            }
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 768;
                if (this.showTour) this.updatePosition();
            });
            window.addEventListener('scroll', () => {
                if (this.showTour) this.updatePosition();
            }, true);
        },
        startTour() {
            this.currentStep = 1;
            this.showTour = true;
            this.$nextTick(() => {
                setTimeout(() => {
                    this.updatePosition();
                }, 80);
            });
        },
        next() {
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.updatePosition();
                    }, 50);
                });
            } else {
                this.finish();
            }
        },
        prev() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.updatePosition();
                    }, 50);
                });
            }
        },
        finish() {
            this.showTour = false;
            localStorage.setItem('portofinance_guided_tour_done', 'true');
        },
        skip() {
            this.finish();
        },
        updatePosition() {
            this.isMobile = window.innerWidth < 768;
            const step = this.steps[this.currentStep - 1];
            const selector = this.isMobile ? (step.mobileTarget || step.desktopTarget) : step.desktopTarget;
            let el = document.querySelector(selector);
            
            if (!el && this.isMobile) {
                el = document.querySelector(step.desktopTarget);
            }
            
            if (el) {
                const rect = el.getBoundingClientRect();
                if (rect.width > 0 && rect.height > 0) {
                    this.targetRect = {
                        top: rect.top,
                        left: rect.left,
                        width: rect.width,
                        height: rect.height,
                        bottom: rect.bottom,
                        right: rect.right
                    };
                    return;
                }
            }

            // Fallbacks for mobile viewports
            const w = window.innerWidth;
            const h = window.innerHeight;
            if (this.isMobile) {
                if (step.id === 'quick-add') {
                    this.targetRect = { top: h - 70, left: (w / 2) - 24, width: 48, height: 48, bottom: h - 22, right: (w / 2) + 24 };
                } else if (step.id === 'available-money') {
                    this.targetRect = { top: 12, left: w - 160, width: 100, height: 38, bottom: 50, right: w - 60 };
                } else if (step.id === 'projects') {
                    this.targetRect = { top: h - 70, left: w - 80, width: 60, height: 48, bottom: h - 22, right: w - 20 };
                } else if (step.id === 'wishlists') {
                    this.targetRect = { top: h - 70, left: (w / 2) + 30, width: 60, height: 48, bottom: h - 22, right: (w / 2) + 90 };
                } else {
                    this.targetRect = { top: 12, left: w - 50, width: 38, height: 38, bottom: 50, right: w - 12 };
                }
            } else {
                this.targetRect = { top: 80, left: 280, width: 200, height: 60, bottom: 140, right: 480 };
            }
        }
    }"
    @open-interactive-tour.window="startTour()"
    @keydown.window.escape="if (showTour) finish()"
    @keydown.window.arrow-right="if (showTour) next()"
    @keydown.window.arrow-left="if (showTour) prev()"
    x-cloak>

    <!-- Tour Overlay Container -->
    <div x-show="showTour" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[130] pointer-events-auto overflow-hidden">

        <!-- 1. SPOTLIGHT CUTOUT BOX (Follows Active Target with glowing border) -->
        <div class="fixed transition-all duration-300 ease-out pointer-events-none rounded-2xl ring-4 ring-[#C6F24D] shadow-[0_0_0_9999px_rgba(15,23,42,0.75),0_0_30px_rgba(198,242,77,0.45)]"
             :style="`top: ${targetRect.top - 6}px; left: ${targetRect.left - 6}px; width: ${targetRect.width + 12}px; height: ${targetRect.height + 12}px;`">
             <!-- Pulsing Ring Accent -->
             <div class="absolute inset-0 rounded-2xl animate-ping border-2 border-[#C6F24D]/60 pointer-events-none"></div>
        </div>

        <!-- 2. BOUNCING POINTER ARROW (Panah Penunjuk Interaktif) -->
        <div class="fixed transition-all duration-300 ease-out pointer-events-none z-[131]"
             :style="isMobile 
                ? (targetRect.top > window.innerHeight / 2 
                    ? `top: ${targetRect.top - 58}px; ${(targetRect.left + (targetRect.width / 2) > window.innerWidth / 2) ? 'right: ' + Math.max(12, window.innerWidth - targetRect.right - 6) + 'px;' : 'left: ' + Math.max(12, targetRect.left - 6) + 'px;'}` 
                    : `top: ${targetRect.bottom + 12}px; ${(targetRect.left + (targetRect.width / 2) > window.innerWidth / 2) ? 'right: ' + Math.max(12, window.innerWidth - targetRect.right - 6) + 'px;' : 'left: ' + Math.max(12, targetRect.left - 6) + 'px;'}`)
                : (targetRect.left < window.innerWidth / 2
                    ? `top: ${targetRect.top + (targetRect.height / 2) - 24}px; left: ${targetRect.right + 16}px;`
                    : `top: ${targetRect.bottom + 16}px; left: ${Math.max(20, targetRect.left - 40)}px;`)">
            
            <div class="flex items-center gap-2 animate-bounce"
                 :class="(isMobile && (targetRect.left + (targetRect.width / 2) > window.innerWidth / 2)) ? 'flex-row-reverse' : 'flex-row'">
                <!-- Arrow Icon depending on position -->
                <div class="w-10 h-10 rounded-2xl bg-slate-950 text-[#C6F24D] border-2 border-[#C6F24D] shadow-xl flex items-center justify-center shrink-0">
                    <template x-if="isMobile && targetRect.top > window.innerHeight / 2">
                        <!-- Points Down -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14M19 12l-7 7-7-7"/>
                        </svg>
                    </template>
                    <template x-if="isMobile && targetRect.top <= window.innerHeight / 2">
                        <!-- Points Up -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 19V5M5 12l7-7 7 7"/>
                        </svg>
                    </template>
                    <template x-if="!isMobile && targetRect.left < window.innerWidth / 2">
                        <!-- Points Left -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12H5M12 19l-7-7 7-7"/>
                        </svg>
                    </template>
                    <template x-if="!isMobile && targetRect.left >= window.innerWidth / 2">
                        <!-- Points Up -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 19V5M5 12l7-7 7 7"/>
                        </svg>
                    </template>
                </div>
                
                <span class="px-2.5 py-1 rounded-lg bg-slate-950 text-[#C6F24D] text-[10px] font-mono font-black tracking-wider uppercase shadow-xl border border-[#C6F24D]/60 backdrop-blur-md whitespace-nowrap">
                    <span x-text="(isMobile && (targetRect.left + (targetRect.width / 2) > window.innerWidth / 2)) ? '👉 Akses Di Sini' : 'Akses Di Sini 👈'"></span>
                </span>
            </div>
        </div>

        <!-- 3. GUIDED TOUR TOOLTIP CARD -->
        <div class="fixed z-[132] transition-all duration-300 ease-out"
             :style="isMobile
                ? (targetRect.top > window.innerHeight / 2
                    ? 'top: 24px; left: 16px; right: 16px;'
                    : 'bottom: 24px; left: 16px; right: 16px;')
                : (targetRect.left < window.innerWidth / 2
                    ? `top: ${Math.max(30, Math.min(window.innerHeight - 300, targetRect.top - 20))}px; left: ${targetRect.right + 70}px; width: 380px;`
                    : `top: ${Math.max(30, Math.min(window.innerHeight - 300, targetRect.bottom + 70))}px; left: ${Math.max(20, targetRect.left - 240)}px; width: 380px;`)">

            <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 shadow-2xl space-y-4">
                
                <!-- Card Header: Step Pill & Progress Dots -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#84CC16] animate-pulse"></span>
                        <span class="text-[10px] font-mono font-extrabold uppercase text-slate-500 tracking-wider" 
                              x-html="steps[currentStep - 1].badge"></span>
                    </div>

                    <div class="flex items-center gap-1">
                        <template x-for="s in totalSteps" :key="s">
                            <div class="h-1.5 rounded-full transition-all duration-200"
                                 :class="currentStep === s ? 'w-4 bg-slate-950' : 'w-1.5 bg-slate-200'"></div>
                        </template>
                    </div>
                </div>

                <!-- Step Content -->
                <div class="space-y-1.5">
                    <h3 class="text-base font-extrabold text-slate-900 tracking-tight" 
                        x-text="steps[currentStep - 1].title"></h3>
                    <p class="text-xs text-slate-600 leading-relaxed" 
                       x-text="steps[currentStep - 1].desc"></p>
                </div>

                <!-- Footer Navigation Buttons -->
                <div class="pt-2 flex items-center justify-between gap-2">
                    <button type="button" 
                            @click="skip()" 
                            class="text-xs font-bold text-slate-400 hover:text-slate-700 transition-colors cursor-pointer px-2 py-1.5">
                        Lewati Tur
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                x-show="currentStep > 1" 
                                @click="prev()" 
                                class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition-colors cursor-pointer">
                            &larr; Kembali
                        </button>
                        
                        <button type="button" 
                                @click="next()" 
                                class="px-4 py-2 rounded-xl bg-slate-950 hover:bg-slate-800 text-white text-xs font-extrabold shadow-sm active-tap transition-all flex items-center gap-1.5 cursor-pointer">
                            <span x-text="currentStep === totalSteps ? 'Selesai & Mulai 🚀' : 'Lanjut →'"></span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
