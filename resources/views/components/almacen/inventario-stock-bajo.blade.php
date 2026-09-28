    @if ($stockBajo->count())
        <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden">
            <div class="p-5 border-b border-red-100 bg-red-50/50 flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-amber-500"></i>
                <h3 class="font-bold text-slate-700 text-base">
                    Stock bajo{{ $filtroSede ? ' · ' . optional($sedes->firstWhere('id', $filtroSede))->nombre : '' }}
                </h3>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3 text-right">Disponible</th>
                            <th class="px-4 py-3 text-right">Mínimo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($stockBajo as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3.5 font-medium text-slate-700">{{ $p->nombre }}</td>
                                <td class="px-4 py-3.5 text-right text-red-600 font-bold tabular-nums">{{ $p->stockSueltoEnSede($sedeStock) }}</td>
                                <td class="px-4 py-3.5 text-right text-slate-500 tabular-nums">{{ $p->stock_minimo }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
