<div class="space-y-6 max-w-5xl mx-auto pb-16" x-data="{
    scrollToBottom() {
        $nextTick(() => {
            const el = document.getElementById('chat-container');
            if (el) el.scrollTop = el.scrollHeight;
        });
    }
}" x-init="scrollToBottom()" @scroll-to-bottom.window="scrollToBottom()">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold tracking-wider uppercase bg-[#C6F24D] text-slate-950 border border-slate-900/10 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-950 animate-ping"></span>
                    <span>AI Engine Active</span>
                </span>
                <span class="text-xs text-slate-400 font-medium">Real-Time Financial Intelligence</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight flex items-center gap-2.5">
                <span>AI Financial Copilot</span>
                <x-icon name="sparkles" class="w-6 h-6 text-amber-500" />
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Asisten cerdas yang menganalisis seluruh data kas, anggaran, & proyekmu secara instan</p>
        </div>
    </div>

    <!-- LIVE TELEMETRY STATUS BAR -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Available Money</span>
            <div class="text-sm sm:text-base font-black font-mono text-emerald-600 truncate">
                Rp {{ number_format($snapshot['available_money'], 0, ',', '.') }}
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Cash Runway</span>
            <div class="text-sm sm:text-base font-black font-mono text-slate-950">
                {{ $snapshot['runway_months'] }} <span class="text-xs font-normal text-slate-400">Bulan</span>
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Monthly Burn</span>
            <div class="text-sm sm:text-base font-black font-mono text-rose-600 truncate">
                Rp {{ number_format($snapshot['monthly_burn_rate'], 0, ',', '.') }}
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold uppercase text-slate-400 block">Piutang Aktif</span>
            <div class="text-sm sm:text-base font-black font-mono text-amber-600 truncate">
                Rp {{ number_format($snapshot['total_receivables'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- QUICK PROMPTS CHIPS -->
    <div class="space-y-2">
        <label class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 block">Pertanyaan Cepat Rekomendasi:</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            @foreach($quickPrompts as $qp)
            <button type="button" wire:click="selectPrompt('{{ addslashes($qp['query']) }}')"
                class="p-3 bg-white hover:bg-slate-50 border border-slate-200/90 hover:border-slate-400 rounded-2xl text-left shadow-2xs transition-all flex items-start gap-2.5 cursor-pointer active:scale-95 group">
                <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-slate-950 group-hover:text-[#C6F24D] text-slate-800 flex items-center justify-center shrink-0 transition-colors">
                    <x-icon :name="$qp['icon']" class="w-3.5 h-3.5" />
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-extrabold text-slate-900 block truncate group-hover:text-slate-950">{{ $qp['title'] }}</span>
                    <span class="text-[10px] text-slate-400 line-clamp-1 mt-0.5">{{ $qp['query'] }}</span>
                </div>
            </button>
            @endforeach
        </div>
    </div>

    <!-- CHAT INTERFACE BOX -->
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden flex flex-col h-[580px]">
        
        <!-- Chat History Scrollable Area -->
        <div id="chat-container" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-4 bg-slate-50/50">
            @foreach($conversation as $msg)
                @if($msg['role'] === 'user')
                <!-- User Bubble -->
                <div class="flex items-start justify-end gap-2.5 animate-fade-in">
                    <div class="max-w-md sm:max-w-lg bg-slate-950 text-white rounded-2xl rounded-tr-xs p-4 shadow-sm space-y-1">
                        <p class="text-xs sm:text-sm font-medium leading-relaxed">{{ $msg['content'] }}</p>
                        <span class="text-[10px] font-mono text-slate-400 block text-right">{{ $msg['time'] }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
                @else
                <!-- AI Copilot Card Bubble -->
                <div class="flex items-start gap-2.5 animate-fade-in">
                    <div class="w-8 h-8 rounded-2xl bg-slate-950 text-[#C6F24D] flex items-center justify-center shrink-0 shadow-2xs">
                        <x-icon name="bot" class="w-4 h-4 text-[#C6F24D]" />
                    </div>

                    <div class="max-w-xl sm:max-w-2xl bg-white border border-slate-200 rounded-3xl rounded-tl-xs p-5 shadow-sm space-y-3.5">
                        
                        <!-- Verdict Header Badge -->
                        @php
                            $res = $msg['content'];
                            $type = $res['verdict_type'] ?? 'info';
                        @endphp
                        <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-mono font-black uppercase tracking-wider {{ $type === 'safe' ? 'bg-emerald-100 text-emerald-800' : ($type === 'warning' ? 'bg-amber-100 text-amber-800' : ($type === 'danger' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-800')) }}">
                                {{ $res['verdict'] ?? 'ANALISIS FINANSIAL' }}
                            </span>
                            <span class="text-[10px] font-mono text-slate-400">{{ $msg['time'] }}</span>
                        </div>

                        <!-- Title & Body -->
                        <div class="space-y-1.5">
                            <h4 class="text-sm font-extrabold text-slate-950">{{ $res['title'] ?? 'Hasil Analisis' }}</h4>
                            <div class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                                {!! nl2br(e($res['message'] ?? '')) !!}
                            </div>
                        </div>

                        <!-- Metrics Grid -->
                        @if(!empty($res['metrics']))
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1">
                            @foreach($res['metrics'] as $lbl => $val)
                            <div class="p-2.5 bg-slate-50 border border-slate-200/80 rounded-xl space-y-0.5">
                                <span class="text-[9px] font-bold uppercase text-slate-400 block">{{ $lbl }}</span>
                                <span class="text-xs font-black font-mono text-slate-900 block truncate">{{ $val }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Actionable Recommendation Box -->
                        @if(!empty($res['recommendation']))
                        <div class="p-3 rounded-2xl bg-[#F7FFD9] border border-lime-300 text-slate-900 text-xs space-y-1">
                            <div class="flex items-center gap-1.5 font-extrabold text-[11px] text-slate-950 uppercase tracking-wider">
                                <x-icon name="lightbulb" class="w-3.5 h-3.5 text-amber-600" />
                                <span>Rekomendasi Tindakan:</span>
                            </div>
                            <p class="text-xs font-medium text-slate-800 leading-snug">
                                {{ $res['recommendation'] }}
                            </p>
                        </div>
                        @endif

                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Chat Input Form -->
        <div class="p-3.5 sm:p-4 bg-white border-t border-slate-100 shrink-0">
            <form wire:submit.prevent="sendQuery" class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" 
                        wire:model="userQuery" 
                        placeholder="Tanya apa saja seputar keuanganmu (e.g. 'Aman gak beli laptop 12 juta?')"
                        class="w-full bg-[#F8F9FA] border border-slate-200 rounded-2xl px-4 py-3 text-xs sm:text-sm font-medium text-slate-900 focus:ring-2 focus:ring-slate-950 focus:bg-white transition-all pr-10">
                </div>
                <button type="submit" 
                    class="px-5 py-3 rounded-2xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] font-extrabold text-xs sm:text-sm shadow-sm active:scale-95 transition-all flex items-center gap-1.5 cursor-pointer shrink-0">
                    <span>Kirim</span>
                    <x-icon name="send" class="w-4 h-4 text-[#C6F24D]" />
                </button>
            </form>
        </div>

    </div>

</div>
