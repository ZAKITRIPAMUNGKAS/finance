<div class="space-y-6 max-w-5xl mx-auto pb-16">
    
    <!-- Hero Coming Soon Banner -->
    <div class="relative p-8 sm:p-12 rounded-3xl bg-slate-950 text-white overflow-hidden border border-slate-800 shadow-xl space-y-6">
        <!-- Background Glow Accent -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#C6F24D]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-4 max-w-2xl">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-xs font-mono font-black uppercase tracking-wider bg-[#C6F24D] text-slate-950 border border-slate-900/20 shadow-xs flex items-center gap-1.5">
                    <x-icon name="sparkles" class="w-3.5 h-3.5 text-slate-950" />
                    <span>Coming Soon</span>
                </span>
                <span class="text-xs text-slate-400 font-medium font-mono">Next Gen Financial AI</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white leading-tight">
                AI Financial Copilot <br>
                <span class="text-[#C6F24D]">Sedang Disiapkan.</span>
            </h1>

            <p class="text-sm sm:text-base text-slate-400 leading-relaxed">
                Asisten finansial berbasis AI cerdas generasi baru untuk freelancer dan kreator digital Indonesia. Menganalisis daya tahan kas (*runway*), simulasi kelayakan belanja (*can I afford this*), dan rekomendasi otomatis.
            </p>

            <div class="pt-2 flex flex-wrap items-center gap-3">
                <a href="{{ route('dashboard') }}" 
                   class="px-5 py-2.5 rounded-2xl bg-white text-slate-950 text-xs font-extrabold hover:bg-slate-100 transition-all shadow-sm active:scale-95 flex items-center gap-2">
                    <span>← Kembali ke Dashboard</span>
                </a>
                <a href="{{ route('purchase-planning') }}" 
                   class="px-5 py-2.5 rounded-2xl bg-slate-900 text-slate-300 hover:text-white border border-slate-800 text-xs font-bold transition-all flex items-center gap-2">
                    <x-icon name="calculator" class="w-3.5 h-3.5 text-[#C6F24D]" />
                    <span>Coba Kalkulator Can I Afford This?</span>
                </a>
            </div>
        </div>
    </div>

    <!-- UPCOMING CAPABILITIES SHOWCASE CARDS -->
    <div class="space-y-3">
        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Fitur Unggulan yang Sedang Kami Bangun:</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- 1. Realtime Cash Runway Reasoning -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-3 hover:border-slate-300 transition-all">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-900 flex items-center justify-center font-bold">
                    <x-icon name="shield-check" class="w-5 h-5 text-emerald-600" />
                </div>
                <h4 class="text-sm font-extrabold text-slate-950">Auto-Runway & Health Audit</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    AI membaca seluruh saldo rekening dan fixed burn rate langganan untuk memprediksi ketahanan kas jika tidak ada proyek baru.
                </p>
                <div class="pt-2">
                    <span class="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Realtime Telemetry</span>
                </div>
            </div>

            <!-- 2. Voice & Natural Language Shopping Simulator -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-3 hover:border-slate-300 transition-all">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-900 flex items-center justify-center font-bold">
                    <x-icon name="mic" class="w-5 h-5 text-rose-600" />
                </div>
                <h4 class="text-sm font-extrabold text-slate-950">Voice & Natural Language Q&A</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Tanyakan langsung lewat suara: *"Aman gak kalau beli kamera Rp 12 juta bulan ini?"* dan dapatkan pertimbangan rasional berbasis data.
                </p>
                <div class="pt-2">
                    <span class="text-[10px] font-mono font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">Voice Recognition</span>
                </div>
            </div>

            <!-- 3. Smart WhatsApp Invoice Follow-up -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-3 hover:border-slate-300 transition-all">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-900 flex items-center justify-center font-bold">
                    <x-icon name="message-circle" class="w-5 h-5 text-amber-600" />
                </div>
                <h4 class="text-sm font-extrabold text-slate-950">Intelligent Action Generator</h4>
                <p class="text-xs text-slate-500 leading-relaxed">
                    AI otomatis merekomendasikan dan membuat draf penagihan invoice klien terlambat dengan 1 tombol direct WhatsApp link.
                </p>
                <div class="pt-2">
                    <span class="text-[10px] font-mono font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md">Action Trigger</span>
                </div>
            </div>

        </div>
    </div>

</div>
