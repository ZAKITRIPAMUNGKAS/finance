<div x-data="{
        isOpen: false,
        title: 'Konfirmasi Tindakan',
        message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        confirmText: 'Ya, Lanjutkan',
        cancelText: 'Batal',
        isDanger: true,
        onConfirmCallback: null,

        openConfirm(detail) {
            this.message = detail.message || 'Apakah Anda yakin?';
            this.title = detail.title || (this.isDeleteMessage(this.message) ? 'Konfirmasi Hapus' : 'Konfirmasi Tindakan');
            this.confirmText = detail.confirmText || (this.isDeleteMessage(this.message) ? 'Ya, Hapus' : 'Ya, Lanjutkan');
            this.cancelText = detail.cancelText || 'Batal';
            this.isDanger = detail.isDanger !== undefined ? detail.isDanger : this.isDeleteMessage(this.message);
            this.onConfirmCallback = detail.onConfirm || null;
            this.isOpen = true;
        },

        isDeleteMessage(msg) {
            let lower = (msg || '').toLowerCase();
            return lower.includes('hapus') || lower.includes('delete') || lower.includes('remove') || lower.includes('reset');
        },

        confirm() {
            this.isOpen = false;
            if (typeof this.onConfirmCallback === 'function') {
                this.onConfirmCallback();
            }
        },

        cancel() {
            this.isOpen = false;
            this.onConfirmCallback = null;
        }
     }"
     @open-custom-confirm.window="openConfirm($event.detail)"
     @keydown.escape.window="if(isOpen) cancel()"
     x-cloak
     class="relative z-999">

    <!-- Backdrop -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#0F172A]/70 backdrop-blur-xs flex items-center justify-center p-4">
        
        <!-- Dialog Modal Box -->
        <div x-show="isOpen"
             @click.away="cancel()"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="bg-white rounded-3xl p-6 sm:p-7 max-w-sm w-full shadow-2xl border border-slate-200/90 space-y-5 text-center">
            
            <!-- Warning / Info Icon -->
            <div class="mx-auto w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm"
                 :class="isDanger ? 'bg-rose-50 border border-rose-100 text-rose-600' : 'bg-[#F7FFD9] border border-lime-200 text-[#0F172A]'">
                <template x-if="isDanger">
                    <x-icon name="alert-triangle" class="w-7 h-7 text-rose-600" strokeWidth="2.2" />
                </template>
                <template x-if="!isDanger">
                    <x-icon name="help-circle" class="w-7 h-7 text-[#0F172A]" strokeWidth="2.2" />
                </template>
            </div>

            <!-- Content -->
            <div class="space-y-1.5">
                <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight" x-text="title"></h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" x-text="message"></p>
                <template x-if="isDanger">
                    <p class="text-[11px] font-bold text-rose-500 mt-1">Tindakan ini bersifat permanen.</p>
                </template>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-center gap-2.5 pt-1">
                <button type="button" 
                        @click="cancel()"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 bg-white font-bold text-xs sm:text-sm text-slate-700 hover:bg-slate-50 hover:border-slate-300 cursor-pointer shadow-2xs transition-all active:scale-95">
                    <span x-text="cancelText"></span>
                </button>
                <button type="button" 
                        @click="confirm()"
                        class="flex-1 px-4 py-2.5 rounded-xl font-black text-xs sm:text-sm cursor-pointer shadow-sm transition-all active:scale-95 text-white"
                        :class="isDanger ? 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20' : 'bg-slate-950 hover:bg-slate-800'">
                    <span x-text="confirmText"></span>
                </button>
            </div>

        </div>

    </div>

</div>

<script>
    // Universal Interceptor for all wire:confirm in the application
    (function() {
        if (window.__portoConfirmInitialized) return;
        window.__portoConfirmInitialized = true;

        document.addEventListener('click', function(e) {
            // Find closest element with wire:confirm
            const target = e.target.closest('[wire\\:confirm]');
            if (!target) return;

            // If already confirmed by our custom dialog, let the action pass
            if (target.dataset.customConfirmed === 'true') {
                delete target.dataset.customConfirmed;
                return;
            }

            // Intercept event and prevent native browser confirm dialog
            e.stopImmediatePropagation();
            e.preventDefault();

            const message = target.getAttribute('wire:confirm');

            window.dispatchEvent(new CustomEvent('open-custom-confirm', {
                detail: {
                    message: message,
                    onConfirm: function() {
                        target.dataset.customConfirmed = 'true';
                        target.click();
                    }
                }
            }));
        }, true); // Capture phase is critical to intercept before Livewire's internal click handler

        // Helper for global JS calls
        window.customConfirm = function(message, onConfirm, options = {}) {
            window.dispatchEvent(new CustomEvent('open-custom-confirm', {
                detail: Object.assign({
                    message: message,
                    onConfirm: onConfirm
                }, options)
            }));
        };
    })();
</script>
