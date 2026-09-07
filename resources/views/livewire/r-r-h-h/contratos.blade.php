<div class="container mx-auto py-12">
    <div class="bg-gray-200 p-8 rounded-xl w-full">
        {{-- Filtros --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
            <div class="px-5 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700">
                            <i class="fas fa-file-signature mr-2 text-indigo-500"></i>Gestión de Contratos
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Control de periodos laborales, remuneraciones y documentos</p>
                    </div>
                    @hasanyrole('Administrador del sistema|administrador')
                        <button wire:click="$dispatch('abrir-modal-crear')"
                            class="bg-orange-500 px-5 py-2.5 rounded-lg text-white text-sm font-semibold tracking-wide cursor-pointer hover:bg-orange-600 transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-plus-circle"></i> Nuevo Contrato
                        </button>
                    @endhasanyrole
                </div>
            </div>

            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Buscar</x-label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live="search" placeholder="Empleado o cargo..."
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm pl-10">
                        </div>
                    </div>
                    <div>
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Mostrar</x-label>
                        <select wire:model.live="cant"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                            <option value="10">10 entradas</option>
                            <option value="20">20 entradas</option>
                            <option value="50">50 entradas</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Estado</x-label>
                        <select wire:model.live="filterStatus" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                            <option value="todos">Todos</option>
                            <option value="Activo">Activo</option>
                            <option value="Vencido">Vencido</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>
                    <div>
                        <x-label class="text-gray-600 font-semibold mb-1 text-xs">Tipo de contrato</x-label>
                        <select wire:model.live="filterTipo" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                            <option value="">Todos</option>
                            <option value="Plazo Fijo">Plazo Fijo</option>
                            <option value="Indeterminado">Indeterminado</option>
                            <option value="Temporal">Temporal</option>
                            <option value="Por Locacion">Por Locación</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de contratos -->
        @if ($contratos->count())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-4">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                Empleado
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                Cargo
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                Periodo Laboral
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                Remuneración
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                Estado
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                Info. Vacaciones
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($contratos as $item)
                            <tr class="hover:bg-gray-50 transition-colors" wire:key="contrato-{{ $item->id }}">
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <div class="flex items-center justify-center">
                                        <div class="ml-3 text-left">
                                            <p class="text-gray-900 font-bold uppercase">{{ $item->user->name }}</p>
                                            <p class="text-gray-500 text-xs">DNI: {{ $item->user->dni ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <span class="font-semibold text-indigo-600">{{ $item->cargo }}</span><br>
                                    <span class="text-xs text-gray-400">{{ $item->tipo_contrato }}</span>
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <div class="text-xs">
                                        <span class="block"><b class="text-gray-400">INICIO:</b>
                                            {{ $item->fecha_inicio_contrato->format('d/m/Y') }}</span>
                                        <span class="block mt-1"><b class="text-gray-400">VENCE:</b>
                                            {{ $item->fecha_vencimiento ? $item->fecha_vencimiento->format('d/m/Y') : 'Indet.' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <p class="text-xs text-gray-400">Bruto: S/
                                        {{ number_format($item->sueldo_bruto, 2) }}</p>
                                    <p class="text-lg font-black text-green-600 leading-tight">S/
                                        {{ number_format($item->sueldo_neto, 2) }}</p>
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <span
                                        class="px-3 py-1 rounded-full font-bold text-xs {{ $item->status == 'Activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100 min-w-[140px]">
                                    @if ($item->vacaciones)
                                        @php
                                            $ganados = round($item->vacaciones->dias_ganados);
                                            $tomados = round($item->vacaciones->dias_tomados);
                                            $restantes = round($item->vacaciones->dias_restantes);
                                            $porcentaje = $ganados > 0 ? ($tomados / $ganados) * 100 : 0;
                                            $porcentaje = $porcentaje > 100 ? 100 : $porcentaje;
                                        @endphp
                                        <div class="space-y-2">
                                            <div class="flex justify-between text-[10px]">
                                                <span class="text-gray-400 uppercase">Ganadas</span>
                                                <span class="font-bold">{{ $ganados }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500"
                                                    style="width: {{ $porcentaje }}%">
                                                </div>
                                            </div>
                                            <div class="flex justify-between text-[10px] font-bold">
                                                <span class="text-orange-500">Tom: {{ $tomados }}</span>
                                                <span class="text-green-600">Rest: {{ $restantes }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center text-gray-300 italic text-[10px]">
                                            Sin registro
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition focus:outline-none">
                                            <i class="fas fa-ellipsis-v text-lg"></i>
                                        </button>
                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl z-[9999] py-2"
                                            style="display: none;">

                                            <a href="{{ route('rrhh.contrato.pdf', $item->id) }}" target="_blank"
                                                class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition rounded-md mx-2">
                                                <i class="fas fa-file-pdf w-5 mr-3 text-red-500"></i> Ver Contrato PDF
                                            </a>
                                            <a href="{{ route('rrhh.vacaciones.index', $item->id) }}"
                                                class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition rounded-md mx-2">
                                                <i class="fas fa-calendar-alt w-5 mr-3 text-blue-500"></i> Vacaciones
                                            </a>
                                            <a href="{{ route('rrhh.documentos', $item->user->id) }}"
                                                class="flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition rounded-md mx-2">
                                                <i class="fas fa-folder-open w-5 mr-3 text-orange-500"></i> Documentos
                                            </a>
                                            <div class="border-t border-gray-100 my-1"></div>
                                            @hasanyrole('Administrador del sistema|administrador')
                                                <button wire:click="$dispatch('editar-contrato', { id: {{ $item->id }} })" @click="open = false"
                                                    class="w-full text-left flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-lime-50 hover:text-lime-700 transition rounded-lg mx-2">
                                                    <i class="fas fa-pencil-alt w-5 mr-3 text-lime-600"></i> Editar Datos
                                                </button>
                                                <button
                                                    onclick="Swal.fire({ title: '¿Estás seguro?', text: 'Este contrato será eliminado permanentemente.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' }).then((result) => { if (result.isConfirmed) { @this.call('delete', {{ $item->id }}) } })"
                                                    class="w-full text-left flex items-center px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition rounded-lg mx-2">
                                                    <i class="fas fa-trash-alt w-5 mr-3"></i> Eliminar
                                                </button>
                                            @endhasanyrole
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-5 py-3 border-t border-gray-100">
                    {{ $contratos->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-4">
                <div class="px-6 py-16 text-center">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-signature text-2xl text-indigo-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500">No hay contratos registrados con el criterio "<span class="font-semibold text-gray-700">{{ $search }}</span>".</p>
                    <p class="text-xs text-gray-400 mt-1">Intenta con otros términos de búsqueda.</p>
                </div>
            </div>
        @endif
    </div>

    @livewire('r-r-h-h.create-contrato')
</div>
