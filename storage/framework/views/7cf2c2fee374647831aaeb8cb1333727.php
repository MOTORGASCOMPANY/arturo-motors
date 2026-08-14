<div class="container mx-auto py-12">
    <div class="bg-gray-200 p-8 rounded-xl w-full">
        
        <div class="items-center pb-6 md:block sm:block">
            <div class="px-2 w-64 mb-4 md:w-full">
                <h2 class="text-gray-600 font-semibold text-2xl">
                    <i class="fas fa-file-signature mr-2"></i>Gestión de Empleados
                </h2>
                <span class="text-xs">Control de periodos laborales, remuneraciones, vaciones y documentos</span>
            </div>
            <div class="w-full items-center md:flex md:justify-between">
                <div class="flex bg-gray-50 items-center p-2 rounded-md mb-4">
                    <span>Mostrar</span>
                    <select wire:model.live="cant"
                        class="bg-gray-50 mx-2 border-indigo-500 rounded-md outline-none ml-1 block">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                    <span>Entradas</span>
                </div>
                <div class="flex bg-gray-50 items-center lg:w-2/6 p-2 rounded-md mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                    <input
                        class="bg-gray-50 outline-none block rounded-md border-indigo-500 w-full border-none focus:ring-0"
                        type="text" wire:model.live="search" placeholder="Buscar por empleado o cargo...">
                </div>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Administrador del sistema|administrador')): ?>
                    <div class="mb-4">
                        <button wire:click="$dispatch('abrir-modal-crear')"
                            class="bg-orange-500 px-6 py-4 rounded-md text-white font-semibold tracking-wide cursor-pointer hover:bg-orange-600 transition flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i> Nuevo Contrato
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tabla de contratos -->
        <?php if($contratos->count()): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal rounded-md overflow-hidden">
                    <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                Empleado
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                Cargo
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                Periodo Laboral
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                Remuneración
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">
                                Estado
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                Info. Vacaciones
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $contratos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-b border-gray-200 bg-white text-sm" wire:key="contrato-<?php echo e($item->id); ?>">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        <div class="ml-3">
                                            <p class="text-gray-900 font-bold uppercase"><?php echo e($item->user->name); ?></p>
                                            <p class="text-gray-500 text-xs">DNI: <?php echo e($item->user->dni ?? 'N/A'); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-semibold text-indigo-600"><?php echo e($item->cargo); ?></span><br>
                                    <span class="text-xs text-gray-400"><?php echo e($item->tipo_contrato); ?></span>
                                </td>
                                <td class="px-4 py-4 text-gray-600">
                                    <div class="text-xs">
                                        <span class="block"><b class="text-gray-400">INICIO:</b>
                                            <?php echo e($item->fecha_inicio_contrato->format('d/m/Y')); ?></span>
                                        <span class="block mt-1"><b class="text-gray-400">VENCE:</b>
                                            <?php echo e($item->fecha_vencimiento ? $item->fecha_vencimiento->format('d/m/Y') : 'Indet.'); ?></span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="">
                                        <p class="text-xs text-gray-400">Bruto: S/
                                            <?php echo e(number_format($item->sueldo_bruto, 2)); ?></p>
                                        <p class="text-lg font-black text-green-600 leading-tight">S/
                                            <?php echo e(number_format($item->sueldo_neto, 2)); ?></p>
                                    </div>
                                </td>
                                <td class="px-4 py-4text-center">
                                    <span
                                        class="px-3 py-1 rounded-full font-bold text-xs <?php echo e($item->status == 'Activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); ?>">
                                        <?php echo e($item->status); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-4 min-w-[140px]">
                                    <?php if($item->vacaciones): ?>
                                        <?php
                                            // Convertimos a enteros.
                                            // round() redondeará 7.5 a 8, floor() lo dejaría en 7.
                                            $ganados = round($item->vacaciones->dias_ganados);
                                            $tomados = round($item->vacaciones->dias_tomados);
                                            $restantes = round($item->vacaciones->dias_restantes);

                                            $porcentaje = $ganados > 0 ? ($tomados / $ganados) * 100 : 0;
                                            $porcentaje = $porcentaje > 100 ? 100 : $porcentaje;
                                        ?>
                                        <div class="space-y-2">
                                            <div class="flex justify-between text-[10px]">
                                                <span class="text-gray-400 uppercase">Ganadas</span>
                                                <span class="font-bold"><?php echo e($ganados); ?></span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500"
                                                    style="width: <?php echo e($porcentaje); ?>%">
                                                </div>
                                            </div>
                                            <div class="flex justify-between text-[10px] font-bold">
                                                <span class="text-orange-500">Tom: <?php echo e($tomados); ?></span>
                                                <span class="text-green-600">Rest: <?php echo e($restantes); ?></span>
                                            </div>
                                            
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-gray-300 italic text-[10px]">
                                            Sin registro
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="" x-data="{ open: false }">
                                        <button @click="open = !open" @click.away="open = false"
                                            class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100 transition focus:outline-none">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50 py-1"
                                            style="display: none;">

                                            <a href="<?php echo e(route('rrhh.contrato.pdf', $item->id)); ?>" target="_blank"
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white transition">
                                                <i class="fas fa-file-pdf w-5 mr-2 text-red-500"></i> Ver Contrato PDF
                                            </a>
                                            <a href="<?php echo e(route('rrhh.vacaciones.index', $item->id)); ?>"
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white transition border-b border-gray-100">
                                                <i class="fas fa-calendar-alt w-5 mr-2 text-blue-500"></i> Vacaciones
                                            </a>
                                            <a href="<?php echo e(route('rrhh.documentos', $item->user->id)); ?>"
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white transition border-b border-gray-100">
                                                <i class="fas fa-folder-open w-5 mr-2 text-orange-500"></i> Documentos
                                            </a>
                                            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Administrador del sistema|administrador')): ?>
                                                <button wire:click="$dispatch('editar-contrato', { id: <?php echo e($item->id); ?> })" @click="open = false"
                                                    class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-lime-500 hover:text-white transition">
                                                    <i class="fas fa-pencil-alt w-5 mr-2 text-lime-600"></i> Editar Datos
                                                </button>
                                                <button wire:click="delete(<?php echo e($item->id); ?>)"
                                                    onclick="confirm('¿Seguro de eliminar este contrato?') || event.stopImmediatePropagation()"
                                                    class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-600 hover:text-white transition">
                                                    <i class="fas fa-trash-alt w-5 mr-2"></i> Eliminar
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <?php echo e($contratos->links()); ?>

            </div>
        <?php else: ?>
            <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                No hay contratos registrados con el criterio "<?php echo e($search); ?>".
            </div>
        <?php endif; ?>
    </div>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('r-r-h-h.create-contrato');

$__html = app('livewire')->mount($__name, $__params, 'lw-4135577723-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\livewire\r-r-h-h\contratos.blade.php ENDPATH**/ ?>