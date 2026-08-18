<div class="max-w-6xl mx-auto py-12 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Hola, <?php echo e(auth()->user()->name); ?></h1>
        <p class="text-sm text-gray-500"><?php echo e(now()->translatedFormat('l d \d\e F, Y')); ?></p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Caja -->
        <a href="<?php echo e($sesionCaja ? route('caja.historial') : route('caja.abrir')); ?>"
           class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-gray-500 uppercase"><i class="fas fa-cash-register mr-1"></i> Caja</span>
                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e($sesionCaja ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'); ?>">
                    <?php echo e($sesionCaja ? 'Abierta' : 'Cerrada'); ?>

                </span>
            </div>
            <!--[if BLOCK]><![endif]--><?php if($sesionCaja): ?>
                <p class="text-sm text-gray-600">Ingresos hoy: <strong class="text-emerald-600">S/ <?php echo e(number_format($ingresosHoy, 2)); ?></strong></p>
                <p class="text-sm text-gray-600">Egresos hoy: <strong class="text-red-600">S/ <?php echo e(number_format($egresosHoy, 2)); ?></strong></p>
                <p class="text-xs text-gray-400 mt-1">Abierta por <?php echo e($sesionCaja->abiertaPor->name); ?></p>
            <?php else: ?>
                <p class="text-sm text-gray-400">No hay caja abierta. Toca para abrir.</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </a>
        <!-- Órdenes de hoy -->
        <a href="<?php echo e(route('ordenes.listado')); ?>"
           class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 hover:shadow-md transition">
            <span class="text-xs font-bold text-gray-500 uppercase"><i class="fas fa-file-alt mr-1"></i> Órdenes de hoy</span>
            <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($ordenesHoy); ?></p>
        </a>
        <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Jefe de Taller|Administrador del sistema')): ?>
            <!-- Pendientes de asignar técnico -->
            <a href="<?php echo e(route('conversiones.asignar')); ?>"
               class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 hover:shadow-md transition">
                <span class="text-xs font-bold text-gray-500 uppercase"><i class="fas fa-user-check mr-1"></i> Por asignar técnico</span>
                <p class="text-3xl font-bold <?php echo e($pendientesAsignarTecnico > 0 ? 'text-amber-600' : 'text-gray-800'); ?> mt-2">
                    <?php echo e($pendientesAsignarTecnico); ?>

                </p>
            </a>
            <!-- Pendientes de entrega -->
            <a href="<?php echo e(route('conversiones.entregas-pendientes')); ?>"
               class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 hover:shadow-md transition">
                <span class="text-xs font-bold text-gray-500 uppercase"><i class="fas fa-hand-holding mr-1"></i> Listas para entrega</span>
                <p class="text-3xl font-bold <?php echo e($pendientesEntrega > 0 ? 'text-amber-600' : 'text-gray-800'); ?> mt-2">
                    <?php echo e($pendientesEntrega); ?>

                </p>
            </a>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Tecnico|Administrador del sistema')): ?>
            <a href="<?php echo e(route('conversiones.mis-asignadas')); ?>"
               class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 hover:shadow-md transition">
                <span class="text-xs font-bold text-gray-500 uppercase"><i class="fas fa-wrench mr-1"></i> Mis conversiones</span>
                <p class="text-3xl font-bold <?php echo e($misPendientes > 0 ? 'text-amber-600' : 'text-gray-800'); ?> mt-2">
                    <?php echo e($misPendientes); ?>

                </p>
            </a>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Almacen|Administrador del sistema')): ?>
            <a href="<?php echo e(route('conversiones.almacen-pendientes')); ?>"
               class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5 hover:shadow-md transition">
                <span class="text-xs font-bold text-gray-500 uppercase"><i class="fas fa-boxes mr-1"></i> Esperando equipos</span>
                <p class="text-3xl font-bold <?php echo e($pendientesAlmacen > 0 ? 'text-amber-600' : 'text-gray-800'); ?> mt-2">
                    <?php echo e($pendientesAlmacen); ?>

                </p>
            </a>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Accesos rápidos -->
    <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Vendedor|Jefe de Taller|Administrador del sistema')): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-5">
            <span class="text-xs font-bold text-gray-500 uppercase mb-3 block">Accesos rápidos</span>
            <div class="flex flex-wrap gap-2">
                <a href="<?php echo e(route('ordenes.simple.crear')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    + Orden de servicio simple
                </a>
                <a href="<?php echo e(route('conversiones.crear')); ?>" class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                    + Orden de conversión
                </a>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div><?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/dashboard.blade.php ENDPATH**/ ?>