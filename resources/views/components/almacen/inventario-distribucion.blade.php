    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                <i class="fas fa-table-cells text-slate-400"></i>
                Distribución de stock por sede
            </h3>
            <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                {{ count($distribucion) }} productos
            </span>
        </div>
        <div class="overflow-x-auto max-h-[420px] overflow-y-auto custom-scrollbar">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider sticky top-0 z-10 border-b border-slate-200 shadow-sm">
                    <tr>
                        <th class="px-4 py-3">Producto</th>
                        <th class="px-4 py-3">Categoría</th>
                        @foreach ($sedes as $s)
                            <th class="px-4 py-3 text-right">{{ $s->nombre }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-right font-bold text-slate-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($distribucion as $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 font-medium text-slate-700">{{ $row['producto']->nombre }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-1 text-xs text-slate-600 font-semibold">
                                    {{ $row['producto']->categoria->nombre }}
                                </span>
                            </td>
                            @foreach ($sedes as $s)
                                <td class="px-4 py-3.5 text-right tabular-nums {{ ($row['por_sede'][$s->id] ?? 0) > 0 ? 'text-slate-700 font-medium' : 'text-slate-300' }}">
                                    {{ number_format($row['por_sede'][$s->id] ?? 0) }}
                                </td>
                            @endforeach
                            <td class="px-4 py-3.5 text-right font-bold text-slate-800 tabular-nums">{{ number_format($row['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $sedes->count() + 3 }}" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-inbox text-3xl mb-3 block text-slate-300"></i>
                                <span class="font-medium">Sin resultados para los filtros seleccionados.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
