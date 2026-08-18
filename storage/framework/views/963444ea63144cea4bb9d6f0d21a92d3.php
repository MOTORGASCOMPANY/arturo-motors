<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
    <div class="p-4 bg-blue-100 rounded-lg shadow">
        <h3 class="text-sm text-gray-600">Conversiones Activas</h3>
        <p class="text-2xl font-bold text-blue-700"><?php echo e($conversionesActivas); ?></p>
    </div>
    <div class="p-4 bg-green-100 rounded-lg shadow">
        <h3 class="text-sm text-gray-600">Conversiones Finalizadas este Mes</h3>
        <p class="text-2xl font-bold text-green-700"><?php echo e($conversionesMes); ?></p>
    </div>
    <div class="p-4 bg-yellow-100 rounded-lg shadow">
        <h3 class="text-sm text-gray-600">Citas para Hoy</h3>
        <p class="text-2xl font-bold text-yellow-700"><?php echo e($citasHoy); ?></p>
    </div>
    <div class="p-4 bg-red-100 rounded-lg shadow">
        <h3 class="text-sm text-gray-600">Solicitudes FISE Pendientes</h3>
        <p class="text-2xl font-bold text-red-700"><?php echo e($fisePendientes); ?></p>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Arturo\resources\views/livewire/dashboard-cards.blade.php ENDPATH**/ ?>