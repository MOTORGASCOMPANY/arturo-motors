<div x-data="{ showUpload: false }" x-init="$watch('$wire.showUploadModal', v => showUpload = v)"
    @documento-subido.window="showUpload = false; Swal.fire({icon:'success', title:'¡Listo!', text: $event.detail.mensaje || 'Documento subido correctamente.', timer:2200, showConfirmButton:false})"
    @documento-eliminado.window="Swal.fire({icon:'success', title:'Eliminado', text: $event.detail.mensaje || 'Documento eliminado.', timer:2200, showConfirmButton:false})"
    @documento-error.window="Swal.fire({icon:'error', title:'Ups', text: $event.detail.mensaje || 'Ocurrió un error.'})">

    <?php if (! $__env->hasRenderedOnce('1f5f9af0-ecc6-4197-bf63-7e797da024ba')): $__env->markAsRenderedOnce('1f5f9af0-ecc6-4197-bf63-7e797da024ba'); ?>
        <script>
            if (typeof Swal === 'undefined') {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                s.defer = true;
                document.head.appendChild(s);
            }
        </script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('visorArchivos', () => ({
                    show: false, url: '', title: '', status: 'loading', isImage: false,
                    zoom: 1, panning: false, panStartX: 0, panStartY: 0,
                    scrollStartX: 0, scrollStartY: 0, moved: false, cache: {},

                    async abrir(url, title) {
                        this.url = url; this.title = title; this.status = 'loading';
                        this.isImage = false; this.zoom = 1; this.show = true;
                        if (this.cache[url]) { this.status = 'ok'; this.isImage = this.cache[url] === 'image'; return; }
                        try {
                            const r = await fetch(url, { method: 'HEAD', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const ct = r.headers.get('content-type') || '';
                            if (ct.includes('pdf')) { this.cache[url] = 'pdf'; this.status = 'ok'; }
                            else if (ct.includes('image')) { this.cache[url] = 'image'; this.status = 'ok'; this.isImage = true; }
                            else this.status = 'error';
                        } catch { this.status = 'error'; }
                    },
                    zoomIn() { this.zoom = Math.min(3, +(this.zoom + 0.25).toFixed(2)); },
                    zoomOut() { this.zoom = Math.max(0.5, +(this.zoom - 0.25).toFixed(2)); },
                    zoomReset() { this.zoom = 1; },
                    cerrar() { this.show = false; setTimeout(() => { this.url = ''; this.status = 'loading'; this.isImage = false; this.zoom = 1; }, 150); },
                    startPan(e) { if (this.zoom <= 1) return; const c = this.$refs.vp; if (!c) return; this.panning = true; this.moved = false; this.panStartX = e.clientX; this.panStartY = e.clientY; this.scrollStartX = c.scrollLeft; this.scrollStartY = c.scrollTop; },
                    movePan(e) { if (!this.panning) return; const c = this.$refs.vp; if (!c) return; const dx = e.clientX - this.panStartX, dy = e.clientY - this.panStartY; if (Math.abs(dx) > 3 || Math.abs(dy) > 3) this.moved = true; c.scrollLeft = this.scrollStartX - dx; c.scrollTop = this.scrollStartY - dy; },
                    stopPan() { this.panning = false; },
                }));
            });
        </script>
    <?php endif; ?>

    <div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
        
        <a href="<?php echo e(route('ordenes.listado')); ?>" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
            <i class="fas fa-arrow-left text-gray-500"></i> Volver a las órdenes de servicios
        </a>

        
        <div class="bg-gray-200 p-8 rounded-xl w-full border border-gray-300/80 shadow-sm">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-sm font-bold text-lg"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 leading-tight">
                            Orden #<?php echo e($orden->id); ?>

                            <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion'): ?>
                                <span class="text-xs bg-purple-200 text-purple-800 px-2 py-0.5 rounded-full font-semibold align-middle ml-1">Conversión</span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </h2>
                        <p class="text-xs text-gray-700 flex items-center gap-1 mt-0.5">
                            <i class="far font-normal fa-calendar-alt text-gray-600"></i>
                            Creada el <?php echo e($orden->created_at->format('d/m/Y H:i')); ?> por <span class="font-medium text-gray-800"><?php echo e($orden->creadoPor->name); ?></span>
                        </p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm animate-pulse">
                    <span class="w-2 h-2 rounded-full <?php echo e(match (true) {
                        str_contains(strtolower($orden->estado), 'cancel') || str_contains(strtolower($orden->estado), 'rechaz') => 'bg-red-500',
                        in_array(strtolower($orden->estado), ['entregada', 'entregado']) => 'bg-emerald-500',
                        default => 'bg-amber-500',
                    }); ?>"></span> <?php echo e(ucfirst(str_replace('_', ' ', $orden->estado))); ?>

                </span>
            </div>
        </div>

        
        <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i class="fas fa-user mr-1 text-gray-600"></i> Cliente y vehículo</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-600 text-xs font-medium">Cliente</p>
                    <p class="font-bold text-gray-900"><?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?></p>
                    <p class="text-gray-700 text-xs"><?php echo e($orden->cliente->documento); ?> — <?php echo e($orden->cliente->telefono ?? 'sin teléfono'); ?></p>
                </div>
                <div>
                    <p class="text-gray-600 text-xs font-medium">Vehículo</p>
                    <p class="font-bold text-gray-900"><?php echo e($orden->vehiculo->placa); ?> — <?php echo e($orden->vehiculo->marca); ?> <?php echo e($orden->vehiculo->modelo); ?></p>
                    <p class="text-gray-700 text-xs">Año: <?php echo e($orden->vehiculo->anio ?? '—'); ?></p>
                </div>
            </div>
        </div>

        
        <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i class="fas fa-tag mr-1 text-gray-600"></i> Servicio y precio</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><p class="text-gray-600 text-xs font-medium">Servicio</p><p class="font-bold text-gray-900"><?php echo e($orden->service->nombre); ?></p></div>
                <div><p class="text-gray-600 text-xs font-medium">Precio de lista</p><p class="font-bold text-gray-800">S/ <?php echo e(number_format($orden->precio_lista, 2)); ?></p></div>
                <div><p class="text-gray-600 text-xs font-medium">Precio final</p><p class="font-bold <?php echo e($orden->precio_final != $orden->precio_lista ? 'text-amber-700' : 'text-gray-900'); ?>">S/ <?php echo e(number_format($orden->precio_final, 2)); ?></p></div>
            </div>
            <!--[if BLOCK]><![endif]--><?php if($orden->descuento_motivo): ?>
                <p class="text-xs text-gray-700 mt-3 font-medium"><i class="fas fa-comment-dots mr-1 text-gray-500"></i> Motivo del ajuste: <?php echo e($orden->descuento_motivo); ?></p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion' && $orden->checklist_evaluacion): ?>
            <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i class="fas fa-clipboard-check mr-1 text-gray-600"></i> Evaluación técnica</h3>
                <div class="flex items-center gap-3 mb-4 text-sm">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($orden->evaluacion_aprobada ? 'bg-emerald-200 text-emerald-900' : 'bg-red-200 text-red-900'); ?>"><?php echo e($orden->evaluacion_aprobada ? 'Apto' : 'No apto'); ?></span>
                    <span class="text-gray-700 font-medium">Por <?php echo e($orden->evaluadoPor?->name); ?> — <?php echo e($orden->evaluado_en?->format('d/m/Y H:i')); ?></span>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($orden->evaluacion_observaciones): ?>
                    <p class="text-sm bg-white/80 border border-gray-300 rounded-lg p-3 mb-4 text-gray-800 font-medium"><?php echo e($orden->evaluacion_observaciones); ?></p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 text-xs">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $checklistGrupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clave => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center gap-1.5 py-1">
                                <i class="fas <?php echo e(($orden->checklist_evaluacion[$clave] ?? false) ? 'fa-check-circle text-emerald-600' : 'fa-times-circle text-gray-400'); ?>"></i>
                                <span class="<?php echo e(($orden->checklist_evaluacion[$clave] ?? false) ? 'text-gray-900 font-semibold' : 'text-gray-600'); ?>"><?php echo e($label); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion' && $orden->tecnico): ?>
            <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i class="fas fa-wrench mr-1 text-gray-600"></i> Conversión</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm mb-4">
                    <div><p class="text-gray-600 text-xs font-medium">Técnico</p><p class="font-bold text-gray-900"><?php echo e($orden->tecnico->name); ?></p></div>
                    <div><p class="text-gray-600 text-xs font-medium">Inicio</p><p class="font-bold text-gray-900"><?php echo e($orden->fecha_inicio_conversion?->format('d/m/Y H:i') ?? '—'); ?></p></div>
                    <div><p class="text-gray-600 text-xs font-medium">Fin</p><p class="font-bold text-gray-900"><?php echo e($orden->fecha_fin_conversion?->format('d/m/Y H:i') ?? '—'); ?></p></div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($orden->items->count()): ?>
                    <p class="text-xs font-bold text-gray-700 uppercase mb-2">Equipos instalados</p>
                    <div class="space-y-1.5 mb-4">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between text-sm bg-white/80 border border-gray-300 rounded-lg px-3 py-2">
                                <span class="font-medium text-gray-800"><?php echo e($item->producto->categoria->nombre); ?> — <?php echo e($item->producto->nombre); ?> (<?php echo e($item->producto->marca); ?>)</span>
                                <span class="font-mono text-xs text-gray-700 font-semibold">Serie: <?php echo e($item->serie); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <!--[if BLOCK]><![endif]--><?php if($orden->movimientosStock->count()): ?>
                    <p class="text-xs font-bold text-gray-700 uppercase mb-2">Repuestos utilizados</p>
                    <div class="space-y-1.5">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->movimientosStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between text-sm bg-white/80 border border-gray-300 rounded-lg px-3 py-2">
                                <span class="font-medium text-gray-800"><?php echo e($mov->producto->nombre); ?></span>
                                <span class="text-gray-700 font-semibold">× <?php echo e($mov->cantidad); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        
        <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i class="fas fa-receipt mr-1 text-gray-600"></i> Comprobante</h3>
            <!--[if BLOCK]><![endif]--><?php if($orden->comprobante): ?>
                <div class="flex items-center justify-between text-sm">
                    <div>
                        <p class="font-bold text-gray-900">Folio: <?php echo e($orden->comprobante->folio); ?></p>
                        <p class="text-gray-700 text-xs font-medium"><?php echo e(ucfirst($orden->comprobante->metodo_pago)); ?> — <?php echo e($orden->comprobante->created_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <a href="<?php echo e(route('comprobantes.pdf', $orden->id)); ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors shadow-sm">Ver PDF →</a>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-600 font-medium">Aún no se ha generado comprobante (pendiente de cobro).</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6" x-data="visorArchivos">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800 uppercase"><i class="fas fa-file-pdf mr-1 text-gray-600"></i> Documentos</h3>
                <button type="button" wire:click="abrirModalSubida(<?php echo e($orden->id); ?>)"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition active:scale-95">
                    <i class="fa-solid fa-plus text-[10px]"></i> Subir
                </button>
            </div>

            
            <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion'): ?>
                <div class="flex flex-wrap gap-2 mb-4">
                    <!--[if BLOCK]><![endif]--><?php if($orden->checklist_evaluacion): ?>
                        <a href="<?php echo e(route('conversiones.pdf.evaluacion', $orden->id)); ?>" target="_blank" class="bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg border border-gray-200 transition"><i class="fas fa-clipboard-check mr-1"></i> Evaluación</a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php if($orden->items->count()): ?>
                        <a href="<?php echo e(route('conversiones.pdf.ficha-tecnica', $orden->id)); ?>" target="_blank" class="bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg border border-gray-200 transition"><i class="fas fa-wrench mr-1"></i> Ficha técnica</a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php if(in_array(strtolower($orden->estado), ['entregado', 'entregada'])): ?>
                        <a href="<?php echo e(route('conversiones.pdf.garantia', $orden->id)); ?>" target="_blank" class="bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg border border-gray-200 transition"><i class="fas fa-shield-alt mr-1"></i> Garantía</a>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <?php
                $sistemaDocs = collect([
                    $orden->comprobante ? ['label' => 'Comprobante', 'icon' => 'fa-receipt', 'url' => route('comprobantes.pdf', $orden->id), 'color' => 'blue'] : null,
                    $orden->vehiculo ? ['label' => 'Carta garantía', 'icon' => 'fa-shield-halved', 'url' => route('vehiculo.pdf', $orden->vehiculo_id), 'color' => 'emerald'] : null,
                    $orden->vehiculo ? ['label' => 'Manual', 'icon' => 'fa-book', 'url' => route('manual.pdf', $orden->vehiculo_id), 'color' => 'amber'] : null,
                ])->filter();
                $todosDocs = $sistemaDocs->concat(
                    $orden->documentos->map(fn($d) => ['label' => str_replace('_', ' ', $d->tipo), 'icon' => 'fa-file-lines', 'url' => $d->url, 'color' => 'slate', 'id' => $d->id])
                );
            ?>

            <!--[if BLOCK]><![endif]--><?php if($todosDocs->count()): ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sistemaDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" @click="abrir('<?php echo e($doc['url']); ?>', '<?php echo e($doc['label']); ?>')"
                            class="flex items-center gap-2.5 p-3 bg-white border border-<?php echo e($doc['color']); ?>-200 rounded-xl hover:shadow-md hover:border-<?php echo e($doc['color']); ?>-400 transition-all text-left group">
                            <div class="w-9 h-9 rounded-lg bg-<?php echo e($doc['color']); ?>-500 text-white flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <i class="fa-solid <?php echo e($doc['icon']); ?> text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-800 truncate"><?php echo e($doc['label']); ?></p>
                                <p class="text-[10px] text-gray-400">Sistema</p>
                            </div>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->documentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-2.5 p-3 bg-white border border-gray-200 rounded-xl hover:shadow-md hover:border-indigo-300 transition-all group">
                            <button type="button" @click="abrir('<?php echo e($d->url); ?>', '<?php echo e(str_replace('_', ' ', $d->tipo)); ?>')"
                                class="flex items-center gap-2.5 flex-1 min-w-0 text-left">
                                <div class="w-9 h-9 rounded-lg bg-gray-100 text-gray-500 group-hover:bg-indigo-500 group-hover:text-white flex items-center justify-center shrink-0 transition-all">
                                    <i class="fa-solid fa-file-lines text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-800 capitalize truncate"><?php echo e(str_replace('_', ' ', $d->tipo)); ?></p>
                                    <p class="text-[10px] text-gray-400">Subido</p>
                                </div>
                            </button>
                            <button type="button" @click="
                                Swal.fire({ icon:'warning', title:'¿Eliminar?', text:'Acción irreversible.', showCancelButton:true, confirmButtonText:'Sí', cancelButtonText:'Cancelar', confirmButtonColor:'#dc2626' })
                                .then(r => { if(r.isConfirmed) $wire.eliminarDocumento(<?php echo e($d->id); ?>) })
                            " class="shrink-0 w-7 h-7 flex items-center justify-center rounded-md text-gray-300 hover:text-red-500 hover:bg-red-50 transition">
                                <i class="fa-solid fa-trash text-[10px]"></i>
                            </button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php else: ?>
                <div class="flex flex-col items-center py-6 bg-white border-2 border-dashed border-gray-200 rounded-xl text-center">
                    <div class="w-10 h-10 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mb-2"><i class="fa-solid fa-inbox text-sm"></i></div>
                    <p class="text-xs font-semibold text-gray-500">Sin documentos</p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <template x-teleport="body">
                <div x-show="show" x-cloak @keydown.escape.window="cerrar()" class="fixed inset-0 z-[90]" style="display:none;">
                    <div class="absolute inset-0 bg-black/60" @click="cerrar()"></div>
                    <div class="absolute inset-2 sm:inset-4 bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden"
                        x-show="show" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-100 bg-white shrink-0">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <i class="fa-solid" :class="isImage ? 'fa-image text-emerald-500' : 'fa-file-pdf text-red-500'"></i>
                                <span class="text-sm font-bold text-gray-800 truncate" x-text="title"></span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <template x-if="isImage && status === 'ok'">
                                    <div class="flex items-center gap-0.5 bg-gray-100 rounded-lg p-0.5 mr-1">
                                        <button @click="zoomOut()" class="w-6 h-6 flex items-center justify-center rounded text-gray-500 hover:bg-white transition"><i class="fa-solid fa-minus text-[10px]"></i></button>
                                        <span class="text-[10px] font-bold text-gray-600 w-8 text-center" x-text="Math.round(zoom*100)+'%'"></span>
                                        <button @click="zoomIn()" class="w-6 h-6 flex items-center justify-center rounded text-gray-500 hover:bg-white transition"><i class="fa-solid fa-plus text-[10px]"></i></button>
                                        <button @click="zoomReset()" class="w-6 h-6 flex items-center justify-center rounded text-gray-500 hover:bg-white transition"><i class="fa-solid fa-expand text-[10px]"></i></button>
                                    </div>
                                </template>
                                <button @click="window.open(url, '_blank')" x-show="status==='ok'" class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg hover:bg-indigo-100 transition hidden sm:block">Externo</button>
                                <button @click="cerrar()" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </div>

                        <div class="flex-1 bg-gray-100 relative overflow-hidden">
                            <div x-show="status==='loading'" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white z-10">
                                <div class="w-8 h-8 rounded-full border-[3px] border-indigo-200 border-t-indigo-600 animate-spin"></div>
                                <span class="text-xs font-bold text-gray-500">Cargando...</span>
                            </div>
                            <div x-show="status==='error'" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white z-10">
                                <i class="fa-solid fa-triangle-exclamation text-amber-400 text-2xl"></i>
                                <p class="text-sm font-bold text-gray-600">No disponible</p>
                                <button @click="cerrar()" class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition">Cerrar</button>
                            </div>
                            <template x-if="status==='ok' && !isImage">
                                <iframe :src="url" class="w-full h-full border-0" loading="lazy"></iframe>
                            </template>
                            <template x-if="status==='ok' && isImage">
                                <div class="w-full h-full overflow-auto p-4 select-none" :class="zoom<=1?'flex items-center justify-center':''" x-ref="vp"
                                    @mousedown="startPan($event)" @mousemove.window.throttle.16ms="movePan($event)" @mouseup.window="stopPan()" @mouseleave="stopPan()">
                                    <img :src="url" :style="zoom>1?`width:${zoom*100}%`:''"
                                        @click="if(moved){moved=false}else{zoom=zoom===1?1.5:1}"
                                        @dragstart.prevent :class="zoom<=1?'max-w-full max-h-[85vh] object-contain cursor-zoom-in':(panning?'block mx-auto max-w-none cursor-grabbing':'block mx-auto max-w-none cursor-grab')"
                                        class="rounded-xl shadow-lg transition-[width] duration-100 bg-white" draggable="false" loading="lazy" alt="Documento">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        
        <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
            <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i class="fas fa-history mr-1 text-gray-600"></i> Historial</h3>
            <div class="space-y-3">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->historialEstados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-600 mt-1 flex-shrink-0"></div>
                        <div>
                            <p class="text-gray-900">
                                <!--[if BLOCK]><![endif]--><?php if($h->estado_anterior): ?><span class="text-gray-600"><?php echo e(ucfirst(str_replace('_', ' ', $h->estado_anterior))); ?></span> → <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <span class="font-bold"><?php echo e(ucfirst(str_replace('_', ' ', $h->estado_nuevo))); ?></span>
                            </p>
                            <p class="text-xs text-gray-700 font-medium"><?php echo e($h->created_at->format('d/m/Y H:i')); ?> — <?php echo e($h->usuario->name ?? 'Sistema'); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($showUploadModal): ?>
        <div id="modal-subida-container" class="fixed inset-0 z-[80] flex items-center justify-center p-3 sm:p-6" wire:key="modal-subida">
            <div class="fixed inset-0 bg-black/40" wire:click="cerrarModalSubida"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" x-data="{ dragging: false }"
                x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                <div wire:loading wire:target="guardarDocumento" class="absolute inset-0 bg-white/95 z-50 flex flex-col items-center justify-center">
                    <div class="w-12 h-12 rounded-full border-[3px] border-indigo-200 border-t-indigo-600 animate-spin mb-3"></div>
                    <p class="text-sm font-bold text-gray-700">Subiendo...</p>
                </div>

                <div class="flex items-center justify-between px-5 py-3.5 bg-indigo-600 text-white">
                    <h3 class="text-sm font-bold flex items-center gap-2"><i class="fa-solid fa-cloud-arrow-up text-indigo-200"></i> Subir Documento</h3>
                    <button wire:click="cerrarModalSubida" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition"><i class="fa-solid fa-xmark text-sm"></i></button>
                </div>

                <div class="p-5 space-y-4">
                    <!--[if BLOCK]><![endif]--><?php if($ordenSeleccionada): ?>
                        <div class="flex items-center gap-2.5 p-2.5 bg-indigo-50 border border-indigo-100 rounded-lg text-xs">
                            <i class="fa-solid fa-car text-indigo-500"></i>
                            <span class="font-bold text-indigo-700">Orden #<?php echo e($ordenSeleccionada->id); ?></span>
                            <span class="text-gray-500">•</span>
                            <span class="font-bold text-gray-800"><?php echo e($ordenSeleccionada->vehiculo->placa ?? '—'); ?></span>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tipo</label>
                        <input type="text" wire:model="tipoDocumento" list="tiposDoc" placeholder="Ej: SOAT, Revisión..."
                            class="w-full bg-white border border-gray-200 rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        <datalist id="tiposDoc">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tiposDocumento; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($t); ?>"></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </datalist>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['tipoDocumento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-[11px] font-bold text-red-500"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div @dragover.prevent="dragging=true" @dragenter.prevent="dragging=true" @dragleave.self="dragging=false"
                        @drop.prevent="dragging=false; const i=$refs.fInput; if($event.dataTransfer.files.length&&i){const dt=new DataTransfer();dt.items.add($event.dataTransfer.files[0]);i.files=dt.files;i.dispatchEvent(new Event('change',{bubbles:true}))}">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Archivo</label>
                        <div class="rounded-lg border-2 transition-all p-0.5" :class="dragging?'border-indigo-500 bg-indigo-50/50 border-dashed':'border-gray-200'">
                            <input type="file" wire:model="archivo" x-ref="fInput" accept="image/*,.pdf"
                                class="w-full text-xs text-gray-500 border-0 rounded-lg cursor-pointer bg-white file:mr-3 file:py-2 file:px-3 file:border-0 file:bg-indigo-50 file:text-indigo-600 file:font-bold file:text-xs hover:file:bg-indigo-100 transition">
                        </div>
                        <div wire:loading wire:target="archivo" class="flex items-center gap-2 mt-2 text-xs font-bold text-indigo-600">
                            <div class="w-3.5 h-3.5 rounded-full border-2 border-indigo-200 border-t-indigo-600 animate-spin"></div> Procesando...
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['archivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-[11px] font-bold text-red-500"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        <div wire:loading.remove wire:target="archivo">
                            <!--[if BLOCK]><![endif]--><?php if($archivo): ?>
                                <div class="mt-2 rounded-lg border border-gray-200 overflow-hidden bg-white inline-block">
                                    <!--[if BLOCK]><![endif]--><?php if(str_starts_with($archivo->getMimeType(), 'image')): ?>
                                        <img src="<?php echo e($archivo->temporaryUrl()); ?>" class="h-20 w-auto object-contain p-1">
                                    <?php else: ?>
                                        <div class="flex items-center gap-2 px-3 py-2"><i class="fa-solid fa-file-pdf text-red-500 text-xs"></i><span class="text-xs font-bold text-gray-700 truncate max-w-[200px]"><?php echo e($archivo->getClientOriginalName()); ?></span></div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-gray-100 bg-gray-50">
                    <button wire:click="cerrarModalSubida" class="px-3.5 py-2 text-xs font-bold text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg transition">Cancelar</button>
                    <button wire:click="guardarDocumento" wire:loading.attr="disabled" wire:target="guardarDocumento"
                        class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow transition disabled:opacity-50 flex items-center gap-1.5">
                        <span wire:loading.remove wire:target="guardarDocumento"><i class="fa-solid fa-check"></i> Guardar</span>
                        <span wire:loading wire:target="guardarDocumento"><i class="fa-solid fa-circle-notch fa-spin"></i> Subiendo...</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/service-orders/detalle.blade.php ENDPATH**/ ?>