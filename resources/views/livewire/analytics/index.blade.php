<div class="max-w-5xl mx-auto space-y-4 sm:space-y-6" x-data="{
    initRadar() {
        const ctx = document.getElementById('healthRadarChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: {{ Js::from($radarLabels) }},
                    datasets: [{
                        label: 'Skor Pilar',
                        data: {{ Js::from($radarData) }},
                        backgroundColor: 'rgba(132, 204, 22, 0.15)',
                        borderColor: '#84CC16',
                        borderWidth: 2,
                        pointBackgroundColor: '#0F172A',
                        pointBorderColor: '#84CC16',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 700 },
                            bodyFont: { family: 'JetBrains Mono', size: 11 },
                            padding: 10,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.label + ': ' + ctx.raw + '/100';
                                }
                            }
                        }
                    },
                    scales: {
                        r: {
                            min: 0,
                            max: 100,
                            ticks: { display: false, stepSize: 25 },
                            grid: { color: '#E2E8F0', circular: true },
                            angleLines: { color: '#CBD5E1' },
                            pointLabels: {
                                font: { family: 'Plus Jakarta Sans', size: 10, weight: '700' },
                                color: '#475569'
                            }
                        }
                    }
                }
            });
        }
    }
}" x-init="initRadar()">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  1. FINANCIAL HEALTH SCORE HERO (Clean & Breathable)         -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-8 shadow-xs">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-8 items-center">
            
            <!-- LEFT: GRADE & SUMMARY (7 Cols) -->
            <div class="lg:col-span-7 space-y-4 sm:space-y-6">
                
                <div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5 sm:mb-1">Diagnosa Finansial</span>
                    <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight">Financial Health Index</h2>
                </div>

                <!-- GRADE + BIG SCORE CARD -->
                <div class="flex items-center gap-4 sm:gap-5 p-3.5 sm:p-5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100">
                    <!-- Big Health Icon Box -->
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0 shadow-2xs">
                        <x-icon name="shield-check" class="w-8 h-8 sm:w-10 sm:h-10" strokeWidth="2.2" />
                    </div>

                    <!-- Score & Status -->
                    <div class="space-y-1 min-w-0">
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl sm:text-4xl font-black font-mono text-slate-950">{{ $healthData['total_score'] }}</span>
                            <span class="text-xs sm:text-sm font-bold text-slate-400">/ 100</span>
                        </div>
                        
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <span class="inline-flex items-center gap-1 px-2 sm:px-2.5 py-0.5 rounded-full bg-[#EBFAD2] text-slate-900 border border-[#D4F66C] text-[10px] sm:text-xs font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#84CC16]"></span>
                                <span>{{ $healthData['status'] }}</span>
                            </span>
                            <span class="text-[10px] sm:text-[11px] text-slate-400 font-mono">Index</span>
                        </div>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    {{ $healthData['summary'] }}
                </p>

            </div>

            <!-- RIGHT: PENTAGON RADAR (5 Cols) -->
            <div class="lg:col-span-5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 p-3.5 sm:p-5 flex flex-col items-center justify-center">
                <div class="w-full flex items-center justify-between mb-2">
                    <span class="text-[9px] sm:text-[10px] uppercase font-bold tracking-wider text-slate-400">Pentagon Radar</span>
                    <span class="text-[9px] sm:text-[10px] font-mono text-slate-500">5 Dimensi</span>
                </div>
                <div class="h-56 sm:h-72 w-full relative">
                    <canvas id="healthRadarChart"></canvas>
                </div>
            </div>

        </div>

    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  2. RINCIAN 5 PILAR (Spacious & Clean Breakdown)             -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-7 shadow-xs space-y-3.5 sm:space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Rincian 5 Pilar Keuangan</h3>
                <p class="text-[11px] sm:text-xs text-slate-400">Komponen pembentuk skor kesehatan finansial</p>
            </div>
            <span class="text-[10px] sm:text-xs font-mono text-slate-400 hidden sm:inline">Rolling 3-Bulan</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 sm:gap-3.5 pt-0.5">
            @foreach($healthData['breakdown'] as $key => $pillar)
            <div class="p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 hover:border-slate-300 transition-colors space-y-2.5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[9px] sm:text-[10px] font-mono font-extrabold text-slate-400">Bobot {{ $pillar['weight'] }}</span>
                        <span class="text-[11px] sm:text-xs font-black font-mono text-slate-900 bg-white px-2 py-0.5 rounded-lg border border-slate-200 shadow-2xs">{{ $pillar['score'] }}/100</span>
                    </div>
                    <span class="font-bold text-xs text-slate-900 block leading-snug">{{ $pillar['label'] }}</span>
                </div>

                <div class="pt-1.5 border-t border-slate-200/60 flex items-center justify-between text-[10px] sm:text-[11px]">
                    <span class="text-slate-400">{{ $pillar['metric_name'] }}</span>
                    <span class="font-bold text-slate-700 font-mono">{{ $pillar['value'] }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  3. BOTTOM CARDS: BISNIS VS LIFESTYLE & LEADERBOARD          -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

        <!-- CARD 1: BUSINESS VS LIFESTYLE EXPENSE -->
        <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-3.5 sm:space-y-4">
            <div>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Operasional Bisnis vs Lifestyle</h3>
                <p class="text-[11px] sm:text-xs text-slate-400">Pemisahan pengeluaran kerja vs kebutuhan pribadi</p>
            </div>

            @php
                $bizPct  = $totalExpense > 0 ? ($businessExpense / $totalExpense) * 100 : 0;
                $persPct = $totalExpense > 0 ? ($personalExpense / $totalExpense) * 100 : 0;
            @endphp

            <!-- Clean Ratio Progress Bar -->
            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden flex">
                <div class="h-full bg-slate-950" style="width: {{ $bizPct }}%"></div>
                <div class="h-full bg-[#84CC16]" style="width: {{ $persPct }}%"></div>
            </div>

            <div class="grid grid-cols-2 gap-2.5 sm:gap-3 pt-0.5">
                <!-- Business -->
                <div class="p-3 sm:p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                        <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg bg-slate-950 text-[#C6F24D] flex items-center justify-center">
                            <x-icon name="briefcase" class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-700">Operasional</span>
                    </div>
                    <span class="text-xs sm:text-sm font-extrabold font-mono text-slate-900 block truncate">Rp {{ number_format($businessExpense, 0, ',', '.') }}</span>
                    <span class="text-[9px] sm:text-[10px] text-slate-400">{{ number_format($bizPct, 0) }}% dari total</span>
                </div>

                <!-- Personal -->
                <div class="p-3 sm:p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100">
                    <div class="flex items-center gap-1.5 sm:gap-2 mb-1">
                        <div class="w-5 h-5 sm:w-6 sm:h-6 rounded-lg bg-[#C6F24D] text-slate-950 flex items-center justify-center">
                            <x-icon name="shopping-bag" class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                        </div>
                        <span class="text-[10px] sm:text-[11px] font-bold text-slate-700">Lifestyle</span>
                    </div>
                    <span class="text-xs sm:text-sm font-extrabold font-mono text-slate-900 block truncate">Rp {{ number_format($personalExpense, 0, ',', '.') }}</span>
                    <span class="text-[9px] sm:text-[10px] text-slate-400">{{ number_format($persPct, 0) }}% dari total</span>
                </div>
            </div>
        </div>

        <!-- CARD 2: PROJECT PROFITABILITY LEADERBOARD -->
        <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xs space-y-3.5 sm:space-y-4">
            <div>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Project Paling Menguntungkan</h3>
                <p class="text-[11px] sm:text-xs text-slate-400">Peringkat margin profit per project freelance</p>
            </div>

            <div class="space-y-2 pt-0.5">
                @foreach($projects as $index => $proj)
                <div class="p-3 sm:p-3.5 bg-[#F8F9FA] rounded-xl sm:rounded-2xl border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 pr-2">
                        <span class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center font-extrabold text-[10px] sm:text-xs shrink-0">
                            {{ $index + 1 }}
                        </span>
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-slate-900 block truncate">{{ $proj->name }}</span>
                            <span class="text-[10px] text-slate-400 block truncate">{{ $proj->client->name ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-[11px] sm:text-xs font-extrabold font-mono text-emerald-600 block">+Rp {{ number_format($proj->profit, 0, ',', '.') }}</span>
                        <span class="text-[9px] sm:text-[10px] font-bold font-mono text-slate-400">{{ $proj->margin_percentage }}% margin</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
