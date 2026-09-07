<x-app-layout>
    <x-slot name="title">Detalle de Sesión de Caja - Convencionales</x-slot>

    <div class="max-w-6xl mx-auto py-8 space-y-6">

        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                        <i class="fa-solid fa-cash-register text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Detalle Sesión #{{ $sesion->id }}</h1>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Métodos convencionales &nbsp;·&nbsp;
                            {{ $sesion->abierta_en->format('d/m/Y H:i') }} 
                            @if($sesion->cerrada_en) - {{ $sesion->cerrada_en->format('d/m/Y H:i') }} @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('caja.sesion.fise', $sesion) }}"
                       class="text-amber-600 hover:text-amber-800 font-bold px-3 py-1.5 
                              border border-amber-300 rounded-lg text-sm transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-landmark"></i> Ver FISE
                    </a>
                    <a href="{{ route('caja.historial') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg px-4 py-2 transition-colors border border-gray-200 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left text-sm"></i> Volver al historial
                    </a>
                </div>
            </div>

            {{-- Badge estado --}}
            <div class="mt-4 flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                      {{ $sesion->estado === 'abierta' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($sesion->estado) }}
                </span>
                @if($sesion->cerrada_en)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                        Cerrada por {{ $sesion->cerradaPor->name ?? '—' }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Resumen tarjetas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                <span class="text-xs font-bold text-gray-500 uppercase">Efectivo</span>
                <p class="text-2xl font-bold text-emerald-600 mt-1">S/ {{ number_format($resumen['efectivo'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                <span class="text-xs font-bold text-gray-500 uppercase">Tarjeta</span>
                <p class="text-2xl font-bold text-blue-600 mt-1">S/ {{ number_format($resumen['tarjeta'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                <span class="text-xs font-bold text-gray-500 uppercase">Yape / Transfer</span>
                <p class="text-2xl font-bold text-indigo-600 mt-1">S/ {{ number_format($resumen['transferencia'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                <span class="text-xs font-bold text-gray-500 uppercase">Egresos</span>
                <p class="text-2xl font-bold text-red-600 mt-1">S/ {{ number_format($resumen['egresos'], 2) }}</p>
            </div>
        </div>

        {{-- Totales --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <span class="text-xs font-bold text-gray-500 uppercase block">Total Ingresos</span>
                    <p class="text-xl font-bold text-gray-900">S/ {{ number_format($resumen['total_ingresos'], 2) }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <span class="text-xs font-bold text-gray-500 uppercase block">Total Egresos</span>
                    <p class="text-xl font-bold text-red-600">S/ {{ number_format($resumen['egresos'], 2) }}</p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg">
                    <span class="text-xs font-bold text-emerald-700 uppercase block">Neto Caja</span>
                    <p class="text-xl font-bold text-emerald-700">S/ {{ number_format($resumen['neto'], 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Tabla de movimientos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="p-6 border-b border-gray-200/60 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Movimientos ({{ $movimientos->count() }})</h3>
                <span class="text-xs text-gray-500">Sin FISE</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha / Hora</th>
                            <th class="px-4 py-3 text-left">Concepto</th>
                            <th class="px-4 py-3 text-center">Método</th>
                            <th class="px-4 py-3 text-left">Cliente / Vehículo</th>
                            <th class="px-4 py-3 text-right">Monto</th>
                            <th class="px-4 py-3 text-center">Tipo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($movimientos as $m)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $m->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $m->concepto }}</p>
                                    @if($m->serviceOrder)
                                        <p class="text-xs text-gray-500">OC #{{ $m->serviceOrder->id }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @switch($m->metodo_pago)
                                        @case('efectivo')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                <i class="fa-solid fa-money-bill-wave mr-1"></i> Efectivo
                                            </span>
                                            @break
                                        @case('tarjeta')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                <i class="fa-solid fa-credit-card mr-1"></i> Tarjeta
                                            </span>
                                            @break
                                        @case('transferencia')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                                <i class="fa-solid fa-phone-missed mr-1"></i> Transfer
                                            </span>
                                            @break
                                        @case('otro')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                                <i class="fa-solid fa-ellipsis-h mr-1"></i> Otro
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-3">
                                    @if($m->serviceOrder)
                                        <p class="text-xs text-gray-600">
                                            {{ $m->serviceOrder->cliente->nombre ?? '' }} 
                                            {{ $m->serviceOrder->cliente->apellido ?? '' }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $m->serviceOrder->vehiculo->placa ?? '' }} · 
                                            {{ $m->serviceOrder->vehiculo->marca ?? '' }} 
                                            {{ $m->serviceOrder->vehiculo->modelo ?? '' }}
                                        </p>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold 
                                    {{ $m->tipo === 'ingreso' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $m->tipo === 'ingreso' ? '+' : '-' }} S/ {{ number_format($m->monto, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold 
                                        {{ $m->tipo === 'ingreso' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($m->tipo) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    No hay movimientos convencionales en esta sesión.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Footer info --}}
        <div class="bg-gray-50 rounded-xl p-4 text-xs text-gray-500 text-center">
            Caja física al cierre: <strong class="text-gray-900">S/ {{ number_format($sesion->monto_cierre ?? 0, 2) }}</strong>
            &nbsp;|&nbsp; Diferencia: 
            <strong class="{{ ($sesion->diferencia ?? 0) == 0 ? 'text-emerald-600' : 'text-red-600' }}">
                S/ {{ number_format($sesion->diferencia ?? 0, 2) }}
            </strong>
        </div>

    </div>
</x-app-layout>