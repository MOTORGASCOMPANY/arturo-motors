<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'icon',
    'variant' => 'ghost',
    'wireClick' => null,
    'title' => null,
    'disabled' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'icon',
    'variant' => 'ghost',
    'wireClick' => null,
    'title' => null,
    'disabled' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $variantClasses = [
        'ghost'   => 'text-gray-400 hover:text-gray-600 hover:bg-gray-100',
        'warning' => 'text-amber-500 hover:text-amber-600 hover:bg-amber-50',
        'success' => 'text-green-600 hover:text-green-700 hover:bg-green-50',
        'danger'  => 'text-red-500 hover:text-red-600 hover:bg-red-50',
        'default' => 'text-gray-400 hover:text-gray-600 hover:bg-gray-100',
    ];
?>

<button <?php echo e($attributes->merge([
        'class' => 'w-9 h-9 flex items-center justify-center rounded-lg transition-all disabled:opacity-40 disabled:cursor-not-allowed '
            . ($variantClasses[$variant] ?? $variantClasses['default']),
    ])); ?>

    <?php echo $wireClick ? "wire:click=\"{$wireClick}\"" : ''; ?>

    <?php echo e($disabled ? 'disabled' : ''); ?>

    title="<?php echo e($title ?? ''); ?>">
    <i class="<?php echo e($icon); ?> text-xs"></i>
</button><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/components/cms/action-button.blade.php ENDPATH**/ ?>