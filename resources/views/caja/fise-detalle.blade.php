<x-app-layout>
    <x-slot name="title">Detalle FISE - Sesión de Caja</x-slot>

    <div class="max-w-6xl mx-auto py-8 space-y-6">

        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <i class="fa-solid fa-landmark text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">FISE - Sesión #{{ $sesion->id }}</h1>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Financiamiento Estatal &nbsp;·&nbsp;
                            {{ $sesion->abierta_en->format('d/m/Y H:i') }} 
                            @if($sesion->cerrada_en) - {{ $sesion->cerrada_en->format('d/m/Y H:i') }} @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('caja.sesion.detalle', $sesion) }}"
                       class="text-emerald-600 hover:text-emerald-800 font-medium px-3 py-1.5 
                              border border-emerald-300 rounded-lg text-sm transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-cash-register"></i> Ver Convencionales
                    </a>
                    <a href="{{ route('caja.historial') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg px-4 py-2 transition-colors border border-gray-200 flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left text-sm"></i> Volver al historial
                    </a>
                </div>
            </div>

            {{-- Badge estado --}}
            <div class="mt-4 flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                    SOLO FISE
                </span>
                @if($sesion->cerrada_en)
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                        Cerrada por {{ $sesion->cerradaPor->name ?? '—' }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Resumen FISE --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                <span class="text-xs font-bold text-gray-500 uppercase">Total FISE</span>
                <p class="text-2xl font-bold text-amber-700 mt-1">S/ {{ number_format($resumen['total_fise'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                <span class="text-xs font-bold text-gray-500 uppercase">Cobrado</span>
                <p class="text-2xl font-bold text-emerald-600 mt-1">S/ {{ number_format($resumen['cobrado'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                <span class="text-xs font-bold text-gray-500 uppercase">Pendiente</span>
                <p class="text-2xl font-bold text-red-600 mt-1">S/ {{ number_format($resumen['pendiente'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
                <span class="text-xs font-bold text-gray-500 uppercase">Cantidad</span>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $resumen['cantidad'] }}</p>
            </div>
        </div>

        {{-- Resumen de Caja (con FISE incluido) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
            <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-cash-register text-indigo-600"></i> Resumen de Caja de esta Sesión
            </h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs text-blue-600 font-medium">Apertura</p>
                    <p class="text-sm font-bold text-blue-700">S/ {{ number_format($caja['apertura'], 2) }}</p>
                </div>
                <div class="text-center p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                    <p class="text-xs text-emerald-600 font-medium">Ingresos</p>
                    <p class="text-sm font-bold text-emerald-700">S/ {{ number_format($caja['ingresos'], 2) }}</p>
                </div>
                <div class="text-center p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-xs text-red-600 font-medium">Egresos</p>
                    <p class="text-sm font-bold text-red-700">S/ {{ number_format($caja['egresos'], 2) }}</p>
                </div>
                <div class="text-center p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                    <p class="text-xs text-indigo-600 font-medium">Esperado</p>
                    <p class="text-sm font-bold text-indigo-700">S/ {{ number_format($caja['monto_esperado'], 2) }}</p>
                </div>
                @if($caja['monto_cierre'] !== null)
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-600 font-medium">Cierre</p>
                    <p class="text-sm font-bold text-gray-700">S/ {{ number_format($caja['monto_cierre'], 2) }}</p>
                </div>
                <div class="text-center p-3 {{ $caja['diferencia'] == 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }} rounded-lg border">
                    <p class="text-xs {{ $caja['diferencia'] == 0 ? 'text-emerald-600' : 'text-amber-600' }} font-medium">Diferencia</p>
                    <p class="text-sm font-bold {{ $caja['diferencia'] == 0 ? 'text-emerald-700' : 'text-amber-700' }}">S/ {{ number_format($caja['diferencia'], 2) }}</p>
                </div>
                @endif
            </div>
            {{-- Totales --}}
            <div class="mt-4 pt-3 border-t border-gray-200 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-600">Total Caja (convencionales):</span>
                    <span class="text-sm font-bold text-gray-800">S/ {{ number_format($caja['caja_convencional'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-amber-600">Total FISE:</span>
                    <span class="text-sm font-bold text-amber-700">S/ {{ number_format($resumen['total_fise'], 2) }}</span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                    <span class="text-base font-bold text-gray-800">Total Caja (convencionales + FISE):</span>
                    <span class="text-lg font-bold text-indigo-700">S/ {{ number_format($caja['ingresos'], 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Detalle por estado --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-emerald-200/80 p-5">
                <h4 class="text-xs font-bold text-emerald-700 uppercase mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-check-circle"></i> Cobrados ({{ $resumen['cantidad_cobrada'] }})
                </h4>
                <p class="text-xl font-bold text-emerald-600">S/ {{ number_format($resumen['cobrado'], 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-red-200/80 p-5">
                <h4 class="text-xs font-bold text-red-700 uppercase mb-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-clock"></i> Pendientes ({{ $resumen['cantidad_pendiente'] }})
                </h4>
                <p class="text-xl font-bold text-red-600">S/ {{ number_format($resumen['pendiente'], 2) }}</p>
            </div>
        </div>

        {{-- Tabla de movimientos FISE --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="p-6 border-b border-gray-200/60 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Movimientos FISE ({{ $movimientos->count() }})</h3>
                <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">SOLO FISE</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Fecha / Hora</th>
                            <th class="px-4 py-3 text-left">Conversión</th>
                            <th class="px-4 py-3 text-left">Cliente / Vehículo</th>
                            <th class="px-4 py-3 text-right">Monto FISE</th>
                            <th class="px-4 py-3 text-center">Estado Cobro</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($movimientos as $m)
                            @php
                                $fisePago = $fisePagos->get($m->service_order_id);
                                $estado = $fisePago->estado ?? 'sin_registro';
                                $montoPagado = $fisePago->monto_pagado ?? 0;
                            @endphp
                            <tr class="hover:bg-amber-50/30">
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                    {{ $m->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $m->concepto }}</p>
                                    @if($m->serviceOrder)
                                        <p class="text-xs text-gray-500">OC #{{ $m->serviceOrder->id }}</p>
                                    @endif
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
                                <td class="px-4 py-3 text-right font-bold text-amber-700">
                                    S/ {{ number_format($m->monto, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @switch($estado)
                                        @case('pagado')
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 flex items-center justify-center gap-1 mx-auto">
                                                <i class="fa-solid fa-check"></i> Pagado
                                            </span>
                                            @break
                                        @case('pendiente')
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 flex items-center justify-center gap-1 mx-auto">
                                                <i class="fa-solid fa-clock"></i> Pendiente
                                            </span>
                                            @break
                                        @default
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 flex items-center justify-center gap-1 mx-auto">
                                                <i class="fa-solid fa-circle-question"></i> Sin registro
                                            </span>
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                    No hay movimientos FISE en esta sesión.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Acceso rápido a auditoría FISE --}}
        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-landmark text-amber-600 text-lg"></i>
                    <span class="text-sm font-medium text-amber-800">Ver auditoría completa FISE</span>
                </div>
                <a href="{{ route('caja.fise') }}" 
                   class="bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg px-4 py-2 transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-chart-bar"></i> Ir a Auditoría FISE
                </a>
            </div>
        </div>

        {{-- Footer info --}}
        <div class="bg-gray-50 rounded-xl p-4 text-xs text-gray-500 text-center">
            Total FISE sesión: <strong class="text-amber-700">S/ {{ number_format($resumen['total_fise'], 2) }}</strong>
            &nbsp;|&nbsp; Cobrado: <strong class="text-emerald-600">S/ {{ number_format($resumen['cobrado'], 2) }}</strong>
            &nbsp;|&nbsp; Pendiente: <strong class="text-red-600">S/ {{ number_format($resumen['pendiente'], 2) }}</strong>
        </div>

    </div>
</x-app-layout>