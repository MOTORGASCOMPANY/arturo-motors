<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-5xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <!-- Encabezado con título a la izquierda y botón a la derecha -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6">
                <!-- Título y subtítulo -->
                <div class="px-2">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-boxes mr-2"></i>Conversiones esperando equipos
                    </h2>
                    <span class="text-xs text-gray-500">Listado de órdenes aprobadas pendientes de asignación de kits/equipos</span>
                </div>
                <!-- Buscador rápido -->
                <div class="w-full md:w-72">
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar por placa, cliente..."
                            class="w-full pl-9 pr-4 py-2 bg-white text-sm text-gray-700 rounded-lg border border-gray-300 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Registros -->
            <!--[if BLOCK]><![endif]--><?php if($ordenes->count()): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Aprobado
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Cliente
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Vehículo
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Servicio
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Técnico
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $ordenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orden): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr wire:key="almacen-orden-<?php echo e($orden->id); ?>">
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <?php echo e($orden->evaluado_en ? $orden->evaluado_en->format('d/m/Y H:i') : '—'); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <span class="font-bold text-gray-800"><?php echo e($orden->vehiculo->placa); ?></span> — <?php echo e($orden->vehiculo->marca); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->service->nombre); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->tecnico->name ?? 'Sin asignar'); ?>

                                    </td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <a href="<?php echo e(route('conversiones.asignar-equipos', $orden->id)); ?>"
                                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition inline-block shadow-sm">
                                            <i class="fas fa-boxes-packing mr-1"></i> Asignar equipos
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-200/60">
                    <?php echo e($ordenes->links()); ?>

                </div>
            <?php else: ?>
                <div class="px-6 py-8 text-center font-semibold bg-indigo-50 text-indigo-700 rounded-lg border border-indigo-100 shadow-sm">
                    <i class="fas fa-box-open text-3xl mb-2 block text-indigo-400"></i>
                    No hay conversiones esperando equipos en este momento.
                </div>
                
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/conversiones/almacen-pendientes.blade.php ENDPATH**/ ?>