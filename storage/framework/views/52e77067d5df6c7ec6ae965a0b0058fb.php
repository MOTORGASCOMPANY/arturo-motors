<div x-data="sortableList()" x-init="init()"
     class="space-y-3"
     :id="listId"
     wire:ignore>
    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if (isset($component)) { $__componentOriginal22c3416241bd13185beb9fb89a01cdd3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cms.card','data' => ['class' => 'sortable-item flex items-center gap-4 p-4','dataId' => $item[$idField] ?? $item['id'] ?? $loop->index,'style' => 'animation: cardEntry 0.3s ease-out '.e($loop->index * 0.05).'s both; cursor: grab;']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cms.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'sortable-item flex items-center gap-4 p-4','data-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item[$idField] ?? $item['id'] ?? $loop->index),'style' => 'animation: cardEntry 0.3s ease-out '.e($loop->index * 0.05).'s both; cursor: grab;']); ?>
            
            <div class="drag-handle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                 title="Arrastrar para reordenar"
                 aria-label="Arrastrar para reordenar <?php echo e($item[$labelField] ?? $item['title'] ?? 'elemento'); ?>">
                <i class="fa-solid fa-grip-vertical text-lg"></i>
            </div>

            
            <?php
                $iconClass = $item[$iconField] ?? $item['icon'] ?? 'fa-solid fa-cog';
            ?>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900 flex-shrink-0">
                <i class="<?php echo e($iconClass); ?>"></i>
            </div>

            
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <h5 class="font-semibold text-gray-900 dark:text-white truncate">
                        <?php echo e($item[$labelField] ?? $item['title'] ?? 'Sin título'); ?>

                    </h5>
                    <span class="text-xs font-medium px-2 py-1 rounded-full
                        <?php echo e(($item[$activeField] ?? $item['is_active'] ?? true)
                            ? 'bg-green-50 text-green-600 border border-green-200'
                            : 'bg-gray-50 text-gray-400 border border-gray-200'); ?>">
                        <?php echo e(($item[$activeField] ?? $item['is_active'] ?? true) ? 'Activo' : 'Inactivo'); ?>

                    </span>
                </div>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $previewFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <!--[if BLOCK]><![endif]--><?php if(isset($item[$field])): ?>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo e(Str::limit($item[$field], 60)); ?></p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400 text-sm font-medium flex-shrink-0">
                <?php echo e($loop->index + 1); ?>

            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal22c3416241bd13185beb9fb89a01cdd3)): ?>
<?php $attributes = $__attributesOriginal22c3416241bd13185beb9fb89a01cdd3; ?>
<?php unset($__attributesOriginal22c3416241bd13185beb9fb89a01cdd3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal22c3416241bd13185beb9fb89a01cdd3)): ?>
<?php $component = $__componentOriginal22c3416241bd13185beb9fb89a01cdd3; ?>
<?php unset($__componentOriginal22c3416241bd13185beb9fb89a01cdd3); ?>
<?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if(empty($items)): ?>
        <div class="text-center py-12">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-gray-700">
                <i class="fa-solid fa-list text-2xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400">No hay elementos para ordenar</p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <script>
        function sortableList() {
            return {
                listId: 'sortable-' + Math.random().toString(36).substr(2, 9),
                sortable: null,
                init() {
                    this.$nextTick(() => {
                        this.initSortable();
                    });
                },
                initSortable() {
                    if (typeof Sortable === 'undefined') {
                        console.warn('SortableJS not loaded');
                        return;
                    }

                    const el = document.getElementById(this.listId);
                    if (!el) return;

                    this.sortable = new Sortable(el, {
                        animation: 200,
                        handle: '.drag-handle',
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        dragClass: 'sortable-drag',
                        forceFallback: false,
                        delay: 100,
                        delayOnTouchOnly: true,
                        onEnd: (evt) => {
                            const newOrder = Array.from(el.querySelectorAll('.sortable-item'))
                                .map(item => item.dataset.id);

                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('handleSortableUpdate', newOrder);
                        }
                    });
                }
            }
        }
    </script>

    <style>
        @keyframes cardEntry { from { opacity: 0; transform: translateY(10px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .sortable-ghost {
            opacity: 0.4;
            background: #eff6ff !important;
            border: 2px dashed #3b82f6 !important;
        }

        .sortable-chosen {
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
            transform: scale(1.02);
        }

        .sortable-drag {
            box-shadow: 0 20px 40px rgba(0,0,0,0.2) !important;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        /* Reduce motion */
        @media (prefers-reduced-motion: reduce) {
            .sortable-item { animation: none !important; transition: none !important; }
            .sortable-ghost { transition: none !important; }
        }
    </style>
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/components/sortable-list.blade.php ENDPATH**/ ?>