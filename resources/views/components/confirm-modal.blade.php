{{--
  Confirm Modal Component
  Usage: @include('components.confirm-modal')
  Trigger: window.dispatchEvent(new CustomEvent('confirm-modal:show', { detail: { title, message, action } }))
  action: { componentId, method, params }
--}}
<div
    x-data="{
        open: false,
        title: '',
        message: '',
        action: null,
        processing: false,
        init() {
            window.addEventListener('confirm-modal:show', (e) => {
                this.title = e.detail.title || 'Confirmar';
                this.message = e.detail.message || '';
                this.action = e.detail.action || null;
                this.processing = false;
                this.open = true;
            });
        },
        confirm() {
            if (!this.action) { this.open = false; return; }
            this.processing = true;
            Livewire.find(this.action.componentId)[this.action.method](...(this.action.params || []));
            setTimeout(() => { this.open = false; this.processing = false; }, 300);
        },
        cancel() {
            this.open = false;
            this.processing = false;
        }
    }"
    x-cloak
    x-show="open"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="cancel()"></div>

    {{-- Modal --}}
    <div
        class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        @click.stop
    >
        {{-- Header --}}
        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white" x-text="title"></h3>
            </div>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5">
            <p class="text-gray-600 text-sm leading-relaxed" x-text="message"></p>
        </div>

        {{-- Footer --}}
        <div class="px-6 pb-5 flex gap-3 justify-end">
            <button
                @click="cancel()"
                class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
                Cancelar
            </button>
            <button
                @click="confirm()"
                :disabled="processing"
                class="px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-colors disabled:opacity-50"
            >
                <span x-show="!processing">Eliminar</span>
                <span x-show="processing" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Eliminando...
                </span>
            </button>
        </div>
    </div>
</div>