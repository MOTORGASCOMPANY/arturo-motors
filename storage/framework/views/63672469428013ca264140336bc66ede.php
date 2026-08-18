<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-12 space-y-6">

    <div class="bg-gray-200 p-8 rounded-xl w-full">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-gray-600 font-semibold text-2xl">
                    <i class="fas fa-chart-simple mr-2"></i>Reporte de ventas y servicios
                </h2>
                <span class="text-xs">Qué se vendió, quién convirtió más, y la tendencia del período</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <input type="date" wire:model.live="desde" class="text-sm rounded-lg border-gray-300">
                <span class="text-gray-500 text-sm">a</span>
                <input type="date" wire:model.live="hasta" class="text-sm rounded-lg border-gray-300">
                <select wire:model.live="tipoServicio" class="text-sm rounded-lg border-gray-300">
                    <option value="todos">Todos los servicios</option>
                    <option value="simple">Solo simples</option>
                    <option value="conversion">Solo conversión</option>
                </select>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Total vendido</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">S/ <?php echo e(number_format($totalVentas, 2)); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Órdenes cobradas</span>
            <p class="text-2xl font-bold text-blue-600 mt-1"><?php echo e($totalOrdenes); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Ticket promedio</span>
            <p class="text-2xl font-bold text-gray-700 mt-1">S/ <?php echo e(number_format($ticketPromedio, 2)); ?></p>
        </div>
    </div>

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore>
        <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Ventas por día</h3>
        <canvas id="chartReporteVentas" height="90"></canvas>
    </div>

    <script>
        function renderReporteVentasChart() {
            const ctx = document.getElementById('chartReporteVentas');
            if (!ctx) return;

            if (window.chartReporteVentasInstance) window.chartReporteVentasInstance.destroy();

            window.chartReporteVentasInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($labels, 15, 512) ?>,
                    datasets: [{
                        label: 'Ventas (S/)',
                        data: <?php echo json_encode($data, 15, 512) ?>,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5,150,105,0.1)',
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }

        document.addEventListener('livewire:navigated', renderReporteVentasChart);
        Livewire.hook('morph.updated', ({ component }) => {
            if (component.name === 'servicios.reporte') renderReporteVentasChart();
        });
    </script>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="p-4 border-b border-gray-200/60">
                <h3 class="font-semibold text-gray-800 text-sm">Ventas por servicio</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $ventasPorServicio; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nombre => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-2.5">
                                <?php echo e($nombre); ?>

                                <span class="text-xs text-gray-400">(<?php echo e($info['cantidad']); ?>)</span>
                            </td>
                            <td class="px-4 py-2.5 text-right font-semibold">S/ <?php echo e(number_format($info['total'], 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td class="px-4 py-6 text-center text-gray-400">Sin datos en este período.</td></tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
            <div class="p-4 border-b border-gray-200/60">
                <h3 class="font-semibold text-gray-800 text-sm">Conversiones por técnico</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-100">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $ventasPorTecnico; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nombre => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4 py-2.5">
                                <?php echo e($nombre); ?>

                                <span class="text-xs text-gray-400">(<?php echo e($info['cantidad']); ?>)</span>
                            </td>
                            <td class="px-4 py-2.5 text-right font-semibold">S/ <?php echo e(number_format($info['total'], 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td class="px-4 py-6 text-center text-gray-400">Sin conversiones cobradas en este período.</td></tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/servicios/reporte.blade.php ENDPATH**/ ?>