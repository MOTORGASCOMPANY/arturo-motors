<!-- resources/views/livewire/lista-expedientes.blade.php -->
<div class="flex box-border">
    <div class="container mx-auto py-4">
        <?php if (isset($component)) { $__componentOriginal4e9b10855d7e168474ae564efb795cfc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4e9b10855d7e168474ae564efb795cfc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.table-expedientes','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table-expedientes'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <!--[if BLOCK]><![endif]--><?php if(count($expedientes)): ?>
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asesor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tecnico</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creación</th>
                                <th class="px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $expedientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($expe->id ?? null); ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        <?php echo e($expe->cliente->nombre . ' ' . $expe->cliente->apellido); ?>

                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($expe->vehiculo->placa ?? null); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($expe->cita->nombre_asesor ?? null); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($expe->tecnico->name ?? 'No Asignado'); ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <?php
                                            $colors = [
                                                'en_evaluacion' => 'bg-yellow-100 text-yellow-800',
                                                'evaluacion_rechazada' => 'bg-red-100 text-red-800',
                                                'aprobado_conversion' => 'bg-blue-100 text-blue-800',
                                                'en_conversion' => 'bg-indigo-100 text-indigo-800',
                                                'conversion_completada' => 'bg-green-100 text-green-800',
                                                'en_control_calidad' => 'bg-purple-100 text-purple-800',
                                                'listo_para_entrega' => 'bg-teal-100 text-teal-800',
                                                'entregado' => 'bg-green-100 text-green-800',
                                                'cancelado' => 'bg-red-100 text-red-800',
                                            ];
                                            $labels = [
                                                'en_evaluacion' => 'En Evaluación',
                                                'evaluacion_rechazada' => 'Evaluación Rechazada',
                                                'aprobado_conversion' => 'Aprobado para Conversión',
                                                'en_conversion' => 'En Conversión',
                                                'conversion_completada' => 'Conversión Completada',
                                                'en_control_calidad' => 'En Control de Calidad',
                                                'listo_para_entrega' => 'Listo para Entrega',
                                                'entregado' => 'Entregado',
                                                'cancelado' => 'Cancelado',
                                            ];
                                        ?>
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($colors[$expe->estado] ?? 'bg-gray-100 text-gray-800'); ?>">
                                            <?php echo e($labels[$expe->estado] ?? 'Desconocido'); ?>

                                        </span>
                                    </td>
                                    <td class="px-10 py-4 text-sm text-gray-500"><?php echo e($expe->created_at->format('d/m/Y H:i')); ?></td>
                                    <td class="text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <!-- Botones de acción aquí -->
                                            <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Administrador del sistema|Vendedor|Jefe de Taller')): ?>
                                            <div class="relative group">
                                                <a wire:click="verExpediente(<?php echo e($expe->id); ?>)"
                                                    class="py-1 px-2 text-center rounded-md bg-amber-300 font-bold text-black cursor-pointer hover:bg-amber-400">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>
                                                <span
                                                    class="absolute bottom-full  mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                                                    Editar
                                                </span>
                                            </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Administrador del sistema|Tecnico')): ?>
                                                <div class="relative group">
                                                    <a wire:click="verEvaluacion(<?php echo e($expe->id); ?>)"
                                                        class="py-1 px-2 text-center rounded-md bg-green-200 font-bold text-black cursor-pointer hover:bg-green-300">
                                                        <i class="fa-solid fa-car-side"></i>
                                                    </a>
                                                    <span
                                                        class="absolute bottom-full  mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                                                        Evaluar
                                                    </span>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <div class="relative group">
                                                <a href="<?php echo e(route('expedientesEvaluacion.pdf', $expe->id)); ?>" target="__blank"
                                                    class="py-1 px-2 text-center rounded-md bg-red-400 font-bold text-black cursor-pointer hover:bg-red-500">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <span class="absolute bottom-full  mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                                                        Ficha
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <!--[if BLOCK]><![endif]--><?php if($expedientes->hasPages()): ?>
                    <div class="mt-2 bg-white px-5 py-5 border-t rounded-lg">
                        <?php echo e($expedientes->links()); ?>

                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            <?php else: ?>
                <div class="px-6 py-4 text-center font-bold bg-teal-100 rounded-md">
                    No se encontró ningún registro.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4e9b10855d7e168474ae564efb795cfc)): ?>
