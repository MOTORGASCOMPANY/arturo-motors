<div class="container mx-auto py-8 antialiased bg-gray-100">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold">Reporte de Citas</h2>
                    <p class="text-xs text-gray-500">Listado de citas por período y estado</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-semibold backdrop-blur-sm">
                        <i class="fas fa-calendar-check mr-1"></i>
                        Total: <span class="font-extrabold">{{ $citas->total() }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="descargarPdf"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-full py-2 px-4 shadow-sm transition-colors flex items-center gap-1.5">
                            <i class="fas fa-file-pdf mr-1"></i>
                            PDF
                        </button>
                        <button wire:click="descargarExcel"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-full py-2 px-4 shadow-sm transition-colors flex items-center gap-1.5">
                            <i class="fas fa-file-excel mr-1"></i>
                            Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden p-4" x-data="{ expandir: false }">
            <div class="flex flex-col md:flex-row md:items-end gap-4 mb-4">
                <div class="flex-1">
                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Buscar</x-label>
                    <div class="relative">
                        <x-input icon="fas fa-search" wire:model.live="search" placeholder="Cliente, placa, documento..." class="w-full" />
                    </div>
                </div>
                <div>
                    <button type="button" x-on:click="expandir = !expandir" class="w-full md:w-auto px-4 py-2 h-[42px] text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center justify-between gap-2">
                        <span><i class="fas fa-sliders-h mr-1"></i> Más filtros</span>
                        <i class="fas" :class="expandir ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                </div>
            </div>

            <div x-show="expandir" class="mt-2" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Estado</x-label>
                        <select wire:model.live="estado"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                            <option value="todos">Todos</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="aceptada">Aceptada</option>
                            <option value="rechazada">Rechazada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                    <div>
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Sede</x-label>
                        <select wire:model.live="sede_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition text-sm">
                            <option value="todos">Todas</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Fecha Desde</x-label>
                        <input type="date" wire:model.live="fechaInicio" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                    <div>
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Fecha Hasta</x-label>
                        <input type="date" wire:model.live="fechaFin" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end mt-4">
                <button type="button" wire:click="limpiarFiltros" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    <i class="fas fa-eraser mr-1"></i> Limpiar filtros
                </button>
            </div>
        </div>

        <div class="px-6 pb-6">
            <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 mt-6">
                @if ($citas->count())
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200 cursor-pointer"
                                    wire:click="order('id')">
                                    ID
                                    @if ($sort === 'id')
                                        <span class="ml-1 text-indigo-600">{!! $direction === 'asc' ? '&#x25B2;' : '&#x25BC;' !!}</span>
                                    @endif
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200 cursor-pointer"
                                    wire:click="order('fecha_cita')">
                                    Fecha
                                    @if ($sort === 'fecha_cita')
                                        <span class="ml-1 text-indigo-600">{!! $direction === 'asc' ? '&#x25B2;' : '&#x25BC;' !!}</span>
                                    @endif
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200 cursor-pointer"
                                    wire:click="order('sede_id')">
                                    Sede
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200 cursor-pointer"
                                    wire:click="order('cliente_id')">
                                    Cliente
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200">
                                    Placa
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200 cursor-pointer"
                                    wire:click="order('asesor_id')">
                                    Asesor
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200 cursor-pointer"
                                    wire:click="order('estado')">
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($citas as $cita)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 py-3 text-sm border-b border-gray-200">{{ $cita->id }}</td>
                                    <td class="px-4 py-3 text-sm border-b border-gray-200">
                                        {{ $cita->fecha_cita->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm border-b border-gray-200">
                                        {{ $cita->sede->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 border-b border-gray-200">
                                        {{ $cita->cliente->nombre ?? 'N/A' }} {{ $cita->cliente->apellido ?? '' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm border-b border-gray-200">
                                        {{ $cita->vehiculo->placa ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm border-b border-gray-200">
                                        {{ $cita->asesor->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200">
                                        @php
                                            $colors = [
                                                'pendiente' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                                                'aceptada' => 'bg-green-50 text-green-700 border border-green-200',
                                                'rechazada' => 'bg-red-50 text-red-700 border border-red-200',
                                                'cancelada' => 'bg-gray-50 text-gray-700 border border-gray-200',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$cita->estado] ?? 'bg-gray-50 text-gray-700' }}">
                                            {{ ucfirst($cita->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-calendar-times text-4xl mb-3"></i>
                        <p class="text-sm">No se encontraron citas con los filtros seleccionados.</p>
                    </div>
                @endif
            </div>

            @if ($citas->hasPages())
                <div class="mt-4 p-4 border-t border-gray-200/60">
                    {{ $citas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@script
@script
<script>
    Livewire.on('descargar-pdf', (params) => {
        const url = params.url;
        if (!url || url === '#') {
            Swal.fire({ title: 'Sin datos', text: 'No hay datos para exportar en este período.', icon: 'warning', timer: 3000, showConfirmButton: false });
            return;
        }
        Swal.fire({
            title: 'Exportando PDF',
            text: 'Generando el reporte, por favor espera...',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                window.location.href = url;
                setTimeout(() => {
                    Swal.close();
                    Swal.fire({ title: 'Descarga iniciada', text: 'El archivo PDF se está descargando.', icon: 'success', timer: 2000, showConfirmButton: false });
                }, 3000);
            }
        });
    });

    Livewire.on('descargar-excel', (params) => {
        const url = params.url;
        if (!url || url === '#') {
            Swal.fire({ title: 'Sin datos', text: 'No hay datos para exportar en este período.', icon: 'warning', timer: 3000, showConfirmButton: false });
            return;
        }
        Swal.fire({
            title: 'Exportando Excel',
            text: 'Generando el reporte, por favor espera...',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                window.location.href = url;
                setTimeout(() => {
                    Swal.close();
                    Swal.fire({ title: 'Descarga iniciada', text: 'El archivo Excel se está descargando.', icon: 'success', timer: 2000, showConfirmButton: false });
                }, 3000);
            }
        });
    });
</script>
@endscript
@endscript
