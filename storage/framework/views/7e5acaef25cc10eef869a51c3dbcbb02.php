<!-- resources>views>livewire>reportes>reporte-citas.blade.php -->
<div class="container mx-auto py-8 antialiased bg-gray-100">
    <!-- Main Card Container -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header Section -->
        
        <div class="bg-gradient-to-r from-blue-500 to-green-500 text-white p-4 rounded-t-3xl shadow-lg">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <h2 class="text-2xl font-extrabold tracking-tight">
                    Reporte de Citas
                </h2>
                <div class="mt-4 md:mt-0 bg-white bg-opacity-20 px-4 py-2 rounded-full text-lg font-semibold">
                    <p>Total: <span class="font-extrabold"><?php echo e($citas->total()); ?></span></p>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-8">
            <!-- Filtros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Buscar -->
                <div>
                    <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'search','value' => 'Buscar Cliente/Motivo','class' => 'text-gray-600 font-semibold mb-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'search','value' => 'Buscar Cliente/Motivo','class' => 'text-gray-600 font-semibold mb-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $attributes = $__attributesOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__attributesOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $component = $__componentOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__componentOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['id' => 'search','type' => 'text','class' => 'block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 transition','wire:model.live' => 'search','placeholder' => 'Buscar...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'search','type' => 'text','class' => 'block w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 transition','wire:model.live' => 'search','placeholder' => 'Buscar...']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
                </div>
                <!-- Estado -->
                <div>
                    <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'estado','value' => 'Estado','class' => 'text-gray-600 font-semibold mb-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'estado','value' => 'Estado','class' => 'text-gray-600 font-semibold mb-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $attributes = $__attributesOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__attributesOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $component = $__componentOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__componentOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
                    <select id="estado"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition"
                        wire:model.live="estado">
                        <option value="todos">Seleccione estado</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="aceptada">Aceptada</option>
                        <option value="rechazada">Rechazada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
                <!-- Fecha Inicio -->
                <div>
                    <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'fechaInicio','value' => 'Fecha Inicio','class' => 'text-gray-600 font-semibold mb-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'fechaInicio','value' => 'Fecha Inicio','class' => 'text-gray-600 font-semibold mb-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $attributes = $__attributesOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__attributesOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $component = $__componentOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__componentOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['id' => 'fechaInicio','type' => 'date','class' => 'block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition','wire:model.live' => 'fechaInicio']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'fechaInicio','type' => 'date','class' => 'block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition','wire:model.live' => 'fechaInicio']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
                </div>
                <!-- Fecha Fin -->
                <div>
                    <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'fechaFin','value' => 'Fecha Fin','class' => 'text-gray-600 font-semibold mb-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'fechaFin','value' => 'Fecha Fin','class' => 'text-gray-600 font-semibold mb-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $attributes = $__attributesOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__attributesOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8ba2b4c22a13c55321e34443c386276)): ?>
<?php $component = $__componentOriginald8ba2b4c22a13c55321e34443c386276; ?>
<?php unset($__componentOriginald8ba2b4c22a13c55321e34443c386276); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['id' => 'fechaFin','type' => 'date','class' => 'block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition','wire:model.live' => 'fechaFin']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'fechaFin','type' => 'date','class' => 'block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition','wire:model.live' => 'fechaFin']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $attributes = $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1)): ?>
<?php $component = $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1; ?>
<?php unset($__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1); ?>
<?php endif; ?>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200">
                <!--[if BLOCK]><![endif]--><?php if($citas->count()): ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-600 text-white">
                            <tr>
                                <th class="cursor-pointer px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider hover:bg-gray-800 transition"
                                    wire:click="order('id')">
                                    ID
                                    <!--[if BLOCK]><![endif]--><?php if($sort === 'id'): ?>
                                        <span class="ml-1 text-white"><?php echo $direction === 'asc' ? '&#x25B2;' : '&#x25BC;'; ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </th>
                                <th class="cursor-pointer px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider hover:bg-gray-800 transition"
                                    wire:click="order('fecha_cita')">
                                    Fecha
                                    <!--[if BLOCK]><![endif]--><?php if($sort === 'fecha_cita'): ?>
                                        <span class="ml-1 text-white"><?php echo $direction === 'asc' ? '&#x25B2;' : '&#x25BC;'; ?></span>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Cliente
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Vehículo
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Asesor
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Motivo
                                </th>
                                <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $citas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo e($cita->id); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo e($cita->fecha_cita->format('d/m/Y')); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo e($cita->cliente->nombre ?? 'N/A'); ?> <?php echo e($cita->cliente->apellido ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo e($cita->vehiculo->placa ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <?php echo e($cita->asesor->name ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo e($cita->motivo); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php
                                            $colors = [
                                                'pendiente' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                                                'aceptada' => 'bg-green-50 text-green-700 border border-green-200',
                                                'rechazada' => 'bg-red-50 text-red-700 border border-red-200',
                                                'cancelada' => 'bg-gray-50 text-gray-700 border border-gray-200',
                                            ];
                                        ?>
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($colors[$cita->estado] ?? 'bg-gray-50 text-gray-700 border border-gray-200'); ?>">
                                            <?php echo e(ucfirst($cita->estado)); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-10 text-gray-500">
                        <p>No se encontraron citas con los filtros seleccionados.</p>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Pagination -->
            <!--[if BLOCK]><![endif]--><?php if($citas->hasPages()): ?>
                <div class="mt-8">
                    <?php echo e($citas->links()); ?>

                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

</div>
<?php /**PATH C:\xampp\htdocs\Lifegas\resources\views/livewire/reportes/reporte-citas.blade.php ENDPATH**/ ?>