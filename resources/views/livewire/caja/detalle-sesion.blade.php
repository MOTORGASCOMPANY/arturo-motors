<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">

    <a href="{{ route('caja.historial') }}" class="text-sm text-blue-600 font-semibold">← Volver al historial</a>

    {{-- Resumen de la sesión --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            Sesión #{{ $sesion->id }} — {{ $sesion->abierta_en->format('d/m/Y') }}
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-500 text-xs">Apertura</p>
                <p class="font-semibold">S/ {{ number_format($sesion->monto_apertura, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Ingresos</p>
                <p class="font-semibold text-emerald-600">S/ {{ number_format($totalIngresos, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Egresos</p>
                <p class="font-semibold text-red-600">S/ {{ number_format($totalEgresos, 2) }}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs">Diferencia al cierre</p>
                <p class="font-semibold {{ $sesion->diferencia === null ? 'text-gray-400' : ($sesion->diferencia == 0 ? 'text-emerald-600' : 'text-red-600') }}">
                    {{ $sesion->diferencia !== null ? 'S/ '.number_format($sesion->diferencia, 2) : 'Sesión aún abierta' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Movimientos --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="p-4 border-b border-gray-200/60 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Movimientos</h3>
            <select wire:model.live="tipo" class="text-sm rounded-lg border-gray-300">
                <option value="todos">Todos</option>
                <option value="ingreso">Solo ingresos</option>
                <option value="egreso">Solo egresos</option>
            </select>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Hora</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Concepto</th>
                    <th class="px-4 py-3 text-left">Usuario</th>
                    <th class="px-4 py-3 text-right">Monto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($movimientos as $m)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3">{{ $m->created_at->format('H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $m->tipo === 'ingreso' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($m->tipo) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $m->concepto }}</td>
                        <td class="px-4 py-3">{{ $m->usuario->name }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $m->tipo === 'ingreso' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $m->tipo === 'ingreso' ? '+' : '−' }} S/ {{ number_format($m->monto, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Sin movimientos aún.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-200/60">
            {{ $movimientos->links() }}
        </div>
    </div>
</div>