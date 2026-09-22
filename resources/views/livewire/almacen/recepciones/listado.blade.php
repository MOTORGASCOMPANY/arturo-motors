<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('almacen.recepciones.crear') }}"
                       class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition" title="Nueva recepción">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Historial de Recepciones</h1>
                        <p class="text-sm text-gray-500 mt-1">Kits, items serializados y productos por cantidad</p>
                    </div>
                </div>
                <a href="{{ route('almacen.recepciones.crear') }}"
                   class="bg-indigo-500 px-5 py-3 rounded-md text-white font-semibold hover:bg-indigo-600 transition">
                    + Nueva Recepción
                </a>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 mb-6">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text"
                           wire:model.live="search"
                           placeholder="Buscar por nombre..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                    @php
                        $tabs = [
                            'todos' => ['label' => 'Todos', 'icon' => 'fa-layer-group'],
                            'kits' => ['label' => 'Kits', 'icon' => 'fa-box'],
                            'serializados' => ['label' => 'Serializados', 'icon' => 'fa-barcode'],
                            'cantidad' => ['label' => 'Por cantidad', 'icon' => 'fa-cubes'],
                        ];
                    @endphp
                    @foreach ($tabs as $key => $tab)
                        <button type="button"
                            wire:click="$set('filtroTipo', '{{ $key }}')"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                   {{ $filtroTipo === $key ? 'bg-white shadow text-indigo-700' : 'text-gray-500 hover:text-gray-700' }}">
                            <i class="fas {{ $tab['icon'] }} text-xs"></i>
                            {{ $tab['label'] }}
                            @if ($key !== 'todos')
                                <span class="ml-0.5 text-xs {{ $filtroTipo === $key ? 'text-indigo-500' : 'text-gray-400' }}">
                                    ({{ $conteos[$key] ?? 0 }})
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sede</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado / Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recepciones as $r)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">
                                    #{{ $r['id'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $r['tipo_color'] }}">
                                        {{ $r['tipo_label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    {{ $r['nombre'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $r['sede'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($r['estado'])
                                        @php
                                            $estadoMeta = match($r['estado']) {
                                                'en_stock' => ['label' => 'En stock', 'chip' => 'bg-green-100 text-green-700'],
                                                'asignado' => ['label' => 'Asignado', 'chip' => 'bg-yellow-100 text-yellow-700'],
                                                'instalado' => ['label' => 'Instalado', 'chip' => 'bg-blue-100 text-blue-700'],
                                                'completado' => ['label' => 'Completado', 'chip' => 'bg-purple-100 text-purple-700'],
                                                'consumido' => ['label' => 'Consumido', 'chip' => 'bg-red-100 text-red-700'],
                                                'reemplazado' => ['label' => 'Reemplazado', 'chip' => 'bg-gray-100 text-gray-700'],
                                                default => ['label' => $r['estado'], 'chip' => 'bg-gray-100 text-gray-700'],
                                            };
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estadoMeta['chip'] }}">
                                            {{ $estadoMeta['label'] }}
                                        </span>
                                    @else
                                        <span class="text-sm font-bold text-amber-600 tabular-nums">×{{ $r['cantidad'] ?? 0 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $r['fecha']->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
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

            <div class="mt-4">
                {{ $recepciones->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>
