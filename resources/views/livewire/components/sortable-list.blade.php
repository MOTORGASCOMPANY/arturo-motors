<div x-data="sortableList()" x-init="init()"
     class="space-y-3"
     :id="listId"
     wire:ignore>
    @foreach ($items as $index => $item)
        <x-cms.card class="sortable-item flex items-center gap-4 p-4"
                    :data-id="$item[$idField] ?? $item['id'] ?? $loop->index"
                    style="animation: cardEntry 0.3s ease-out {{ $loop->index * 0.05 }}s both; cursor: grab;">
            {{-- Drag Handle --}}
            <div class="drag-handle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                 title="Arrastrar para reordenar"
                 aria-label="Arrastrar para reordenar {{ $item[$labelField] ?? $item['title'] ?? 'elemento' }}">
                <i class="fa-solid fa-grip-vertical text-lg"></i>
            </div>

            {{-- Icon --}}
            @php
                $iconClass = $item[$iconField] ?? $item['icon'] ?? 'fa-solid fa-cog';
            @endphp
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900 flex-shrink-0">
                <i class="{{ $iconClass }}"></i>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <h5 class="font-semibold text-gray-900 dark:text-white truncate">
                        {{ $item[$labelField] ?? $item['title'] ?? 'Sin título' }}
                    </h5>
                    <span class="text-xs font-medium px-2 py-1 rounded-full
                        {{ ($item[$activeField] ?? $item['is_active'] ?? true)
                            ? 'bg-green-50 text-green-600 border border-green-200'
                            : 'bg-gray-50 text-gray-400 border border-gray-200' }}">
                        {{ ($item[$activeField] ?? $item['is_active'] ?? true) ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                @foreach($previewFields as $field)
                    @if(isset($item[$field]))
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Str::limit($item[$field], 60) }}</p>
                    @endif
                @endforeach
            </div>

            {{-- Order Number --}}
            <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400 text-sm font-medium flex-shrink-0">
                {{ $loop->index + 1 }}
            </div>
        </x-cms.card>
    @endforeach

    @if(empty($items))
        <div class="text-center py-12">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-gray-700">
                <i class="fa-solid fa-list text-2xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400">No hay elementos para ordenar</p>
        </div>
    @endif

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

                            @this.call('handleSortableUpdate', newOrder);
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
</div>