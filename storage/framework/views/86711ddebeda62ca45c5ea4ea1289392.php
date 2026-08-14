<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow">

    
    <div class="flex justify-between mb-6 text-sm font-medium">
        <span class="<?php echo e($paso >= 1 ? 'text-indigo-600' : 'text-gray-400'); ?>">1. Cliente y vehículo</span>
        <span class="<?php echo e($paso >= 2 ? 'text-indigo-600' : 'text-gray-400'); ?>">2. Servicio</span>
        <span class="<?php echo e($paso >= 3 ? 'text-indigo-600' : 'text-gray-400'); ?>">3. Cobro</span>
        <span class="<?php echo e($paso >= 4 ? 'text-indigo-600' : 'text-gray-400'); ?>">4. Listo</span>
    </div>

    <?php if($paso === 1): ?>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Buscar cliente</label>
                <input type="text" wire:model.live.debounce.300ms="buscarCliente"
                       class="w-full border-gray-300 rounded-md" placeholder="Buscar por nombre...">

                <?php if(count($clientesEncontrados)): ?>
                    <ul class="border rounded-md mt-1 divide-y">
                        <?php $__currentLoopData = $clientesEncontrados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li wire:click="seleccionarCliente(<?php echo e($c['id']); ?>)"
                                class="p-2 hover:bg-gray-100 cursor-pointer">
                                <?php echo e($c['nombre'] . ' ' . $c['apellido']); ?> — <?php echo e($c['documento']); ?>

                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
                <?php $__errorArgs = ['clienteId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <?php if($clienteId): ?>
                <div>
                    <label class="block text-sm font-medium mb-1">Vehículo</label>
                    <select wire:model="vehiculoId" class="w-full border-gray-300 rounded-md">
                        <option value="">-- Selecciona --</option>
                        <?php $__currentLoopData = $this->vehiculos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($v->id); ?>"><?php echo e($v->placa); ?> — <?php echo e($v->marca); ?> <?php echo e($v->modelo); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['vehiculoId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <!-- Crear nuevo vehiculo -->
                    <button wire:click="$set('creandoVehiculoNuevo', true)" type="button"
                            class="text-sm text-indigo-600 mt-1">+ Vehículo nuevo</button>
                    <?php if($creandoVehiculoNuevo): ?>
                        <div class="mt-2 space-y-2 border p-3 rounded-md bg-gray-50">
                            <input wire:model="nuevaPlaca" placeholder="Placa" class="w-full border-gray-300 rounded-md">
                            <input wire:model="nuevaMarca" placeholder="Marca" class="w-full border-gray-300 rounded-md">
                            <input wire:model="nuevoModelo" placeholder="Modelo" class="w-full border-gray-300 rounded-md">
                            <button wire:click="guardarVehiculoNuevo" type="button"
                                    class="bg-indigo-600 text-white px-3 py-1 rounded-md text-sm">Guardar vehículo</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <button wire:click="irAPaso2" type="button"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md">Siguiente</button>
        </div>
    <?php endif; ?>

    <?php if($paso === 2): ?>
        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-2">
                <?php $__currentLoopData = $servicios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center justify-between border rounded-md p-3 cursor-pointer
                                   <?php echo e($serviceId === $s->id ? 'border-indigo-600 bg-indigo-50' : ''); ?>">
                        <span>
                            <input type="radio" wire:click="seleccionarServicio(<?php echo e($s->id); ?>)" class="mr-2">
                            <?php echo e($s->nombre); ?>

                        </span>
                        <span class="text-gray-500">S/ <?php echo e(number_format($s->precio_base, 2)); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['serviceId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <?php if($serviceId): ?>
                <div>
                    <label class="block text-sm font-medium mb-1">Precio a cobrar (negociable)</label>
                    <input type="number" step="0.01" wire:model="precioFinal" class="w-full border-gray-300 rounded-md">
                    <p class="text-xs text-gray-500">Precio de lista: S/ <?php echo e(number_format($precioLista, 2)); ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Motivo del ajuste (si aplica)</label>
                    <input type="text" wire:model="descuentoMotivo" class="w-full border-gray-300 rounded-md">
                    <?php $__errorArgs = ['descuentoMotivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            <?php endif; ?>

            <button wire:click="irAPaso3" type="button"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md">Siguiente</button>
        </div>
    <?php endif; ?>

    <?php if($paso === 3): ?>
        <div class="space-y-4">
            <p class="text-lg">Total a cobrar: <strong>S/ <?php echo e(number_format($precioFinal, 2)); ?></strong></p>

            <div>
                <label class="block text-sm font-medium mb-1">Método de pago</label>
                <select wire:model="metodoPago" class="w-full border-gray-300 rounded-md">
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="otro">Otro</option>
                </select>
            </div>

            <?php $__errorArgs = ['caja'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <button wire:click="procesarCobro" type="button"
                    class="bg-green-600 text-white px-4 py-2 rounded-md">Confirmar cobro</button>
        </div>
    <?php endif; ?>

    <?php if($paso === 4): ?>
        <div class="text-center space-y-3">
            <p class="text-green-600 text-xl">✔ Orden #<?php echo e($ordenCreadaId); ?> registrada</p>
            <p>Folio: <strong><?php echo e($folioGenerado); ?></strong></p>
            <a href="<?php echo e(route('comprobantes.pdf', $ordenCreadaId)); ?>" target="_blank"
               class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md">Ver comprobante PDF</a>
        </div>
    <?php endif; ?>
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\livewire\service-orders\crear-simple.blade.php ENDPATH**/ ?>