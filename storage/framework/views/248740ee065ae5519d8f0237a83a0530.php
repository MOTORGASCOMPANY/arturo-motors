<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
    <!-- Botón Volver (Estilo Pill con Hover) -->
    <div>
        <a href="<?php echo e(route('ordenes.listado')); ?>"
            class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
            <i class="fas fa-arrow-left text-gray-500"></i> Volver a las órdenes de servicio
        </a>
    </div>
    <!-- Cabecera -->
    <div class="bg-gray-200 p-8 rounded-xl w-full border border-gray-300/80 shadow-sm">
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-sm font-bold text-lg">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 leading-tight">
                        Orden #<?php echo e($orden->id); ?>

                        <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion'): ?>
                            <span
                                class="text-xs bg-purple-200 text-purple-800 px-2 py-0.5 rounded-full font-semibold align-middle">Conversión</span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </h2>
                    <p class="text-xs text-gray-700 flex items-center gap-1 mt-0.5">
                        <i class="far font-normal fa-calendar-alt text-gray-600"></i>
                        Creada el <?php echo e($orden->created_at->format('d/m/Y H:i')); ?> por
                        <span class="font-medium text-gray-800"><?php echo e($orden->creadoPor->name); ?></span>
                    </p>
                </div>
            </div>
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm animate-pulse">
                <span
                    class="w-2 h-2 rounded-full bg-emerald-500
                    <?php echo e(match (true) {
                        str_contains($orden->estado, 'cancel') || str_contains($orden->estado, 'rechaz') => 'bg-red-500 text-red-700',
                        in_array($orden->estado, ['entregada', 'entregado']) => 'bg-emerald-500 text-emerald-700',
                        default => 'bg-amber-500 text-amber-700',
                    }); ?>">
                </span> <?php echo e(ucfirst(str_replace('_', ' ', $orden->estado))); ?>

            </span>
        </div>
    </div>
    <!-- Datos generales -->
    <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
        <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">
            <i class="fas fa-user mr-1 text-gray-600"></i> Cliente y vehículo
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-600 text-xs font-medium">Cliente</p>
                <p class="font-bold text-gray-900"><?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?></p>
                <p class="text-gray-700 text-xs"><?php echo e($orden->cliente->documento); ?> —
                    <?php echo e($orden->cliente->telefono ?? 'sin teléfono'); ?></p>
            </div>
            <div>
                <p class="text-gray-600 text-xs font-medium">Vehículo</p>
                <p class="font-bold text-gray-900"><?php echo e($orden->vehiculo->placa); ?> — <?php echo e($orden->vehiculo->marca); ?>

                    <?php echo e($orden->vehiculo->modelo); ?></p>
                <p class="text-gray-700 text-xs">Año: <?php echo e($orden->vehiculo->anio ?? '—'); ?></p>
            </div>
        </div>
    </div>
    <!-- Servicio y precio -->
    <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
        <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">
            <i class="fas fa-tag mr-1 text-gray-600"></i> Servicio y precio
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-gray-600 text-xs font-medium">Servicio</p>
                <p class="font-bold text-gray-900"><?php echo e($orden->service->nombre); ?></p>
            </div>
            <div>
                <p class="text-gray-600 text-xs font-medium">Precio de lista</p>
                <p class="font-bold text-gray-800">S/ <?php echo e(number_format($orden->precio_lista, 2)); ?></p>
            </div>
            <div>
                <p class="text-gray-600 text-xs font-medium">Precio final</p>
                <p class="font-bold <?php echo e($orden->precio_final != $orden->precio_lista ? 'text-amber-700' : 'text-gray-900'); ?>">
                    S/ <?php echo e(number_format($orden->precio_final, 2)); ?>

                </p>
            </div>
        </div>
        <!--[if BLOCK]><![endif]--><?php if($orden->descuento_motivo): ?>
            <p class="text-xs text-gray-700 mt-3 font-medium">
                <i class="fas fa-comment-dots mr-1 text-gray-500"></i> Motivo del ajuste:
                <?php echo e($orden->descuento_motivo); ?>

            </p>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Evaluación (solo conversión) -->
    <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion' && $orden->checklist_evaluacion): ?>
        <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">
                <i class="fas fa-clipboard-check mr-1 text-gray-600"></i> Evaluación técnica
            </h3>
            <div class="flex items-center gap-3 mb-4 text-sm">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($orden->evaluacion_aprobada ? 'bg-emerald-200 text-emerald-900' : 'bg-red-200 text-red-900'); ?>">
                    <?php echo e($orden->evaluacion_aprobada ? 'Apto' : 'No apto'); ?>

                </span>
                <span class="text-gray-700 font-medium">
                    Por <?php echo e($orden->evaluadoPor?->name); ?> — <?php echo e($orden->evaluado_en?->format('d/m/Y H:i')); ?>

                </span>
            </div>
            <!--[if BLOCK]><![endif]--><?php if($orden->evaluacion_observaciones): ?>
                <p class="text-sm bg-white/80 border border-gray-300 rounded-lg p-3 mb-4 text-gray-800 font-medium">
                    <?php echo e($orden->evaluacion_observaciones); ?>

                </p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 text-xs">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $checklistGrupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clave => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-1.5 py-1">
                            <!--[if BLOCK]><![endif]--><?php if($orden->checklist_evaluacion[$clave] ?? false): ?>
                                <i class="fas fa-check-circle text-emerald-600"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle text-gray-400"></i>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <span class="<?php echo e($orden->checklist_evaluacion[$clave] ?? false ? 'text-gray-900 font-semibold' : 'text-gray-600'); ?>"><?php echo e($label); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Conversión: técnico y equipos -->
    <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion' && $orden->tecnico): ?>
        <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">
                <i class="fas fa-wrench mr-1 text-gray-600"></i> Conversión
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm mb-4">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Técnico</p>
                    <p class="font-bold text-gray-900"><?php echo e($orden->tecnico->name); ?></p>
                </div>
                <div>
                    <p class="text-gray-600 text-xs font-medium">Inicio</p>
                    <p class="font-bold text-gray-900"><?php echo e($orden->fecha_inicio_conversion?->format('d/m/Y H:i') ?? '—'); ?></p>
                </div>
                <div>
                    <p class="text-gray-600 text-xs font-medium">Fin</p>
                    <p class="font-bold text-gray-900"><?php echo e($orden->fecha_fin_conversion?->format('d/m/Y H:i') ?? '—'); ?></p>
                </div>
            </div>
            <!--[if BLOCK]><![endif]--><?php if($orden->items->count()): ?>
                <p class="text-xs font-bold text-gray-700 uppercase mb-2">Equipos instalados</p>
                <div class="space-y-1.5 mb-4">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex justify-between text-sm bg-white/80 border border-gray-300 rounded-lg px-3 py-2">
                            <span class="font-medium text-gray-800">
                                <?php echo e($item->producto->categoria->nombre); ?> — <?php echo e($item->producto->nombre); ?> (<?php echo e($item->producto->marca); ?>)
                            </span>
                            <span class="font-mono text-xs text-gray-700 font-semibold">
                                Serie: <?php echo e($item->serie); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <!--[if BLOCK]><![endif]--><?php if($orden->movimientosStock->count()): ?>
                <p class="text-xs font-bold text-gray-700 uppercase mb-2">Repuestos utilizados</p>
                <div class="space-y-1.5">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->movimientosStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex justify-between text-sm bg-white/80 border border-gray-300 rounded-lg px-3 py-2">
                            <span class="font-medium text-gray-800"><?php echo e($mov->producto->nombre); ?></span>
                            <span class="text-gray-700 font-semibold">× <?php echo e($mov->cantidad); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Comprobante -->
    <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
        <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">
            <i class="fas fa-receipt mr-1 text-gray-600"></i> Comprobante
        </h3>
        <!--[if BLOCK]><![endif]--><?php if($orden->comprobante): ?>
            <div class="flex items-center justify-between text-sm">
                <div>
                    <p class="font-bold text-gray-900">Folio: <?php echo e($orden->comprobante->folio); ?></p>
                    <p class="text-gray-700 text-xs font-medium"><?php echo e(ucfirst($orden->comprobante->metodo_pago)); ?> —
                        <?php echo e($orden->comprobante->created_at->format('d/m/Y H:i')); ?></p>
                </div>
                <a href="<?php echo e(route('comprobantes.pdf', $orden->id)); ?>" target="_blank"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors shadow-sm">
                    Ver PDF →
                </a>
            </div>
        <?php else: ?>
            <p class="text-sm text-gray-600 font-medium">Aún no se ha generado comprobante (pendiente de cobro).</p>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion'): ?>
        <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">
                <i class="fas fa-file-pdf mr-1 text-gray-600"></i> Documentos
            </h3>
            <div class="flex flex-wrap gap-2">
                <!--[if BLOCK]><![endif]--><?php if($orden->checklist_evaluacion): ?>
                    <a href="<?php echo e(route('conversiones.pdf.evaluacion', $orden->id)); ?>" target="_blank"
                    class="bg-gray-100 hover:bg-gray-300 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg">
                        <i class="fas fa-clipboard-check mr-1"></i> Ficha de evaluación
                    </a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><?php if($orden->items->count()): ?>
                    <a href="<?php echo e(route('conversiones.pdf.ficha-tecnica', $orden->id)); ?>" target="_blank"
                    class="bg-gray-100 hover:bg-gray-300 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg">
                        <i class="fas fa-wrench mr-1"></i> Ficha técnica
                    </a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><?php if(in_array($orden->estado, ['entregado', 'entregada'])): ?>
                    <a href="<?php echo e(route('conversiones.pdf.garantia', $orden->id)); ?>" target="_blank"
                    class="bg-gray-100 hover:bg-gray-300 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg">
                        <i class="fas fa-shield-alt mr-1"></i> Carta de garantía
                    </a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Historial de estados -->
    <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
        <h3 class="text-sm font-bold text-gray-800 uppercase mb-3">
            <i class="fas fa-history mr-1 text-gray-600"></i> Historial
        </h3>
        <div class="space-y-3">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->historialEstados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-start gap-3 text-sm">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-600 mt-1 flex-shrink-0"></div>
                    <div>
                        <p class="text-gray-900">
                            <!--[if BLOCK]><![endif]--><?php if($h->estado_anterior): ?>
                                <span class="text-gray-600"><?php echo e(ucfirst(str_replace('_', ' ', $h->estado_anterior))); ?></span> →
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <span class="font-bold text-gray-900"><?php echo e(ucfirst(str_replace('_', ' ', $h->estado_nuevo))); ?></span>
                        </p>
                        <p class="text-xs text-gray-700 font-medium"><?php echo e($h->created_at->format('d/m/Y H:i')); ?> — <?php echo e($h->usuario->name ?? 'Sistema'); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/service-orders/detalle.blade.php ENDPATH**/ ?>