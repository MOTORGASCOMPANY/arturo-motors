
<div class="p-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-folder-open mr-2 text-orange-600"></i> Legajo Digital: <?php echo e($usuario->name); ?>

            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php $__currentLoopData = $documentosRequeridos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $docSubido = $usuario->documentos->where('tipo_documento_id', $tipo->id)->first();
                ?>

                <div class="border rounded-lg p-4 flex items-center justify-between bg-gray-50">
                    <div class="flex items-center">
                        <div class="mr-4">
                            <?php if($docSubido): ?>
                                <?php if($docSubido->estado == 'Aprobado'): ?>
                                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                                <?php elseif($docSubido->estado == 'Rechazado'): ?>
                                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                                <?php else: ?>
                                    <i class="fas fa-clock text-orange-500 text-2xl"></i>
                                <?php endif; ?>
                            <?php else: ?>
                                <i class="fas fa-file-upload text-gray-300 text-2xl"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-gray-700"><?php echo e($tipo->nombre); ?></p>
                            <p class="text-xs text-gray-500">
                                <?php if($docSubido): ?>
                                    <span class="capitalize">Estado: <?php echo e($docSubido->estado); ?></span>
                                <?php else: ?>
                                    <span class="text-red-400">Pendiente de subir</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <?php if($docSubido): ?>
                            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Administrador del sistema|administrador')): ?>
                                <?php if($docSubido->estado == 'Pendiente'): ?>
                                    <button wire:click="cambiarEstado(<?php echo e($docSubido->id); ?>, 'Aprobado')"
                                            class="p-2 bg-green-100 text-green-600 rounded-md hover:bg-green-200" title="Aprobar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button wire:click="cambiarEstado(<?php echo e($docSubido->id); ?>, 'Rechazado')"
                                            class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200" title="Rechazar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>

                            
                            <a href="<?php echo e(Storage::url($docSubido->ruta)); ?>" target="_blank"
                            class="p-2 bg-indigo-100 text-indigo-600 rounded-md hover:bg-indigo-200" title="Ver documento">
                                <i class="fas fa-eye"></i>
                            </a>

                            <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Administrador del sistema|administrador')): ?>
                                <button wire:confirm="¿Estás seguro de eliminar este documento?"
                                        wire:click="eliminarDocumento(<?php echo e($docSubido->id); ?>)"
                                        class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>

                        
                        <?php if(!$docSubido || $docSubido->estado == 'Rechazado'): ?>
                            <button wire:click="$dispatch('abrir-modal-subir', {tipoId: <?php echo e($tipo->id); ?>, nombreTipo: '<?php echo e($tipo->nombre); ?>', userId: <?php echo e($usuarioId); ?>})"
                                    class="p-2 bg-orange-100 text-orange-600 rounded-md hover:bg-orange-200">
                                <i class="fas fa-upload"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('r-r-h-h.subir-documento', ['usuarioId' => $usuarioId]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3349261347-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\livewire\r-r-h-h\gestion-documentos.blade.php ENDPATH**/ ?>