<?php $attributes = $__attributesOriginal4e9b10855d7e168474ae564efb795cfc; ?>
<?php unset($__attributesOriginal4e9b10855d7e168474ae564efb795cfc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4e9b10855d7e168474ae564efb795cfc)): ?>
<?php $component = $__componentOriginal4e9b10855d7e168474ae564efb795cfc; ?>
<?php unset($__componentOriginal4e9b10855d7e168474ae564efb795cfc); ?>
<?php endif; ?>
    </div>

    <!-- Modal para ver y subir documentos y asignar tecnico -->
    <!--[if BLOCK]><![endif]--><?php if($open): ?>
        <?php if (isset($component)) { $__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dialog-modal','data' => ['wire:model' => 'open','wire:loading.attr' => 'disabled','wire:target' => '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dialog-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'open','wire:loading.attr' => 'disabled','wire:target' => '']); ?>
             <?php $__env->slot('title', null, []); ?> 
                Revision de Expediente
             <?php $__env->endSlot(); ?>
             <?php $__env->slot('content', null, []); ?> 
                <!--[if BLOCK]><![endif]--><?php if($expedienteSeleccionado): ?>
                    <!-- Detalles del expediente -->
                    <div class="mb-4  justify-between md:flex md:flex-row justify-content-center sm:block">
                        <h3 class="text-sm font-bold ">Cliente : </h3>
                        <span class="relative inline-block px-3  font-semibold text-black-900 leading-tight">
                            <span aria-hidden class="absolute inset-0 bg-lime-300 opacity-50 rounded-full"></span>
                            <span class="relative"><?php echo e($expedienteSeleccionado->cliente->nombre); ?>

                                <?php echo e($expedienteSeleccionado->cliente->apellido); ?></span>
                        </span>
                        <h3 class="text-sm font-bold ">Vehiculo : </h3>
                        <span class="relative inline-block px-3  font-semibold text-black-900 leading-tight">
                            <span aria-hidden class="absolute inset-0 bg-blue-300 opacity-50 rounded-full"></span>
                            <span class="relative"><?php echo e($expedienteSeleccionado->vehiculo->placa); ?></span>
                        </span>
                        <h3 class="text-sm font-bold ">Fecha : </h3>
                        <p class="text-sm font-bold text-red-500">
                            <?php echo e($expedienteSeleccionado->created_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <!-- Asignación de técnico -->
                    <div class="mb-4">
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['value' => 'Asignar Tecnico:']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Asignar Tecnico:']); ?>
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
                        <select wire:model="tecnico_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Seleccione...</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tecnicos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tec->id); ?>"><?php echo e($tec->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'tecnico_id']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'tecnico_id']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>
                    <!-- Subir documento sectorizado por tipo -->
                    <span class="text-sm text-gray-800">Subir Documentos: </span>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tiposDocumentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-4 border border-gray-200 rounded-lg shadow-sm">
                                <h4 class="font-semibold text-gray-800 text-center mb-2">📄 <?php echo e($tipo->nombre_tipo); ?>

                                </h4>
                                <div id="file-upload-container-<?php echo e($tipo->id); ?>"
                                    class="relative mt-1 flex flex-col items-center justify-center rounded-md border-2 border-dashed border-teal-200 px-6 pt-5 pb-6">
                                    <!-- Input seleccionar -->
                                    <div class="space-y-1 text-center">
                                        <input id="file-upload-<?php echo e($tipo->id); ?>"
                                            name="file-upload-<?php echo e($tipo->id); ?>" type="file"
                                            wire:model="documentoNuevo.<?php echo e($tipo->id); ?>"
                                            accept=".jpg,.png,.jpeg,.gif,.bmp,.tif,.tiff,.pdf" multiple class="sr-only">
                                        
                                        <!--[if BLOCK]><![endif]--><?php if(!isset($documentoNuevo[$tipo->id]) && $files->where('tipo_documento_id', $tipo->id)->isEmpty()): ?>
                                            <div class="flex flex-col items-center">
                                                <label for="file-upload-<?php echo e($tipo->id); ?>"
                                                    class="relative cursor-pointer rounded-md bg-white font-medium text-teal-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-teal-800 focus-within:ring-offset-2 hover:text-teal-800">
                                                    <span>Arrastre aquí o seleccione</span>
                                                </label>
                                                <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 10MB</p>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <!-- Existentes (DB) -->
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $files->where('tipo_documento_id', $tipo->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fil): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="relative w-full mb-2">
                                            <?php
                                                $extension = pathinfo($fil->ruta, PATHINFO_EXTENSION);
                                            ?>
                                            <!--[if BLOCK]><![endif]--><?php if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tif', 'tiff'])): ?>
                                                <img src="<?php echo e($fil->ruta); ?>"
                                                    class="w-full h-40 object-cover rounded-md border border-gray-300" />
                                            <?php elseif($extension === 'pdf'): ?>
                                                <img src="<?php echo e(asset('images/pdf.png')); ?>" alt="PDF Icon"
                                                    class="w-20 object-contain mx-auto my-4" />
                                            <?php else: ?>
                                                <div
                                                    class="w-full h-32 flex items-center justify-center bg-gray-200 rounded-md border border-gray-300">
                                                    <span class="text-gray-500 text-sm">Archivo</span>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <!-- boton para eliminar -->
                                            <button type="button" wire:click="deleteFile(<?php echo e($fil->id); ?>)"
                                                class="absolute top-1 right-1 p-1 rounded-full bg-red-500 text-white hover:bg-red-700 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    <!-- Nuevos (temporal) -->
                                    <!--[if BLOCK]><![endif]--><?php if(isset($documentoNuevo[$tipo->id]) && is_array($documentoNuevo[$tipo->id])): ?>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $documentoNuevo[$tipo->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $otro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="relative w-full mb-2">
                                                <?php
                                                    $extension = pathinfo(
                                                        $otro->getClientOriginalName(),
                                                        PATHINFO_EXTENSION,
                                                    );
                                                ?>
                                                <!--[if BLOCK]><![endif]--><?php if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'tif', 'tiff'])): ?>
                                                    <img src="<?php echo e($otro->temporaryUrl()); ?>"
                                                        class="w-full h-40 object-cover rounded-md border border-green-500" />
                                                <?php elseif($extension === 'pdf'): ?>
                                                    <img src="<?php echo e(asset('images/pdf.png')); ?>" alt="PDF Icon"
                                                        class="w-20 object-contain mx-auto my-4" />
                                                <?php else: ?>
                                                    <div
                                                        class="w-full h-32 flex items-center justify-center bg-green-200 rounded-md border border-green-500">
                                                        <span class="text-green-800 text-sm">Archivo</span>
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <!-- Boton eliminar -->
                                                <button type="button"
                                                    wire:click="deleteFileUpload(<?php echo e($tipo->id); ?>, <?php echo e($key); ?>)"
                                                    class="absolute top-1 right-1 p-1 rounded-full bg-red-500 text-white hover:bg-red-700 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <!-- loading -->
                                <div wire:loading.flex wire:target="documentoNuevo.<?php echo e($tipo->id); ?>"
                                    class="mt-4 items-center justify-center p-4 rounded-lg bg-teal-100 text-teal-700 font-semibold">
                                    Cargando...
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-teal-700"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'documentoNuevo.'.e($tipo->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'documentoNuevo.'.e($tipo->id).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'documentoNuevo.'.e($tipo->id).'.*']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'documentoNuevo.'.e($tipo->id).'.*']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">Cargando información...</p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
             <?php $__env->endSlot(); ?>
             <?php $__env->slot('footer', null, []); ?> 
                <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['wire:click' => '$set(\'open\', false)','class' => 'mx-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => '$set(\'open\', false)','class' => 'mx-2']); ?>
                    Cerrar
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['wire:click' => 'subirDocumento','wire:loading.attr' => 'disabled','wire:target' => 'subirDocumento,documentoNuevo,deleteFile,deleteFileUpload']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'subirDocumento','wire:loading.attr' => 'disabled','wire:target' => 'subirDocumento,documentoNuevo,deleteFile,deleteFileUpload']); ?>
                    Guardar
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
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
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Modal para registrar evaluacion -->
    <!--[if BLOCK]><![endif]--><?php if($openevaluar): ?>
        <?php if (isset($component)) { $__componentOriginal49bd1c1dd878e22e0fb84faabf295a3f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal49bd1c1dd878e22e0fb84faabf295a3f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dialog-modal','data' => ['wire:model' => 'openevaluar','wire:loading.attr' => 'disabled','wire:target' => '','maxWidth' => '4xl']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dialog-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'openevaluar','wire:loading.attr' => 'disabled','wire:target' => '','maxWidth' => '4xl']); ?>
             <?php $__env->slot('title', null, []); ?> 
                <h2 class="text-lg font-bold text-center">FICHA DE RECEPCIÓN DEL VEHÍCULO</h2>
             <?php $__env->endSlot(); ?>

             <?php $__env->slot('content', null, []); ?> 
                <!--[if BLOCK]><![endif]--><?php if($expedienteSeleccionado): ?>
                    

                    <!-- Cliente y DNI -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="font-bold text-gray-700">Cliente / Propietario:</h3>
                            <p class="text-gray-900"><?php echo e($expedienteSeleccionado->cliente->nombre); ?>

                                <?php echo e($expedienteSeleccionado->cliente->apellido); ?></p>
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="font-bold text-gray-700">DNI Ó RUC:</h3>
                            <p class="text-gray-900"><?php echo e($expedienteSeleccionado->cliente->documento); ?></p>
                        </div>
                    </div>

                    <!-- Insertar 3 columnas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
                        <!-- Detalles de 1° columna -->
                        <div class="col-span-1 md:col-span-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Telf. Fijo:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->cliente->telefono); ?></p>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Placa Actual:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->vehiculo->placa); ?></p>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Marca:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->vehiculo->marca); ?></p>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Modelo:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->vehiculo->modelo); ?></p>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Año:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->vehiculo->anio); ?></p>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Kilometraje:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->vehiculo->kilometraje ?? null); ?>

                                </p>
                            </div>
                        </div>
                        <!-- Detalles de 2° columna -->
                        <div class="col-span-1 md:col-span-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Telf. Móvil:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->cliente->telefono_movil); ?></p>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Placa Anterior:</h3>
                                <p class="text-gray-900">
                                    <?php echo e($expedienteSeleccionado->vehiculo->placa_anterior ?? 'N/A'); ?></p>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">N° Motor:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->vehiculo->serie); ?></p>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Color:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->vehiculo->color); ?></p>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-bold text-gray-700">Combustible:</h3>
                                <p class="text-gray-900"><?php echo e($expedienteSeleccionado->vehiculo->combustible); ?></p>
                            </div>
                        </div>
                        <!-- 3° columna (aun no implementaremos la logica solo diseño) -->
                        <div class="col-span-1 md:col-span-1 flex items-center">
                            <div class="border border-gray-300 rounded p-3 text-sm">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" wire:model="inyectado"
                                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                                    <p class="text-gray-900">Inyectado</p>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" wire:model="carburado"
                                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                                    <p class="text-gray-900">Carburado</p>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" wire:model="monopunto"
                                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                                    <p class="text-gray-900">Monopunto</p>
                                </label>
                                <div class="flex items-center">
                                    <h3 class="font-bold text-gray-700 mr-2">Motor de:</h3>
                                    <?php if (isset($component)) { $__componentOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc2fcfa88dc54fee60e0757a7e0572df1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input','data' => ['wire:model' => 'motor_tipo','class' => 'w-32']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'motor_tipo','class' => 'w-32']); ?>
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
                                <div class="mt-2 flex items-center space-x-2">
                                    <p class="text-gray-900">3CIL</p> <input type="checkbox" wire:model="cil3"
                                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                                    <input type="checkbox" wire:model=""
                                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                                    <p class="text-gray-900">.........CIL</p>
                                    <input type="checkbox" wire:model=""
                                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla checklist -->
                    <div class="flex flex-col md:flex-row mt-4 border rounded-lg">
                        <!-- 1° Columna -->
                        <div class="flex-1 border-r border-gray-300 flex flex-col justify-between text-center">
                            <div class="p-4">
                                <h2 class="text-lg font-bold mb-4">ORDEN DE TRABAJO</h2>
                                <p>Nº ..........................................</p>
                            </div>
                            <div class="mt-4 border-t border-gray-300"></div>
                            <div class="p-4">
                                <p class="text-sm mt-8">Con la presente yo y/o en representación autorizo el trabajo a
                                    realizarse en mi vehículo</p>
                            </div>
                            <div class="mt-auto p-4">
                                <div class="mt-8 border-b border-gray-500 pb-2"></div>
                                <p class="text-xs mt-2">FIRMA AUTORIZADA<br>(CLIENTE)</p>
                                <div class="mt-28 border-b border-gray-500 pb-2"></div>
                                <p class="text-xs mt-2">RECIBÍ CONFORME<br>(CLIENTE)</p>
                            </div>
                        </div>
                        <!-- 2° Columna -->
                        <div class="flex-1 p-4 border-r border-gray-300 flex flex-col items-center justify-between">
                            <div class="mt-16">
                                <img src="<?php echo e(asset('images/carro.png')); ?>" alt="Medidor de gasolina" class="">
                            </div>
                        </div>
                        <!-- 3° Columna -->
                        <div class="flex-1">
                            <div class="grid grid-cols-[3fr_1fr_1fr] text-xs font-bold text-center border-b border-gray-300">
                                <div class="text-left px-4"></div>
                                <div class="text-center border-l border-r border-gray-300">SI</div>
                                <div class="text-center">NO</div>
                            </div>

                            <?php
                                // Usamos un array con las claves de la base de datos para simplificar el wire:model
                                $accesorios = [
                                    'tarjeta_propiedad' => 'Tarjeta de propiedad',
                                    'soat' => 'SOAT',
                                    'llave_contacto' => 'Llave de contacto',
                                    'espejos' => 'Espejos',
                                    'antena' => 'Antena',
                                    'plumillas' => 'Plumillas',
                                    'vasos' => 'Vasos',
                                    'emblemas' => 'Emblemas',
                                    'tapa_combustible' => 'Tapa Combustible',
                                    'bateria' => 'Batería',
                                    'seguro_bateria' => 'Seguro de Batería',
                                    'claxon' => 'Claxon',
                                    'tapa_aceite' => 'Tapa Aceite',
                                    'tapa_radiador' => 'Tapa Radiador',
                                    'barita_capot' => 'Varilla de Capot',
                                    'espejo_anterior' => 'Espejo Interior',
                                    'tapasoles' => 'Tapasoles',
                                    'radio' => 'Radio',
                                    'reproductor_cd' => 'Reproductor de CD',
                                    'parlantes' => 'Parlantes',
                                    'cenicero' => 'Cenicero',
                                    'encendedor' => 'Encendedor',
                                    'pisos' => 'Pisos',
                                    'fundas_forros' => 'Fundas o Forros',
                                    'cinturones' => 'Cinturones',
                                    'llanta_repuesto' => 'Llanta de Repuesto',
                                    'gata_palanca' => 'Gata y Palanca',
                                    'llave_ruedas' => 'Llave de Ruedas',
                                    'triangulo' => 'Triángulo',
                                    'extintor' => 'Extintor',
                                    'linterna' => 'Linterna',
                                    'herramientas' => 'Herramientas',
                                    'botiquin' => 'Botiquín',
                                ];
                            ?>
                            
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $accesorios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $display_name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="grid grid-cols-[3fr_1fr_1fr] text-xs border-b border-gray-200 last:border-b-0">
                                    <div class="px-4"><?php echo e($display_name); ?></div>
                                    <div class="text-center border-l border-r border-gray-300">
                                <input type="radio"
                                    name="detalles_<?php echo e($key); ?>"
                                    wire:model="detalles.<?php echo e($key); ?>"
                                    value="1"
                                    class="w-3 h-3 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                            </div>
                            <div class="text-center">
                                <input type="radio"
                                    name="detalles_<?php echo e($key); ?>"
                                    wire:model="detalles.<?php echo e($key); ?>"
                                    value="0"
                                    class="w-3 h-3 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                            </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <!-- PARA CREAR EVALUACION -->
                    <div class="flex items-center mt-2">
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['value' => 'Resultado:','class' => 'mr-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Resultado:','class' => 'mr-2']); ?>
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
                        <!--x-input wire:model="" class="flex-1" /-->
                        <select wire:model="resultado"
                            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option value="">Seleccione...</option>
                            <option value="apto">Apto</option>
                            <option value="no apto">No apto</option>
                        </select>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'resultado','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'resultado','class' => 'mt-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>
                    <div class="col-span-2">
                        <?php if (isset($component)) { $__componentOriginald8ba2b4c22a13c55321e34443c386276 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8ba2b4c22a13c55321e34443c386276 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.label','data' => ['value' => 'Observaciones:']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('label'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => 'Observaciones:']); ?>
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
                        <textarea wire:model="observaciones"
                            class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                            style="height: 100px;"></textarea>
                        <?php if (isset($component)) { $__componentOriginalf94ed9c5393ef72725d159fe01139746 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf94ed9c5393ef72725d159fe01139746 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-error','data' => ['for' => 'observaciones','class' => 'mt-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['for' => 'observaciones','class' => 'mt-1']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $attributes = $__attributesOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__attributesOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf94ed9c5393ef72725d159fe01139746)): ?>
<?php $component = $__componentOriginalf94ed9c5393ef72725d159fe01139746; ?>
<?php unset($__componentOriginalf94ed9c5393ef72725d159fe01139746); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
             <?php $__env->endSlot(); ?>

             <?php $__env->slot('footer', null, []); ?> 
                <?php if (isset($component)) { $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.secondary-button','data' => ['wire:click' => '$set(\'openevaluar\', false)','class' => 'mx-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('secondary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => '$set(\'openevaluar\', false)','class' => 'mx-2']); ?>
                    Cancelar
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $attributes = $__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__attributesOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af)): ?>
<?php $component = $__componentOriginal3b0e04e43cf890250cc4d85cff4d94af; ?>
<?php unset($__componentOriginal3b0e04e43cf890250cc4d85cff4d94af); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['wire:click' => 'guardarEvaluacion','wire:loading.attr' => 'disabled','wire:target' => 'guardarEvaluacion']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'guardarEvaluacion','wire:loading.attr' => 'disabled','wire:target' => 'guardarEvaluacion']); ?>
                    Guardar
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
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
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\xampp\htdocs\Lifegas\resources\views/livewire/lista-expedientes.blade.php ENDPATH**/ ?>