<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-amber-600"></i> Control FISE
            </h2>
            <p class="text-xs text-gray-500 mt-1">Seguimiento de pagos por financiamiento FISE</p>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-medium">Total Placas</p>
            <p class="text-lg font-bold text-gray-800">{{ $totales->total ?? 0 }}</p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-medium">Pendientes</p>
            <p class="text-lg font-bold text-amber-600">{{ $totales->pendientes ?? 0 }}</p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-medium">Parciales</p>
            <p class="text-lg font-bold text-blue-600">{{ $totales->parciales ?? 0 }}</p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-medium">Pagados</p>
            <p class="text-lg font-bold text-emerald-600">{{ $totales->pagados ?? 0 }}</p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-medium">Total Monto</p>
            <p class="text-sm font-bold text-gray-800">S/ {{ number_format($totales->total_monto ?? 0, 2) }}</p>
        </div>
        <div class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center">
            <p class="text-xs text-gray-500 font-medium">Total Pagado</p>
            <p class="text-sm font-bold text-emerald-600">S/ {{ number_format($totales->total_pagado ?? 0, 2) }}</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expandir: false }">
        <div class="p-4">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <x-label for="buscar" value="Buscar" class="text-gray-600 font-semibold mb-1 text-xs" />
                    <x-input id="buscar" type="text" wire:model.live="buscar" placeholder="Placa o cliente..." class="w-full" />
                </div>
                <div class="w-36">
                    <x-label for="estado" value="Estado" class="text-gray-600 font-semibold mb-1 text-xs" />
                    <select id="estado" wire:model.live="estado"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                        <option value="todos">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="parcial">Parcial</option>
                        <option value="pagado">Pagado</option>
                    </select>
                </div>
                <button @click="expandir = !expandir"
                    class="px-3 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                    <i class="fas fa-sliders-h"></i>
                    <span x-text="expandir ? 'Menos' : 'Más filtros'"></span>
                    <i class="fas" :class="expandir ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
                <button wire:click="limpiarFiltros"
                    class="px-3 py-2 text-xs font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center gap-1.5">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
            </div>

            <!-- Filtros avanzados -->
            <div x-show="expandir" x-transition class="mt-4 pt-4 border-t border-gray-100">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    <div>
                        <x-label for="fechaDesde" value="Desde" class="text-gray-500 text-xs mb-1" />
                        <input id="fechaDesde" type="date" wire:model.live="fechaDesde"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm" />
                    </div>
                    <div>
                        <x-label for="fechaHasta" value="Hasta" class="text-gray-500 text-xs mb-1" />
                        <input id="fechaHasta" type="date" wire:model.live="fechaHasta"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm" />
                    </div>
                    <div>
                        <x-label for="montoMin" value="Monto mín. (S/)" class="text-gray-500 text-xs mb-1" />
                        <input id="montoMin" type="number" step="0.01" min="0" wire:model.live="montoMin"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm" placeholder="0" />
                    </div>
                    <div>
                        <x-label for="montoMax" value="Monto máx. (S/)" class="text-gray-500 text-xs mb-1" />
                        <input id="montoMax" type="number" step="0.01" min="0" wire:model.live="montoMax"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm" placeholder="0" />
                    </div>
                    <div>
                        <x-label for="tecnicoId" value="Técnico" class="text-gray-500 text-xs mb-1" />
                        <select id="tecnicoId" wire:model.live="tecnicoId"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                            <option value="">Todos</option>
                            @foreach ($tecnicos as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-label for="diasPendientes" value="Pendientes +días" class="text-gray-500 text-xs mb-1" />
                        <select id="diasPendientes" wire:model.live="diasPendientes"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm">
                            <option value="0">Todos</option>
                            <option value="7">+7 días</option>
                            <option value="15">+15 días</option>
                            <option value="30">+30 días</option>
                            <option value="60">+60 días</option>
                            <option value="90">+90 días</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer px-3 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors w-full justify-center">
                            <input type="checkbox" wire:model.live="soloSaldo"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="text-xs font-semibold">Solo con saldo</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($pagos->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Placa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Cliente</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Pagado</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Saldo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100">Estado</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($pagos as $fp)
                            @php
                                $saldo = (float) $fp->monto_total - (float) $fp->monto_pagado;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-center font-bold text-gray-800 border-r border-gray-100">
                                    {{ $fp->serviceOrder->vehiculo->placa ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 border-r border-gray-100">
                                    {{ $fp->serviceOrder->cliente->nombre ?? '' }} {{ $fp->serviceOrder->cliente->apellido ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-800 border-r border-gray-100">
                                    S/ {{ number_format($fp->monto_total, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold text-emerald-600 border-r border-gray-100">
                                    S/ {{ number_format($fp->monto_pagado, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center font-semibold {{ $saldo > 0 ? 'text-red-600' : 'text-emerald-600' }} border-r border-gray-100">
                                    S/ {{ number_format($saldo, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center border-r border-gray-100">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ match($fp->estado) {
                                            'pagado' => 'bg-emerald-100 text-emerald-700',
                                            'parcial' => 'bg-blue-100 text-blue-700',
                                            default => 'bg-amber-100 text-amber-700',
                                        } }}">
                                        {{ ucfirst($fp->estado) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($fp->estado !== 'pagado')
                                        <div class="inline-flex items-center gap-1.5">
                                            <button wire:click="abrirModalPago({{ $fp->id }})"
                                                class="px-2.5 py-1 text-[11px] font-semibold bg-indigo-100 text-indigo-700 hover:bg-indigo-200 rounded-lg transition-colors"
                                                title="Registrar pago parcial">
                                                <i class="fas fa-money-bill-wave mr-0.5"></i>Pago
                                            </button>
                                            <button onclick="confirmarMarcarPagado({{ $fp->id }}, '{{ $fp->serviceOrder->vehiculo->placa ?? 'N/A' }}')"
                                                class="px-2.5 py-1 text-[11px] font-semibold bg-emerald-100 text-emerald-700 hover:bg-emerald-200 rounded-lg transition-colors"
                                                title="Marcar pagado completo">
                                                <i class="fas fa-check-double mr-0.5"></i>Pagado
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs text-emerald-600 font-semibold">
                                            <i class="fas fa-check-circle mr-1"></i>Completo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $pagos->links() }}
            </div>
        @else
            <div class="px-6 py-10 text-center">
                <div class="w-16 h-16 bg-amber-100 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-file-invoice-dollar text-2xl"></i>
                </div>
                <p class="text-gray-600 font-semibold">No hay registros FISE</p>
                <p class="text-xs text-gray-400 mt-1">Aún no se han registrado conversiones con método de pago FISE.</p>
            </div>
        @endif
    </div>

    <!-- Modal Registrar Pago -->
    @if ($modalAbierto)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('modalAbierto', false)"></div>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fas fa-money-bill-wave text-emerald-600"></i> Registrar Pago FISE
                            </h3>
                            <button wire:click="$set('modalAbierto', false)" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4 mb-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Monto Total:</span>
                                <span class="font-bold text-gray-800">S/ {{ number_format($montoTotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Ya Pagado:</span>
                                <span class="font-bold text-emerald-600">S/ {{ number_format($montoPagado, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm border-t border-gray-200 pt-2">
                                <span class="text-gray-500 font-semibold">Saldo Pendiente:</span>
                                <span class="font-bold text-red-600">S/ {{ number_format($montoTotal - $montoPagado, 2) }}</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <x-label for="montoPago" value="Monto a pagar (S/)" class="text-gray-700 font-medium text-xs" />
                                <x-input id="montoPago" type="number" step="0.01" min="0.01" :max="$montoTotal - $montoPagado"
                                    wire:model="montoPago" class="w-full mt-1" placeholder="0.00" />
                                <x-input-error for="montoPago" class="mt-1" />
                            </div>
                            <div>
                                <x-label for="fechaPago" value="Fecha de pago" class="text-gray-700 font-medium text-xs" />
                                <x-input id="fechaPago" type="date" wire:model="fechaPago" class="w-full mt-1" />
                                <x-input-error for="fechaPago" class="mt-1" />
                            </div>
                            <div>
                                <x-label for="observaciones" value="Observaciones (opcional)" class="text-gray-700 font-medium text-xs" />
                                <textarea id="observaciones" wire:model="observaciones" rows="2"
                                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-0 text-sm"
                                    placeholder="Nota sobre el pago..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                        <button wire:click="$set('modalAbierto', false)"
                            class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button onclick="confirmarRegistrarPago()"
                            class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                            <i class="fas fa-save mr-1"></i> Registrar Pago
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Scripts SweetAlert2 -->
    <script>
        function confirmarMarcarPagado(id, placa) {
            Swal.fire({
                title: '¿Marcar como pagado?',
                html: `Se marcará la placa <strong>${placa}</strong> como pagado completo.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, marcar pagado',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('marcarPagado', id);
                }
            });
        }

        function confirmarRegistrarPago() {
            Swal.fire({
                title: '¿Registrar este pago?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('registrarPago');
                }
            });
        }

        // Loading durante_save
        Livewire.on('registrarPago', function() {
            Swal.showLoading();
        });
    </script>
</div>
