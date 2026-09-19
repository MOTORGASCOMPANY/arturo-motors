<div>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('almacen.recepciones.crear') }}" 
                       class="w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 transition" title="Volver a crear recepción">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Historial de Recepciones</h1>
                        <p class="text-sm text-gray-500 mt-1">Kits recibidos del proveedor</p>
                    </div>
                </div>
                <a href="{{ route('almacen.recepciones.crear') }}" 
                   class="bg-indigo-500 px-5 py-3 rounded-md text-white font-semibold hover:bg-indigo-600 transition">
                    + Nueva Recepción
                </a>
            </div>

            <div class="flex gap-4 mb-6">
                <input type="text" 
                       wire:model.live="search" 
                       placeholder="Buscar por proveedor..."
                       class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                
                <select wire:model.live="filtroProveedor" 
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Todos los proveedores</option>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov }}">{{ $prov }}</option>
                    @endforeach
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recepciones as $recepcion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ $recepcion->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $recepcion->producto->nombre }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $recepcion->atributos['proveedor'] ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($recepcion->estado === 'en_stock')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            En stock
                                        </span>
                                    @elseif($recepcion->estado === 'asignado')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Asignado
                                        </span>
                                    @elseif($recepcion->estado === 'instalado')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Instalado
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $recepcion->estado }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $recepcion->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No hay recepciones registradas.
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