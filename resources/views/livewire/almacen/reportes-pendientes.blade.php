<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">

        <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-tools mr-2 text-amber-600"></i>
                    Solicitudes de Piezas Pendientes
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Piezas que los técnicos reportaron como "no calza" y necesitan reemplazo
                </p>
            </div>
            <button wire:click="abrirHistorial" type="button"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <i class="fas fa-history text-xs"></i> Historial
            </button>
        </div>

        <div class="p-6">
            @if(empty($reportes))
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium">No hay solicitudes pendientes</p>
                    <p class="text-sm text-gray-400 mt-1">Las piezas reportadas como "no calza" aparecerán aquí</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($reportes as $reporte)
                        @php
                            $esCantidad = str_starts_with($reporte['item_no_encajado']['serie'] ?? '', 'CANT-')
                                || ($reporte['item_no_encajado']['atributos']['tipo'] ?? '') === 'cantidad';
                            $esPendiente = in_array($reporte['estado'], ['pendiente', 'buscando_pieza', 'solicitando_almacen']);
                            $esUrgente = $esPendiente && !$esCantidad;
                        @endphp
                        <div class="border rounded-xl overflow-hidden {{ $esUrgente ? 'border-red-300 bg-red-50/30' : ($esPendiente ? 'border-amber-300 bg-amber-50/30' : 'border-gray-200') }} {{ $reporteSeleccionadoId === $reporte['id'] ? 'ring-2 ring-amber-500' : '' }}">

                            <div class="p-4 {{ $esUrgente ? 'bg-red-50' : ($esPendiente ? 'bg-amber-50' : 'bg-gray-50') }} cursor-pointer hover:bg-gray-100 transition"
                                 wire:click="seleccionarReporte({{ $reporte['id'] }})">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full {{ $esUrgente ? 'bg-red-100' : ($esPendiente ? 'bg-amber-100' : 'bg-green-100') }} flex items-center justify-center">
                                            <i class="fas fa-{{ $esUrgente ? 'exclamation-triangle text-red-600' : ($esPendiente ? 'clock text-amber-600' : 'check-circle text-green-600') }}"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">
                                                {{ $reporte['item_no_encajado']['producto']['nombre'] ?? 'N/A' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Orden #{{ $reporte['service_order_id'] }} —
                                                {{ $reporte['service_order']['vehiculo']['placa'] ?? 'N/A' }} —
                                                Técnico: {{ $reporte['tecnico']['name'] ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            {{ match(true) {
                                                $esUrgente => 'bg-red-100 text-red-700',
                                                $esPendiente => 'bg-amber-100 text-amber-700',
                                                $reporte['estado'] === 'kit_abierto' => 'bg-green-100 text-green-700',
                                                default => 'bg-gray-100 text-gray-700',
                                            } }}">
                                            {{ match(true) {
                                                $esUrgente => 'Urgente',
                                                $esPendiente => 'Pendiente',
                                                $reporte['estado'] === 'kit_abierto' => 'Despachado',
                                                default => ucfirst(str_replace('_', ' ', $reporte['estado'])),
                                            } }}
                                        </span>
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

                            @if($reporteSeleccionadoId === $reporte['id'] && $reporteSeleccionado)
                                <div class="p-4 border-t border-gray-200 bg-white space-y-4">

                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 text-xs uppercase tracking-wider">Pieza que no calza</p>
                                            <p class="font-semibold text-gray-800">
                                                {{ $reporteSeleccionado->itemNoEncajado->producto->nombre }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 text-xs uppercase tracking-wider">Motivo</p>
                                            <p class="text-gray-700">
                                                {{ $reporteSeleccionado->motivo_no_encaja ?? 'No especificado' }}
                                            </p>
                                        </div>
                                    </div>

                                    @php
                                        $sedeId = 1;
                                        $productoNecesarioId = $reporteSeleccionado->itemNoEncajado->producto_id;
                                        // Search for the specific piece needed
                                        $piezaNecesaria = \App\Models\ItemSerializado::with('producto.categoria')
                                            ->where('producto_id', $productoNecesarioId)
                                            ->where('estado', 'en_stock')
                                            ->where('sede_id', $sedeId)
                                            ->whereNull('service_order_id')
                                            ->where('id', '!=', $reporteSeleccionado->item_no_encajado_id)
                                            ->first();
                                    @endphp

                                    {{-- Pieza específica necesaria --}}
                                    @if($piezaNecesaria)
                                        @php $esSer = $piezaNecesaria->producto->categoria->es_serializado ?? false; @endphp
                                        <div class="rounded-lg p-3 {{ $esSer ? 'bg-green-50 border border-green-200' : 'bg-blue-50 border border-blue-200' }}">
                                            <p class="text-xs font-bold uppercase tracking-wider mb-2 {{ $esSer ? 'text-green-700' : 'text-blue-700' }}">
                                                <i class="fas fa-check-circle mr-1"></i> Pieza disponible en almacén
                                            </p>
                                            <div class="flex items-center justify-between bg-white p-3 rounded-lg border {{ $esSer ? 'border-green-200' : 'border-blue-200' }}">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $esSer ? 'bg-green-100' : 'bg-blue-100' }}">
                                                        <i class="fas {{ $esSer ? 'fa-barcode text-green-600' : 'fa-cubes text-blue-600' }}"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-800">{{ $piezaNecesaria->producto->nombre }}</p>
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $esSer ? 'bg-green-200 text-green-800' : 'bg-blue-200 text-blue-800' }}">
                                                            {{ $esSer ? 'Serializada' : 'Por cantidad' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <button wire:click="asignarPiezaSuelta({{ $piezaNecesaria->id }})"
                                                        wire:loading.attr="disabled"
                                                        class="px-4 py-2 bg-green-600 text-white rounded-lg text-xs font-semibold hover:bg-green-700 transition">
                                                    Asignar
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Kits disponibles --}}
                                    @if(!empty($kitsDisponibles))
                                        @php $kit = $kitsDisponibles[0]; @endphp
                                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                                            <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-2">
                                                <i class="fas fa-box-open mr-1"></i> No hay pieza suelta — Abrir kit
                                            </p>
                                            <div class="bg-white p-3 rounded-lg border border-amber-200">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="font-semibold text-gray-800 text-sm">{{ $kit['producto'] }}</p>
                                                        <p class="text-xs text-gray-500">Caja #{{ $kit['id'] }}</p>
                                                    </div>
                                                    <button wire:click="abrirModalKit" type="button"
                                                            class="px-4 py-2 bg-amber-500 text-white rounded-lg text-xs font-semibold hover:bg-amber-600 transition">
                                                        <i class="fas fa-box-open mr-1"></i>Abrir kit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mx-auto mb-2">
                                                <i class="fas fa-box-open text-gray-400 text-sm"></i>
                                            </div>
                                            <p class="text-sm font-medium text-gray-600">No hay kits disponibles</p>
                                            <p class="text-xs text-gray-400 mt-0.5">No se encontraron kits con esta pieza en stock</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($modalAbrirKit && $reporteSeleccionado)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarModalKit"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-lg max-h-[90vh] overflow-hidden flex flex-col">

                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                <i class="fas fa-box-open mr-2 text-blue-600"></i>Abrir kit
                            </h3>
                            <p class="text-sm text-gray-500 mt-0.5">
                                Seleccioná el kit y la pieza que necesitás
                            </p>
                        </div>
                        <button wire:click="cerrarModalKit" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 flex-1 overflow-y-auto">
                    @if(!empty($kitsDisponibles))
                        <div class="space-y-3">
                            @foreach($kitsDisponibles as $kit)
                                <div wire:click="seleccionarKit({{ $kit['id'] }})"
                                     class="p-4 rounded-xl border-2 cursor-pointer transition-all
                                            {{ $kitSeleccionadoId === $kit['id']
                                               ? 'border-blue-500 bg-blue-50 shadow-md'
                                               : 'border-gray-200 hover:border-blue-300 bg-white' }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $kit['producto'] }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ $kit['serie'] }}</p>
                                            <p class="text-xs text-blue-600 mt-0.5">
                                                <i class="fas fa-map-marker-alt mr-1"></i>{{ \App\Models\Sede::find($kit['sede_id'])->nombre ?? 'N/A' }}
                                            </p>
                                        </div>
                                        @if($kitSeleccionadoId === $kit['id'])
                                            <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 rounded-full border-2 border-gray-300"></div>
                                        @endif
                                    </div>

                                    <div class="space-y-1">
                                        @foreach($kit['componentes'] as $comp)
                                            @if($comp['es_necesaria'])
                                                <div class="flex items-center gap-2 text-sm border-2 rounded-lg px-3 py-2
                                                            border-amber-400 bg-amber-50 text-amber-900 font-semibold"
                                                     onclick="event.stopPropagation()">
                                                    <i class="fas fa-star text-amber-500 text-xs"></i>
                                                    <span class="select-none">{{ $comp['nombre'] }}</span>
                                                    <span class="ml-auto text-xs text-amber-700 font-bold bg-amber-200 px-2 py-0.5 rounded-full">Solicitada</span>
                                                </div>
                                            @else
                                                <div class="flex items-center gap-2 text-sm border border-gray-200 rounded-lg px-3 py-1.5
                                                            bg-gray-50 text-gray-600"
                                                     onclick="event.stopPropagation()">
                                                    <i class="fas fa-cube text-gray-400 text-xs"></i>
                                                    <span class="select-none">{{ $comp['nombre'] }}</span>
                                                    @if($comp['cantidad'] > 1)
                                                        <span class="ml-auto text-xs text-gray-400">x{{ $comp['cantidad'] }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    @if($kitSeleccionadoId === $kit['id'])
                                        <div class="mt-3 text-xs text-blue-600 bg-blue-100 rounded-lg px-3 py-2 text-center font-medium">
                                            <i class="fas fa-info-circle mr-1"></i>Al abrir, la pieza solicitada se asigna y el resto queda como pieza suelta
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-600 font-medium">No hay kits disponibles</p>
                            <p class="text-sm text-gray-400 mt-1">No se encontraron kits que contengan esta pieza</p>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex gap-3">
                    <button wire:click="cerrarModalKit" type="button"
                            class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition font-medium text-sm">
                        Cancelar
                    </button>

                    @if($kitSeleccionadoId)
                        <button wire:click="confirmarAperturaKit"
                                wire:loading.attr="disabled"
                                type="button"
                                class="flex-1 px-4 py-2.5 bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition font-semibold text-sm shadow-md">
                            <i class="fas fa-box-open mr-1"></i> Abrir kit
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

@if($modalHistorial)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarHistorial"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">

            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-2xl flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Historial de movimientos</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Movimientos de stock agrupados por fecha y orden de conversión</p>
                </div>
                <button wire:click="cerrarHistorial" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="px-6 py-4 flex-1 overflow-y-auto">
                @if(empty($historialAgrupado))
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-600 font-medium">No hay movimientos registrados</p>
                        <p class="text-sm text-gray-400 mt-1">Los movimientos de stock aparecerán aquí cuando se realicen</p>
                    </div>
                @else
                    @foreach($historialAgrupado as $fecha => $ordenes)
                        <div class="sticky top-0 z-10 bg-white py-2 mt-4 first:mt-0">
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 bg-gray-800 text-white text-xs font-bold rounded-full">
                                    {{ \Carbon\Carbon::parse($fecha)->format('d \d\e F \d\e Y') }}
                                </span>
                                <span class="text-xs text-gray-400">{{ count($ordenes) }} orden{{ count($ordenes) !== 1 ? 'es' : '' }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @foreach($ordenes as $groupKey => $data)
                                @php
                                    $orden = $data['orden'];
                                    $movimientos = $data['movimientos'];
                                    $pieza = $data['pieza'];
                                    $esKit = ($pieza->categoria->es_kit ?? false);
                                    $totalEntrada = collect($movimientos)->where('tipo', 'entrada')->sum('cantidad');
                                    $totalSalida = collect($movimientos)->where('tipo', 'salida')->sum('cantidad');
                                    $esSinOrden = ($orden === null);
                                @endphp
                                <div class="border border-gray-200 rounded-xl overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <div class="flex items-center gap-3">
                                                @if($esSinOrden)
                                                    <span class="font-bold text-gray-900">Movimientos sin orden</span>
                                                @else
                                                    <span class="font-bold text-gray-900">Orden #{{ $orden->id }}</span>
                                                @endif
                                                @if($esKit)
                                                    <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-bold rounded">KIT</span>
                                                @else
                                                    <span class="px-1.5 py-0.5 bg-orange-100 text-orange-700 text-[10px] font-bold rounded">CANT.</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                                @if(!$esSinOrden)
                                                    <span><i class="fas fa-car mr-1"></i>{{ $orden->vehiculo->placa ?? 'N/A' }}</span>
                                                    <span><i class="fas fa-user mr-1"></i>{{ $orden->cliente->nombre ?? 'N/A' }}</span>
                                                    <span><i class="fas fa-wrench mr-1"></i>{{ $orden->tecnico->name ?? 'N/A' }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if(!$esSinOrden)
                                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                            <span><i class="fas fa-cog mr-1"></i>{{ $orden->service->nombre ?? 'N/A' }}</span>
                                            @if($orden->vehiculo)
                                                <span>{{ $orden->vehiculo->marca ?? '' }} {{ $orden->vehiculo->modelo ?? '' }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    <div class="px-4 py-2 border-b border-gray-100 flex items-center gap-4 text-xs">
                                        @if($totalSalida > 0)
                                            <span class="text-red-600 font-bold"><i class="fas fa-arrow-up mr-1"></i>{{ $totalSalida }} salida{{ $totalSalida !== 1 ? 's' : '' }}</span>
                                        @endif
                                        @if($totalEntrada > 0)
                                            <span class="text-green-600 font-bold"><i class="fas fa-arrow-down mr-1"></i>{{ $totalEntrada }} entrada{{ $totalEntrada !== 1 ? 's' : '' }}</span>
                                        @endif
                                    </div>

                                    <div class="divide-y divide-gray-100">
                                        @foreach($movimientos as $mov)
                                            @php
                                                $esEntrada = $mov->tipo === 'entrada';
                                            @endphp
                                            <div class="flex items-start gap-3 p-3 hover:bg-gray-50 transition">
                                                <div class="w-8 h-8 rounded-full {{ $esEntrada ? 'bg-green-100' : 'bg-red-100' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <i class="fas fa-{{ $esEntrada ? 'arrow-down text-green-600' : 'arrow-up text-red-600' }} text-xs"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="font-semibold text-sm text-gray-900">{{ $mov->producto->nombre ?? 'N/A' }}</span>
                                                        <span class="px-1.5 py-0.5 text-[10px] font-bold rounded {{ $esEntrada ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                            {{ $esEntrada ? 'ENTRADA' : 'SALIDA' }}
                                                        </span>
                                                        <span class="text-xs font-bold text-gray-700">x{{ $mov->cantidad }}</span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $mov->motivo ?? 'Sin motivo' }}</p>
                                                    <div class="flex items-center gap-3 mt-1 text-[11px] text-gray-400">
                                                        @if($mov->usuario)
                                                            <span><i class="fas fa-user mr-1"></i>{{ $mov->usuario->name }}</span>
                                                        @endif
                                                        <span><i class="fas fa-clock mr-1"></i>{{ $mov->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl flex justify-end">
                <button wire:click="cerrarHistorial" type="button"
                        class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-100 transition font-medium text-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
@endif
</div>