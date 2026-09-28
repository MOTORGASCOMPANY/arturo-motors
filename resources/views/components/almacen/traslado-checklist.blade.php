    @if ($this->mostrarChecklist)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4" x-data>
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarChecklist"></div>

            <div class="relative bg-white sm:rounded-2xl rounded-t-2xl shadow-2xl border border-gray-200 w-full max-h-[88vh] sm:max-h-[85vh] sm:max-w-lg overflow-hidden flex flex-col">

                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 bg-gray-50 shrink-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-clipboard-check text-gray-600"></i> Confirmar envío
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5 truncate">
                                <i class="fas fa-location-dot mr-1"></i>{{ $this->sedes->firstWhere('id', $this->sedeDestinoId)?->nombre ?? '' }}
                                <span class="text-gray-300 mx-1">·</span>
                                {{ $this->seleccionCount }} items
                            </p>
                        </div>
                        <button wire:click="cerrarChecklist" type="button" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition w-9 h-9 flex items-center justify-center shrink-0 -mr-1">
                            <i class="fas fa-times text-lg sm:text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-4 sm:px-6 py-4 flex-1 overflow-y-auto">
                    <div class="space-y-3">

                        @forelse ($this->checklistData as $comp)
                            <div class="border border-gray-200 rounded-xl overflow-hidden">

                                <div class="flex items-center gap-2 sm:gap-3 p-3 bg-white">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                                        {{ $comp['tipo'] === 'kit' ? 'bg-emerald-100' : ($comp['tipo'] === 'pieza' ? 'bg-blue-100' : 'bg-amber-100') }}">
                                        <i class="fas text-sm
                                            {{ $comp['tipo'] === 'kit' ? 'fa-box text-emerald-600' : ($comp['tipo'] === 'pieza' ? 'fa-puzzle-piece text-blue-600' : 'fa-cubes text-amber-600') }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                            <span class="text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</span>
                                            @if ($comp['tipo'] === 'kit')
                                                @if ($comp['es_completo'])
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 shrink-0">Completo</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 shrink-0">Incompleto</span>
                                                @endif
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-400 truncate block mt-0.5">{{ $comp['detalle'] }}</span>
                                    </div>
                                </div>

                                @if ($comp['tipo'] === 'kit' && isset($comp['componentes']))
                                    <div class="px-3 pb-3 pt-2 bg-gray-50 border-t border-gray-100">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                                            {{ $comp['totalPresente'] }}/{{ $comp['totalEsperado'] }} piezas
                                        </p>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1">
                                            @foreach ($comp['componentes'] as $c)
                                                <div class="flex items-center gap-1.5 text-[11px] p-1.5 rounded
                                                    {{ $c['completo'] ? 'text-green-700 bg-green-50/50' : 'text-red-600 bg-red-50/50' }}">
                                                    <i class="fas {{ $c['completo'] ? 'fa-check text-green-500' : 'fa-times text-red-500' }} text-[9px] shrink-0"></i>
                                                    <span class="truncate">{{ $c['nombre'] }}</span>
                                                    <span class="text-gray-400 ml-auto shrink-0 font-medium">{{ $c['presente'] }}/{{ $c['esperada'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-400">
                                <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm font-medium text-gray-500">No hay items en el envío</p>
                            </div>
                        @endforelse

                    </div>
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50 flex gap-3 shrink-0">
                    <button wire:click="cerrarChecklist" type="button"
                        class="flex-1 px-4 py-3 sm:py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 active:scale-[0.98] transition font-medium text-sm">
                        Volver
                    </button>
                    <button wire:click="confirmarEnvio" wire:loading.attr="disabled" type="button"
                        class="flex-1 px-4 py-3 sm:py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white rounded-xl transition font-semibold text-sm flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="confirmarEnvio"><i class="fas fa-check mr-1"></i> Confirmar envío</span>
                        <span wire:loading wire:target="confirmarEnvio"><i class="fas fa-circle-notch fa-spin mr-1"></i> Enviando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
