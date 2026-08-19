<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-3xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <!-- Encabezado con título a la izquierda y botón a la derecha -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6">
                <!-- Título y subtítulo -->
                <div class="px-2">
                    <h2 class="text-gray-600 font-semibold text-2xl">
                        <i class="fas fa-layer-group mr-2"></i>Categorías de almacén
                    </h2>
                    <span class="text-xs">Tipos de equipos y repuestos que maneja el taller</span>
                </div>
                <!-- Botón alineado a la derecha en la misma fila -->
                <div>
                    <a wire:click="$dispatch('abrir-modal-categoria')" type="button" class="bg-indigo-500 px-5 py-3 rounded-md text-white font-semibold tracking-wide cursor-pointer hover:bg-indigo-600 transition inline-block">
                        Nueva categoría &nbsp;<i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>

            <!-- Tabla -->
            <!--[if BLOCK]><![endif]--><?php if($categorias->count()): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Nombre
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Serializado
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Atributos
                                </th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Productos
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr wire:key="categoria-<?php echo e($c->id); ?>">
                                    <td class="px-4 py-3 font-medium border-b border-gray-200 bg-white text-sm"><?php echo e($c->nombre); ?></td>
                                    <td class="px-4 py-3 text-center border-b border-gray-200 bg-white text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo e($c->es_serializado ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600'); ?>">
                                            <?php echo e($c->es_serializado ? 'Por serie' : 'Por cantidad'); ?>

                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 border-b border-gray-200 bg-white text-sm"><?php echo e($c->esquema_atributos ? implode(', ', $c->esquema_atributos) : '—'); ?></td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 bg-white text-sm"><?php echo e($c->productos_count); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                    No hay categorias registrados.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('almacen.categorias.crear', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-3855848522-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/almacen/categorias/listado.blade.php ENDPATH**/ ?>