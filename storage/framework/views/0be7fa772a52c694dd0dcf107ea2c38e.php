<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-5xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="items-center pb-2 md:block sm:block">
                <!-- Titulo y subtitulo -->
                <div class="px-2 w-full mb-4">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-tools mr-2"></i>Listas para entrega y cobro
                    </h2>
                    <span class="text-xs">Todas las conversiones listas para entrega y cobro</span>
                </div>
            </div>
            <!-- Tabla -->
            <!--[if BLOCK]><![endif]--><?php if($ordenes->count()): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Fin de conversión
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
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Monto
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $ordenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orden): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr wire:key="entrega-orden-<?php echo e($orden->id); ?>" class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <?php echo e($orden->fecha_fin_conversion ? $orden->fecha_fin_conversion->format('d/m/Y H:i') : '—'); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?>

                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <span class="font-bold text-gray-800"><?php echo e($orden->vehiculo->placa); ?></span>
                                    </td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm">
                                        <?php echo e($orden->service->nombre); ?>

                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        S/ <?php echo e(number_format($orden->precio_final, 2)); ?>

                                    </td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 bg-white text-sm whitespace-nowrap">
                                        <a href="<?php echo e(route('conversiones.entregar', $orden->id)); ?>"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition inline-block shadow-sm">
                                            Entregar y cobrar
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
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                    No hay conversiones listas para entrega.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/conversiones/entrega-pendientes.blade.php ENDPATH**/ ?>