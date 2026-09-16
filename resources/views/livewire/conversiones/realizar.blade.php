<div wire:loading.class="opacity-50 pointer-events-none" class="min-h-screen bg-gray-50">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <a href="{{ route('conversiones.mis-asignadas') }}" class="text-gray-400 hover:text-gray-700 transition">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900">Conversi&oacute;n</h1>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded">#{{ $orden->id }}</span>
                    </div>
                    <p class="text-sm text-gray-500 ml-7">
                        {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }} &mdash;
                        <span class="font-semibold text-gray-700">{{ $orden->vehiculo->placa }}</span>
                    </p>
                </div>
                <div>
                    @if($orden->fecha_fin_conversion)
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Completada</span>
                    @elseif($orden->fecha_inicio_conversion)
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">En conversi&oacute;n</span>
                    @else
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Pendiente</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido --}}
    <div class="max-w-7xl mx-auto px-6 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- IZQUIERDA --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Kit --}}
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-box text-gray-400 text-sm"></i>
                            <h3 class="text-sm font-bold text-gray-900">Kit GNV</h3>
                            @if($this->generacionKit)
                                <span class="px-2.5 py-1 bg-purple-600 text-white text-xs font-bold rounded-lg">{{ $this->generacionKit }}</span>
                            @endif
                        </div>
                        @if($this->kitItems->isNotEmpty())
                            <button wire:click="abrirPartesGenerales" type="button"
                                    class="text-xs text-gray-500 hover:text-gray-900 transition">
                                <i class="fas fa-eye mr-1"></i> Ver componentes
                            </button>
                        @endif
                    </div>
                    <div class="p-4">
                        @if($this->kitItems->isNotEmpty())
                            <div class="space-y-2">
                                @foreach($this->kitItems as $item)
                                    <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg">
                                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-microchip text-gray-500 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-medium text-gray-900 block truncate">{{ $item->producto->nombre }}</span>
                                            <span class="text-xs text-gray-400 font-mono">{{ $item->serie ?? '—' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 text-center py-2">No se encontr&oacute; kit asignado</p>
                        @endif
                    </div>
                </div>

                {{-- Piezas despachadas por almacén --}}
                @php
                    $reemplazos = \App\Models\ReportePiezaNoEncajada::where('service_order_id', $orden->id)
                        ->whereIn('estado', ['kit_abierto', 'resuelto'])
                        ->with('itemNoEncajado.producto')
                        ->get();
                @endphp
                @if($reemplazos->isNotEmpty())
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                            <i class="fas fa-exchange-alt text-green-500 text-sm"></i>
                            <h3 class="text-sm font-bold text-gray-900">Piezas despachadas</h3>
                            <span class="ml-auto px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">{{ $reemplazos->count() }}</span>
                        </div>
                        <div class="p-4">
                            <div class="space-y-2">
                                @foreach($reemplazos as $repo)
                                    <div class="flex items-center gap-3 p-3 border border-green-200 bg-green-50 rounded-lg">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-check text-green-500 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm font-medium text-gray-900 block">{{ $repo->itemNoEncajado->producto->nombre ?? 'Pieza' }}</span>
                                            <span class="text-xs text-gray-500 block">
                                                @if(!empty($repo->itemNoEncajado->serie)) Serie: {{ $repo->itemNoEncajado->serie }} &mdash; @endif {{ $repo->motivo_no_encaja }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-gray-400 shrink-0">{{ $repo->created_at->format('d/m H:i') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Botón principal --}}
                @if($orden->estado === 'en_conversion')
                    @if(!$orden->fecha_inicio_conversion)
                        <button wire:click="iniciar" wire:loading.attr="disabled" type="button"
                                class="w-full flex items-center justify-center gap-2 px-5 py-4 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition font-semibold text-sm shadow-lg">
                            <i class="fas fa-play"></i> Iniciar conversi&oacute;n
                        </button>
                    @elseif($this->puedeFinalizar)
                        <button wire:click="finalizar" wire:loading.attr="disabled" type="button"
                                class="w-full flex items-center justify-center gap-2 px-5 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-semibold text-sm shadow-lg">
                            <i class="fas fa-check-circle"></i> Finalizar conversi&oacute;n
                        </button>
                    @else
                        <div class="w-full flex items-center justify-center gap-2 px-5 py-4 bg-gray-200 text-gray-500 rounded-lg font-semibold text-sm cursor-not-allowed">
                            <i class="fas fa-lock"></i> Esperando piezas del almac&eacute;n
                        </div>
                    @endif
                @endif
            </div>

            {{-- DERECHA --}}
            <div class="space-y-6">
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400 text-sm"></i>
                        <h3 class="text-sm font-bold text-gray-900">Proceso</h3>
                    </div>
                    <div class="p-5 space-y-4">
                        {{-- Inicio --}}
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 {{ $orden->fecha_inicio_conversion ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                <i class="fas fa-play text-xs {{ $orden->fecha_inicio_conversion ? 'text-green-600' : 'text-gray-400' }}"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-900 block">Inicio</span>
                                <span class="text-xs text-gray-500">{{ $orden->fecha_inicio_conversion ? $orden->fecha_inicio_conversion->format('d/m H:i') : 'Pendiente' }}</span>
                            </div>
                        </div>

                        @php
                            $devoluciones = \App\Models\ReportePiezaNoEncajada::where('service_order_id', $orden->id)
                                ->with('itemNoEncajado.producto')->orderBy('created_at')->get();
                        @endphp

                        @if($devoluciones->isNotEmpty())
                            <div class="border-l-2 border-gray-200 ml-4 space-y-3">
                                @foreach($devoluciones as $repo)
                                    @php $ok = in_array($repo->estado, ['kit_abierto', 'resuelto']); @endphp
                                    <div class="relative pl-4">
                                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full {{ $ok ? 'bg-green-400' : 'bg-amber-400' }} border-2 border-white"></div>
                                        <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200">
                                            <p class="text-xs font-semibold text-gray-800">{{ $repo->itemNoEncajado->producto->nombre ?? 'Pieza' }}</p>
                                            <p class="text-[11px] text-gray-500">{{ $repo->created_at->diffForHumans() }} &mdash; {{ $ok ? 'Despachado' : 'Esperando' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="border-l-2 border-gray-200 ml-4 h-4"></div>

                        {{-- Fin --}}
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 {{ $orden->fecha_fin_conversion ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                <i class="fas fa-flag-checkered text-xs {{ $orden->fecha_fin_conversion ? 'text-green-600' : 'text-gray-400' }}"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-900 block">Finalizaci&oacute;n</span>
                                <span class="text-xs text-gray-500">{{ $orden->fecha_fin_conversion ? $orden->fecha_fin_conversion->format('d/m H:i') : 'Pendiente' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($orden->estado === 'conversion_completada')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                        <p class="text-sm font-semibold text-green-800">Completada</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL: COMPONENTES --}}
    @if($modalPartesAbierto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" wire:click="cerrarPartesGenerales"></div>
            <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-sm">Componentes del kit</h3>
                    <button wire:click="cerrarPartesGenerales" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="px-5 py-4 max-h-[28rem] overflow-y-auto">
                    @if($this->todasPiezasKit->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-4">No hay componentes</p>
                    @else
                        <div class="space-y-1.5">
                            @foreach($this->todasPiezasKit as $comp)
                                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-gray-50">
                                    <span class="text-sm font-medium text-gray-900">{{ $comp->nombre }}</span>
                                    <div class="flex items-center gap-2">
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
                <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex justify-end">
                    <button wire:click="cerrarPartesGenerales" type="button"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-xs">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
