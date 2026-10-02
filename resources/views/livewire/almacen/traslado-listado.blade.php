<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6 font-sans">

    {{-- Encabezado --}}
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3">
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800 tracking-tight">Traslados a sedes</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Historial de envíos desde Arturo Motors</p>
                </div>
            </div>

            <a href="{{ route('almacen.traslados.crear') }}" wire:navigate
               class="inline-flex items-center gap-2 bg-indigo-500 px-5 py-3 rounded-lg text-white text-sm font-semibold hover:bg-indigo-600 transition shadow-sm shrink-0">
                <i class="fas fa-plus text-xs"></i>
                <span>Nuevo traslado</span>
            </a>
        </div>
    </div>

    {{-- Indicadores --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 hover:shadow-md transition">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total traslados</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-2 tabular-nums">{{ $totalTraslados }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 hover:shadow-md transition">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Últimos 30 días</p>
            <p class="text-3xl font-extrabold text-indigo-600 mt-2 tabular-nums">{{ $ultimos30 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 hover:shadow-md transition">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kits incompletos</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-2 tabular-nums">{{ $kitsIncompletos }}</p>
        </div>
    </div>

    {{-- Listado con accordion --}}
    <div class="space-y-3">
        @forelse ($traslados as $t)
            @php $abierto = $trasladoExpandido === $t->id; @endphp
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden transition duration-200 hover:shadow-md">

                {{-- Fila principal (click para expandir) --}}
                <div wire:click="toggleDetalle({{ $t->id }})"
                     @class([
                         'p-5 cursor-pointer flex flex-wrap justify-between items-center gap-x-3 gap-y-3',
                         'bg-gray-50' => $abierto,
                     ])
                     style="user-select: none;">

                    <div class="flex flex-wrap items-center gap-3 min-w-0 flex-1">
                        <span class="px-2 py-0.5 text-[10px] font-bold text-gray-500 bg-gray-100 rounded-full shrink-0 tabular-nums">
                            N.° {{ $t->nro ?? $loop->iteration }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate">
                                <i class="fas fa-map-marker-alt text-indigo-400 mr-1"></i>{{ $t->sedeDestino->nombre }}
                            </p>
                            <p class="text-xs text-gray-500">
                                <i class="far fa-clock mr-1"></i>{{ $t->created_at->format('d/m/Y H:i') }}
                                <span class="mx-1">&bull;</span>
                                <i class="far fa-user mr-1"></i>{{ $t->enviadoPor->name }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if($t->es_kit_completo === true)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 shrink-0">
                                <i class="fas fa-check-circle mr-0.5"></i> COMPLETO
                            </span>
                        @elseif($t->es_kit_completo === false)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200 shrink-0">
                                <i class="fas fa-exclamation-triangle mr-0.5"></i> INCOMPLETO
                            </span>
                        @endif
                        <i @class([
                                'fas fa-chevron-down text-gray-400 text-sm transition-transform duration-200',
                                'rotate-180' => $abierto,
                            ])
                            aria-hidden="true"></i>
                    </div>
                </div>

                {{-- Contenido expandible: una sola class por tag (dos class = el navegador ignora la 2da) --}}
                <div @class([
                        'grid transition-[grid-template-rows] duration-200 ease-out bg-gray-50/50',
                        'grid-rows-[1fr]' => $abierto,
                        'grid-rows-[0fr]' => ! $abierto,
                    ])
                     @unless($abierto) inert @endunless>
                    <div class="overflow-hidden min-h-0">
                        <div class="p-5 pt-0 border-t border-gray-100">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm mb-3">
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Referencia</p>
                                    <p class="font-mono font-semibold text-indigo-600">#{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Sede destino</p>
                                    <p class="font-medium text-gray-700">{{ $t->sedeDestino->nombre }}</p>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fecha</p>
                                    <p class="font-medium text-gray-700">{{ $t->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Enviado por</p>
                                    <p class="font-medium text-gray-700">{{ $t->enviadoPor->name }}</p>
                                </div>
                            </div>

                            @php
                                $agrupados = $t->detalles->map(fn($d) => [
                                    'nombre' => $d->producto->nombre ?? 'Producto',
                                    'es_serializado' => (bool) $d->item_serializado_id,
                                    'cantidad' => $d->item_serializado_id ? 1 : ($d->cantidad ?? 0),
                                ])->groupBy('nombre')->map(fn($items, $nombre) => [
                                    'nombre' => $nombre,
                                    'es_serializado' => $items->first()['es_serializado'],
                                    'cantidad' => $items->sum('cantidad'),
                                ])->values();
                                $totalProductos = $t->detalles->groupBy(fn($d) => $d->producto->nombre ?? 'x')->count();
                            @endphp
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">
                                Contenido del traslado ({{ $totalProductos }} producto{{ $totalProductos !== 1 ? 's' : '' }})
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach ($agrupados as $g)
                                    <div class="flex items-center justify-between gap-2 text-xs bg-white px-3 py-2 rounded-lg border border-gray-100">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <i class="fas {{ $g['es_serializado'] ? 'fa-barcode' : 'fa-box' }} text-gray-400 text-[10px] shrink-0"></i>
                                            <span class="truncate font-medium text-gray-700">{{ $g['nombre'] }}</span>
                                        </div>
                                        <span class="shrink-0 text-gray-600 font-bold bg-gray-200/70 px-1.5 py-0.5 rounded border border-gray-200 tabular-nums">
                                            &times;{{ $g['cantidad'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if ($t->observaciones)
                                <div class="mt-4 bg-amber-50/50 border border-amber-100 p-2.5 rounded-lg text-xs text-amber-800 flex gap-2 items-start">
                                    <i class="fas fa-comment-alt mt-0.5 opacity-60"></i>
                                    <span class="italic leading-relaxed">{{ $t->observaciones }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-14 px-4 bg-white rounded-2xl border border-dashed border-gray-300">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-truck text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">No hay traslados registrados aún.</p>
                <a href="{{ route('almacen.traslados.crear') }}" wire:navigate
                   class="mt-4 inline-flex items-center gap-2 bg-indigo-500 px-5 py-2.5 rounded-lg text-white text-sm font-semibold hover:bg-indigo-600 transition">
                    <i class="fas fa-plus text-xs"></i> Crear el primer traslado
                </a>
            </div>
        @endforelse

        @if ($traslados->hasPages())
            <div class="mt-4">{{ $traslados->links('pagination::tailwind') }}</div>
        @endif
    </div>

</div>
