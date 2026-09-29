@props(['detalleOrdenes', 'filtroBadge'])

{{-- Tabla detalle de conversiones: 7 columnas + fila desplegable al hacer clic --}}
<div class="bg-white rounded-card shadow-card border border-gray-200 overflow-hidden font-inter">

    {{-- Encabezado --}}
    <div class="px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <h3 class="font-bold text-gray-700 text-base flex items-center gap-2">
            <i class="fas fa-list-check text-gray-400"></i>
            Detalle de conversiones
        </h3>
        <span class="bg-gray-100 text-gray-600 py-1.5 px-3.5 rounded-full text-xs font-semibold">
            {{ count($detalleOrdenes) }} órdenes · {{ $filtroBadge }}
        </span>
    </div>

    <div class="max-h-[560px] overflow-auto custom-scrollbar">
        <table class="w-full min-w-[760px] text-sm text-left border-separate border-spacing-0">
            <thead class="sticky top-0 z-10">
                <tr class="bg-gray-100 text-gray-500 text-xs font-semibold">
                    <th class="w-12 pl-5 pr-2 py-3.5 border-b border-gray-200"></th>
                    <th class="px-4 py-3.5 w-16 border-b border-gray-200">#</th>
                    <th class="px-4 py-3.5 border-b border-gray-200">Cliente</th>
                    <th class="px-4 py-3.5 border-b border-gray-200">Placa</th>
                    <th class="px-4 py-3.5 border-b border-gray-200">Kit</th>
                    <th class="px-4 py-3.5 whitespace-nowrap border-b border-gray-200">Inicio</th>
                    <th class="px-4 py-3.5 text-center border-b border-gray-200">Duración</th>
                    <th class="px-4 py-3.5 pr-6 text-center border-b border-gray-200">Estado</th>
                </tr>
            </thead>

            @forelse ($detalleOrdenes as $d)
                @php
                    [$estadoTexto, $estadoCls] = match ($d['estado']) {
                        'conversion_completada' => ['Completada', 'bg-brand-50 text-brand-700 border-brand-200'],
                        'entregado' => ['Entregada', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        'en_conversion' => ['En conversión', 'bg-amber-50 text-amber-700 border-amber-200'],
                        'aprobado_conversion' => ['Aprobada', 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                        'listo_para_entrega' => ['Lista para entrega', 'bg-cyan-50 text-cyan-700 border-cyan-200'],
                        'en_evaluacion' => ['En evaluación', 'bg-slate-100 text-slate-600 border-slate-300'],
                        default => [ucfirst(str_replace('_', ' ', $d['estado'])), 'bg-white text-gray-500 border-gray-200'],
                    };
                    $zebra = $loop->odd ? 'bg-white' : 'bg-gray-50/70';
                @endphp

                <tbody class="detalle-orden">
                    {{-- Fila principal (clic para desplegar) --}}
                    <tr class="detalle-main {{ $zebra }} hover:bg-gray-100/70 transition-colors cursor-pointer focus:outline-none focus-visible:bg-gray-100"
                        tabindex="0" role="button" aria-expanded="false"
                        aria-label="Ver detalle de la orden {{ $d['orden']->id }}">
                        <td class="pl-5 pr-2 py-4 border-b border-gray-100 text-gray-400">
                            <i class="detalle-chevron fas fa-chevron-right text-xs inline-block transition-transform duration-200"></i>
                        </td>
                        <td class="px-4 py-4 border-b border-gray-100 font-bold text-gray-700 tabular-nums">{{ $d['orden']->id }}</td>
                        <td class="px-4 py-4 border-b border-gray-100 font-medium text-gray-700">{{ $d['cliente'] }}</td>
                        <td class="px-4 py-4 border-b border-gray-100">
                            <span class="inline-block rounded-md border border-gray-200 bg-white px-2.5 py-1 text-xs font-mono font-semibold text-gray-700 whitespace-nowrap">
                                {{ $d['placa'] }}
                            </span>
                        </td>
                        <td class="px-4 py-4 border-b border-gray-100 text-gray-600 whitespace-nowrap">{{ $d['kit_nombre'] }}</td>
                        <td class="px-4 py-4 border-b border-gray-100 text-gray-500 text-xs whitespace-nowrap tabular-nums">{{ $d['fecha_inicio'] ?? '—' }}</td>
                        <td class="px-4 py-4 border-b border-gray-100 text-center text-gray-600 font-medium tabular-nums whitespace-nowrap">
                            @if ($d['duracion_horas'] === null)
                                —
                            @elseif ($d['duracion_horas'] < 0)
                                {{-- fin anterior al inicio: dato inconsistente, no se muestra como duración --}}
                                —
                            @elseif ($d['duracion_horas'] < 1)
                                {{ $d['duracion_min'] }} min
                            @else
                                {{ $d['duracion_horas'] }} h
                            @endif
                        </td>
                        <td class="px-4 py-4 pr-6 border-b border-gray-100 text-center">
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold whitespace-nowrap {{ $estadoCls }}">
                                {{ $estadoTexto }}
                            </span>
                        </td>
                    </tr>

                    {{-- Fila desplegable --}}
                    <tr class="detalle-extra hidden">
                        <td colspan="8" class="px-6 py-6 bg-gray-50 border-b border-gray-200">
                            <div class="grid gap-8 md:grid-cols-2 md:pl-9">

                                {{-- Items serializados --}}
                                <section>
                                    <h4 class="text-sm font-semibold text-gray-700 pb-2 mb-1 border-b border-gray-200">Items serializados</h4>
                                    @if (count($d['items_serializados']))
                                        <dl>
                                            @foreach ($d['items_serializados'] as $item)
                                                <div class="grid grid-cols-[8rem_1fr] items-baseline gap-x-4 py-2.5 border-b border-gray-100 last:border-0">
                                                    <dt class="text-xs text-gray-500">{{ $item['nombre'] }}</dt>
                                                    <dd class="text-xs font-mono text-gray-800 break-all">{{ $item['serie'] }}</dd>
                                                </div>
                                            @endforeach
                                        </dl>
                                    @else
                                        <p class="py-2.5 text-xs text-gray-400">Sin items serializados</p>
                                    @endif
                                </section>

                                {{-- Resumen --}}
                                <section>
                                    <h4 class="text-sm font-semibold text-gray-700 pb-2 mb-1 border-b border-gray-200">Resumen</h4>
                                    <dl>
                                        @foreach ([
                                            'Técnico' => $d['tecnico'] ?? '—',
                                            'Sede' => $d['sede'] ?? '—',
                                            'Generación' => $d['kit_generacion'] ?: '—',
                                            'Componentes' => $d['total_componentes'],
                                            'Instalados' => $d['instalados'],
                                            'Reportes' => $d['reportes'],
                                            'Fin' => $d['fecha_fin'] ?? '—',
                                            'Precio final' => isset($d['precio']) ? 'S/ ' . number_format($d['precio'], 2) : '—',
                                            'Folio' => $d['folio'] ?? '—',
                                        ] as $etiqueta => $valor)
                                            <div class="grid grid-cols-[8rem_1fr] items-baseline gap-x-4 py-2.5 border-b border-gray-100 last:border-0">
                                                <dt class="text-xs text-gray-500">{{ $etiqueta }}</dt>
                                                <dd class="text-xs font-semibold text-gray-800 tabular-nums">{{ $valor }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </section>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @empty
                <tbody>
                    <tr>
                        <td colspan="8" class="px-6 py-14 text-center text-gray-400">
                            <i class="fas fa-inbox text-3xl mb-3 block text-gray-300"></i>
                            <span class="font-medium">Sin conversiones para los filtros seleccionados.</span>
                        </td>
                    </tr>
                </tbody>
            @endforelse
        </table>
    </div>
</div>

<script>
    (function () {
        function alternar(fila) {
            const extra = fila.closest('.detalle-orden').querySelector('.detalle-extra');
            const abierto = fila.getAttribute('aria-expanded') === 'true';
            fila.setAttribute('aria-expanded', String(!abierto));
            extra.classList.toggle('hidden', abierto);
            fila.querySelector('.detalle-chevron').style.transform = abierto ? '' : 'rotate(90deg)';
        }
        document.addEventListener('click', function (e) {
            const fila = e.target.closest('.detalle-main');
            if (fila) alternar(fila);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            const fila = e.target.closest && e.target.closest('.detalle-main');
            if (fila) { e.preventDefault(); alternar(fila); }
        });
    })();
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f9fafb; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>