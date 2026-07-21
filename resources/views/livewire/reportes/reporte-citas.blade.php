<!-- resources>views>livewire>reportes>reporte-citas.blade.php -->
<div class="container mx-auto py-8 antialiased bg-gray-100">
    <!-- Main Card Container -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header Section -->
        {{-- 
        <div class="bg-blue-600 text-white p-6 rounded-t-xl shadow">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <h2 class="text-3xl font-bold tracking-wide">
                    Reporte de Citas
                </h2>
                <div
                    class="mt-4 md:mt-0 bg-white bg-opacity-10 px-4 py-1 rounded-full text-lg font-semibold border border-white border-opacity-30">
                    <p>Total: <span class="font-bold">{{ $citas->total() }}</span></p>
                </div>
            </div>
        </div>
        --}}
        <div class="bg-gradient-to-r from-blue-500 to-green-500 text-white p-4 rounded-t-3xl shadow-lg">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <h2 class="text-2xl font-extrabold tracking-tight">
                    Reporte de Citas
                </h2>
                <div class="mt-4 md:mt-0 bg-white bg-opacity-20 px-4 py-2 rounded-full text-lg font-semibold">
                    <p>Total: <span class="font-extrabold">{{ $citas->total() }}</span></p>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-8">
            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Buscar -->
                <div>
                    <x-label for="search" value="Buscar Cliente/Motivo" class="text-gray-600 font-semibold mb-1" />
                    <x-input id="search" type="text"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 transition"
                        wire:model.live="search" placeholder="Buscar..." />
                </div>
                <!-- Estado -->
                <div>
                    <x-label for="estado" value="Estado" class="text-gray-600 font-semibold mb-1" />
                    <select id="estado"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition"
                        wire:model.live="estado">
                        <option value="todos">Seleccione estado</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aceptada">Aceptada</option>
                        <option value="rechazada">Rechazada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <!-- Fecha Inicio -->
                <div>
                    <x-label for="fechaInicio" value="Fecha Inicio" class="text-gray-600 font-semibold mb-1" />
                    <x-input id="fechaInicio" type="date"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition"
                        wire:model.live="fechaInicio" />
                </div>
                <!-- Fecha Fin -->
                <div>
                    <x-label for="fechaFin" value="Fecha Fin" class="text-gray-600 font-semibold mb-1" />
                    <x-input id="fechaFin" type="date"
                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition"
                        wire:model.live="fechaFin" />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                @if ($citas->count())
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-600 text-white">
                            <tr>
                                <th class="cursor-pointer px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider hover:bg-gray-800 transition"
                                    wire:click="order('id')">
                                    ID
                                    @if ($sort === 'id')
                                        <span class="ml-1 text-white">{!! $direction === 'asc' ? '&#x25B2;' : '&#x25BC;' !!}</span>
                                    @endif
                                </th>
                                <th class="cursor-pointer px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider hover:bg-gray-800 transition"
                                    wire:click="order('fecha_cita')">
                                    Fecha
                                    @if ($sort === 'fecha_cita')
                                        <span class="ml-1 text-white">{!! $direction === 'asc' ? '&#x25B2;' : '&#x25BC;' !!}</span>
                                    @endif
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Cliente
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Vehículo
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Asesor
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Motivo
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($citas as $cita)
                                <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $cita->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $cita->fecha_cita->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $cita->cliente->nombre ?? 'N/A' }} {{ $cita->cliente->apellido ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $cita->vehiculo->placa ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $cita->asesor->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $cita->motivo }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $colors = [
                                                'pendiente' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                                                'aceptada' => 'bg-green-50 text-green-700 border border-green-200',
                                                'rechazada' => 'bg-red-50 text-red-700 border border-red-200',
                                                'cancelada' => 'bg-gray-50 text-gray-700 border border-gray-200',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$cita->estado] ?? 'bg-gray-50 text-gray-700 border border-gray-200' }}">
                                            {{ ucfirst($cita->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-10 text-gray-500">
                        <p>No se encontraron citas con los filtros seleccionados.</p>
                    </div>
                @endif
            </div>

            <!-- Pagination -->
            @if ($citas->hasPages())
                <div class="mt-8">
                    {{ $citas->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
