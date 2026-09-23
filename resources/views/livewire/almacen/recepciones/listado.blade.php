<div>
    <div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0 flex items-center gap-3">
                <span class="hidden sm:flex w-11 h-11 rounded-xl bg-indigo-50 items-center justify-center shrink-0">
                    <i class="fas fa-truck text-indigo-600"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Recepciones</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Historial de ingresos al almacén</p>
                </div>
            </div>
            <a href="{{ route('almacen.recepciones.crear') }}"
               class="shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm shadow-indigo-600/10 transition-colors flex items-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                <i class="fas fa-plus text-xs"></i>
                Nueva recepción
            </a>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-xl border border-gray-200 p-2.5 flex flex-wrap items-center gap-2.5">
            @php
                $tabs = [
                    'todos'        => ['label' => 'Todos',        'icon' => 'fa-layer-group'],
                    'kits'         => ['label' => 'Kits',         'icon' => 'fa-box'],
                    'serializados' => ['label' => 'Serializados', 'icon' => 'fa-barcode'],
                    'cantidad'     => ['label' => 'Por cantidad', 'icon' => 'fa-cubes'],
                ];
            @endphp
            <nav class="flex gap-1 bg-gray-100 rounded-lg p-1" aria-label="Filtrar por tipo">
                @foreach ($tabs as $key => $tab)
                    @php $activo = $filtroTipo === $key; @endphp
                    <button type="button" wire:click="$set('filtroTipo', '{{ $key }}')"
                        class="flex items-center gap-1.5 px-3 h-9 text-sm font-semibold rounded-md transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
                               {{ $activo ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fas {{ $tab['icon'] }} text-xs"></i>
                        <span class="hidden sm:inline">{{ $tab['label'] }}</span>
                        @if ($key !== 'todos')
                            <span class="px-1.5 py-0.5 text-[10px] font-black rounded-full tabular-nums {{ $activo ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-500' }}">
                                {{ $conteos[$key] ?? 0 }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </nav>

            <label class="flex items-center h-10 bg-gray-50 border border-gray-200 rounded-lg px-3 w-full sm:w-64 sm:ml-auto transition-colors hover:border-gray-300 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500">
                <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 p-0"
                    type="text" wire:model.live.debounce.400ms="search"
                    placeholder="Buscar por nombre...">
            </label>
        </div>

        {{-- Tabla --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">N.°</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Recepción</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Sede</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($recepciones as $r)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if (in_array($r['tipo'], ['kit', 'serializado'], true))
                                        <a href="{{ route('almacen.productos.listado') }}"
                                           class="text-sm font-mono font-semibold text-indigo-600 hover:text-indigo-800 hover:underline"
                                           title="Mismo ID que en Inventario ({{ $r['origen'] }})">
                                            #{{ $r['id'] }}
                                        </a>
                                    @else
                                        <span class="text-sm font-mono text-gray-400" title="movimientos_stock">{{ $r['origen'] === 'movimientos_stock' ? '#' : '' }}{{ $r['id'] }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="text-xs font-semibold text-gray-400 tabular-nums">N.° {{ $r['nro'] }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="px-2 py-0.5 text-[10px] font-black rounded-full whitespace-nowrap {{ $r['tipo_color'] }}">
                                            {{ $r['tipo_label'] }}
                                        </span>
                                        <span class="text-sm font-bold text-gray-800 truncate">{{ $r['nombre'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $r['sede'] }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($r['estado'])
                                        @php
                                            $estadoMeta = match ($r['estado']) {
                                                'en_stock'    => ['label' => 'En stock',    'chip' => 'bg-green-100 text-green-700'],
                                                'asignado'    => ['label' => 'Asignado',    'chip' => 'bg-yellow-100 text-yellow-700'],
                                                'instalado'   => ['label' => 'Instalado',   'chip' => 'bg-blue-100 text-blue-700'],
                                                'completado'  => ['label' => 'Completado',  'chip' => 'bg-purple-100 text-purple-700'],
                                                'consumido'   => ['label' => 'Consumido',   'chip' => 'bg-red-100 text-red-700'],
                                                'reemplazado' => ['label' => 'Reemplazado', 'chip' => 'bg-gray-100 text-gray-700'],
                                                default       => ['label' => $r['estado'],  'chip' => 'bg-gray-100 text-gray-700'],
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $estadoMeta['chip'] }}">
                                            {{ $estadoMeta['label'] }}
                                        </span>
                                    @else
                                        <span class="text-sm font-bold text-amber-600 tabular-nums">×{{ $r['cantidad'] ?? 0 }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-sm text-gray-500 whitespace-nowrap text-right tabular-nums">
                                    {{ $r['fecha']->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fas fa-inbox text-3xl text-gray-300"></i>
                                        <p class="text-gray-500 text-sm">No hay recepciones registradas.</p>
                                        <a href="{{ route('almacen.recepciones.crear') }}" class="text-indigo-600 text-sm font-medium hover:underline">
                                            + Registrar primera recepción
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-3 border-t border-gray-100">
                {{ $recepciones->links('pagination::tailwind') }}
            </div>
        </div>

    </div>
</div>
