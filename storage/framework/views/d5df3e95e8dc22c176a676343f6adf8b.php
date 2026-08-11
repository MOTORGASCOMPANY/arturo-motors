<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['section']));

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

foreach (array_filter((['section']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div class="rounded-xl overflow-hidden border border-gray-200 bg-white relative" style="height: 500px;">
    <div style="width: 100%; height: 100%; overflow: hidden;">
        <iframe
            src="<?php echo e(url('/')); ?>#<?php echo e($section['key'] === 'hero' ? 'inicio' : ($section['key'] === 'about' ? 'nosotros' : ($section['key'] === 'services' ? 'servicios' : ($section['key'] === 'why' ? 'proceso' : ($section['key'] === 'process' ? 'proceso' : 'contacto'))))); ?>"
            class="border-0"
            style="width: 200%; height: 200%; transform: scale(0.5); transform-origin: top left; pointer-events: none;"
            loading="lazy"
            sandbox="allow-scripts allow-same-origin"
            title="Preview de <?php echo e($section['title']); ?>"
        ></iframe>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/components/section-preview.blade.php ENDPATH**/ ?>