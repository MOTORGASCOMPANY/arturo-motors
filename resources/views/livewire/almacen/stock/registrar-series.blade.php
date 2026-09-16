<div>
    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-barcode text-indigo-600"></i>
                        Registrar Series
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Kits con componentes pendientes de serie</p>
                </div>
                <a href="{{ route('almacen.stock') }}"
                   class="text-sm text-gray-500 hover:text-gray-700">
                    ← Volver al stock
                </a>
            </div>

            {{-- Filtros --}}
            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                <div class="flex-1">
                    <select wire:model.live="filtroSedeId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todas las sedes</option>
                        @foreach(\App\Models\Sede::activas()->get() as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="busqueda"
                           placeholder="Buscar kit por nombre..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Lista de kits pendientes --}}
            @if ($this->kitsPendientes->isEmpty())
                <div class="text-center py-16 text-gray-400">
                    <i class="fas fa-check-circle text-5xl mb-3 text-green-300"></i>
                    <p class="text-lg font-medium">Todos los kits tienen sus series registradas</p>
                    <p class="text-sm mt-1">No hay kits con componentes pendientes de serie.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($this->kitsPendientes as $kit)
                        @php
                            $total = $kit->total_piezas ?? 0;
                            $conSerie = $kit->piezas_con_serie ?? 0;
                            $pendientes = $total - $conSerie;
                            $progreso = $total > 0 ? round(($conSerie / $total) * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-box text-indigo-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $kit->producto->nombre }}
                                        <span class="text-gray-400 font-normal">#{{ $kit->id }}</span>
                                    </p>
                                    <div class="flex items-center gap-3 mt-0.5">
                                        <span class="text-xs text-gray-400">
                                            {{ $conSerie }}/{{ $total }} piezas con serie
                                        </span>
                                        @if ($pendientes > 0)
                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full">
                                                {{ $pendientes }} pendiente(s)
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">
                                                Completo
                                            </span>
                                        @endif
                                    </div>
                                    {{-- Barra de progreso --}}
                                    <div class="w-48 bg-gray-200 rounded-full h-1.5 mt-2">
                                        <div class="h-1.5 rounded-full transition-all duration-300 {{ $progreso == 100 ? 'bg-green-500' : 'bg-indigo-500' }}"
                                             style="width: {{ $progreso }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <button wire:click="abrirModal({{ $kit->id }})"
                                    class="px-4 py-2 text-sm font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-sm">
                                <i class="fas fa-edit mr-1"></i> Registrar
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Registrar series                                     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if ($modalAbierto)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" wire:click="cerrarModal"></div>

            {{-- Panel --}}
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl"
                         wire:click.away="cerrarModal">

                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                        <i class="fas fa-barcode"></i>
                                        Registrar Series
                                    </h3>
                                    <p class="text-sm text-indigo-100 mt-0.5">{{ $kitNombre }}</p>
                                </div>
                                <button wire:click="cerrarModal" class="text-white/70 hover:text-white transition">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="px-6 py-4 max-h-[55vh] overflow-y-auto">
                            @php
                                $serializados = collect($itemsPendientes)->where('es_serializado', true);
                                $porCantidad = collect($itemsPendientes)->where('es_serializado', false);
                            @endphp

                            {{-- Serializados --}}
                            @if ($serializados->isNotEmpty())
                                <div class="mb-5">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                        <i class="fas fa-microchip text-indigo-500"></i> Serializados
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($serializados as $idx => $item)
                                            @php
                                                $realIndex = array_search($item, $itemsPendientes);
                                                $tieneSerie = !empty(trim($item['serie_actual']));
                                            @endphp
                                            <div class="flex items-center gap-3 p-3 rounded-xl border transition-colors {{ $tieneSerie ? 'bg-green-50 border-green-200' : 'bg-indigo-50 border-indigo-200' }}">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $tieneSerie ? 'bg-green-100 text-green-600' : 'bg-indigo-100 text-indigo-600' }}">
                                                    @if ($tieneSerie)
                                                        <i class="fas fa-check text-sm"></i>
                                                    @else
                                                        <i class="fas fa-barcode text-sm"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $item['producto_nombre'] }}</p>
                                                    @if ($tieneSerie)
                                                        <p class="text-xs text-green-600 font-medium">Serie: {{ $item['serie_actual'] }}</p>
                                                    @else
                                                        <p class="text-xs text-amber-500 font-medium">Sin serie</p>
                                                    @endif
                                                </div>
                                                <div class="w-48">
                                                    <input type="text"
                                                           wire:model.live="itemsPendientes.{{ $realIndex }}.serie_nueva"
                                                           placeholder="Ingresar serie..."
                                                           class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 {{ $tieneSerie ? 'bg-green-50' : '' }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Por cantidad --}}
                            @if ($porCantidad->isNotEmpty())
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                        <i class="fas fa-cubes text-amber-500"></i> Por cantidad (sin serie)
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($porCantidad as $item)
                                            <div class="flex items-center gap-3 p-3 rounded-xl border bg-gray-50 border-gray-200 opacity-70">
                                                <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-400 flex items-center justify-center">
                                                    <i class="fas fa-cubes text-sm"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-600 truncate">{{ $item['producto_nombre'] }}</p>
                                                    <p class="text-xs text-gray-400">No requiere serie</p>
                                                </div>
                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-[10px] font-bold rounded-full">
                                                    Cantidad
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Footer --}}
                        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t">
                            <button wire:click="cerrarModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                Cancelar
                            </button>
                            <button wire:click="guardarSeries"
                                    wire:loading.attr="disabled"
                                    class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm disabled:opacity-50">
                                <span wire:loading.remove wire:target="guardarSeries">
                                    <i class="fas fa-save mr-1"></i> Guardar series
                                </span>
                                <span wire:loading wire:target="guardarSeries">Guardando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
