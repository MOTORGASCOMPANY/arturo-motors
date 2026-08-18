<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    <!--x-application-logo class="block h-12 w-auto" /-->
    <div class="text-2xl">
        Hola, <?php echo e(Auth::user()->name); ?> 👋
        <span> </span>
    </div>

    <!-- Métricas principales -->
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dashboard-cards', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-1882259563-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    <!-- Gráficas -->
    
    
</div>




<?php /**PATH C:\xampp\htdocs\Lifegas\resources\views/components/welcome.blade.php ENDPATH**/ ?>