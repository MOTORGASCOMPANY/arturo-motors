<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['model' => 'icon', 'label' => 'Ícono']));

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

foreach (array_filter((['model' => 'icon', 'label' => 'Ícono']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $icons = [
        'Servicios' => [
            'fa-solid fa-gas-pump' => 'Gas / GNV',
            'fa-solid fa-file-signature' => 'Certificado',
            'fa-solid fa-sliders' => 'Mantenimiento',
            'fa-solid fa-screwdriver-wrench' => 'Mecánica',
            'fa-solid fa-laptop-code' => 'Escáner',
            'fa-solid fa-filter-circle-xmark' => 'Filtros',
            'fa-solid fa-wrench' => 'Herramienta',
            'fa-solid fa-car' => 'Auto',
            'fa-solid fa-oil-can' => 'Aceite',
            'fa-solid fa-gauge-high' => 'Velocidad',
            'fa-solid fa-bolt' => 'Eléctrico',
        ],
        'Proceso' => [
            'fa-solid fa-magnifying-glass' => 'Búsqueda',
            'fa-solid fa-gear' => 'Proceso',
            'fa-solid fa-certificate' => 'Sello',
            'fa-solid fa-clipboard-check' => 'Checklist',
            'fa-solid fa-truck-fast' => 'Entrega',
            'fa-solid fa-handshake' => 'Acuerdo',
        ],
        'Ventajas' => [
            'fa-solid fa-microchip' => 'Tecnología',
            'fa-solid fa-file-contract' => 'Legal',
            'fa-solid fa-piggy-bank' => 'Ahorro',
            'fa-solid fa-clock-rotate-left' => 'Puntualidad',
            'fa-solid fa-shield-halved' => 'Seguridad',
            'fa-solid fa-award' => 'Calidad',
            'fa-solid fa-star' => 'Estrella',
            'fa-solid fa-heart' => 'Pasión',
            'fa-solid fa-thumbs-up' => 'Like',
            'fa-solid fa-check-double' => 'Garantía',
        ],
        'Contacto' => [
            'fa-solid fa-map-location-dot' => 'Mapa',
            'fa-solid fa-phone' => 'Teléfono',
            'fa-solid fa-clock' => 'Horario',
            'fa-solid fa-envelope' => 'Correo',
            'fa-solid fa-location-dot' => 'Pin',
            'fa-solid fa-building' => 'Taller',
        ],
        'Redes' => [
            'fa-brands fa-facebook-f' => 'Facebook',
            'fa-brands fa-instagram' => 'Instagram',
            'fa-brands fa-whatsapp' => 'WhatsApp',
            'fa-brands fa-tiktok' => 'TikTok',
            'fa-brands fa-youtube' => 'YouTube',
            'fa-brands fa-x-twitter' => 'X',
            'fa-brands fa-linkedin-in' => 'LinkedIn',
            'fa-brands fa-telegram' => 'Telegram',
        ],
    ];
?>

<div x-data="{ open: false, search: '' }" class="relative" @click.outside="open = false">
    <label class="block text-sm font-semibold text-gray-700 mb-1.5"><?php echo e($label); ?></label>
    <div class="flex items-center gap-2">
        <div class="w-11 h-11 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-lg border border-blue-100 shrink-0"
             x-data="{ icon: <?php if ((object) ($model) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($model->value()); ?>')<?php echo e($model->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($model); ?>')<?php endif; ?> }">
            <i :class="icon || 'fa-solid fa-cog'"></i>
        </div>
        <input type="hidden" <?php echo e($attributes->wire('model', $model)); ?>>
        <button type="button"
                @click="open = !open"
                class="flex-1 h-11 flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-4 text-sm text-gray-600 hover:bg-blue-50 hover:border-blue-300 transition-colors">
            <span x-data="{ icon: <?php if ((object) ($model) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($model->value()); ?>')<?php echo e($model->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($model); ?>')<?php endif; ?> }" x-text="icon ? 'Ícono seleccionado' : 'Elegir ícono'" :class="icon ? 'text-blue-600 font-medium' : 'text-gray-400'"></span>
            <i class="fa-solid fa-palette text-gray-400"></i>
        </button>
    </div>

    
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-[60] mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden"
         style="right: 0;">

        
        <div class="p-3 border-b border-gray-100">
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text"
                       x-model="search"
                       placeholder="Buscar ícono..."
                       class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        
        <div class="p-3 max-h-64 overflow-y-auto">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $icons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $iconList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2 mt-3 first:mt-0"><?php echo e($category); ?></p>
                    <div class="grid grid-cols-4 gap-1.5">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $iconList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconClass => $iconLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button"
                                    x-show="!search || '<?php echo e(strtolower($iconLabel)); ?>'.includes(search.toLowerCase()) || '<?php echo e(strtolower($iconClass)); ?>'.includes(search.toLowerCase())"
                                    @click="$wire.$set('<?php echo e($model); ?>', '<?php echo e($iconClass); ?>'); open = false"
                                    class="flex flex-col items-center justify-center p-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-600 transition-all"
                                    title="<?php echo e($iconLabel); ?>">
                                <i class="<?php echo e($iconClass); ?> text-lg mb-0.5"></i>
                                <span class="text-[9px] leading-tight text-center truncate w-full"><?php echo e($iconLabel); ?></span>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="p-2 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-[10px] text-gray-400">Elegí un ícono de la lista</span>
            <button type="button" @click="open = false" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Cerrar</button>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/components/icon-picker.blade.php ENDPATH**/ ?>