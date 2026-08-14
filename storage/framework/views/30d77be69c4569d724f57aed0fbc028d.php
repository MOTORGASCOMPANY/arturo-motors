<div class="container mx-auto py-12 px-2">
    <div class="bg-gray-200 rounded-lg shadow-sm p-4">
        <!-- Cabecera y filtros -->
        <div class="items-center pb-6 md:block sm:block">
            <div class="px-2 w-64 mb-4 md:w-full">
                <h2 class="text-gray-600 font-semibold text-2xl">
                    <i class="fas fa-file-signature mr-2"></i>Gestión de Planillas
                </h2>
                <span class="text-xs">Control de planillas de remuneraciones</span>
            </div>

            <div class="w-full items-center md:flex md:justify-between">
                <div class="flex items-center space-x-2">
                    <div class="flex bg-gray-50 items-center p-2 rounded-md mb-4">
                        <span class="text-sm">Mostrar</span>
                        <select wire:model.live="cant" class="bg-gray-50 mx-2 border-indigo-500 rounded-md outline-none text-sm">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="flex bg-gray-50 items-center p-2 rounded-md mb-4">
                        <select wire:model.live="periodoSeleccionado" class="bg-gray-50 mx-2 border-indigo-500 rounded-md outline-none text-sm">
                            <option value="">-- SELECCIONAR --</option>
                            <?php $__currentLoopData = $listaPeriodos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->periodo->format('Y-m-d')); ?>">
                                    Periodo: <?php echo e($p->periodo->format('d/m/Y')); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="flex bg-gray-50 items-center lg:w-2/6 p-2 rounded-md mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input class="bg-gray-50 outline-none block rounded-md border-none focus:ring-0 w-full text-sm" type="text" wire:model.live="search" placeholder="Buscar trabajador...">
                </div>

                <div class="mb-4">
                    <button wire:click="$dispatch('abrir-modal-planilla')" class="bg-orange-500 px-6 py-3 rounded-md text-white font-semibold hover:bg-orange-600 transition flex items-center shadow-sm">
                        <i class="fas fa-plus-circle mr-2"></i> Crear Planilla
                    </button>
                </div>

            </div>
        </div>

        <!-- Tabla de planillas -->
        <?php if($planillas->count()): ?>
            <div class="overflow-x-auto bg-white rounded-lg border border-gray-300">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr class="bg-gray-100 text-left text-xs font-bold uppercase text-gray-500 border-b whitespace-nowrap">
                            <th class="px-3 py-4">TRABAJADOR</th>
                            <th class="px-2 py-4 text-center">INGRESO</th>
                            <th class="px-2 py-4 text-right">BASE</th> <th class="px-2 py-4 text-right text-blue-600">EXTRAS</th>
                            <th class="px-2 py-4 text-right text-red-600">DSCTOS.</th>
                            <th class="px-2 py-4 text-left w-62 min-w-[220px]">OBSERVACIÓN</th>
                            <th class="px-2 py-4 text-center">N° CUENTA</th>
                            <th class="px-1 py-4 text-center bg-blue-600 text-white">TOTAL</th>
                            
                            <th class="px-2 py-4 text-center">PAGADO</th>
                            <th class="px-3 py-4 text-center">BOLETAS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php $__currentLoopData = $planillas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-3">
                                    <div class="text-[13px] font-bold text-gray-900 leading-tight"><?php echo e($item->contrato->user->name); ?></div>
                                    <div class="text-[11px] uppercase text-gray-400 font-semibold tracking-tighter"><?php echo e($item->contrato->cargo); ?></div>
                                </td>
                                <td class="px-2 py-3 text-center text-xs text-gray-500">
                                    <?php echo e(\Carbon\Carbon::parse($item->contrato->fecha_ingreso)->format('d/m/y')); ?>

                                </td>
                                <td class="px-2 py-3 text-right text-xs whitespace-nowrap">
                                    S/ <?php echo e(number_format($item->sueldo_base, 2)); ?>

                                </td>
                                <td class="px-2 py-3 text-right text-xs text-blue-600 whitespace-nowrap">
                                    +<?php echo e(number_format($item->horas_extras + $item->movilidad + $item->otros_ingresos + $item->asignacion_familiar, 2)); ?>

                                </td>
                                <td class="px-2 py-3 text-right text-xs text-red-600 whitespace-nowrap">
                                    -<?php echo e(number_format($item->otros_descuentos, 2)); ?>

                                </td>
                                <td class="px-2 py-3 w-52 min-w-[220px] max-w-[280px]">
                                    <div class="flex flex-col gap-1">
                                        <?php if($item->observacion): ?>
                                            <span class="text-gray-500 italic text-[11px] block mb-1 leading-tight break-words">
                                                <?php echo e($item->observacion); ?>

                                            </span>
                                        <?php endif; ?>

                                        <div class="flex flex-wrap gap-1">
                                            <?php if($item->asignacion_familiar > 0): ?>
                                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold whitespace-nowrap">
                                                    Asig: <?php echo e(number_format($item->asignacion_familiar, 2)); ?>

                                                </span>
                                            <?php endif; ?>

                                            <?php if($item->horas_extras > 0): ?>
                                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold whitespace-nowrap">
                                                    H.E: <?php echo e(number_format($item->horas_extras, 2)); ?>

                                                </span>
                                            <?php endif; ?>

                                            <?php if($item->movilidad > 0): ?>
                                                <span class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-[10px] font-bold whitespace-nowrap">
                                                    Mov: <?php echo e(number_format($item->movilidad, 2)); ?>

                                                </span>
                                            <?php endif; ?>

                                            <?php if($item->otros_ingresos > 0): ?>
                                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[10px] font-bold whitespace-nowrap">
                                                    Otros+: <?php echo e(number_format($item->otros_ingresos, 2)); ?>

                                                </span>
                                            <?php endif; ?>

                                            <?php if($item->otros_descuentos > 0): ?>
                                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-[10px] font-bold whitespace-nowrap">
                                                    Desc: <?php echo e(number_format($item->otros_descuentos, 2)); ?>

                                                </span>
                                            <?php endif; ?>

                                            <?php if(!$item->observacion && $item->asignacion_familiar == 0 && $item->horas_extras == 0 && $item->movilidad == 0 && $item->otros_ingresos == 0 && $item->otros_descuentos == 0): ?>
                                                <span class="text-gray-300">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-3 text-center font-mono text-[13px] text-gray-600">
                                    <?php echo e($item->contrato->user->numero_cuenta ?? '-'); ?>

                                </td>
                                <td class="px-2 py-3 text-center font-black bg-blue-600 text-white text-sm whitespace-nowrap">
                                    S/ <?php echo e(number_format($item->total_pagado, 2)); ?>

                                </td>
                                
                                <td class="px-2 py-3 text-center">
                                    <?php if (isset($component)) { $__componentOriginal74b62b190a03153f11871f645315f4de = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal74b62b190a03153f11871f645315f4de = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.checkbox','data' => ['wire:click' => 'togglePago('.e($item->id).')','checked' => $item->estado_pago,'class' => 'w-4 h-4 text-indigo-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'togglePago('.e($item->id).')','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->estado_pago),'class' => 'w-4 h-4 text-indigo-600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal74b62b190a03153f11871f645315f4de)): ?>
