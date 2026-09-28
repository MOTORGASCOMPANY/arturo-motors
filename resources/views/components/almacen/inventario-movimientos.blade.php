    @if ($movimientosRecientes->count())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-3">
                <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                    <i class="fas fa-arrows-turn-to-dots text-slate-400"></i>
                    Últimos movimientos de stock
                </h3>
                <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                    Últimos 15
                </span>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">Sede</th>
                            <th class="px-4 py-3 text-center">Tipo</th>
                            <th class="px-4 py-3 text-right">Cantidad</th>
                            <th class="px-4 py-3">Motivo</th>
                            <th class="px-4 py-3">Usuario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($movimientosRecientes as $mov)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3.5 text-xs text-slate-500">{{ $mov['fecha'] }}</td>
                                <td class="px-4 py-3.5 font-medium text-slate-700">{{ $mov['producto'] }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $mov['sede'] }}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center rounded-md border {{ $mov['tipo'] === 'entrada' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-600' }} px-2.5 py-1 text-xs font-bold uppercase tracking-wider">
                                        {{ ucfirst($mov['tipo']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold tabular-nums {{ $mov['tipo'] === 'entrada' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $mov['tipo'] === 'entrada' ? '+' : '-' }}{{ $mov['cantidad'] }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">{{ $mov['motivo'] ?: '—' }}</td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">{{ $mov['usuario'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
