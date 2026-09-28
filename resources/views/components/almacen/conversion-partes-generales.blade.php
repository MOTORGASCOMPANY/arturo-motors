    @if($this->modalPartesAbierto)
        <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center sm:p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarPartesGenerales"></div>
            <div class="relative bg-white sm:rounded-2xl rounded-t-2xl shadow-2xl border border-gray-200 w-full max-h-[85vh] sm:max-h-none sm:max-w-xl overflow-hidden flex flex-col">
                <div class="px-4 sm:px-5 py-3 border-b border-gray-200 flex items-center justify-between gap-2 shrink-0">
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="fas fa-cogs text-gray-400 text-sm shrink-0"></i>
                        <h3 class="font-bold text-gray-900 text-sm truncate">Componentes del kit</h3>
                        @if($this->generacionKit)
                            <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded shrink-0">{{ $this->generacionKit }}</span>
                        @endif
                    </div>
                    <button wire:click="cerrarPartesGenerales" class="w-8 h-8 sm:w-7 sm:h-7 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition shrink-0">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="px-4 sm:px-5 py-4 flex-1 overflow-y-auto">
                    @if($this->todasPiezasKit->isNotEmpty())
                        <p class="text-xs text-gray-500 mb-3">{{ $this->todasPiezasKit->count() }} piezas</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                            @foreach($this->todasPiezasKit as $comp)
                                <div class="flex items-center justify-between py-2.5 sm:py-2 px-3 rounded-lg bg-gray-50">
                                    <span class="text-sm font-medium text-gray-900 truncate">{{ $comp->nombre }}</span>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="text-xs font-bold text-gray-700">x{{ $comp->cantidad_esperada }}</span>
                                        <span class="px-1.5 py-0.5 {{ $comp->es_serializado ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-600' }} text-[10px] font-semibold rounded">
                                            {{ $comp->es_serializado ? 'Serial' : 'Cant.' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="px-4 sm:px-5 py-3 border-t border-gray-200 bg-gray-50 flex justify-end shrink-0">
                    <button wire:click="cerrarPartesGenerales" class="w-full sm:w-auto px-4 py-2.5 sm:py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-xs">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