<?php $attributes = $__attributesOriginal74b62b190a03153f11871f645315f4de; ?>
<?php unset($__attributesOriginal74b62b190a03153f11871f645315f4de); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal74b62b190a03153f11871f645315f4de)): ?>
<?php $component = $__componentOriginal74b62b190a03153f11871f645315f4de; ?>
<?php unset($__componentOriginal74b62b190a03153f11871f645315f4de); ?>
<?php endif; ?>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <div class="flex justify-center space-x-1">
                                        <button wire:click="$dispatch('abrir-modal-archivos', { id: <?php echo e($item->id); ?> })"
                                            class="py-1 px-2 bg-yellow-400 text-white rounded hover:bg-yellow-500 transition shadow-sm">
                                            <i class="fa-solid fa-folder text-[11px]"></i>
                                        </button>
                                        
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t-2 border-gray-300">
                            <td colspan="7" class="px-4 py-4 text-right">
                                <span class="text-gray-500 font-bold uppercase text-xs">Total Periodo:</span>
                            </td>
                            <td class="px-2 py-3 text-center">
                                <span class="text-[15px] font-black text-blue-800 border-b-4 border-blue-300">
                                    S/ <?php echo e(number_format($totales['general'], 2)); ?>

                                </span>
                            </td>
                            
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-4">
                <?php echo e($planillas->links()); ?>

            </div>
        <?php else: ?>
            <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                No hay contratos registrados con el criterio.
            </div>
        <?php endif; ?>
    </div>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('r-r-h-h.crear-planilla');

$__html = app('livewire')->mount($__name, $__params, 'lw-1656377630-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('r-r-h-h.planilla-archivos');

$__html = app('livewire')->mount($__name, $__params, 'lw-1656377630-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\livewire\r-r-h-h\lista-planilla.blade.php ENDPATH**/ ?>