<div class="space-y-6 max-w-5xl mx-auto pb-16" 
    x-data="{
        isListening: false,
        recognition: null,
        initSpeech() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (SpeechRecognition) {
                this.recognition = new SpeechRecognition();
                this.recognition.lang = 'id-ID';
                this.recognition.continuous = false;
                this.recognition.interimResults = false;

                this.recognition.onstart = () => { this.isListening = true; };
                this.recognition.onend = () => { this.isListening = false; };
                this.recognition.onerror = () => { this.isListening = false; };
                this.recognition.onresult = (event) => {
                    const transcript = event.results[0][0].transcript;
                    if (transcript) {
                        $wire.set('userQuery', transcript);
                        setTimeout(() => $wire.sendQuery(), 200);
                    }
                };
            }
        },
        toggleVoice() {
            if (!this.recognition) this.initSpeech();
            if (!this.recognition) {
                alert('Browser Anda tidak mendukung Web Speech API. Silakan gunakan Google Chrome.');
                return;
            }
            if (this.isListening) {
                this.recognition.stop();
            } else {
                this.recognition.start();
            }
        },
        scrollToBottom() {
            $nextTick(() => {
                const el = document.getElementById('chat-container');
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    }" 
    x-init="initSpeech(); scrollToBottom();" 
    @scroll-to-bottom.window="scrollToBottom()">

    <!-- Toast Message -->
    @if(session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold text-xs flex items-center justify-between shadow-xs animate-fade-in">
        <div class="flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ session('message') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold tracking-wider uppercase bg-[#C6F24D] text-slate-950 border border-slate-900/10 flex items-center gap-1.5 shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-950 animate-ping"></span>
                    <span>AI Copilot Engine Active</span>
                </span>
                <span class="text-xs text-slate-400 font-medium">Real-Time Financial Intelligence</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-950 tracking-tight flex items-center gap-2.5">
                <span>AI Financial Copilot</span>
                <x-icon name="sparkles" class="w-6 h-6 text-amber-500" />
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Asisten cerdas yang menganalisis seluruh data kas, anggaran, & proyekmu secara instan</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" wire:click="clearHistory" 
                class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                <x-icon name="trash-2" class="w-3.5 h-3.5 text-slate-400" />
                <span>Reset Chat</span>
            </button>
            <button type="button" wire:click="$set('showApiKeyModal', true)"
                class="px-3.5 py-2 rounded-xl bg-slate-950 text-[#C6F24D] text-xs font-extrabold hover:bg-slate-800 transition-all shadow-2xs flex items-center gap-1.5 cursor-pointer active:scale-95">
                <x-icon name="key" class="w-3.5 h-3.5 text-[#C6F24D]" />
                <span>API Key</span>
            </button>
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
        <label class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 block">Pilihan Pertanyaan Cepat:</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
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
    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden flex flex-col h-[560px]">
        
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

                        <!-- Dynamic Action Button -->
                        @if(!empty($res['action_type']))
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-end">
                            @if($res['action_type'] === 'invoice')
                            <a href="{{ route('clients') }}" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-extrabold text-xs shadow-2xs flex items-center gap-1.5 transition-all">
                                <x-icon name="message-circle" class="w-3.5 h-3.5" />
                                <span>{{ $res['action_label'] ?? 'Buka Penagihan Klien' }}</span>
                            </a>
                            @elseif($res['action_type'] === 'wishlist')
                            <a href="{{ route('wishlists') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] font-extrabold text-xs shadow-2xs flex items-center gap-1.5 transition-all">
                                <x-icon name="shopping-bag" class="w-3.5 h-3.5 text-[#C6F24D]" />
                                <span>{{ $res['action_label'] ?? 'Buka Wishlist' }}</span>
                            </a>
                            @elseif($res['action_type'] === 'budget')
                            <a href="{{ route('budgets') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-950 hover:bg-slate-800 text-[#C6F24D] font-extrabold text-xs shadow-2xs flex items-center gap-1.5 transition-all">
                                <x-icon name="pie-chart" class="w-3.5 h-3.5 text-[#C6F24D]" />
                                <span>{{ $res['action_label'] ?? 'Atur Pos Anggaran' }}</span>
                            </a>
                            @elseif($res['action_type'] === 'report')
                            <a href="{{ route('reports.financial-statement') }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-2xs flex items-center gap-1.5 transition-all">
                                <x-icon name="file-text" class="w-3.5 h-3.5" />
                                <span>{{ $res['action_label'] ?? 'Cetak Laporan Keuangan' }}</span>
                            </a>
                            @endif
                        </div>
                        @endif

                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- Chat Input Form & Speech-to-Text -->
        <div class="p-3.5 sm:p-4 bg-white border-t border-slate-100 shrink-0">
            <form wire:submit.prevent="sendQuery" class="flex items-center gap-2">
                
                <!-- Voice Mic Button -->
                <button type="button" 
                    @click="toggleVoice()" 
                    :class="isListening ? 'bg-rose-500 text-white animate-pulse' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                    class="p-3 rounded-2xl transition-all cursor-pointer flex items-center justify-center shrink-0 shadow-2xs"
                    :title="isListening ? 'Mendengarkan... Klik untuk berhenti' : 'Bicara / Voice Input'">
                    <x-icon name="mic" class="w-4 h-4" />
                </button>

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

    <!-- API KEY CONFIG MODAL -->
    @if($showApiKeyModal)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-fade-in" x-cloak>
        <div class="relative w-full max-w-md bg-white border border-slate-200 rounded-t-[28px] sm:rounded-3xl shadow-2xl p-6 space-y-4 anim-scale-up">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-slate-950 text-[#C6F24D] flex items-center justify-center">
                        <x-icon name="key" class="w-4 h-4 text-[#C6F24D]" />
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-950">Konfigurasi Google Gemini API</h3>
                </div>
                <button wire:click="$set('showApiKeyModal', false)" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed">
                Secara default, AI Copilot menggunakan **Local Financial Expert Engine**. Anda dapat memasukkan API Key Google Gemini (gratis di Google AI Studio) untuk kemampuan penalaran LLM tingkat lanjut.
            </p>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold uppercase text-slate-400">Gemini API Key</label>
                <input type="password" wire:model="customApiKey" placeholder="AIzaSy..."
                    class="w-full bg-[#F8F9FA] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:outline-none focus:border-slate-950 focus:bg-white">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" wire:click="$set('showApiKeyModal', false)" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                    Batal
                </button>
                <button type="button" wire:click="saveApiKey" class="px-4 py-2 bg-slate-950 text-[#C6F24D] text-xs font-extrabold rounded-xl shadow-2xs active:scale-95">
                    Simpan Key
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
