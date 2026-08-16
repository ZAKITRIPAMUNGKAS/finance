<div
    x-data="{
        toasts: @entangle('toasts').live,
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
            $wire.dismiss(id);
        },
        autoRemove(id, delay = 5000) {
            setTimeout(() => this.removeToast(id), delay);
        }
    }"
    class="fixed top-5 right-5 z-[999] space-y-2.5 w-[330px] max-w-[calc(100vw-2rem)] pointer-events-none"
    role="region"
    aria-label="Notifikasi"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-init="autoRemove(toast.id)"
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-8 scale-95"
            class="pointer-events-auto bg-white border border-slate-200/80 rounded-2xl shadow-xl overflow-hidden"
        >
            {{-- Accent top bar by color --}}
            <div
                class="h-0.5 w-full"
                :class="{
                    'bg-emerald-500': toast.color === 'emerald',
                    'bg-[#C6F24D]': toast.color === 'lime',
                    'bg-indigo-500': toast.color === 'indigo',
                    'bg-slate-900': toast.color === 'slate'
                }"
            ></div>

            <div class="flex items-start gap-3 p-3.5">
                {{-- Icon --}}
                <div
                    class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                    :class="{
                        'bg-emerald-100 text-emerald-700': toast.color === 'emerald',
                        'bg-[#EBFAD2] text-slate-900': toast.color === 'lime',
                        'bg-indigo-100 text-indigo-700': toast.color === 'indigo',
                        'bg-slate-900 text-white': toast.color === 'slate'
                    }"
                >
                    {{-- Inline SVG icons for the common set --}}
                    <template x-if="toast.icon === 'arrow-down-left'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="17" y1="7" x2="7" y2="17"/><polyline points="17 17 7 17 7 7"/></svg>
                    </template>
                    <template x-if="toast.icon === 'arrow-up-right'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                    </template>
                    <template x-if="toast.icon === 'arrow-right-left'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3 4 7l4 4"/><path d="M4 7h16"/><path d="m16 21 4-4-4-4"/><path d="M20 17H4"/></svg>
                    </template>
                    <template x-if="toast.icon === 'shopping-bag'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </template>
                    <template x-if="toast.icon === 'piggy-bank'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5c-1.5 0-2.8 1.4-3 2-3.5-1.5-11-.3-11 5 0 1.8 0 3 2 4.5V20h4v-2h3v2h4v-4c1-.5 1.7-1 2-2h2v-4h-2c0-1-.5-1.5-1-2V5z"/><path d="M2 9v1a2 2 0 0 0 2 2h1"/><path d="M16 11h.01"/></svg>
                    </template>
                </div>

                {{-- Text --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 leading-snug" x-text="toast.message"></p>
                    <p class="text-[10px] text-slate-500 mt-0.5 font-medium" x-text="toast.sub"></p>
                </div>

                {{-- Dismiss --}}
                <button
                    @click="removeToast(toast.id)"
                    class="shrink-0 p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Progress timer bar --}}
            <div class="px-3.5 pb-2.5">
                <div class="h-0.5 bg-slate-100 rounded-full overflow-hidden">
                    <div
                        class="h-full rounded-full"
                        :class="{
                            'bg-emerald-400': toast.color === 'emerald',
                            'bg-[#C6F24D]': toast.color === 'lime',
                            'bg-indigo-400': toast.color === 'indigo',
                            'bg-slate-400': toast.color === 'slate'
                        }"
                        style="width: 100%; animation: toast-shrink 5s linear forwards;"
                    ></div>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
@keyframes toast-shrink {
    from { width: 100%; }
    to   { width: 0%; }
}
</style>
