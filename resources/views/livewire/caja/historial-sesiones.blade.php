<div wire:loading.attr="disabled" x-data="{ filtros: false }">
    <div class="container mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="pb-6 space-y-4">
                <div class="w-full">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-history mr-2"></i>Historial Sesiones de Caja
                    </h2>
                    <span class="text-xs text-gray-500">
                        Registro y control de aperturas y cierres de caja
                    </span>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-5">
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                            <div class="relative flex-1 w-full">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fas fa-search text-sm"></i>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="search"
                                       placeholder="Buscar por cajero..."
                                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            </div>
                            <button type="button" @click="filtros = !filtros"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors whitespace-nowrap">
                                <i class="fas fa-filter"></i>
                                Filtros
                                <i class="fas text-xs" :class="filtros ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                        </div>

                        <div x-show="filtros" x-collapse>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-100">
                                <div>
                                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Estado</x-label>
                                    <select wire:model.live="estado" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                                        <option value="todos">Todos</option>
                                        <option value="abierta">Abierta</option>
                                        <option value="cerrada">Cerrada</option>
                                    </select>
                                </div>
                                <div>
                                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Cajero</x-label>
                                    <select wire:model.live="filterAbiertaPor" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                                        <option value="">Todos</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Mostrar</x-label>
                                    <select wire:model.live="cant" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                                        <option value="10">10 entradas</option>
                                        <option value="20">20 entradas</option>
                                        <option value="50">50 entradas</option>
                                        <option value="100">100 entradas</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button wire:click="limpiarFiltros" type="button" class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                                        <i class="fas fa-eraser"></i> Limpiar
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 pt-4 border-t border-gray-100">
                                <div>
                                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Fecha Desde</x-label>
                                    <x-input type="date" wire:model.live="desde" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm"/>
                                </div>
                                <div>
                                    <x-label class="text-gray-600 font-semibold mb-1 text-xs">Fecha Hasta</x-label>
                                    <x-input type="date" wire:model.live="hasta" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-6">
                @if ($sesiones->count())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                        Apertura
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                        Abierta por
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                        Cierre
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider border-r border-gray-100 bg-blue-50 text-blue-600">
                                        M. Apertura
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider border-r border-gray-100 bg-amber-50 text-amber-600">
                                        Esperado
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider border-r border-gray-100 bg-emerald-50 text-emerald-600">
                                        Real
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                        Diferencia
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">
                                        Estado
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($sesiones as $s)
                                    <tr wire:key="sesion-{{ $s->id }}" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-center border-r border-gray-100 text-sm whitespace-nowrap">
                                            {{ $s->abierta_en ? $s->abierta_en->format('d/m/Y H:i') : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center border-r border-gray-100 text-sm font-bold text-gray-800">
                                            {{ strtoupper($s->abiertaPor->name) }}
                                        </td>
                                        <td class="px-4 py-3 text-center border-r border-gray-100 text-sm whitespace-nowrap">
                                            {{ $s->cerrada_en?->format('d/m/Y H:i') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center border-r border-gray-100 text-sm font-bold text-blue-700 bg-blue-50/50">
                                            S/ {{ number_format($s->monto_apertura, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-center border-r border-gray-100 text-sm font-bold text-amber-700 bg-amber-50/50">
                                            {{ $s->monto_esperado !== null ? 'S/ '.number_format($s->monto_esperado, 2) : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center border-r border-gray-100 text-sm font-bold text-emerald-700 bg-emerald-50/50">
                                            {{ $s->monto_cierre !== null ? 'S/ '.number_format($s->monto_cierre, 2) : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center border-r border-gray-100 text-sm font-bold {{ $s->diferencia === null ? 'text-gray-400' : ($s->diferencia == 0 ? 'text-emerald-600' : 'text-red-600') }}">
                                            {{ $s->diferencia !== null ? 'S/ '.number_format($s->diferencia, 2) : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-center border-r border-gray-100 text-sm">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $s->estado === 'abierta' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                                {{ ucfirst($s->estado) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm whitespace-nowrap">
                                            <div class="inline-flex items-center justify-center gap-1.5">
                                                <div class="relative inline-block group">
                                                    <a href="{{ route('caja.sesion', $s->id) }}"
                                                       class="inline-flex items-center justify-center w-8 h-8 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-900 rounded-lg transition-colors duration-150">
                                                        <i class="fa-solid fa-eye text-xs"></i>
                                                    </a>
                                                    <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:flex flex-col items-center pointer-events-none z-10">
                                                        <span class="relative z-10 p-1.5 text-[10px] font-semibold leading-none text-white whitespace-nowrap bg-gray-800 rounded shadow-md">
                                                            Ver detalles
                                                        </span>
                                                        <div class="w-2 h-2 -mt-1 rotate-45 bg-gray-800"></div>
                                                    </div>
                                                </div>
                                                @if($s->movimientos()->where('metodo_pago','fise')->exists())
                                                    <a href="{{ route('caja.sesion.fise', $s->id) }}"
                                                       class="inline-flex items-center justify-center w-8 h-8 text-amber-700 bg-amber-50 hover:bg-amber-100 hover:text-amber-900 rounded-lg transition-colors duration-150 font-bold text-sm"
                                                       title="Ver solo FISE">
                                                        F
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-3 border-t border-gray-100">
                        {{ $sesiones->links() }}
                    </div>
                @else
                    <div class="px-6 py-16 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">
                            No se encontraron registros de sesiones de caja con los filtros aplicados.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
