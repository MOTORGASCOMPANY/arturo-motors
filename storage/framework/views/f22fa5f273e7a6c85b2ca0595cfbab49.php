<?php if (isset($component)) { $__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dialog-modal','data' => ['wire:model' => 'open']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dialog-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'open']); ?>
     <?php $__env->slot('title', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold">Archivos de planilla: <?php echo e($planilla->contrato->user->name ?? ''); ?></h2>
        </div>
     <?php $__env->endSlot(); ?>
     <?php $__env->slot('content', null, []); ?> 
        <!--[if BLOCK]><![endif]--><?php if($planilla): ?>
            <div class="space-y-5">
                <!-- información básica del empleado -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-2 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-700 mb-1">Empleado</h3>
                    <p class="text-sm text-gray-900 font-semibold"><?php echo e($planilla->contrato->user->name); ?></p>
                    <p class="text-xs text-gray-500 italic font-medium">Documento: <?php echo e($planilla->contrato->user->dni ?? 'No registrado'); ?></p>
                </div>
                <!-- zona de carga de archivos -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-700 mb-3">Subir archivos</h3>
                    <!-- zona de arrastre y selección de archivos -->
                    <div x-data="{ isDropping: false }" @dragover.prevent="isDropping = true"
                        @dragleave.prevent="isDropping = false"
                        @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                        class="relative border-2 border-dashed rounded-xl p-4 transition-all duration-200 flex flex-col items-center justify-center space-y-3"
                        :class="isDropping ? 'border-indigo-500 bg-blue-50' : 'border-indigo-400 bg-gray-50'">
                        <input type="file" x-ref="fileInput" wire:model="archivo" class="hidden" id="file-upload">
                        <p class="text-gray-500 text-sm font-medium text-center">Arrastra tus archivos aquí o haz clic
                            para seleccionarlos</p>
                        <label for="file-upload"
                            class="cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md text-sm font-bold transition-colors shadow-md">
                            Seleccionar archivos
                        </label>

                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['archivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs font-bold"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <!-- vista previa del archivo seleccionado -->
                    <!--[if BLOCK]><![endif]--><?php if($archivo): ?>
                        <div
                            class="mt-4 p-4 border rounded-lg bg-indigo-50 border-indigo-200 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-16 h-16 bg-white rounded border overflow-hidden flex items-center justify-center shadow-sm">
                                    <!--[if BLOCK]><![endif]--><?php if(in_array($archivo->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])): ?>
                                        <img src="<?php echo e($archivo->temporaryUrl()); ?>" class="object-cover w-full h-full">
                                    <?php else: ?>
                                        <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-indigo-900 truncate w-64">
                                        <?php echo e($archivo->getClientOriginalName()); ?></p>
                                    <p class="text-xs text-indigo-700 font-medium italic">Preparado para subir...</p>

                                    <div class="mt-2 flex items-center space-x-2">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase">Categoría:</span>
                                        <select wire:model="tipo"
                                            class="text-[10px] border-gray-300 rounded p-1 h-7 bg-white font-bold">
                                            <option value="boleta">Boleta</option>
                                            <option value="comprobante">Comprobante</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button wire:click="$set('archivo', null)"
                                class="text-gray-400 hover:text-red-500 transition">
                                <i class="fas fa-times-circle text-xl"></i>
                            </button>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <!-- lista de archivos ya subidos -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-2 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-700 mb-4">Archivos guardados (<?php echo e($planilla->archivos->count()); ?>)</h3>
                    <div class="space-y-4">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ['comprobante', 'boleta', 'boleta_firmada']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>
                                <h4 class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 border-b pb-1"> <?php echo e($t); ?></h4>
                                <div class="space-y-3">
                                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $planilla->archivos->where('tipo', $t); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="flex flex-wrap items-center justify-between p-2 border border-gray-100 rounded-lg bg-white hover:bg-white hover:border-blue-200 transition-all shadow-sm gap-3">
                                            <!-- vista previa del archivo -->
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-white border border-gray-200 rounded flex items-center justify-center shadow-sm text-lg overflow-hidden flex-shrink-0">
                                                    <!--[if BLOCK]><![endif]--><?php if(in_array($file->extension, ['jpg', 'jpeg', 'png'])): ?>
                                                        <img src="<?php echo e(Storage::url($file->ruta)); ?>" alt="<?php echo e($file->nombre); ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <i class="fas fa-file-pdf"></i>
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </div>
                                                <div class="max-w-[180px]">
                                                    <p class="text-xs font-bold text-gray-800 truncate"
                                                        title="<?php echo e($file->nombre); ?>">
                                                        <?php echo e($file->nombre); ?>

                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-medium italic">
                                                        Subido: <?php echo e($file->created_at->format('d/m/Y H:i')); ?>

                                                    </p>
                                                </div>
                                            </div>
                                            <!-- acciones para cada archivo -->
                                            <div class="flex items-center flex-wrap gap-2">
                                                <select class="text-[10px] border-gray-300 rounded-md p-1 h-8 bg-white font-bold text-gray-600 focus:ring-0 shadow-sm"
                                                    wire:change="cambiarTipo(<?php echo e($file->id); ?>, $event.target.value)">
                                                    <option value="comprobante" <?php echo e($file->tipo == 'comprobante' ? 'selected' : ''); ?>>Comprobante</option>
                                                    <option value="boleta" <?php echo e($file->tipo == 'boleta' ? 'selected' : ''); ?>>Boleta</option>
                                                    <option value="boleta_firmada" <?php echo e($file->tipo == 'boleta_firmada' ? 'selected' : ''); ?>>Boleta Firmada</option>
                                                </select>
                                                <a href="<?php echo e(Storage::url($file->ruta)); ?>" target="_blank"
                                                    class="px-3 py-1.5 border border-gray-200 rounded text-[10px] font-bold text-gray-700 hover:bg-gray-100 uppercase transition">Ver</a>
                                                <button wire:click="descargarArchivo(<?php echo e($file->id); ?>)"
                                                    class="px-3 py-1.5 border border-gray-200 rounded text-[10px] font-bold text-gray-700 hover:bg-gray-100 uppercase transition">Descargar</button>
                                                <button wire:click="eliminarArchivo(<?php echo e($file->id); ?>)"
                                                    class="px-3 py-1.5 bg-red-500 text-white rounded text-[10px] font-bold hover:bg-red-600 uppercase transition shadow-sm">Eliminar</button>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <p class="text-[11px] text-gray-400 italic px-2">Sin archivos en esta categoría.</p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="py-10 text-center">
                <i class="fas fa-spinner fa-spin text-gray-400 text-3xl mb-2"></i>
                <p class="text-gray-500 text-sm">Cargando información...</p>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
     <?php $__env->endSlot(); ?>
     <?php $__env->slot('footer', null, []); ?> 
        <div class="flex justify-end space-x-3 border-t pt-5 w-full">
            <button wire:click="$set('open', false)"
                class="px-6 py-2 border border-gray-300 rounded-md text-xs font-bold text-gray-700 hover:bg-gray-50 uppercase tracking-widest transition">
                Cerrar
            </button>
            <button wire:click="save" wire:loading.attr="disabled"
                class="px-6 py-2 bg-orange-500 text-white rounded-md text-xs font-bold hover:bg-orange-600 uppercase tracking-widest transition shadow-lg flex items-center">
                <span wire:loading.remove wire:target="save">Guardar</span>
                <span wire:loading wire:target="save">Procesando...</span>
            </button>
        </div>
     <?php $__env->endSlot(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f)): ?>
<?php $attributes = $__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f; ?>
<?php unset($__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f)): ?>
<?php $component = $__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f; ?>
<?php unset($__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/r-r-h-h/planilla-archivos.blade.php ENDPATH**/ ?>