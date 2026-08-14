<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

    <!-- Próximas citas -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Próximas Citas</h3>
        <ul>
            <?php $__currentLoopData = $citasProximas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cita): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="border-b py-2">
                    <?php echo e($cita->fecha_cita); ?> - Cliente: <?php echo e($cita->cliente_id); ?>

                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <!-- Vehículos recientes -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Vehículos Recientes</h3>
        <ul>
            <?php $__currentLoopData = $vehiculosRecientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehiculo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="border-b py-2">
                    <?php echo e($vehiculo->placa); ?> - <?php echo e($vehiculo->marca); ?> <?php echo e($vehiculo->modelo); ?>

                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <!-- Expedientes abiertos -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Expedientes en Proceso</h3>
        <ul>
            <?php $__currentLoopData = $expedientesAbiertos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expediente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="border-b py-2">
                    ID: <?php echo e($expediente->id); ?> - Cliente: <?php echo e($expediente->cliente_id); ?>

                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\livewire\dashboard-tables.blade.php ENDPATH**/ ?>