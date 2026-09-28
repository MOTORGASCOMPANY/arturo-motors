    @if ($kitsInstalados->isNotEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-3">
                <h3 class="font-bold text-slate-700 text-base flex items-center gap-2">
                    <i class="fas fa-car text-slate-400"></i>
                    Kits instalados por vehículo
                </h3>
                <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-full text-xs font-bold shadow-sm">
                    {{ $kitsInstalados->count() }} conversiones
                </span>
            </div>
            <div class="overflow-x-auto max-h-[600px] overflow-y-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider sticky top-0 z-10 border-b border-slate-200 shadow-sm">
                        <tr>
                            <th class="px-4 py-3">Cliente</th>
                            <th class="px-4 py-3">Vehículo</th>
                            <th class="px-4 py-3">Placa</th>
                            <th class="px-4 py-3">Kit</th>
                            <th class="px-4 py-3">Gen.</th>
                            <th class="px-4 py-3">Items Serializados</th>
                            <th class="px-4 py-3 text-center">Técnico</th>
                            <th class="px-4 py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($kitsInstalados as $ki)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3.5 font-medium text-slate-700">{{ $ki['cliente'] }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $ki['vehiculo'] }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="border border-slate-200 bg-slate-50 px-2 py-1 rounded text-xs font-mono font-bold text-slate-800">
                                        {{ $ki['placa'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-600">{{ $ki['kit']->producto->nombre }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center rounded-md border border-purple-200 bg-purple-50 px-2 py-1 text-xs text-purple-700 font-bold">
                                        {{ $ki['generacion'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @forelse ($ki['seriales'] as $s)
                                        <div class="text-xs leading-relaxed bg-slate-50 rounded-md px-2 py-1 mb-1 last:mb-0">
                                            <span class="font-semibold text-slate-700">{{ $s['nombre'] }}:</span>
                                            <span class="font-mono text-slate-500">{{ $s['serie'] }}</span>
                                        </div>
                                    @empty
                                        <span class="text-xs text-slate-300">—</span>
                                    @endforelse
                                </td>
                                <td class="px-4 py-3.5 text-center text-xs text-slate-500">{{ $ki['tecnico'] }}</td>
                                <td class="px-4 py-3.5 text-xs text-slate-400">{{ $ki['fecha'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
