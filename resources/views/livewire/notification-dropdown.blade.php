<div class="relative" x-data="{ open: @entangle('isOpen') }" @click.outside="open = false">
    <!-- Bell Trigger Button -->
    <button @click="open = !open" 
            type="button"
            class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-slate-700 hover:text-slate-950 flex items-center justify-center transition-colors relative cursor-pointer active-tap shadow-2xs"
            title="Notifikasi & Peringatan Sistem"
            aria-label="Notifikasi">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-800" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white animate-pulse"></span>
        @endif
    </button>

    <!-- Notification Popover Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="absolute right-0 mt-2.5 w-80 sm:w-96 bg-white border border-slate-200/80 rounded-2xl shadow-2xl overflow-hidden z-50 flex flex-col max-h-[80vh]"
         x-cloak>
        
        <!-- Header -->
        <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <h4 class="font-extrabold text-xs text-slate-900">Notifikasi Sistem</h4>
                @if($unreadCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-[#C6F24D] text-slate-950">
                        {{ $unreadCount }} Baru
                    </span>
                @endif
            </div>
            
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" 
                        type="button"
                        class="text-[10px] font-bold text-slate-500 hover:text-slate-900 transition-colors cursor-pointer">
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>

        <!-- Notification List -->
        <div class="overflow-y-auto divide-y divide-slate-100 max-h-[340px]">
            @forelse($notifications as $n)
                <div class="p-3.5 hover:bg-slate-50/80 transition-colors flex items-start justify-between gap-3 group">
                    <a href="{{ $n['link'] }}" @click="open = false" class="flex-1 min-w-0 flex items-start gap-2.5">
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $n['type'] === 'danger' ? 'bg-rose-100 text-rose-600' : ($n['type'] === 'warning' ? 'bg-amber-100 text-amber-600' : ($n['type'] === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-700')) }}">
                            <x-icon :name="$n['icon']" class="w-3.5 h-3.5" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="font-extrabold text-xs text-slate-900 truncate block">{{ $n['title'] }}</span>
                                <span class="text-[9px] text-slate-400 font-mono shrink-0">{{ $n['time'] }}</span>
                            </div>
                            <p class="text-[11px] text-slate-600 leading-snug">{{ $n['message'] }}</p>
                        </div>
                    </a>
                    <button wire:click.stop="dismissNotification('{{ $n['id'] }}')" 
                            type="button" 
                            class="text-slate-300 hover:text-slate-600 p-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity"
                            title="Tutup Notifikasi">
                        <x-icon name="x" class="w-3 h-3" />
                    </button>
                </div>
            @empty
                <div class="p-8 text-center space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <x-icon name="bell-off" class="w-5 h-5" />
                    </div>
                    <span class="font-bold text-xs text-slate-700 block">Tidak ada notifikasi baru</span>
                    <p class="text-[10px] text-slate-400">Semua invoice, budget, dan target tabungan Anda dalam kondisi aman!</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="p-2 border-t border-slate-100 bg-slate-50/50 text-center shrink-0">
            <a href="{{ route('projects') }}" @click="open = false" class="text-[11px] font-bold text-teal-700 hover:text-teal-900 block py-1">
                Buka Dashboard Proyek &rarr;
            </a>
        </div>
    </div>
</div>
