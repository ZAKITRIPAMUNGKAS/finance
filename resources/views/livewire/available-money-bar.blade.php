<div class="inline-flex">
    <a href="{{ route('dashboard') }}"
       x-data="{
           animating: false,
           triggerAnimation() {
               this.animating = true;
               setTimeout(() => { this.animating = false; }, 700);
           }
       }"
       @transaction-saved.window="triggerAnimation()"
       class="h-9 sm:h-9 flex items-center gap-1.5 sm:gap-2 bg-slate-100/90 hover:bg-slate-200/80 border border-slate-200/70 rounded-xl px-2.5 sm:px-3 shadow-2xs transition-all cursor-pointer"
    >
        {{-- Pulse dot: green when up, amber when down --}}
        <span class="w-2 h-2 rounded-full animate-pulse shrink-0 transition-colors duration-500
            {{ $direction === 'up' ? 'bg-emerald-500' : ($direction === 'down' ? 'bg-amber-400' : 'bg-emerald-500') }}">
        </span>

        <div class="text-right leading-none flex flex-col justify-center">
            <span class="text-[8px] sm:text-[9px] uppercase font-bold tracking-wider text-slate-400 block mb-0.5">Available</span>

            {{-- Amount with slide animation on change --}}
            <span
                :class="animating ? 'animate-bounce' : ''"
                class="text-[11px] sm:text-xs font-black font-mono block transition-all duration-300
                    {{ $direction === 'up' ? 'text-emerald-700' : ($direction === 'down' ? 'text-rose-600' : 'text-slate-900') }}"
            >
                Rp {{ number_format($availableMoney, 0, ',', '.') }}
            </span>
        </div>

        {{-- Up/down micro indicator --}}
        @if($direction === 'up')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
        @elseif($direction === 'down')
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-rose-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        @endif
    </a>
</div>
