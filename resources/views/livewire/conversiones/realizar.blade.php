<div wire:loading.class="opacity-50 pointer-events-none" class="min-h-screen bg-gray-50">

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <a href="{{ route('conversiones.mis-asignadas') }}"
                           class="text-gray-400 hover:text-gray-700 transition">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h1 class="text-xl font-bold text-gray-900">Realizar conversi&oacute;n</h1>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded">Orden #{{ $orden->id }}</span>
                    </div>
                    <p class="text-sm text-gray-500 ml-7">
                        {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }} &mdash;
                        <span class="font-semibold text-gray-700">{{ $orden->vehiculo->placa }}</span> &mdash;
                        {{ $orden->service->nombre }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if($orden->fecha_inicio_conversion)
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                            <i class="fas fa-circle text-[6px] mr-1"></i> En conversi&oacute;n
                        </span>
                    @else
                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                            <i class="fas fa-circle text-[6px] mr-1"></i> Pendiente
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-6 py-6">
        <x-input-error for="general" />

        @if(!$this->itemsRegistrados && !$orden->fecha_inicio_conversion)
            {{-- Aviso: No registr&oacute; items --}}
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list text-amber-600 text-2xl"></i>
                </div>
                <p class="text-lg font-bold text-gray-900">Primero registre los items del kit</p>
                <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto">
                    Antes de iniciar la conversi&oacute;n, registre los n&uacute;meros de serie de las piezas que va a instalar.
                </p>
                <a href="{{ route('conversiones.registrar-items-kit', $orden->id) }}"
                   class="inline-flex items-center gap-2 mt-6 px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition font-semibold text-sm">
                    <i class="fas fa-clipboard-list"></i> Registrar Items del Kit
                </a>
            </div>

        @else
            {{-- Flujo Normal --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">

                    {{-- Solicitudes pendientes --}}
                    @if($this->esperandoAlmacen->isNotEmpty())
                        <div class="bg-white rounded-lg border border-gray-200">
                            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                                <i class="fas fa-clock text-amber-500 text-sm"></i>
                                <h3 class="text-sm font-bold text-gray-900">Esperando respuesta del almac&eacute;n</h3>
                                <span class="ml-auto px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded">
                                    {{ $this->esperandoAlmacen->count() }}
                                </span>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @foreach($this->esperandoAlmacen as $reporte)
                                    <div class="px-5 py-3 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-exclamation-triangle text-amber-600 text-xs"></i>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">{{ $reporte->itemNoEncajado->producto->nombre }}</span>
                                                <span class="text-xs text-gray-400 block">{{ $reporte->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-full border border-amber-200">
                                            Pendiente
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Kit asignado --}}
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                            <i class="fas fa-box text-gray-400 text-sm"></i>
                            <h3 class="text-sm font-bold text-gray-900">Kit GNV</h3>
                            @if($this->generacionKit)
                                <span class="px-2.5 py-1 bg-purple-600 text-white text-xs font-bold rounded-lg">
                                    {{ $this->generacionKit }}
                                </span>
                            @endif
                            @if($this->kitItems->isNotEmpty())
                                <span class="ml-auto px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded">
                                    {{ $this->todasPiezasKit->count() }} componentes
                                </span>
                            @endif
                        </div>
                        <div class="p-4">
                            @if($this->kitItems->isNotEmpty())
                                <div class="space-y-2 mb-3">
                                    @foreach($this->kitItems as $item)
                                        @php
                                            $reportado = in_array($item->id, $this->itemsReportados);
                                            $reemplazado = in_array($item->id, $this->itemsReemplazados);
                                        @endphp
                                        <div class="w-full border rounded-lg p-3 {{ $reportado ? 'border-red-300 bg-red-50' : ($reemplazado ? 'border-blue-300 bg-blue-50' : 'border-gray-200') }}">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 {{ $reportado ? 'bg-red-100' : ($reemplazado ? 'bg-blue-100' : 'bg-gray-100') }} rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-microchip {{ $reportado ? 'text-red-500' : ($reemplazado ? 'text-blue-500' : 'text-gray-500') }} text-xs"></i>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium {{ $reportado ? 'text-red-800' : ($reemplazado ? 'text-blue-800' : 'text-gray-900') }} block">{{ $item->producto->nombre }}</span>
                                                    <span class="text-xs {{ $reportado ? 'text-red-400' : ($reemplazado ? 'text-blue-400' : 'text-gray-400') }} font-mono">Serie: {{ $item->serie }}</span>
                                                </div>
                                                @if($reportado)
                                                    <span class="px-1.5 py-0.5 bg-red-100 text-red-700 text-[10px] font-semibold rounded ml-1">Reportado</span>
                                                @elseif($reemplazado)
                                                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-semibold rounded ml-1">Reemplazado</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button wire:click="abrirPartesGenerales" type="button"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition font-medium text-sm">
                                    <i class="fas fa-eye"></i> Ver todos los componentes
                                </button>
                            @else
                                <p class="text-sm text-gray-400 text-center py-2">No se encontr&oacute; kit asignado</p>
                            @endif
                        </div>
                    </div>

                    {{-- Piezas solicitadas --}}
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                            <i class="fas fa-clipboard-list text-gray-400 text-sm"></i>
                            <h3 class="text-sm font-bold text-gray-900">Piezas solicitadas</h3>
                            @if($this->reportesPendientes->isNotEmpty())
                                <span class="ml-auto px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded">
                                    {{ $this->reportesPendientes->count() }}
                                </span>
                            @endif
                        </div>
                        <div class="p-4">
                            @if($this->reportesPendientes->isEmpty())
                                <p class="text-sm text-gray-400 text-center py-2">A&uacute;n no hay solicitudes pendientes</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($this->reportesPendientes as $reporte)
                                        @php
                                            $esCantidad = str_starts_with($reporte->itemNoEncajado->serie ?? '', 'CANT-')
                                                || ($reporte->itemNoEncajado->atributos['tipo'] ?? '') === 'cantidad';
                                            $esExtraidaDeKit = isset($reporte->itemNoEncajado->atributos['extraida_de_kit']);
                                            $esSerial = !$esCantidad && !$esExtraidaDeKit;
                                            $estadoInfo = match($reporte->estado) {
                                                'kit_abierto' => ['label' => 'Asignado', 'bg' => 'bg-green-100 text-green-700', 'border' => 'border-green-200', 'icon_bg' => 'bg-green-100', 'icon_color' => 'text-green-600'],
                                                'resuelto' => ['label' => 'Instalado', 'bg' => 'bg-emerald-100 text-emerald-700', 'border' => 'border-emerald-200', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-600'],
                                                'buscando_pieza' => ['label' => 'Buscando', 'bg' => 'bg-blue-100 text-blue-700', 'border' => 'border-blue-200', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-600'],
                                                default => ['label' => 'Pendiente', 'bg' => 'bg-amber-100 text-amber-700', 'border' => 'border-amber-200', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-600'],
                                            };
                                        @endphp
                                        <div class="flex items-center justify-between p-3 {{ $estadoInfo['border'] }} border rounded-lg">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 {{ $estadoInfo['icon_bg'] }} rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-{{ $reporte->estado === 'resuelto' ? 'check-double' : ($reporte->estado === 'kit_abierto' ? 'check-circle' : 'exclamation-triangle') }} {{ $estadoInfo['icon_color'] }} text-xs"></i>
                                                </div>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-900">{{ $reporte->itemNoEncajado->producto->nombre }}</span>
                                                    <span class="text-xs text-gray-500 block">{{ $reporte->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if($reporte->estado === 'kit_abierto' && $esSerial)
                                                    <button wire:click="abrirConfirmarInstalacion({{ $reporte->id }})" type="button"
                                                            class="px-2 py-1 text-xs font-medium text-green-600 hover:text-green-800 hover:bg-green-50 rounded transition">
                                                        <i class="fas fa-check mr-1"></i>Confirmar
                                                    </button>
                                                @elseif($reporte->estado === 'kit_abierto')
                                                    <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-50 rounded">
                                                        <i class="fas fa-check mr-1"></i>Despachado
                                                    </span>
                                                @elseif($reporte->estado === 'resuelto')
                                                    <span class="px-2 py-1 text-xs font-medium text-emerald-700 bg-emerald-50 rounded">
                                                        <i class="fas fa-check-double mr-1"></i>Instalado
                                                    </span>
                                                @else
                                                    <button wire:click="abrirPartesGenerales({{ $reporte->itemNoEncajado->producto_id }})" type="button"
                                                            class="px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition">
                                                        Ver kit
                                                    </button>
                                                @endif
                                                <span class="px-2 py-0.5 {{ $estadoInfo['bg'] }} text-xs font-semibold rounded-full">
                                                    {{ $estadoInfo['label'] }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>



                    {{-- Bot&oacute;n reportar --}}
                    @if($orden->fecha_inicio_conversion)
                        <div class="grid grid-cols-2 gap-3">
                            @if($this->kitItems->isNotEmpty())
                                <button wire:click="abrirModalSerial"
                                        type="button"
                                        class="flex items-center justify-center gap-2 px-5 py-4 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-semibold text-sm">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Reportar pieza serial
                                </button>
                            @endif
                            @if($this->itemsCantidad->isNotEmpty())
                                <button wire:click="abrirModalCantidad"
                                        type="button"
                                        class="flex items-center justify-center gap-2 px-5 py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition font-semibold text-sm">
                                    <i class="fas fa-box"></i>
                                    Reportar pieza por cantidad
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Panel Lateral --}}
                <div class="space-y-6">
                    {{-- Estado del Proceso --}}
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400 text-sm"></i>
                            <h3 class="text-sm font-bold text-gray-900">Estado del proceso</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            {{-- Inicio --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 {{ $orden->fecha_inicio_conversion ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                        <i class="fas fa-play text-xs {{ $orden->fecha_inicio_conversion ? 'text-green-600' : 'text-gray-400' }}"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 block">Inicio</span>
                                        @if($orden->fecha_inicio_conversion)
                                            <span class="text-xs text-gray-500">{{ $orden->fecha_inicio_conversion->format('d/m/Y H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if(!$orden->fecha_inicio_conversion)
                                    <button wire:click="iniciar" wire:loading.attr="disabled" type="button"
                                            class="px-3 py-1.5 bg-gray-900 text-white text-xs font-semibold rounded-lg hover:bg-gray-800 transition">
                                        Iniciar
                                    </button>
                                @else
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @endif
                            </div>

                            {{-- Timeline de reportes --}}
                            @php
                                $todosReportes = \App\Models\ReportePiezaNoEncajada::where('service_order_id', $orden->id)
                                    ->with('itemNoEncajado.producto')
                                    ->orderBy('created_at')
                                    ->get();
                            @endphp

                            @if($todosReportes->isNotEmpty())
                                <div class="border-l-2 border-gray-200 ml-4 space-y-3">
                                    @foreach($todosReportes as $repo)
                                        @php
                                            $solicitado = $repo->created_at;
                                            $despachado = in_array($repo->estado, ['kit_abierto', 'resuelto']) ? $repo->updated_at : null;
                                            $nombrePieza = $repo->itemNoEncajado->producto->nombre ?? 'Pieza';
                                        @endphp
                                        <div class="relative pl-4">
                                            <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full {{ $despachado ? 'bg-green-400' : 'bg-amber-400' }} border-2 border-white"></div>
                                            <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200">
                                                <p class="text-xs font-semibold text-gray-800">{{ $nombrePieza }}</p>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <span class="text-[11px] text-gray-500">
                                                        <i class="fas fa-paper-plane mr-1 text-amber-500"></i>Solicitó: {{ $solicitado->format('d/m H:i') }}
                                                    </span>
                                                    @if($despachado)
                                                        <span class="text-[11px] text-green-600 font-medium">
                                                            <i class="fas fa-check mr-1"></i>Despachó: {{ $despachado->format('d/m H:i') }}
                                                        </span>
                                                    @else
                                                        <span class="text-[11px] text-amber-500">
                                                            <i class="fas fa-clock mr-1"></i>Esperando almacén
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="border-l-2 border-gray-200 ml-4 h-4"></div>

                            {{-- Fin --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 {{ $orden->fecha_fin_conversion ? 'bg-green-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                                        <i class="fas fa-flag-checkered text-xs {{ $orden->fecha_fin_conversion ? 'text-green-600' : 'text-gray-400' }}"></i>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 block">Finalizaci&oacute;n</span>
                                        @if($orden->fecha_fin_conversion)
                                            <span class="text-xs text-gray-500">{{ $orden->fecha_fin_conversion->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">Pendiente</span>
                                        @endif
                                    </div>
                                </div>
                                @if($orden->fecha_fin_conversion)
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="p-5">
                            @if($orden->estado === 'en_conversion' && $orden->fecha_inicio_conversion)
                                @if($this->puedeFinalizar)
                                    <button wire:click="finalizar" wire:loading.attr="disabled" type="button"
                                            class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition text-sm flex items-center justify-center gap-2">
                                        <i class="fas fa-check-circle"></i> Finalizar conversi&oacute;n
                                    </button>
                                @else
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                                        <div class="flex items-center gap-2 mb-3">
                                            <i class="fas fa-lock text-amber-500"></i>
                                            <p class="text-sm font-semibold text-amber-800">No se puede finalizar</p>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($this->pendientes as $pendiente)
                                                <div class="flex items-start gap-2 text-xs">
                                                    <div class="w-5 h-5 bg-{{ $pendiente['color'] }}-100 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        <i class="fas fa-{{ $pendiente['icono'] }} text-{{ $pendiente['color'] }}-600 text-[10px]"></i>
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-800">{{ $pendiente['texto'] }}</span>
                                                        <span class="text-gray-500"> — {{ $pendiente['detalle'] }}</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @elseif($orden->estado === 'conversion_completada')
                                <div class="text-center py-3 bg-green-50 border border-green-200 rounded-lg">
                                    <i class="fas fa-check-circle text-green-600 mb-1"></i>
                                    <p class="text-sm font-semibold text-green-800">Conversi&oacute;n completada</p>
                                    <p class="text-xs text-green-600">Pendiente entrega y cobro</p>
                                </div>
                            @else
                                <div class="text-center py-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <p class="text-sm text-gray-500">Inicie la conversi&oacute;n para continuar</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Reportar pieza serial --}}
    @if($modalAbierto && $modalTipo === 'serial')
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" wire:click="cerrarModal"></div>
            <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Reportar pieza que no se puede instalar</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Seleccione la pieza y agregue una observaci&oacute;n</p>
                    </div>
                    <button wire:click="cerrarModal" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <div class="space-y-2 mb-4">
                        @foreach($this->kitItems as $item)
                            <div wire:click="seleccionarKitItem({{ $item->id }})"
                                 class="cursor-pointer border rounded-lg p-3 transition {{ $kitItemSeleccionado === $item->id ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-microchip text-gray-500 text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-900">{{ $item->producto->nombre }}</span>
                                        <span class="text-xs text-gray-400 font-mono block">{{ $item->serie }}</span>
                                    </div>
                                    @if($kitItemSeleccionado === $item->id)
                                        <i class="fas fa-check-circle text-red-500"></i>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Observaci&oacute;n *</label>
                        <textarea wire:model.live="motivoNoCalza"
                                  placeholder="Describe por qu&eacute; no se puede instalar..."
                                  rows="2"
                                  class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-gray-900 outline-none resize-none"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                    <button wire:click="cerrarModal" type="button"
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-sm">
                        Cancelar
                    </button>
                    @if($kitItemSeleccionado && !empty($motivoNoCalza))
                        <button wire:click="reportarPiezaNoCalza" wire:loading.attr="disabled" type="button"
                                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-semibold text-sm flex items-center gap-2">
                            <i class="fas fa-paper-plane text-xs"></i>
                            Enviar solicitud al almac&eacute;n
                        </button>
                    @else
                        <div class="px-6 py-2.5 bg-gray-100 text-gray-400 rounded-lg font-medium text-sm">
                            Enviar solicitud
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Reportar pieza por cantidad --}}
    @if($modalAbierto && $modalTipo === 'cantidad')
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" wire:click="cerrarModal"></div>
            <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Reportar pieza que no se puede instalar</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Seleccione la pieza y agregue una observaci&oacute;n</p>
                    </div>
                    <button wire:click="cerrarModal" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <div class="mb-4">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Pieza *</label>
                        <select wire:model="productoCantidadId"
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-gray-900 outline-none">
                            <option value="">Seleccionar pieza...</option>
                            @foreach($this->itemsCantidad as $kc)
                                <option value="{{ $kc->producto_componente_id }}">
                                    {{ $kc->componente->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Observaci&oacute;n *</label>
                        <textarea wire:model.live="motivoNoCalza"
                                  placeholder="Describe por qu&eacute; no se puede instalar..."
                                  rows="2"
                                  class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-gray-900 outline-none resize-none"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                    <button wire:click="cerrarModal" type="button"
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-medium text-sm">
                        Cancelar
                    </button>
                    @if($productoCantidadId && !empty($motivoNoCalza))
                        <button wire:click="reportarPiezaNoCalza" wire:loading.attr="disabled" type="button"
                                class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition font-semibold text-sm flex items-center gap-2">
                            <i class="fas fa-paper-plane text-xs"></i>
                            Enviar solicitud al almac&eacute;n
                        </button>
                    @else
                        <div class="px-6 py-2.5 bg-gray-100 text-gray-400 rounded-lg font-medium text-sm">
                            Enviar solicitud
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Componentes del kit --}}
    @if($modalPartesAbierto)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" wire:click="cerrarPartesGenerales"></div>
            <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-cogs text-gray-400 text-sm"></i>
                        <h3 class="font-bold text-gray-900 text-sm">Componentes del kit</h3>
                        @if($this->generacionKit)
                            <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded">{{ $this->generacionKit }}</span>
                        @endif
                    </div>
                    <button wire:click="cerrarPartesGenerales" class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="px-5 py-4 max-h-[28rem] overflow-y-auto">
                    @if($this->todasPiezasKit->isEmpty())
                        <p class="text-sm text-gray-400 text-center py-4">No hay componentes definidos para este kit</p>
                    @else
                        <p class="text-xs text-gray-500 mb-3">{{ $this->todasPiezasKit->count() }} piezas &mdash; {{ $orden->vehiculo->placa }}</p>
                        <div class="space-y-1.5">
                            @foreach($this->todasPiezasKit as $comp)
                                @php
                                    $esSolicitado = $productoSolicitadoId && $comp->producto_id == $productoSolicitadoId;
                                @endphp
                                <div class="flex items-center justify-between py-2 px-3 rounded-lg {{ $esSolicitado ? 'bg-blue-50 border border-blue-300' : 'bg-gray-50' }}">
                                    <div class="flex items-center gap-2">
                                        @if($esSolicitado)
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        @endif
                                        <span class="text-sm font-medium {{ $esSolicitado ? 'text-blue-800' : 'text-gray-900' }}">{{ $comp->nombre }}</span>
                                        <span class="text-xs {{ $esSolicitado ? 'text-blue-500' : 'text-gray-400' }}">{{ $comp->categoria }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold {{ $esSolicitado ? 'text-blue-700' : 'text-gray-700' }}">x{{ $comp->cantidad_esperada }}</span>
                                        @if($esSolicitado)
                                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-semibold rounded">Solicitada</span>
                                        @elseif($comp->es_serializado)
                                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-semibold rounded">Serial</span>
                                        @else
                                            <span class="px-1.5 py-0.5 bg-gray-200 text-gray-600 text-[10px] font-semibold rounded">Cant.</span>
                                        @endif
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

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: CONFIRMAR INSTALACIÓN CON SERIAL --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($modalConfirmarInstalacion)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarConfirmarInstalacion"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-md overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Confirmar instalación</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Ingrese el serial de la pieza nueva</p>
                        </div>
                        <button wire:click="cerrarConfirmarInstalacion" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número de serie de la pieza nueva</label>
                        <input type="text" wire:model="nuevoSerie"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-mono"
                               placeholder="Ej: 31312313">
                        @error('serial')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        El serial debe coincidir con la pieza que instaló físicamente.
                    </p>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex gap-3">
                    <button wire:click="cerrarConfirmarInstalacion" type="button"
                            class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition font-medium text-sm">
                        Cancelar
                    </button>
                    <button wire:click="confirmarInstalacion"
                            wire:loading.attr="disabled"
                            type="button"
                            class="flex-1 px-4 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 transition font-semibold text-sm shadow-md disabled:opacity-50">
                        <i class="fas fa-check mr-1"></i> Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
