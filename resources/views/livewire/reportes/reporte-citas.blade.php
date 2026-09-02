<!-- resources/views/livewire/reportes/reporte-citas.blade.php -->
<div class="container mx-auto py-8 antialiased bg-gray-100">
    <!-- Main Card Container -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-900 to-blue-900 text-white p-5 rounded-t-xl shadow-lg">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h2 class="text-xl font-extrabold tracking-tight">Reporte de Citas</h2>
                    <p class="text-xs text-blue-200 mt-0.5">Listado completo de citas registradas</p>
                </div>
                <div class="flex items-center gap-3 mt-4 md:mt-0">
                    <div class="bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-semibold backdrop-blur-sm">
                        <i class="fas fa-calendar-check mr-1"></i>
                        Total: <span class="font-extrabold">{{ $citas->total() }}</span>
                    </div>
                    <!-- Botones de exportación -->
                    <button onclick="exportarPDF()"
                        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-full text-sm font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-file-pdf text-sm"></i>
                        PDF
                    </button>
                    <button onclick="exportarExcel()"
                        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-full text-sm font-bold transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-file-excel text-sm"></i>
                        Excel
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="p-6 pb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
                <div>
                    <x-label for="search" value="Buscar Cliente/Motivo" class="text-gray-600 font-semibold mb-1" />
                    <x-input id="search" type="text"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 transition"
                        wire:model.live="search" placeholder="Buscar..." />
                </div>
                <div>
                    <x-label for="estado" value="Estado" class="text-gray-600 font-semibold mb-1" />
                    <select id="estado"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition"
                        wire:model.live="estado">
                        <option value="todos">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aceptada">Aceptada</option>
                        <option value="rechazada">Rechazada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <div>
                    <x-label for="fechaInicio" value="Fecha Inicio" class="text-gray-600 font-semibold mb-1" />
                    <x-input id="fechaInicio" type="date"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition"
                        wire:model.live="fechaInicio" />
                </div>
                <div>
                    <x-label for="fechaFin" value="Fecha Fin" class="text-gray-600 font-semibold mb-1" />
                    <x-input id="fechaFin" type="date"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition"
                        wire:model.live="fechaFin" />
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="px-6 pb-6">
            <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                @if ($citas->count())
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200"
                                    wire:click="order('id')">
                                    ID
                                    @if ($sort === 'id')
                                        <span class="ml-1 text-indigo-600">{!! $direction === 'asc' ? '&#x25B2;' : '&#x25BC;' !!}</span>
                                    @endif
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200"
                                    wire:click="order('fecha_cita')">
                                    Fecha
                                    @if ($sort === 'fecha_cita')
                                        <span class="ml-1 text-indigo-600">{!! $direction === 'asc' ? '&#x25B2;' : '&#x25BC;' !!}</span>
                                    @endif
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200">
                                    Cliente
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200">
                                    Placa
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200">
                                    Asesor
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b-2 border-gray-200">
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

            <!-- Paginación -->
            @if ($citas->hasPages())
                <div class="mt-4 p-4 border-t border-gray-200/60">
                    {{ $citas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function exportarPDF() {
        Swal.fire({
            title: 'Exportando PDF',
            text: 'Generando el reporte, por favor espera...',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                window.location.href = '{{ $this->exportPdfUrl() }}';
                setTimeout(() => {
                    Swal.close();
                    Swal.fire({
                        title: 'Descarga iniciada',
                        text: 'El archivo PDF se esta descargando.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }, 3000);
            }
        });
    }

    function exportarExcel() {
        Swal.fire({
            title: 'Exportando Excel',
            text: 'Generando el reporte, por favor espera...',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
                window.location.href = '{{ $this->exportExcelUrl() }}';
                setTimeout(() => {
                    Swal.close();
                    Swal.fire({
                        title: 'Descarga iniciada',
                        text: 'El archivo Excel se esta descargando.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }, 3000);
            }
        });
    }
</script>
