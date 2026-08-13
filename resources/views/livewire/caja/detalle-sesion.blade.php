<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
    <!-- Botón Volver (Estilo Pill con Hover) -->
    <div>
        <a href="{{ route('caja.historial') }}" 
           class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
            <i class="fas fa-arrow-left text-gray-500"></i> Volver al historial
        </a>
    </div>
    <!-- Resumen de la sesión (Rediseñado) -->
    <div class="bg-gray-200 rounded-2xl p-6 border border-gray-200/80 space-y-5">
        <!-- Encabezado de Sesión -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-300/60 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-sm font-bold text-lg">
                    <i class="fas fa-cash-register"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800 leading-tight">
                        Sesión #{{ $sesion->id }}
                    </h2>
                    <p class="text-xs text-gray-600 flex items-center gap-1">
                        <i class="far font-normal fa-calendar-alt text-gray-500"></i>
                        Apertura: {{ $sesion->abierta_en->format('d/m/Y - H:i') }} hrs
                    </p>
                </div>
            </div>

            <!-- Badge Estado de Sesión -->
            <div>
                @if ($sesion->diferencia !== null)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-300 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-gray-500"></span> Sesión Cerrada
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Sesión Activa
                    </span>
                @endif
            </div>
        </div>

        <!-- Tarjetas KPI de Métricas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Apertura -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                    <i class="fas fa-box-open text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Apertura</p>
                    <p class="text-lg font-bold text-gray-800 truncate">
                        S/ {{ number_format($sesion->monto_apertura, 2) }}
                    </p>
                </div>
            </div>

            <!-- Ingresos -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg shrink-0">
                    <i class="fas fa-arrow-down text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Ingresos</p>
                    <p class="text-lg font-bold text-emerald-600 truncate">
                        + S/ {{ number_format($totalIngresos, 2) }}
                    </p>
                </div>
            </div>

            <!-- Egresos -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
                <div class="p-3 bg-red-50 text-red-600 rounded-lg shrink-0">
                    <i class="fas fa-arrow-up text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Egresos</p>
                    <p class="text-lg font-bold text-red-600 truncate">
                        - S/ {{ number_format($totalEgresos, 2) }}
                    </p>
                </div>
            </div>

            <!-- Diferencia o Resultado -->
            <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
                <div class="p-3 {{ $sesion->diferencia === null ? 'bg-gray-100 text-gray-500' : ($sesion->diferencia == 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600') }} rounded-lg shrink-0">
                    <i class="fas {{ $sesion->diferencia === null ? 'fa-clock' : ($sesion->diferencia == 0 ? 'fa-check-circle' : 'fa-exclamation-triangle') }} text-base"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Diferencia cierre</p>
                    <p class="text-lg font-bold truncate {{ $sesion->diferencia === null ? 'text-gray-400' : ($sesion->diferencia == 0 ? 'text-emerald-600' : 'text-red-600') }}">
                        @if($sesion->diferencia !== null)
                            S/ {{ number_format($sesion->diferencia, 2) }}
                        @else
                            <span class="text-xs font-medium text-gray-500">En curso</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Movimientos -->
    <div class="bg-gray-200 p-6 rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6">
            <!-- Contenedor de Título y Subtítulo a la izquierda -->
            <div class="px-2">
                <h2 class="text-gray-600 font-semibold">
                    <i class="fas fa-exchange-alt mr-2"></i>Movimientos
                </h2>
                <span class="text-xs">Todos los movimientos de caja</span>
            </div>
            <!-- Filtro a la derecha -->
            <div class="flex items-center bg-white border border-gray-300 p-1 rounded-lg shadow-sm">
                <x-label value="Tipos:" />
                <select wire:model.live="tipo"
                    class="border-none bg-transparent text-gray-700 text-sm focus:ring-0 focus:outline-none cursor-pointer">
                    <option value="todos">Todos</option>
                    <option value="ingreso">Solo ingresos</option>
                    <option value="egreso">Solo egresos</option>
                </select>
            </div>
        </div>

        <!-- Tabla -->
        @if ($movimientos->count())
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal rounded-md overflow-hidden">
                    <thead>
                        <tr>
                            <th
                                class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                Hora</th>
                            <th
                                class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                Tipo</th>
                            <th
                                class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                Concepto</th>
                            <th
                                class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                Usuario</th>
                            <th
                                class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movimientos as $m)
                            <tr>
                                <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                    {{ $m->created_at->format('H:i') }}</td>
                                <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-semibold
                                        {{ $m->tipo === 'ingreso' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($m->tipo) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">{{ $m->concepto }}
                                </td>
                                <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">{{ $m->usuario->name }}
                                </td>
                                <td
                                    class="px-4 py-3 border-b border-gray-200 bg-white text-sm text-right font-semibold {{ $m->tipo === 'ingreso' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $m->tipo === 'ingreso' ? '+' : '−' }} S/ {{ number_format($m->monto, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-200/60">
                {{ $movimientos->links() }}
            </div>
        @else
            <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                No hay movimientos registrados.
            </div>
        @endif
    </div>
</div>
