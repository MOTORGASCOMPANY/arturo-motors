@if($this->modalPartesAbierto)
    <x-dialog-modal wire:model="modalPartesAbierto" maxWidth="xl">
        <x-slot name="title">
            <div class="flex items-center gap-2 min-w-0">
                <i class="fas fa-cogs text-gray-400 text-sm shrink-0"></i>
                <h3 class="font-bold text-gray-900 text-sm truncate">Componentes del kit</h3>
                @if($this->generacionKit)
                    <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded shrink-0">{{ $this->generacionKit }}</span>
                @endif
            </div>
        </x-slot>

        <x-slot name="content">
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
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('modalPartesAbierto', false)" class="w-full sm:w-auto">
                Cerrar
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
@endif