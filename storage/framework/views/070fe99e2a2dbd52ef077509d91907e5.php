<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['section', 'refreshKey' => '', 'highlight' => false]));

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

foreach (array_filter((['section', 'refreshKey' => '', 'highlight' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $anchorMap = [
        'hero' => 'inicio',
        'about' => 'nosotros',
        'services' => 'servicios',
        'why' => 'proceso',
        'process' => 'proceso',
        'contact' => 'contacto',
    ];
    $anchor = $anchorMap[$section['key']] ?? 'contacto';
    $baseUrl = url('/');
    $params = [];
    if ($refreshKey) {
        $params[] = "v={$refreshKey}";
    }
    if ($highlight) {
        $params[] = 'highlight=1';
    }
    $queryString = $params ? '?' . implode('&', $params) : '';
    $iframeUrl = "{$baseUrl}{$queryString}#{$anchor}";
?>


<div wire:ignore class="rounded-xl overflow-hidden border border-gray-200 bg-white relative" style="height: 500px;">

    
    <div class="preview-skeleton absolute inset-0 flex items-center justify-center bg-gray-50 z-10">
        <div class="flex flex-col items-center gap-2">
            <div class="w-6 h-6 border-2 border-blue-200 border-t-blue-600 rounded-full" style="animation: spin 0.7s linear infinite"></div>
            <span class="text-[11px] text-gray-400">Cargando vista previa...</span>
        </div>
    </div>

    <div style="width: 100%; height: 100%; overflow: hidden;">
        <iframe
            src="<?php echo e($iframeUrl); ?>"
            class="border-0 preview-iframe"
            style="width: 200%; height: 200%; transform: scale(0.5); transform-origin: top left; pointer-events: none;"
            loading="lazy"
            tabindex="-1"
            sandbox="allow-scripts allow-same-origin"
            title="Preview de <?php echo e($section['title']); ?>"
        ></iframe>
    </div>
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\components\section-preview.blade.php ENDPATH**/ ?>