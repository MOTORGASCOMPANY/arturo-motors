<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-12 space-y-6">
    <!-- Titulo y subtitulo -->
    <div class="bg-gray-200 p-8 rounded-xl w-full">
        <h2 class="text-gray-600 font-semibold text-2xl">
            <i class="fas fa-warehouse mr-2"></i>Reporte de almacén
        </h2>
        <span class="text-xs">Stock, valorización y distribución entre sedes</span>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Valor total (todas las sedes)</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">S/ {{ number_format($valorTotal, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Stock bajo en Arturo Motors</span>
            <p class="text-2xl font-bold {{ $stockBajo->count() > 0 ? 'text-red-600' : 'text-gray-700' }} mt-1">{{ $stockBajo->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Sin precio cargado</span>
            <p class="text-2xl font-bold {{ $sinPrecio->count() > 0 ? 'text-amber-600' : 'text-gray-700' }} mt-1">{{ $sinPrecio->count() }}</p>
        </div>
    </div>

    <!-- Gráfico: valor por sede -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore>
        <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Valor de inventario por sede</h3>
        <canvas id="chartAlmacenSedes" height="80"></canvas>
    </div>
    <script>
        function renderAlmacenSedesChart() {
            const ctx = document.getElementById('chartAlmacenSedes');
            if (!ctx) return;
            if (window.chartAlmacenSedesInstance) window.chartAlmacenSedesInstance.destroy();

            window.chartAlmacenSedesInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [{ label: 'Valor (S/)', data: @json($data), backgroundColor: '#4f46e5' }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }
        document.addEventListener('livewire:navigated', renderAlmacenSedesChart);
    </script>

    <!-- Distribución por sede -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="p-4 border-b border-gray-200/60">
            <h3 class="font-semibold text-gray-800 text-sm">Distribución de stock por sede</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Producto</th>
                        <th class="px-4 py-2 text-left">Categoría</th>
                        @foreach ($sedes as $s)
                            <th class="px-4 py-2 text-right">{{ $s->nombre }}</th>
                        @endforeach
                        <th class="px-4 py-2 text-right font-bold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($distribucion as $row)
                        <tr>
                            <td class="px-4 py-2.5 font-medium">{{ $row['producto']->nombre }}</td>
                            <td class="px-4 py-2.5 text-gray-500">{{ $row['producto']->categoria->nombre }}</td>
                            @foreach ($sedes as $s)
                                <td class="px-4 py-2.5 text-right {{ $row['por_sede'][$s->id] > 0 ? '' : 'text-gray-300' }}">
                                    {{ $row['por_sede'][$s->id] }}
                                </td>
                            @endforeach
                            <td class="px-4 py-2.5 text-right font-bold">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $sedes->count() + 3 }}" class="px-4 py-6 text-center text-gray-400">Sin stock registrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Stock bajo -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="p-4 border-b border-gray-200/60">
            <h3 class="font-semibold text-gray-800 text-sm"><i class="fas fa-triangle-exclamation text-amber-500 mr-1"></i> Stock bajo en Arturo Motors</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Producto</th>
                    <th class="px-4 py-2 text-right">Disponible</th>
                    <th class="px-4 py-2 text-right">Mínimo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($stockBajo as $p)
                    <tr>
                        <td class="px-4 py-2.5 font-medium">{{ $p->nombre }}</td>
                        <td class="px-4 py-2.5 text-right text-red-600 font-semibold">{{ $p->stockEnSede(1) }}</td>
                        <td class="px-4 py-2.5 text-right text-gray-500">{{ $p->stock_minimo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Ningún producto está bajo su mínimo en Arturo Motors.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($sinPrecio->count())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
            <h3 class="text-sm font-bold text-amber-700 uppercase mb-3">Productos con stock pero sin precio referencial</h3>
            <div class="space-y-1">
                @foreach ($sinPrecio as $p)
                    <div class="text-sm bg-white rounded-lg px-3 py-2">{{ $p->nombre }}</div>
                @endforeach
            </div>
        </div>
    @endif
</div>