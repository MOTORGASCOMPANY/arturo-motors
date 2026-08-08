<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">

        <div class="p-6 border-b border-gray-200/60">
            <h2 class="text-xl font-bold text-gray-800">Asignar técnico</h2>
            <p class="text-xs text-gray-500 mt-1">Conversiones pendientes de asignación</p>
        </div>

        @if (session('mensaje'))
            <div class="m-4 p-3 bg-emerald-50 text-emerald-700 text-sm rounded-lg">{{ session('mensaje') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Fecha</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Vehículo</th>
                        <th class="px-4 py-3 text-left">Servicio</th>
                        <th class="px-4 py-3 text-left">Técnico</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($ordenes as $orden)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-4 py-3">{{ $orden->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</td>
                            <td class="px-4 py-3">{{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->marca }}</td>
                            <td class="px-4 py-3">{{ $orden->service->nombre }}</td>
                            <td class="px-4 py-3">
                                <select wire:model="tecnicoSeleccionado.{{ $orden->id }}" class="text-sm rounded-lg border-gray-300">
                                    <option value="">-- Selecciona --</option>
                                    @foreach ($tecnicos as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="tecnico.{{ $orden->id }}" class="mt-1" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="asignar({{ $orden->id }})" wire:loading.attr="disabled" type="button"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                                    Asignar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No hay conversiones pendientes de asignar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200/60">
            {{ $ordenes->links() }}
        </div>
    </div>
</div>