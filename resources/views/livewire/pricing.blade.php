<div class="space-y-12 max-w-6xl mx-auto py-4 sm:py-8">

    <!-- HERO HEADER -->
    <div class="text-center space-y-4 max-w-3xl mx-auto">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#C6F24D]/15 border border-[#C6F24D]/30 text-[#4d6a00] text-xs font-mono font-black uppercase tracking-wider">
            <x-icon name="crown" class="w-3.5 h-3.5 text-[#658c00]" strokeWidth="2.5" />
            <span>Pilihan Paket PortoFinance PRO</span>
        </div>

        <h1 class="text-3xl sm:text-5xl font-black text-slate-950 tracking-tight leading-tight">
            Investasi Cerdas untuk <br class="hidden sm:inline">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-slate-950 via-slate-800 to-slate-900">Kendali Finansial Tanpa Batas</span>
        </h1>

        <p class="text-sm sm:text-base text-slate-600 font-medium">
            Tingkatkan efisiensi pembukuan, kelola tagihan klien, dan nikmati kemudahan AI Voice & OCR tanpa batasan kuota.
        </p>

        <!-- BILLING TOGGLE (Monthly / Yearly) -->
        <div class="pt-4 flex items-center justify-center">
            <div class="p-1.5 bg-white border border-slate-200 rounded-2xl shadow-xs inline-flex items-center gap-1">
                <button type="button" 
                        wire:click="$set('billingCycle', 'monthly')" 
                        class="px-4 py-2 rounded-xl text-xs font-black transition-all cursor-pointer {{ $billingCycle === 'monthly' ? 'bg-slate-950 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Bulanan
                </button>
                <button type="button" 
                        wire:click="$set('billingCycle', 'yearly')" 
                        class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer {{ $billingCycle === 'yearly' ? 'bg-slate-950 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>Tahunan</span>
                    <span class="px-2 py-0.5 rounded-lg bg-[#C6F24D] text-slate-950 text-[10px] font-mono font-black">Hemat 30%</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 3 PRICING CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
        
        <!-- 1. FREE STARTER -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="space-y-1">
                    <span class="text-xs font-mono font-bold uppercase text-slate-400">Pemula</span>
                    <h3 class="text-xl font-black text-slate-950">Free Starter</h3>
                    <p class="text-xs text-slate-500 font-medium">Cocok untuk freelancer yang baru memulai mencatat keuangan.</p>
                </div>

                <div class="py-3 border-y border-slate-100 flex items-baseline gap-1">
                    <span class="text-3xl font-black text-slate-950 font-mono">Rp 0</span>
                    <span class="text-xs text-slate-400 font-bold">/ selamanya</span>
                </div>

                <ul class="space-y-3 text-xs text-slate-600 font-medium">
                    <li class="flex items-center gap-2.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Maksimal <strong>2 Rekening</strong> Bank/E-Wallet</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Maksimal <strong>2 Proyek Aktif</strong></span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Kuota AI Voice & OCR: <strong>5x / bulan</strong></span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-emerald-500 font-bold">✓</span>
                        <span>Sistem Anggaran 50/30/20 & Envelopes</span>
                    </li>
                    <li class="flex items-center gap-2.5 text-slate-400">
                        <span class="text-slate-300 font-bold">✕</span>
                        <span>Watermark PortoFinance pada Invoice</span>
                    </li>
                </ul>
            </div>

            @auth
                <button type="button" disabled class="w-full py-3 rounded-2xl bg-slate-100 text-slate-400 font-bold text-xs text-center cursor-not-allowed">
                    Paket Dasar Anda
                </button>
            @else
                <a href="{{ route('register') }}" class="w-full py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-black text-xs text-center transition-colors block">
                    Daftar Akun Gratis
                </a>
            @endauth
        </div>

        <!-- 2. PRO MEMBER (FEATURED) -->
        <div class="bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-8 border-2 border-[#C6F24D] shadow-2xl flex flex-col justify-between space-y-6 relative overflow-hidden transform md:-translate-y-2">
            <!-- Glow Accent -->
            <div class="absolute -top-16 -right-16 w-48 h-48 bg-[#C6F24D]/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="space-y-4 relative z-10">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold uppercase text-[#C6F24D]">Paling Populer 🔥</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] text-[10px] font-mono font-black border border-[#C6F24D]/40">REKOMENDASI</span>
                </div>

                <div class="space-y-1">
                    <h3 class="text-2xl font-black text-white">PortoFinance PRO</h3>
                    <p class="text-xs text-slate-300 font-medium">Akses penuh tanpa batas untuk freelancer aktif & agensi studio.</p>
                </div>

                <div class="py-3 border-y border-slate-800/80 flex items-baseline gap-1.5">
                    @if($billingCycle === 'monthly')
                        <span class="text-3xl sm:text-4xl font-black text-[#C6F24D] font-mono">Rp 49.000</span>
                        <span class="text-xs text-slate-400 font-bold">/ bulan</span>
                    @else
                        <div class="space-y-0.5">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-3xl sm:text-4xl font-black text-[#C6F24D] font-mono">Rp 399.000</span>
                                <span class="text-xs text-slate-400 font-bold">/ tahun</span>
                            </div>
                            <div class="text-[10px] font-mono text-slate-400">Setara ~Rp 33.250 / bulan (Hemat Rp 189.000)</div>
                        </div>
                    @endif
                </div>

                <ul class="space-y-3 text-xs text-slate-200 font-medium">
                    <li class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span><strong>Unlimited</strong> Rekening Bank & E-Wallet</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span><strong>Unlimited</strong> Proyek & Klien Portal</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span><strong>Unlimited</strong> AI Voice Command & OCR Struk</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span><strong>White-label Invoice</strong> (Bebas Watermark)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span>Simulasi <em>"Can I Afford This?"</em> & Wishlist</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-4 h-4 rounded-full bg-[#C6F24D]/20 text-[#C6F24D] flex items-center justify-center font-bold text-[10px]">✓</span>
                        <span>Dukungan Prioritas WhatsApp 24/7</span>
                    </li>
                </ul>
            </div>

            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20PortoFinance%2C%20saya%20ingin%20upgrade%20ke%20paket%20PRO%20{{ $billingCycle === 'yearly' ? 'Tahunan%20(Rp%20399.000)' : 'Bulanan%20(Rp%2049.000)' }}" 
               target="_blank"
               class="w-full py-3.5 rounded-2xl bg-[#C6F24D] hover:bg-[#b8e640] text-slate-950 font-black text-xs sm:text-sm text-center transition-all shadow-lg flex items-center justify-center gap-2 cursor-pointer active-tap relative z-10">
                <x-icon name="zap" class="w-4 h-4 text-slate-950" strokeWidth="2.5" />
                <span>Pilih Paket PRO Sekarang 🚀</span>
            </a>
        </div>

        <!-- 3. LIFETIME VIP PASS -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-200/80 shadow-xs flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="space-y-1">
                    <span class="text-xs font-mono font-bold uppercase text-purple-600">VIP Exclusive</span>
                    <h3 class="text-xl font-black text-purple-950">Lifetime VIP Pass</h3>
                    <p class="text-xs text-slate-500 font-medium">Sekali bayar, nikmati akses penuh selamanya tanpa biaya bulanan.</p>
                </div>

                <div class="py-3 border-y border-purple-100 flex items-baseline gap-1">
                    <span class="text-3xl font-black text-purple-950 font-mono">Rp 799.000</span>
                    <span class="text-xs text-slate-400 font-bold">/ sekali bayar</span>
                </div>

                <ul class="space-y-3 text-xs text-slate-600 font-medium">
                    <li class="flex items-center gap-2.5">
                        <span class="text-purple-600 font-bold">✓</span>
                        <span><strong>Seluruh Fitur PRO</strong> Selamanya</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-purple-600 font-bold">✓</span>
                        <span><strong>Bebas Biaya Perpanjangan</strong> Bulanan/Tahunan</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-purple-600 font-bold">✓</span>
                        <span>Akses Awal ke Seluruh Fitur AI Masa Depan</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-purple-600 font-bold">✓</span>
                        <span>Badge Profil Eksklusif 👑 Lifetime VIP</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="text-purple-600 font-bold">✓</span>
                        <span>Direct Support ke Founder</span>
                    </li>
                </ul>
            </div>

            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20PortoFinance%2C%20saya%20tertarik%20mengambil%20paket%20LIFETIME%20VIP%20PASS%20(Rp%20799.000%20Sekali%20Bayar%20Selamanya)." 
               target="_blank"
               class="w-full py-3 rounded-2xl bg-purple-900 hover:bg-purple-950 text-white font-black text-xs text-center transition-colors block cursor-pointer active-tap shadow-xs">
                Ambil Lifetime Pass 👑
            </a>
        </div>

    </div>

    <!-- COMPARISON TABLE -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
        <div class="text-center space-y-1">
            <h3 class="text-xl sm:text-2xl font-black text-slate-950">Tabel Perbandingan Fitur Lengkap</h3>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">Bandingkan rincian fitur setiap paket sesuai kebutuhan bisnis Anda.</p>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-100">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-mono font-bold uppercase text-slate-400 border-b border-slate-100">
                        <th class="py-3.5 px-4 font-black">Fitur Utama</th>
                        <th class="py-3.5 px-3 text-center font-black">Free Starter</th>
                        <th class="py-3.5 px-3 text-center font-black text-emerald-600 bg-emerald-50/50">PRO Member</th>
                        <th class="py-3.5 px-3 text-center font-black text-purple-600">Lifetime VIP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-900">Rekening Bank & E-Wallet</td>
                        <td class="py-3 px-3 text-center font-mono text-slate-600">Maks. 2 Akun</td>
                        <td class="py-3 px-3 text-center font-mono font-black text-emerald-600 bg-emerald-50/30">Unlimited</td>
                        <td class="py-3 px-3 text-center font-mono font-black text-purple-600">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-900">Proyek & Klien Aktif</td>
                        <td class="py-3 px-3 text-center font-mono text-slate-600">Maks. 2 Proyek</td>
                        <td class="py-3 px-3 text-center font-mono font-black text-emerald-600 bg-emerald-50/30">Unlimited</td>
                        <td class="py-3 px-3 text-center font-mono font-black text-purple-600">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-900">AI Voice Command & OCR Scan</td>
                        <td class="py-3 px-3 text-center font-mono text-slate-600">5x / Bulan</td>
                        <td class="py-3 px-3 text-center font-mono font-black text-emerald-600 bg-emerald-50/30">Unlimited</td>
                        <td class="py-3 px-3 text-center font-mono font-black text-purple-600">Unlimited</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-900">Pembuatan Invoice Resmi & Link Klien</td>
                        <td class="py-3 px-3 text-center text-emerald-600 font-bold">✓ (Ada Watermark)</td>
                        <td class="py-3 px-3 text-center font-bold text-emerald-600 bg-emerald-50/30">✓ (White-label)</td>
                        <td class="py-3 px-3 text-center font-bold text-purple-600">✓ (White-label)</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-900">Simulasi "Can I Afford This?"</td>
                        <td class="py-3 px-3 text-center text-slate-400">Dasar</td>
                        <td class="py-3 px-3 text-center font-bold text-emerald-600 bg-emerald-50/30">✓ Full Algorithm</td>
                        <td class="py-3 px-3 text-center font-bold text-purple-600">✓ Full Algorithm</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-900">Ekspor Laporan PDF & CSV/Excel</td>
                        <td class="py-3 px-3 text-center text-emerald-600 font-bold">✓</td>
                        <td class="py-3 px-3 text-center text-emerald-600 font-bold bg-emerald-50/30">✓ Prioritas</td>
                        <td class="py-3 px-3 text-center text-purple-600 font-bold">✓ Prioritas</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-900">PWA Mobile Installation (Android & iOS)</td>
                        <td class="py-3 px-3 text-center text-emerald-600 font-bold">✓</td>
                        <td class="py-3 px-3 text-center text-emerald-600 font-bold bg-emerald-50/30">✓</td>
                        <td class="py-3 px-3 text-center text-purple-600 font-bold">✓</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FAQ SECTION -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6" x-data="{ activeFaq: null }">
        <div class="text-center space-y-1">
            <h3 class="text-xl sm:text-2xl font-black text-slate-950">Pertanyaan yang Sering Diajukan (FAQ)</h3>
            <p class="text-xs sm:text-sm text-slate-500 font-medium">Segala hal yang perlu Anda ketahui tentang PortoFinance PRO.</p>
        </div>

        <div class="max-w-3xl mx-auto space-y-3">
            
            <!-- FAQ 1 -->
            <div class="border border-slate-200 rounded-2xl p-4 transition-all">
                <button type="button" @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full flex items-center justify-between text-left font-extrabold text-slate-950 text-xs sm:text-sm cursor-pointer">
                    <span>Bagaimana cara mengaktifkan paket PRO setelah melakukan pembayaran?</span>
                    <x-icon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': activeFaq === 1 }" />
                </button>
                <div x-show="activeFaq === 1" x-cloak class="pt-3 text-xs text-slate-600 font-medium leading-relaxed">
                    Setelah Anda menghubungi WhatsApp kami dan menyelesaikan pembayaran via QRIS / Transfer Bank, akun Anda akan langsung diaktifkan dalam hitungan 1-5 menit oleh tim kami.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="border border-slate-200 rounded-2xl p-4 transition-all">
                <button type="button" @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full flex items-center justify-between text-left font-extrabold text-slate-950 text-xs sm:text-sm cursor-pointer">
                    <span>Apakah data transaksi keuangan saya aman dan terisolasi?</span>
                    <x-icon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': activeFaq === 2 }" />
                </button>
                <div x-show="activeFaq === 2" x-cloak class="pt-3 text-xs text-slate-600 font-medium leading-relaxed">
                    Sangat aman. Setiap data transaksi, rekening, invoice, dan proyek memiliki isolasi tingkat database (multi-tenant scoping). Tidak ada pengguna lain yang dapat melihat atau mengakses data Anda.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="border border-slate-200 rounded-2xl p-4 transition-all">
                <button type="button" @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full flex items-center justify-between text-left font-extrabold text-slate-950 text-xs sm:text-sm cursor-pointer">
                    <span>Apa itu paket Lifetime VIP Pass?</span>
                    <x-icon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': activeFaq === 3 }" />
                </button>
                <div x-show="activeFaq === 3" x-cloak class="pt-3 text-xs text-slate-600 font-medium leading-relaxed">
                    Lifetime VIP Pass adalah penawaran spesial di mana Anda hanya perlu membayar 1x di awal (Rp 799.000) untuk menikmati seluruh fitur PRO selamanya tanpa perlu membayar biaya langganan bulanan atau tahunan lagi.
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="border border-slate-200 rounded-2xl p-4 transition-all">
                <button type="button" @click="activeFaq = activeFaq === 4 ? null : 4" class="w-full flex items-center justify-between text-left font-extrabold text-slate-950 text-xs sm:text-sm cursor-pointer">
                    <span>Apakah bisa di-install di smartphone Android dan iPhone?</span>
                    <x-icon name="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200" ::class="{ 'rotate-180': activeFaq === 4 }" />
                </button>
                <div x-show="activeFaq === 4" x-cloak class="pt-3 text-xs text-slate-600 font-medium leading-relaxed">
                    Ya! PortoFinance dibuat dengan teknologi Progressive Web App (PWA). Anda dapat menambahkannya ke Layar Utama (*Home Screen*) HP Anda langsung dari browser Chrome atau Safari tanpa perlu download dari App Store.
                </div>
            </div>

        </div>
    </div>

</div>
