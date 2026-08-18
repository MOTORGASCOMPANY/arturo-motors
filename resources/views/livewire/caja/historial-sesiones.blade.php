<div wire:loading.attr="disabled">
    <div class="container mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <!-- Cabecera del Módulo -->
            <div class="pb-6 space-y-4">
                <div class="w-full">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-history mr-2"></i>Historial Sesiones de Caja
                    </h2>
                    <span class="text-xs text-gray-500">
                        Registro y control de aperturas y cierres de caja
                    </span>
                </div>
                <!-- Filtros -->
                <div class="w-full flex flex-wrap items-end gap-4">
                    <!-- Mostrar Cantidad -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <span class="text-gray-600 text-sm whitespace-nowrap">
                            Mostrar
                        </span>
                        <select wire:model.live="cant" class="mx-2 w-20 border-none bg-transparent text-gray-700 text-sm focus:ring-0 focus:outline-none">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-gray-600 text-sm whitespace-nowrap">
                            entradas
                        </span>
                    </div>
                    <!-- Desde -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <span class="text-gray-600 text-sm mr-2 font-medium whitespace-nowrap">
                            Desde:
                        </span>
                        <x-input type="date" wire:model.live="desde" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm transition"/>
                    </div>
                    <!-- Hasta -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <span class="text-gray-600 text-sm mr-2 font-medium whitespace-nowrap">
                            Hasta:
                        </span>
                        <x-input type="date" wire:model.live="hasta" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm transition"/>
                    </div>
                    <!-- Estado -->
                    <div class="flex items-center bg-white border border-gray-300 p-2 rounded-lg shadow-sm">
                        <span class="text-gray-600 text-sm mr-1 font-medium whitespace-nowrap">
                            Estado:
                        </span>
                        <select wire:model.live="estado" class="border-none bg-transparent text-gray-700 text-sm focus:ring-0 focus:outline-none cursor-pointer">
                            <option value="todos">Todos</option>
                            <option value="abierta">Abierta</option>
                            <option value="cerrada">Cerrada</option>
                        </select>
                        <button wire:click="limpiarFiltros" type="button" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold ml-2 underline whitespace-nowrap">
                            Limpiar
                        </button>
                    </div>
                    <!-- Botón -->
                    <div class="w-full sm:w-auto">
                        <a href="{{ route('caja.abrir') }}"
                            class="block sm:inline-block w-full sm:w-auto text-center bg-indigo-500 hover:bg-indigo-600 px-6 py-3 rounded-md text-white font-semibold tracking-wide">
                            Ir a caja
                            <i class="fas fa-arrow-right text-xs ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tabla de Datos -->
            @if ($sesiones->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Apertura
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Abierta por
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Cierre
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    M. Apertura
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Esperado
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Real
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Diferencia
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Estado
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sesiones as $s)
                                <tr wire:key="sesion-{{ $s->id }}">
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        {{ $s->abierta_en->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm font-bold text-gray-800">
                                        {{ strtoupper($s->abiertaPor->name) }}
                                    </td>
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        {{ $s->cerrada_en?->format('d/m/Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-right">
                                        S/ {{ number_format($s->monto_apertura, 2) }}
                                    </td>
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-right">
                                        {{ $s->monto_esperado !== null ? 'S/ '.number_format($s->monto_esperado, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-right">
                                        {{ $s->monto_cierre !== null ? 'S/ '.number_format($s->monto_cierre, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-right font-bold {{ $s->diferencia === null ? 'text-gray-400' : ($s->diferencia == 0 ? 'text-emerald-600' : 'text-red-600') }}">
                                        {{ $s->diferencia !== null ? 'S/ '.number_format($s->diferencia, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-center">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $s->estado === 'abierta' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($s->estado) }}
                                        </span>
                                    </td>
                                    {{-- 
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-right">
                                        <a href="{{ route('caja.sesion', $s->id) }}" 
                                           class="py-2 px-3 rounded-md bg-lime-500 font-bold text-white hover:bg-lime-600 transition inline-block">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                    --}}
                                    <td class="px-4 py-4 border-b border-gray-200 bg-white text-sm text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <!-- Botón 1: Ver detalles -->
                                            <div class="relative inline-block group">
                                                <a href="{{ route('caja.sesion', $s->id) }}" 
                                                class="inline-flex items-center justify-center w-8 h-8 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-900 rounded-lg transition-colors duration-150">
                                                    <i class="fa-solid fa-eye text-xs"></i>
                                                </a>                                                
                                                <!-- Tooltip 1 -->
                                                <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:flex flex-col items-center pointer-events-none z-10">
                                                    <span class="relative z-10 p-1.5 text-[10px] font-semibold leading-none text-white whitespace-nowrap bg-gray-800 rounded shadow-md">
                                                        Ver detalles
                                                    </span>
                                                    <div class="w-2 h-2 -mt-1 rotate-45 bg-gray-800"></div>
                                                </div>
                                            </div>
                                            {{-- 
                                            <!-- Botón 2: Imprimir / Exportar (Ejemplo) -->
                                            <div class="relative inline-block group">
                                                <button type="button" 
                                                        wire:click="imprimirReporte({{ $s->id }})"
                                                        class="inline-flex items-center justify-center w-8 h-8 text-emerald-700 bg-emerald-50 hover:bg-emerald-100 hover:text-emerald-900 rounded-lg transition-colors duration-150">
                                                    <i class="fa-solid fa-print text-xs"></i>
                                                </button>                                                
                                                <!-- Tooltip 2 -->
                                                <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:flex flex-col items-center pointer-events-none z-10">
                                                    <span class="relative z-10 p-1.5 text-[10px] font-semibold leading-none text-white whitespace-nowrap bg-gray-800 rounded shadow-md">
                                                        Imprimir reporte
                                                    </span>
                                                    <div class="w-2 h-2 -mt-1 rotate-45 bg-gray-800"></div>
                                                </div>
                                            </div>
                                            <!-- Botón 3: Editar / Cierre (Ejemplo) -->
                                            <div class="relative inline-block group">
                                                <button type="button" 
                                                        wire:click="editarSesion({{ $s->id }})"
                                                        class="inline-flex items-center justify-center w-8 h-8 text-amber-700 bg-amber-50 hover:bg-amber-100 hover:text-amber-900 rounded-lg transition-colors duration-150">
                                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                </button>                                                
                                                <!-- Tooltip 3 -->
                                                <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:flex flex-col items-center pointer-events-none z-10">
                                                    <span class="relative z-10 p-1.5 text-[10px] font-semibold leading-none text-white whitespace-nowrap bg-gray-800 rounded shadow-md">
                                                        Editar sesión
                                                    </span>
                                                    <div class="w-2 h-2 -mt-1 rotate-45 bg-gray-800"></div>
                                                </div>
                                            </div>
                                            --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $sesiones->links() }}
                </div>
            @else
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md text-indigo-900">
                    No se encontraron registros de sesiones de caja con los filtros aplicados.
                </div>
            @endif

        </div>
    </div>
</div>