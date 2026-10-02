<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6 font-sans">

    {{-- Encabezado --}}
    <div class="bg-white border border-gray-200 p-6 sm:p-8 rounded-2xl shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-3">
            <div class="flex items-start gap-4 min-w-0">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-800 tracking-tight">Recepciones</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Historial de ingresos al almacén</p>
                </div>
            </div>

            <a href="{{ route('almacen.recepciones.crear') }}" wire:navigate
               class="inline-flex items-center gap-2 bg-indigo-500 px-5 py-3 rounded-lg text-white text-sm font-semibold hover:bg-indigo-600 transition shadow-sm shrink-0">
                <i class="fas fa-plus text-xs"></i>
                <span>Nueva recepción</span>
            </a>
        </div>
    </div>

    {{-- Indicadores --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 hover:shadow-md transition">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total recepciones</p>
            <p class="text-3xl font-extrabold text-gray-800 mt-2 tabular-nums">{{ $totalRecepciones }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 hover:shadow-md transition">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Últimos 30 días</p>
            <p class="text-3xl font-extrabold text-indigo-600 mt-2 tabular-nums">{{ $ultimos30 }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 hover:shadow-md transition">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kits recibidos</p>
            <p class="text-3xl font-extrabold text-indigo-600 mt-2 tabular-nums">{{ $conteos['kits'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6 hover:shadow-md transition">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Serializados + Cantidad</p>
            <p class="text-3xl font-extrabold text-green-600 mt-2 tabular-nums">{{ $conteos['serializados'] + $conteos['cantidad'] }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm">
        <div class="flex flex-wrap items-center gap-3">
            <nav class="flex flex-wrap gap-1 bg-gray-100 rounded-lg p-1 flex-1 min-w-[280px]" aria-label="Filtrar por tipo">
                @php
                    $tabs = [
                        'todos'        => ['label' => 'Todos',        'icon' => 'fa-layer-group'],
                        'kits'         => ['label' => 'Kits',         'icon' => 'fa-box'],
                        'serializados' => ['label' => 'Serializados', 'icon' => 'fa-barcode'],
                        'cantidad'     => ['label' => 'Por cantidad', 'icon' => 'fa-cubes'],
                    ];
                @endphp
                @foreach ($tabs as $key => $tab)
                    @php $activo = $filtroTipo === $key; @endphp
                    <button type="button" wire:click="$set('filtroTipo', '{{ $key }}')"
                        @class([
                            'flex items-center gap-1.5 px-3 h-9 text-sm font-semibold rounded-md transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500',
                            'bg-white text-indigo-600 shadow-sm' => $activo,
                            'text-gray-500 hover:text-gray-700' => ! $activo,
                        ])">
                        <i class="fas {{ $tab['icon'] }} text-xs"></i>
                        <span class="hidden sm:inline">{{ $tab['label'] }}</span>
                        @if ($key !== 'todos')
                            <span @class([
                                    'px-1.5 py-0.5 text-[10px] font-black rounded-full tabular-nums',
                                    'bg-indigo-100 text-indigo-700' => $activo,
                                    'bg-gray-200 text-gray-500' => ! $activo,
                                ])">
                                {{ $conteos[$key] ?? 0 }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </nav>

            <label class="flex items-center h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 w-full sm:w-64 sm:ml-auto transition-colors hover:border-gray-300 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 shrink-0">
                <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 p-0"
                    type="text" wire:model.live.debounce.400ms="search"
                    placeholder="Buscar por nombre...">
            </label>
        </div>
    </div>

    {{-- Listado con accordion --}}
    <div class="space-y-3">
        @forelse ($recepciones as $r)
            @php $abierto = $recepcionExpandida === $r['id']; @endphp
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden transition duration-200 hover:shadow-md">

                {{-- Fila principal (click para expandir) --}}
                <div wire:click="toggleDetalle({{ $r['id'] }})"
                     @class([
                         'p-5 cursor-pointer flex flex-wrap justify-between items-center gap-x-3 gap-y-3',
                         'bg-gray-50' => $abierto,
                     ])
                     style="user-select: none;">

                    <div class="flex flex-wrap items-center gap-3 min-w-0 flex-1">
                        <span class="px-2 py-0.5 text-[10px] font-bold text-gray-500 bg-gray-100 rounded-full shrink-0 tabular-nums">
                            N.° {{ $r['nro'] }}
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-full whitespace-nowrap shrink-0 {{ $r['tipo_color'] }}">
                            <i class="fas {{ $r['tipo_icon'] }} mr-1"></i>{{ $r['tipo_label'] }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate">{{ $r['nombre'] }}</p>
                            <p class="text-xs text-gray-500">
                                <i class="far fa-clock mr-1"></i>{{ $r['fecha']->format('d/m/Y H:i') }}
                                <span class="mx-1">&bull;</span>
                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>{{ $r['sede'] }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        @unless ($r['estado'])
                            <span class="text-sm font-bold text-amber-600 tabular-nums shrink-0">&times;{{ $r['cantidad'] ?? 0 }}</span>
                        @endunless

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
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-sm">
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Referencia</p>
                                    @if (in_array($r['tipo'], ['kit', 'serializado'], true))
                                        <a href="{{ route('almacen.productos.listado') }}"
                                           class="font-mono font-semibold text-indigo-600 hover:text-indigo-800 hover:underline"
                                           title="Mismo ID que en Inventario ({{ $r['origen'] }})">
                                            #{{ $r['id'] }}
                                        </a>
                                    @else
                                        <span class="font-mono font-semibold text-gray-500" title="{{ $r['origen'] }}">#{{ $r['id'] }}</span>
                                    @endif
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Sede</p>
                                    <p class="font-medium text-gray-700">{{ $r['sede'] }}</p>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fecha</p>
                                    <p class="font-medium text-gray-700">{{ $r['fecha']->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Origen</p>
                                    <p class="font-medium text-gray-700">{{ $r['origen'] === 'items_serializados' ? 'Ítem serializado' : 'Movimiento de stock' }}</p>
                                </div>
                            </div>

                            @unless ($r['estado'])
                                <div class="mt-3 p-3 bg-white rounded-lg border border-gray-100">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Cantidad recibida</p>
                                    <p class="font-bold text-amber-600 text-lg">&times;{{ $r['cantidad'] ?? 0 }}</p>
                                </div>
                            @endunless
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-14 px-4 bg-white rounded-2xl border border-dashed border-gray-300">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">No hay recepciones registradas.</p>
                <a href="{{ route('almacen.recepciones.crear') }}" wire:navigate
                   class="mt-4 inline-flex items-center gap-2 bg-indigo-500 px-5 py-2.5 rounded-lg text-white text-sm font-semibold hover:bg-indigo-600 transition">
                    <i class="fas fa-plus text-xs"></i> Registrar primera recepción
                </a>
            </div>
        @endforelse

        @if ($recepciones->hasPages())
            <div class="mt-4">{{ $recepciones->links('pagination::tailwind') }}</div>
        @endif
    </div>

</div>
