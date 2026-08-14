<!-- resources>views>livewire>solicitud-repuestos.blade.php -->
<div class="rounded-xl m-4 bg-white p-8 mx-auto max-w-max shadow-lg">
    <h2 class="text-gray-600 font-semibold">Solicitud para Conversión # <?php echo e($conversionId); ?></h2>
    <div class="flex flex-col md:flex-row items-baseline gap-4 mb-2">
        <p class="text-sm text-gray-500">
            Cliente: <?php echo e($conversion->expediente->cliente->nombre ?? 'N/A'); ?>

            <?php echo e($conversion->expediente->cliente->apellido ?? ''); ?>

        </p>
        <p class="text-sm text-gray-500">
            Vehículo: <?php echo e($conversion->expediente->vehiculo->placa ?? ''); ?>

        </p>
    </div>

    <div class="flex flex-col md:flex-row items-end gap-4 mb-6">
        <!-- Repuesto -->
        <div class="w-full">
            <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'repuesto_id','value' => 'Repuesto:']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'repuesto_id','value' => 'Repuesto:']); ?>
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
            <select id="repuesto_id" class="bg-white border-gray-300 rounded-md outline-none ml-1 block w-full"
                wire:model="repuesto_id">
                <option value="">Seleccione un repuesto</option>
                <?php $__currentLoopData = $repuestosDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $repuesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($repuesto['id']); ?>"><?php echo e($repuesto['nombre']); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <!-- Cantidad -->
        <div class="w-full">
            <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['for' => 'cantidad','value' => 'Cantidad:']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'cantidad','value' => 'Cantidad:']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['id' => 'cantidad','class' => 'mt-1 block w-full','type' => 'number','wire:model' => 'cantidad']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'cantidad','class' => 'mt-1 block w-full','type' => 'number','wire:model' => 'cantidad']); ?>
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
        
        <div class="md:shrink-0">
            <button wire:click="addRepuesto"
                class="bg-amber-400 px-5 py-3 rounded-md text-white font-semibold tracking-wide hover:bg-amber-600">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>

    <?php if(count($repuestos) > 0): ?>
        <h2 class="text-gray-600 font-semibold">Repuestos en la solicitud:</h2>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:mx-0.5">
                <div class="py-2 inline-block min-w-full ">
                    <div class="overflow-hidden rounded-lg">
                        <table class="min-w-full">
                            <thead class="bg-slate-600 border-b">
                                <tr>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-4 py-2 text-left">
                                        #
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-4 py-2 text-left">
                                        Repuesto
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-4 py-2 text-left">
                                        Cantidad
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-4 py-2 text-left">
                                        Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $repuestos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="bg-gray-100 border-b">
                                        <td class="text-sm text-gray-900 font-light px-4 py-2 whitespace-nowrap">
                                            <?php echo e($index + 1); ?>

                                        </td>
                                        <td class="text-sm text-gray-900 font-light px-4 py-2 whitespace-nowrap">
                                            <?php echo e($item['nombre']); ?>

                                        </td>
                                        <td class="text-sm text-gray-900 font-light px-4 py-2 whitespace-nowrap">
                                            <?php echo e($item['cantidad']); ?>

                                        </td>
                                        <td class="text-sm text-gray-900 font-light px-4 py-2 whitespace-nowrap">
                                            <a wire:click="removeRepuesto(<?php echo e($index); ?>)"
                                                class="hover: cursor-pointer p-4">
                                                <i class="fa-solid fa-trash hover:text-red-500"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    <?php else: ?>
        <p class="text-gray-500 text-sm italic text-center">No hay repuestos en la lista.</p>
    <?php endif; ?>

    
    <div class="mt-6 flex items-center justify-center gap-2">
        <button class="p-3 bg-teal-600 rounded-xl text-white text-sm hover:font-bold hover:bg-teal-700"
            wire:click="saveSolicitud" wire:loading.attr="disabled" wire:target="saveSolicitud">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
        </button>
        <?php if($showButtons): ?> 
            <button class="p-3 bg-red-600 rounded-xl text-white text-sm hover:font-bold hover:bg-red-700"
                wire:click="redirectToRegresar">
                <i class="fa-solid fa-rotate-left"></i> Regresar
            </button>
            <button class="p-3 bg-yellow-500 rounded-xl text-white text-sm hover:font-bold hover:bg-yellow-600"
                wire:click="openPdf">
                <i class="fa-solid fa-clipboard-list"></i> Orden Repuests
            </button>
        <?php endif; ?>
    </div>
</div>


    <?php
        $__scriptKey = '436822126-0';
        ob_start();
    ?>
    <script>
        Livewire.on('open-pdf', (params) => {
            window.open(params.url, '_blank');
        });
    </script>
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>

<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\livewire\solicitud-repuestos.blade.php ENDPATH**/ ?>