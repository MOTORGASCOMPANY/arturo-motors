<div>
    <div class="mx-auto py-6 px-4 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Inventario General</h1>
                    <p class="text-sm text-gray-500 mt-1">Kits sellados y piezas sueltas por sede</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('almacen.recepciones.crear') }}" 
                       class="bg-indigo-500 px-4 py-2 rounded-md text-white text-sm font-semibold hover:bg-indigo-600 transition">
                        + Recibir Kit
                    </a>
                    <a href="{{ route('almacen.traslados.crear') }}" 
                       class="bg-gray-500 px-4 py-2 rounded-md text-white text-sm font-semibold hover:bg-gray-600 transition">
                        📦 Trasladar
                    </a>
                </div>
            </div>

            <div class="flex flex-wrap gap-4 mb-6">
                <select wire:model.live="filtroSedeId" 
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todas las sedes</option>
                    @foreach($sedes as $sede)
                        <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filtroEstado" 
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los estados</option>
                    <option value="en_stock">En stock</option>
                    <option value="asignado">Asignado</option>
                    <option value="instalado">Instalado</option>
                    <option value="devuelto">Devuelto</option>
                </select>

                <input type="text" 
                       wire:model.live="busqueda" 
                       placeholder="Buscar por nombre..."
                       class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            @php
                $kits = $resumen['kits'];
            @endphp

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    📦 Kits Sellados
                    <span class="text-sm font-normal text-gray-500">({{ $kits->flatten()->count() }} unidades)</span>
                </h2>

                @if($kits->isEmpty())
                    <p class="text-gray-400 text-sm bg-gray-50 p-4 rounded-lg">No hay kits con estos filtros.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($kits as $productoId => $items)
                            @php $producto = $items->first()->producto; @endphp
                            @php $sede = $items->first()->sede; @endphp
                            @php $estado = $items->first()->estado; @endphp
                            @php $hayDevueltos = $items->contains('devuelto_por_no_calza', true); @endphp

                            <div class="border rounded-xl p-4 hover:shadow-md transition cursor-pointer bg-white"
                                 wire:click="verDetalle({{ $producto->id }})">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-800 text-sm">{{ $producto->nombre }}</h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $sede->nombre }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $estado === 'en_stock' ? 'bg-green-100 text-green-800' : 
                                           ($estado === 'asignado' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($estado === 'instalado' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                    </span>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-2xl font-bold text-gray-800">{{ $items->count() }}</span>
                                        <span class="text-xs text-gray-500">unidades</span>
                                    </div>
                                    @if($hayDevueltos)
                                        <div class="mt-2 text-xs text-amber-600 font-medium">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>Pieza(s) devuelta(s)
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-3 flex items-center justify-between text-xs text-gray-400">
                                    <span>ID: #{{ $producto->id }}</span>
                                    <span class="text-indigo-500 font-medium">Ver detalle →</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @php
                $sueltos = $resumen['sueltos'];
            @endphp

            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    🔧 Piezas Sueltas
                    <span class="text-sm font-normal text-gray-500">({{ $sueltos->flatten()->count() }} unidades)</span>
                </h2>

                @if($sueltos->isEmpty())
                    <p class="text-gray-400 text-sm bg-gray-50 p-4 rounded-lg">No hay piezas sueltas con estos filtros.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($sueltos as $productoId => $items)
                            @php $producto = $items->first()->producto; @endphp
                            @php $sede = $items->first()->sede; @endphp
                            @php $estado = $items->first()->estado; @endphp

                            <div class="border rounded-xl p-4 hover:shadow-md transition cursor-pointer bg-white"
                                 wire:click="verDetalle({{ $producto->id }})">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-800 text-sm">{{ $producto->nombre }}</h3>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>{{ $sede->nombre }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $estado === 'en_stock' ? 'bg-green-100 text-green-800' : 
                                           ($estado === 'asignado' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($estado === 'instalado' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $estado)) }}
                                    </span>
                                </div>

                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-2xl font-bold text-gray-800">{{ $items->count() }}</span>
                                        <span class="text-xs text-gray-500">unidades</span>
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between text-xs text-gray-400">
                                    <span>ID: #{{ $producto->id }}</span>
                                    <span class="text-indigo-500 font-medium">Ver detalle →</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($detalleProductoId && $detalles->count() > 0)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="cerrarDetalle"></div>
                    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden flex flex-col">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $detalles->first()->producto->nombre }}</h3>
                                <p class="text-sm text-gray-500">{{ $detalles->count() }} unidad(es)</p>
                            </div>
                            <button wire:click="cerrarDetalle" class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto p-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sede</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($detalles as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm font-mono text-gray-900">#{{ $item->id }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $item->sede->nombre }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                @if($item->estado === 'en_stock')
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">En stock</span>
                                                @elseif($item->estado === 'asignado')
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">Asignado</span>
                                                @elseif($item->estado === 'instalado')
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">Instalado</span>
                                                @elseif($item->estado === 'devuelto')
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700">Devuelto</span>
                                                @else
                                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-800">{{ $item->estado }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-xs text-gray-500">{{ $item->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>