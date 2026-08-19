<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <link rel="icon" type="image/png" href="<?php echo e(asset('images/icon.png')); ?>" />
        <title>ARTURO MOTORS</title>
        <!-- Este es el app.blade.php de components/layouts -->

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

        
        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        
        <!-- Scripts -->
        

        <!-- Styles -->
        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>


    <body>
        <?php if (isset($component)) { $__componentOriginalff9615640ecc9fe720b9f7641382872b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalff9615640ecc9fe720b9f7641382872b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.banner','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalff9615640ecc9fe720b9f7641382872b)): ?>
<?php $attributes = $__attributesOriginalff9615640ecc9fe720b9f7641382872b; ?>
<?php unset($__attributesOriginalff9615640ecc9fe720b9f7641382872b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalff9615640ecc9fe720b9f7641382872b)): ?>
<?php $component = $__componentOriginalff9615640ecc9fe720b9f7641382872b; ?>
<?php unset($__componentOriginalff9615640ecc9fe720b9f7641382872b); ?>
<?php endif; ?>

        <div class="min-h-screen bg-gray-100">
            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('custom-nav-menu');

$__html = app('livewire')->mount($__name, $__params, 'lw-3724288248-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

            <!-- Page Content -->
            <main class="pt-16">
                <?php echo e($slot); ?>

            </main>
        </div>

        
        <?php echo $__env->yieldPushContent('modals'); ?>

        <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>


        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Script para SweetAlert2 con Livewire -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                // 1. Notificaciones enviadas por redirección con session()->flash('swal', [...])
                <?php if(session()->has('swal')): ?>
                    (function() {
                        const swalData = <?php echo json_encode(session('swal'), 15, 512) ?>;
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: swalData.icono || swalData.icon || 'success',
                            title: swalData.titulo || swalData.title || '',
                            text: swalData.mensaje || swalData.text || '',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });
                    })();
                <?php endif; ?>

                // 2. Alerta Modal Centrada vía Livewire dispatch (sin redirección)
                Livewire.on('minAlert', function(params) {
                    Swal.fire({
                        title: params.titulo || params['titulo'],
                        text: params.mensaje || params['mensaje'],
                        icon: params.icono || params['icono']
                    });
                });

                // 3. Toast en tiempo real vía Livewire dispatch (sin redirección)
                Livewire.on('minToast', function(params) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: params.icono || params['icono'],
                        title: params.titulo || params['titulo'],
                        text: params.mensaje || params['mensaje'],
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            });
        </script>

        <?php echo $__env->yieldPushContent('js'); ?>


        <footer>
            <div class="text-xs text-slate-700  float-right">
                Powered by GHFDEV ®
            </div>
        </footer>
    </body>

</html>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>