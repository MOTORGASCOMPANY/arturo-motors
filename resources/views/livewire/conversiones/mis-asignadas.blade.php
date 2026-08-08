<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">

        <div class="p-6 border-b border-gray-200/60 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Mis conversiones</h2>
                <p class="text-xs text-gray-500 mt-1">Vehículos asignados a ti</p>
            </div>
            <select wire:model.live="estado" class="text-sm rounded-lg border-gray-300">
                <option value="pendientes">Solo pendientes</option>
                <option value="todas">Todas (incluye finalizadas)</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Vehículo</th>
                        <th class="px-4 py-3 text-left">Servicio</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($ordenes as $orden)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3">{{ $orden->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</td>
                            <td class="px-4 py-3">{{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->marca }}</td>
                            <td class="px-4 py-3">{{ $orden->service->nombre }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    {{ match($orden->estado) {
                                        'en_evaluacion' => 'bg-amber-100 text-amber-700',
                                        'aprobado_conversion' => 'bg-blue-100 text-blue-700',
                                        'en_conversion' => 'bg-purple-100 text-purple-700',
                                        'conversion_completada' => 'bg-emerald-100 text-emerald-700',
                                        'evaluacion_rechazada', 'cancelado' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ ucfirst(str_replace('_', ' ', $orden->estado)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @switch($orden->estado)
                                    @case('en_evaluacion')
                                        <a href="{{ route('conversiones.evaluar', $orden->id) }}"
                                           class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                                            Evaluar
                                        </a>
                                        @break
                                    @case('aprobado_conversion')
                                        <span class="text-xs text-gray-400">Esperando equipos de almacén</span>
                                        @break
                                    @case('en_conversion')
                                        <a href="{{ route('conversiones.realizar', $orden->id) }}"
                                           class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                                            Continuar
                                        </a>
                                        @break
                                    @case('conversion_completada')
                                        <span class="text-xs text-gray-400">Esperando entrega</span>
                                        @break
                                    @default
                                        <span class="text-xs text-gray-400">—</span>
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No tienes conversiones asignadas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200/60">
            {{ $ordenes->links() }}
        </div>
    </div>
</div>