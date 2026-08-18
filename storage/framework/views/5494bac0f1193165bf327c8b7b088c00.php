<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-6xl mx-auto py-12 space-y-6">

    <div class="bg-gray-200 p-8 rounded-xl w-full">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-gray-600 font-semibold text-2xl">
                    <i class="fas fa-chart-line mr-2"></i>Reporte de caja
                </h2>
                <span class="text-xs">Resumen de ingresos y egresos por período</span>
            </div>
            <div class="flex items-center gap-2">
                <input type="date" wire:model.live="desde" class="text-sm rounded-lg border-gray-300">
                <span class="text-gray-500 text-sm">a</span>
                <input type="date" wire:model.live="hasta" class="text-sm rounded-lg border-gray-300">
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Ingresos</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">S/ <?php echo e(number_format($totalIngresos, 2)); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Egresos</span>
            <p class="text-2xl font-bold text-red-600 mt-1">S/ <?php echo e(number_format($totalEgresos, 2)); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 text-center">
            <span class="text-xs font-bold text-gray-500 uppercase">Neto</span>
            <p class="text-2xl font-bold <?php echo e($neto >= 0 ? 'text-blue-600' : 'text-red-600'); ?> mt-1">S/ <?php echo e(number_format($neto, 2)); ?></p>
        </div>
    </div>

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6" wire:ignore wire:key="reporte-caja-chart">
        <h3 class="text-sm font-bold text-gray-500 uppercase mb-4">Ingresos vs. egresos por día</h3>
        <canvas id="chartReporteCaja" height="90"></canvas>
    </div>

    <script>
        function renderReporteCajaChart() {
            const ctx = document.getElementById('chartReporteCaja');
            if (!ctx) return;

            if (window.chartReporteCajaInstance) window.chartReporteCajaInstance.destroy();

            window.chartReporteCajaInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($labels, 15, 512) ?>,
                    datasets: [
                        { label: 'Ingresos', data: <?php echo json_encode($ingresosData, 15, 512) ?>, backgroundColor: '#059669' },
                        { label: 'Egresos', data: <?php echo json_encode($egresosData, 15, 512) ?>, backgroundColor: '#dc2626' },
                    ]
                },
                options: { responsive: true, plugins: { legend: { position: 'top' } } }
            });
        }

        document.addEventListener('livewire:navigated', renderReporteCajaChart);
        Livewire.hook('morph.updated', ({ component }) => {
            if (component.name === 'caja.reporte') renderReporteCajaChart();
        });
    </script>

    
    <!--[if BLOCK]><![endif]--><?php if($sesionesConDescuadre->count()): ?>
        <div class="bg-red-50 border border-red-200 rounded-xl p-5">
            <h3 class="text-sm font-bold text-red-700 uppercase mb-3">
                <i class="fas fa-triangle-exclamation mr-1"></i> Sesiones con descuadre (<?php echo e($sesionesConDescuadre->count()); ?>)
            </h3>
            <div class="space-y-1">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sesionesConDescuadre; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex justify-between text-sm bg-white rounded-lg px-3 py-2">
                        <span><?php echo e($s->abierta_en->format('d/m/Y')); ?> — <?php echo e($s->abiertaPor->name); ?></span>
                        <span class="font-semibold text-red-600">S/ <?php echo e(number_format($s->diferencia, 2)); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 overflow-hidden">
        <div class="p-6 border-b border-gray-200/60">
            <h3 class="font-semibold text-gray-800">Sesiones del período (<?php echo e($sesiones->count()); ?>)</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Cajero</th>
                    <th class="px-4 py-3 text-right">Apertura</th>
                    <th class="px-4 py-3 text-right">Cierre</th>
                    <th class="px-4 py-3 text-right">Diferencia</th>
                    <th class="px-4 py-3 text-center">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $sesiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr wire:key="sesion-reporte-<?php echo e($s->id); ?>">
                        <td class="px-4 py-3"><?php echo e($s->abierta_en->format('d/m/Y H:i')); ?></td>
                        <td class="px-4 py-3"><?php echo e($s->abiertaPor->name); ?></td>
                        <td class="px-4 py-3 text-right">S/ <?php echo e(number_format($s->monto_apertura, 2)); ?></td>
                        <td class="px-4 py-3 text-right"><?php echo e($s->monto_cierre !== null ? 'S/ '.number_format($s->monto_cierre, 2) : '—'); ?></td>
                        <td class="px-4 py-3 text-right font-semibold <?php echo e($s->diferencia === null ? 'text-gray-400' : ($s->diferencia == 0 ? 'text-emerald-600' : 'text-red-600')); ?>">
                            <?php echo e($s->diferencia !== null ? 'S/ '.number_format($s->diferencia, 2) : '—'); ?>

                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e($s->estado === 'abierta' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'); ?>">
                                <?php echo e(ucfirst($s->estado)); ?>

                            </span>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No hay sesiones en este período.</td></tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>
</div><?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/caja/reporte.blade.php ENDPATH**/ ?>