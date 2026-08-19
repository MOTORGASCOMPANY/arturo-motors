<div>
    <div x-data="{ showUpload: false }" x-init="$watch('$wire.showUploadModal', v => showUpload = v)"
        @documento-subido.window="showUpload = false; Swal.fire({icon:'success', title:'¡Listo!', text: $event.detail.mensaje || 'Documento subido correctamente.', timer:2200, showConfirmButton:false})"
        @documento-eliminado.window="Swal.fire({icon:'success', title:'Eliminado', text: $event.detail.mensaje || 'Documento eliminado.', timer:2200, showConfirmButton:false})"
        @documento-error.window="Swal.fire({icon:'error', title:'Ups', text: $event.detail.mensaje || 'Ocurrió un error.'})">

        <?php if (! $__env->hasRenderedOnce('715a7875-c3b6-463b-95e9-282a4997e8bb')): $__env->markAsRenderedOnce('715a7875-c3b6-463b-95e9-282a4997e8bb'); ?>
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
                        show: false,
                        everOpened: false,
                        url: '',
                        title: '',
                        status: 'loading',
                        isImage: false,
                        zoom: 1,
                        panning: false,
                        panStartX: 0,
                        panStartY: 0,
                        scrollStartX: 0,
                        scrollStartY: 0,
                        moved: false,
                        cache: {},

                        async abrir(url, title) {
                            this.everOpened = true;
                            this.url = url;
                            this.title = title;
                            this.status = 'loading';
                            this.isImage = false;
                            this.zoom = 1;
                            this.show = true;

                            if (this.cache[url]) {
                                this.status = 'ok';
                                this.isImage = this.cache[url] === 'image';
                                return;
                            }
                            try {
                                const r = await fetch(url, {
                                    method: 'HEAD',
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                const ct = r.headers.get('content-type') || '';
                                if (ct.includes('pdf')) {
                                    this.cache[url] = 'pdf';
                                    this.status = 'ok';
                                } else if (ct.includes('image')) {
                                    this.cache[url] = 'image';
                                    this.status = 'ok';
                                    this.isImage = true;
                                } else this.status = 'error';
                            } catch {
                                this.status = 'error';
                            }
                        },
                        zoomIn() {
                            this.zoom = Math.min(3, +(this.zoom + 0.25).toFixed(2));
                        },
                        zoomOut() {
                            this.zoom = Math.max(0.5, +(this.zoom - 0.25).toFixed(2));
                        },
                        zoomReset() {
                            this.zoom = 1;
                        },
                        cerrar() {
                            this.show = false;
                            setTimeout(() => {
                                this.url = '';
                                this.status = 'loading';
                                this.isImage = false;
                                this.zoom = 1;
                            }, 150);
                        },
                        startPan(e) {
                            if (this.zoom <= 1) return;
                            const c = this.$refs.vp;
                            if (!c) return;
                            this.panning = true;
                            this.moved = false;
                            this.panStartX = e.clientX;
                            this.panStartY = e.clientY;
                            this.scrollStartX = c.scrollLeft;
                            this.scrollStartY = c.scrollTop;
                        },
                        movePan(e) {
                            if (!this.panning) return;
                            const c = this.$refs.vp;
                            if (!c) return;
                            const dx = e.clientX - this.panStartX,
                                dy = e.clientY - this.panStartY;
                            if (Math.abs(dx) > 3 || Math.abs(dy) > 3) this.moved = true;
                            c.scrollLeft = this.scrollStartX - dx;
                            c.scrollTop = this.scrollStartY - dy;
                        },
                        stopPan() {
                            this.panning = false;
                        },
                    }));

                    Alpine.data('modalSubidaPrevisualizador', () => ({
                        previewUrl: null,
                        isImage: false,
                        isPdf: false,
                        fileName: '',
                        handleFileChange(event) {
                            const file = event.target.files[0];
                            if (file) {
                                this.fileName = file.name;
                                this.isImage = file.type.startsWith('image/');
                                this.isPdf = file.type === 'application/pdf';
                                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                                this.previewUrl = URL.createObjectURL(file);
                            } else {
                                this.previewUrl = null;
                                this.isImage = false;
                                this.isPdf = false;
                                this.fileName = '';
                            }
                        },
                        resetPreview() {
                            if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                            this.previewUrl = null;
                            this.isImage = false;
                            this.isPdf = false;
                            this.fileName = '';
                        }
                    }));
                });
            </script>
        <?php endif; ?>

        <div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
            <a href="<?php echo e(route('ordenes.listado')); ?>"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-arrow-left text-gray-500"></i> Volver a las órdenes de servicios
            </a>

            <div class="bg-gray-200 p-8 rounded-xl w-full border border-gray-300/80 shadow-sm">
                <div class="flex items-start justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-sm font-bold text-lg">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 leading-tight">
                                Orden #<?php echo e($orden->id); ?>

                                <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion'): ?>
                                    <span
                                        class="text-xs bg-purple-200 text-purple-800 px-2 py-0.5 rounded-full font-semibold align-middle ml-1">Conversión</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </h2>
                            <p class="text-xs text-gray-700 flex items-center gap-1 mt-0.5">
                                <i class="far font-normal fa-calendar-alt text-gray-600"></i>
                                Creada el <?php echo e($orden->created_at->format('d/m/Y H:i')); ?> por <span
                                    class="font-medium text-gray-800"><?php echo e($orden->creadoPor->name); ?></span>
                            </p>
                        </div>
                    </div>
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-sm animate-pulse">
                        <span
                            class="w-2 h-2 rounded-full <?php echo e(match (true) {
                                str_contains(strtolower($orden->estado), 'cancel') || str_contains(strtolower($orden->estado), 'rechaz')
                                    => 'bg-red-500',
                                in_array(strtolower($orden->estado), ['entregada', 'entregado']) => 'bg-emerald-500',
                                default => 'bg-amber-500',
                            }); ?>"></span>
                        <?php echo e(ucfirst(str_replace('_', ' ', $orden->estado))); ?>

                    </span>
                </div>
            </div>

            <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i
                        class="fas fa-user mr-1 text-gray-600"></i> Cliente y vehículo</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600 text-xs font-medium">Cliente</p>
                        <p class="font-bold text-gray-900"><?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?>

                        </p>
                        <p class="text-gray-700 text-xs"><?php echo e($orden->cliente->documento); ?> —
                            <?php echo e($orden->cliente->telefono ?? 'sin teléfono'); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-xs font-medium">Vehículo</p>
                        <p class="font-bold text-gray-900"><?php echo e($orden->vehiculo->placa); ?> —
                            <?php echo e($orden->vehiculo->marca); ?> <?php echo e($orden->vehiculo->modelo); ?></p>
                        <p class="text-gray-700 text-xs">Año: <?php echo e($orden->vehiculo->anio ?? '—'); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i class="fas fa-tag mr-1 text-gray-600"></i>
                    Servicio y precio</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600 text-xs font-medium">Servicio</p>
                        <p class="font-bold text-gray-900"><?php echo e($orden->service->nombre); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-xs font-medium">Precio de lista</p>
                        <p class="font-bold text-gray-800">S/ <?php echo e(number_format($orden->precio_lista, 2)); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-xs font-medium">Precio final</p>
                        <p
                            class="font-bold <?php echo e($orden->precio_final != $orden->precio_lista ? 'text-amber-700' : 'text-gray-900'); ?>">
                            S/ <?php echo e(number_format($orden->precio_final, 2)); ?></p>
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($orden->descuento_motivo): ?>
                    <p class="text-xs text-gray-700 mt-3 font-medium"><i
                            class="fas fa-comment-dots mr-1 text-gray-500"></i> Motivo del ajuste:
                        <?php echo e($orden->descuento_motivo); ?></p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion' && $orden->checklist_evaluacion): ?>
                <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
                    <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i
                            class="fas fa-clipboard-check mr-1 text-gray-600"></i> Evaluación técnica</h3>
                    <div class="flex items-center gap-3 mb-4 text-sm">
                        <span
                            class="px-2.5 py-1 rounded-full text-xs font-bold <?php echo e($orden->evaluacion_aprobada ? 'bg-emerald-200 text-emerald-900' : 'bg-red-200 text-red-900'); ?>"><?php echo e($orden->evaluacion_aprobada ? 'Apto' : 'No apto'); ?></span>
                        <span class="text-gray-700 font-medium">Por <?php echo e($orden->evaluadoPor?->name); ?> —
                            <?php echo e($orden->evaluado_en?->format('d/m/Y H:i')); ?></span>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($orden->evaluacion_observaciones): ?>
                        <p
                            class="text-sm bg-white/80 border border-gray-300 rounded-lg p-3 mb-4 text-gray-800 font-medium">
                            <?php echo e($orden->evaluacion_observaciones); ?></p>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 text-xs">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $checklistGrupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupoKey => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clave => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center gap-1.5 py-1"
                                    wire:key="chk-<?php echo e($grupoKey); ?>-<?php echo e($clave); ?>">
                                    <i
                                        class="fas <?php echo e($orden->checklist_evaluacion[$clave] ?? false ? 'fa-check-circle text-emerald-600' : 'fa-times-circle text-gray-400'); ?>"></i>
                                    <span
                                        class="<?php echo e($orden->checklist_evaluacion[$clave] ?? false ? 'text-gray-900 font-semibold' : 'text-gray-600'); ?>"><?php echo e($label); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion' && $orden->tecnico): ?>
                <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
                    <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i
                            class="fas fa-wrench mr-1 text-gray-600"></i> Conversión</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm mb-4">
                        <div>
                            <p class="text-gray-600 text-xs font-medium">Técnico</p>
                            <p class="font-bold text-gray-900"><?php echo e($orden->tecnico->name); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-xs font-medium">Inicio</p>
                            <p class="font-bold text-gray-900">
                                <?php echo e($orden->fecha_inicio_conversion?->format('d/m/Y H:i') ?? '—'); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-xs font-medium">Fin</p>
                            <p class="font-bold text-gray-900">
                                <?php echo e($orden->fecha_fin_conversion?->format('d/m/Y H:i') ?? '—'); ?></p>
                        </div>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($orden->items->count()): ?>
                        <p class="text-xs font-bold text-gray-700 uppercase mb-2">Equipos instalados</p>
                        <div class="space-y-1.5 mb-4">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div wire:key="item-<?php echo e($item->id); ?>"
                                    class="flex justify-between text-sm bg-white/80 border border-gray-300 rounded-lg px-3 py-2">
                                    <span class="font-medium text-gray-800"><?php echo e($item->producto->categoria->nombre); ?> —
                                        <?php echo e($item->producto->nombre); ?> (<?php echo e($item->producto->marca); ?>)</span>
                                    <span class="font-mono text-xs text-gray-700 font-semibold">Serie:
                                        <?php echo e($item->serie); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php if($orden->movimientosStock->count()): ?>
                        <p class="text-xs font-bold text-gray-700 uppercase mb-2">Repuestos utilizados</p>
                        <div class="space-y-1.5">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->movimientosStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div wire:key="mov-<?php echo e($mov->id); ?>"
                                    class="flex justify-between text-sm bg-white/80 border border-gray-300 rounded-lg px-3 py-2">
                                    <span class="font-medium text-gray-800"><?php echo e($mov->producto->nombre); ?></span>
                                    <span class="text-gray-700 font-semibold">× <?php echo e($mov->cantidad); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i
                        class="fas fa-receipt mr-1 text-gray-600"></i> Comprobante</h3>
                <!--[if BLOCK]><![endif]--><?php if($orden->comprobante): ?>
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <p class="font-bold text-gray-900">Folio: <?php echo e($orden->comprobante->folio); ?></p>
                            <p class="text-gray-700 text-xs font-medium">
                                <?php echo e(ucfirst($orden->comprobante->metodo_pago)); ?> —
                                <?php echo e($orden->comprobante->created_at->format('d/m/Y H:i')); ?></p>
                        </div>
                        <a href="<?php echo e(route('comprobantes.pdf', $orden->id)); ?>" target="_blank"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors shadow-sm">Ver
                            PDF →</a>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-gray-600 font-medium">Aún no se ha generado comprobante (pendiente de cobro).
                    </p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div x-data="visorArchivos">

                <?php
                    $estilosSistema = [
                        'Comprobante' => [
                            'icon' => 'fa-file-invoice-dollar',
                            'bgClass' => 'bg-blue-600',
                            'borderClass' => 'border-blue-200 hover:border-blue-400',
                        ],
                        'Carta garantía' => [
                            'icon' => 'fa-stamp',
                            'bgClass' => 'bg-emerald-600',
                            'borderClass' => 'border-emerald-200 hover:border-emerald-400',
                        ],
                        'Manual' => [
                            'icon' => 'fa-book-open',
                            'bgClass' => 'bg-amber-600',
                            'borderClass' => 'border-amber-200 hover:border-amber-400',
                        ],
                    ];

                    $sistemaDocs = collect([
                        $orden->comprobante
                            ? array_merge(
                                [
                                    'label' => 'Comprobante',
                                    'badge' => 'Sistema',
                                    'url' => route('comprobantes.pdf', $orden->id),
                                ],
                                $estilosSistema['Comprobante'],
                            )
                            : null,
                        $orden->vehiculo
                            ? array_merge(
                                [
                                    'label' => 'Carta garantía',
                                    'badge' => 'Sistema',
                                    'url' => route('vehiculo.pdf', $orden->vehiculo_id),
                                ],
                                $estilosSistema['Carta garantía'],
                            )
                            : null,
                        $orden->vehiculo
                            ? array_merge(
                                [
                                    'label' => 'Manual',
                                    'badge' => 'Sistema',
                                    'url' => route('manual.pdf', $orden->vehiculo_id),
                                ],
                                $estilosSistema['Manual'],
                            )
                            : null,
                    ])->filter();

                    $obtenerEstiloExpediente = function ($nombre) {
                        $n = strtolower($nombre);

                        if (str_contains($n, 'soat') || str_contains($n, 'seguro') || str_contains($n, 'poliza')) {
                            return [
                                'icon' => 'fa-file-shield',
                                'bgClass' => 'bg-rose-500',
                                'borderClass' => 'border-rose-200 hover:border-rose-400',
                            ];
                        }
                        if (str_contains($n, 'revision') || str_contains($n, 'tecnica')) {
                            return [
                                'icon' => 'fa-screwdriver-wrench',
                                'bgClass' => 'bg-purple-500',
                                'borderClass' => 'border-purple-200 hover:border-purple-400',
                            ];
                        }
                        if (
                            str_contains($n, 'dni') ||
                            str_contains($n, 'identidad') ||
                            str_contains($n, 'licencia') ||
                            str_contains($n, 'brevete')
                        ) {
                            return [
                                'icon' => 'fa-id-card',
                                'bgClass' => 'bg-cyan-500',
                                'borderClass' => 'border-cyan-200 hover:border-cyan-400',
                            ];
                        }
                        if (str_contains($n, 'tarjeta') || str_contains($n, 'propiedad')) {
                            return [
                                'icon' => 'fa-address-card',
                                'bgClass' => 'bg-indigo-500',
                                'borderClass' => 'border-indigo-200 hover:border-indigo-400',
                            ];
                        }
                        if (str_contains($n, 'factura') || str_contains($n, 'boleta')) {
                            return [
                                'icon' => 'fa-receipt',
                                'bgClass' => 'bg-teal-500',
                                'borderClass' => 'border-teal-200 hover:border-teal-400',
                            ];
                        }

                        return [
                            'icon' => 'fa-folder-open',
                            'bgClass' => 'bg-slate-500',
                            'borderClass' => 'border-slate-200 hover:border-slate-400',
                        ];
                    };

                    $docsSubidos = $orden->documentos->map(function ($d) use ($obtenerEstiloExpediente) {
                        $nombreLimpio = ucfirst(str_replace('_', ' ', $d->tipo));
                        return array_merge(
                            ['id' => $d->id, 'label' => $nombreLimpio, 'badge' => 'Subido', 'url' => $d->url],
                            $obtenerEstiloExpediente($nombreLimpio),
                        );
                    });
                ?>

                <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-800 uppercase"><i
                                class="fas fa-file-pdf mr-1 text-gray-600"></i> Documentos</h3>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($orden->service->tipo === 'conversion'): ?>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <!--[if BLOCK]><![endif]--><?php if($orden->checklist_evaluacion): ?>
                                <a href="<?php echo e(route('conversiones.pdf.evaluacion', $orden->id)); ?>" target="_blank"
                                    class="bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg border border-gray-200 transition"><i
                                        class="fas fa-clipboard-check mr-1"></i> Evaluación</a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php if($orden->items->count()): ?>
                                <a href="<?php echo e(route('conversiones.pdf.ficha-tecnica', $orden->id)); ?>" target="_blank"
                                    class="bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg border border-gray-200 transition"><i
                                        class="fas fa-wrench mr-1"></i> Ficha técnica</a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php if(in_array(strtolower($orden->estado), ['entregado', 'entregada'])): ?>
                                <a href="<?php echo e(route('conversiones.pdf.garantia', $orden->id)); ?>" target="_blank"
                                    class="bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg border border-gray-200 transition"><i
                                        class="fas fa-shield-alt mr-1"></i> Garantía</a>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <!--[if BLOCK]><![endif]--><?php if($sistemaDocs->count()): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $sistemaDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div wire:key="sysdoc-<?php echo e(Str::slug($doc['label'])); ?>"
                                    class="flex items-center justify-between p-3 bg-white border <?php echo e($doc['borderClass']); ?> rounded-xl shadow-sm hover:shadow-md transition-all group">
                                    <button type="button"
                                        @click="abrir('<?php echo e($doc['url']); ?>', '<?php echo e($doc['label']); ?>')"
                                        class="flex items-center gap-3 flex-1 min-w-0 text-left">
                                        <div
                                            class="w-10 h-10 rounded-lg <?php echo e($doc['bgClass']); ?> text-white flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-sm">
                                            <i class="fa-solid <?php echo e($doc['icon']); ?> text-sm"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-800 truncate"><?php echo e($doc['label']); ?>

                                            </p>
                                            <p class="text-[10px] text-gray-400 font-medium"><?php echo e($doc['badge']); ?></p>
                                        </div>
                                    </button>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php else: ?>
                        <div
                            class="flex flex-col items-center py-6 bg-white border-2 border-dashed border-gray-200 rounded-xl text-center">
                            <div
                                class="w-10 h-10 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mb-2">
                                <i class="fa-solid fa-inbox text-sm"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-500">Sin documentos</p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6 mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-800 uppercase"><i
                                class="fas fa-folder-open mr-1 text-gray-600"></i> Expedientes</h3>
                        <button type="button" wire:click="abrirModalSubida(<?php echo e($orden->id); ?>)"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition active:scale-95">
                            <i class="fa-solid fa-plus text-[10px]"></i> Subir
                        </button>
                    </div>

                    <!--[if BLOCK]><![endif]--><?php if($docsSubidos->count()): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $docsSubidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div wire:key="expdoc-<?php echo e($doc['id']); ?>"
                                    class="flex items-center justify-between p-3 bg-white border <?php echo e($doc['borderClass']); ?> rounded-xl shadow-sm hover:shadow-md transition-all group">
                                    <button type="button"
                                        @click="abrir('<?php echo e($doc['url']); ?>', '<?php echo e($doc['label']); ?>')"
                                        class="flex items-center gap-3 flex-1 min-w-0 text-left">
                                        <div
                                            class="w-10 h-10 rounded-lg <?php echo e($doc['bgClass']); ?> text-white flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-sm">
                                            <i class="fa-solid <?php echo e($doc['icon']); ?> text-sm"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-800 truncate"><?php echo e($doc['label']); ?>

                                            </p>
                                            <p class="text-[10px] text-gray-400 font-medium"><?php echo e($doc['badge']); ?></p>
                                        </div>
                                    </button>

                                    <button type="button"
                                        @click="
                                        Swal.fire({ icon:'warning', title:'¿Eliminar?', text:'Acción irreversible.', showCancelButton:true, confirmButtonText:'Sí', cancelButtonText:'Cancelar', confirmButtonColor:'#dc2626' })
                                        .then(r => { if(r.isConfirmed) $wire.eliminarDocumento(<?php echo e($doc['id']); ?>) })
                                    "
                                        class="shrink-0 w-8 h-8 flex items-center justify-center rounded-md text-gray-300 hover:text-red-500 hover:bg-red-50 transition ml-2">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <?php else: ?>
                        <div
                            class="flex flex-col items-center py-6 bg-white border-2 border-dashed border-gray-200 rounded-xl text-center">
                            <div
                                class="w-10 h-10 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mb-2">
                                <i class="fa-solid fa-folder text-sm"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-500">Sin expedientes subidos</p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                <template x-if="everOpened">
                    <template x-teleport="body">
                        <div x-show="show" x-cloak @keydown.escape.window="cerrar()" class="fixed inset-0 z-[90]"
                            style="display:none;">
                            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="cerrar()"></div>
                            <div class="absolute inset-4 sm:inset-8 md:inset-12 bg-white rounded-3xl shadow-2xl flex flex-col overflow-hidden border border-white/20"
                                x-show="show" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95">

                                <div
                                    class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white shrink-0">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                            <i class="fa-solid"
                                                :class="isImage ? 'fa-image text-emerald-500 text-lg' :
                                                    'fa-file-pdf text-red-500 text-lg'"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="text-base font-extrabold text-gray-800 truncate block"
                                                x-text="title"></span>
                                            <span class="text-[11px] text-gray-400 font-medium">Previsualización de
                                                contenido en pantalla completa</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <template x-if="isImage && status === 'ok'">
                                            <div
                                                class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 mr-2 border border-gray-200 shadow-inner">
                                                <button @click="zoomOut()"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-600 hover:bg-white shadow-sm transition"><i
                                                        class="fa-solid fa-minus text-xs"></i></button>
                                                <span
                                                    class="text-xs font-bold text-gray-700 w-10 text-center select-none"
                                                    x-text="Math.round(zoom*100)+'%'"></span>
                                                <button @click="zoomIn()"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-600 hover:bg-white shadow-sm transition"><i
                                                        class="fa-solid fa-plus text-xs"></i></button>
                                                <div class="w-px h-4 bg-gray-300 mx-0.5"></div>
                                                <button @click="zoomReset()"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-600 hover:bg-white shadow-sm transition"
                                                    title="Restablecer zoom"><i
                                                        class="fa-solid fa-expand text-xs"></i></button>
                                            </div>
                                        </template>
                                        <button @click="window.open(url, '_blank')" x-show="status==='ok'"
                                            class="px-3 py-2 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition hidden sm:inline-flex items-center gap-1.5 shadow-sm">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Abrir en
                                            pestaña
                                        </button>
                                        <button @click="cerrar()"
                                            class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                                            <i class="fa-solid fa-xmark text-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <div
                                    class="flex-1 bg-slate-900 relative overflow-hidden flex items-center justify-center">
                                    <div x-show="status==='loading'"
                                        class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white z-10">
                                        <div
                                            class="w-10 h-10 rounded-full border-4 border-indigo-100 border-t-indigo-600 animate-spin">
                                        </div>
                                        <span class="text-xs font-bold text-gray-600">Cargando contenido del
                                            documento...</span>
                                    </div>
                                    <div x-show="status==='error'"
                                        class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white z-10">
                                        <div
                                            class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                        </div>
                                        <p class="text-sm font-bold text-gray-700">No se pudo cargar el contenido</p>
                                        <button @click="cerrar()"
                                            class="text-xs font-bold text-gray-600 bg-gray-100 px-4 py-2 rounded-xl hover:bg-gray-200 transition">Cerrar
                                            visor</button>
                                    </div>
                                    <template x-if="status==='ok' && !isImage">
                                        <div
                                            class="w-full h-full p-2 sm:p-4 flex items-center justify-center bg-slate-950">
                                            <iframe :src="url + '#toolbar=1&view=FitH'"
                                                class="w-full h-full rounded-2xl border-0 shadow-2xl bg-white"
                                                title="Previsualizador de contenido PDF" loading="lazy"></iframe>
                                        </div>
                                    </template>
                                    <template x-if="status==='ok' && isImage">
                                        <div class="w-full h-full overflow-auto p-6 select-none flex items-center justify-center"
                                            :class="zoom <= 1 ? 'flex items-center justify-center' : ''" x-ref="vp"
                                            @mousedown="startPan($event)"
                                            @mousemove.window.throttle.16ms="movePan($event)"
                                            @mouseup.window="stopPan()" @mouseleave="stopPan()">
                                            <img :src="url" :style="zoom > 1 ? `width:${zoom*100}%` : ''"
                                                @click="if(moved){moved=false}else{zoom=zoom===1?1.5:1}"
                                                @dragstart.prevent
                                                :class="zoom <= 1 ? 'max-w-full max-h-full object-contain cursor-zoom-in' :
                                                    'block mx-auto max-w-none cursor-grab'"
                                                class="rounded-2xl shadow-2xl transition-[width] duration-150 bg-white"
                                                draggable="false" loading="lazy" alt="Previsualización de imagen">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </template>
            </div>

            <div class="bg-gray-200 rounded-xl shadow-sm border border-gray-300/80 p-6">
                <h3 class="text-sm font-bold text-gray-800 uppercase mb-3"><i
                        class="fas fa-history mr-1 text-gray-600"></i> Historial</h3>
                <div class="space-y-3">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $orden->historialEstados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div wire:key="hist-<?php echo e($h->id); ?>" class="flex items-start gap-3 text-sm">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-600 mt-1 flex-shrink-0"></div>
                            <div>
                                <p class="text-gray-900">
                                    <!--[if BLOCK]><![endif]--><?php if($h->estado_anterior): ?>
                                        <span
                                            class="text-gray-600"><?php echo e(ucfirst(str_replace('_', ' ', $h->estado_anterior))); ?></span>
                                        →
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <span
                                        class="font-bold"><?php echo e(ucfirst(str_replace('_', ' ', $h->estado_nuevo))); ?></span>
                                </p>
                                <p class="text-xs text-gray-700 font-medium"><?php echo e($h->created_at->format('d/m/Y H:i')); ?>

                                    — <?php echo e($h->usuario->name ?? 'Sistema'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>
        </div>

        <!--[if BLOCK]><![endif]--><?php if($showUploadModal): ?>
            <div id="modal-subida-container" class="fixed inset-0 z-[80] flex items-center justify-center p-3 sm:p-6"
                wire:key="modal-subida">
                <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-md transition-opacity"
                    wire:click="cerrarModalSubida"></div>

                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-7xl transition-all duration-300 overflow-hidden border border-white/20"
                    x-data="modalSubidaPrevisualizador" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                    <div wire:loading wire:target="guardarDocumento"
                        class="absolute inset-0 bg-slate-900/90 backdrop-blur-md z-50 flex flex-col items-center justify-center p-6 text-center text-white h-full w-full">
                        <div class="relative mb-6">
                            <div class="w-24 h-24 bg-indigo-500/20 rounded-full absolute -inset-3 animate-ping"></div>
                            <div
                                class="w-24 h-24 bg-gradient-to-tr from-indigo-600 to-violet-500 rounded-3xl flex items-center justify-center shadow-2xl relative z-10">
                                <i class="fa-solid fa-cloud-arrow-up text-4xl animate-bounce"></i>
                            </div>
                        </div>
                        <h4 class="text-xl font-black tracking-wide text-white">Guardando y procesando archivo...</h4>
                        <p class="text-xs text-slate-300 mt-1.5 mb-6 max-w-sm">Estamos subiendo tu documento de manera
                            segura al servidor para que esté disponible de inmediato.</p>

                        <div
                            class="w-56 bg-slate-800 rounded-full h-2.5 overflow-hidden p-0.5 border border-slate-700 shadow-inner">
                            <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-400 h-full rounded-full animate-[progress_1.5s_infinite_ease-in-out]"
                                style="width: 70%;"></div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between px-8 py-5 bg-gradient-to-r from-indigo-900 via-indigo-800 to-indigo-900 text-white">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20 shadow-inner">
                                <i class="fa-solid fa-cloud-arrow-up text-indigo-200 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold tracking-tight">Subir Documento Nuevo</h3>
                                <p class="text-xs text-indigo-200 font-medium">Adjunta archivos e inspecciona su
                                    contenido previamente</p>
                            </div>
                        </div>
                        <button wire:click="cerrarModalSubida"
                            class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition active:scale-95"><i
                                class="fa-solid fa-xmark text-lg"></i></button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 max-h-[82vh] overflow-y-auto">

                        <div
                            class="lg:col-span-5 p-8 space-y-6 flex flex-col justify-between border-r border-slate-100">
                            <div class="space-y-6">
                                <!--[if BLOCK]><![endif]--><?php if($ordenSeleccionada): ?>
                                    <div
                                        class="flex items-center gap-3 p-4 bg-indigo-50/80 border border-indigo-100 rounded-2xl shadow-sm">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-indigo-500 text-white flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0">
                                            <i class="fa-solid fa-car text-base"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span
                                                class="text-xs font-bold text-indigo-400 uppercase tracking-wide block">Orden
                                                #<?php echo e($ordenSeleccionada->id); ?></span>
                                            <span class="text-sm font-extrabold text-slate-800 truncate block">Placa:
                                                <?php echo e($ordenSeleccionada->vehiculo->placa ?? '—'); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Tipo
                                        de documento</label>
                                    <input type="text" wire:model="tipoDocumento" list="tiposDoc"
                                        placeholder="Ej: SOAT, Revisión técnica, DNI..."
                                        class="w-full bg-white border border-slate-200 rounded-2xl text-sm px-4 py-3.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition shadow-sm">
                                    <datalist id="tiposDoc">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = collect($tiposDocumento)->reject(fn($t) => in_array(strtolower($t), ['comprobante', 'carta garantía', 'carta garantia', 'manual'])); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($t); ?>"></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </datalist>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['tipoDocumento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-xs font-bold text-red-500 mt-1 block"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>

                                <div x-data="{ dragging: false }" @dragover.prevent="dragging=true"
                                    @dragenter.prevent="dragging=true" @dragleave.self="dragging=false"
                                    @drop.prevent="dragging=false; const i=$refs.fInput; if($event.dataTransfer.files.length&&i){const dt=new DataTransfer();dt.items.add($event.dataTransfer.files[0]);i.files=dt.files;i.dispatchEvent(new Event('change',{bubbles:true}))}">
                                    <label
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Archivo
                                        (Imagen o PDF)</label>
                                    <div class="rounded-2xl border-2 transition-all p-1.5"
                                        :class="dragging ? 'border-indigo-500 bg-indigo-50/70 border-dashed shadow-inner' :
                                            'border-slate-200 bg-slate-50/50'">
                                        <input type="file" wire:model="archivo" x-ref="fInput"
                                            accept="image/*,.pdf" @change="handleFileChange($event)"
                                            class="w-full text-xs text-slate-500 border-0 rounded-xl cursor-pointer bg-white file:mr-4 file:py-3.5 file:px-5 file:border-0 file:bg-indigo-50 file:text-indigo-600 file:font-bold file:text-xs hover:file:bg-indigo-100 transition shadow-sm">
                                    </div>
                                    <div wire:loading wire:target="archivo"
                                        class="flex items-center gap-2 mt-2 text-xs font-bold text-indigo-600">
                                        <div
                                            class="w-4 h-4 rounded-full border-2 border-indigo-200 border-t-indigo-600 animate-spin">
                                        </div> Procesando archivo local...
                                    </div>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['archivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-xs font-bold text-red-500 mt-1 block"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>

                            <div class="text-xs text-slate-400 font-medium pt-4">
                                <i class="fa-solid fa-circle-info mr-1 text-indigo-500"></i> Puedes inspeccionar el
                                contenido completo del PDF o imagen antes de confirmar.
                            </div>
                        </div>

                        <div
                            class="lg:col-span-7 bg-slate-900 p-8 flex flex-col justify-between relative min-h-[500px]">
                            <div class="flex items-center justify-between mb-4">
                                <span
                                    class="text-xs font-bold text-indigo-300 uppercase tracking-widest flex items-center gap-2">
                                    <i class="fa-solid fa-eye text-indigo-400"></i> Lector de contenido en vivo
                                </span>
                                <template x-if="fileName">
                                    <span
                                        class="text-[11px] font-semibold text-slate-300 truncate max-w-[250px] bg-slate-800 px-3 py-1 rounded-lg border border-slate-700"
                                        x-text="fileName"></span>
                                </template>
                            </div>

                            <div
                                class="flex-1 w-full bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 shadow-2xl flex items-center justify-center relative p-3 my-2">
                                <template x-if="!previewUrl">
                                    <div
                                        class="flex flex-col items-center justify-center text-center p-8 text-slate-500 space-y-3">
                                        <div
                                            class="w-16 h-16 bg-slate-900 text-slate-600 rounded-2xl flex items-center justify-center text-2xl border border-slate-800">
                                            <i class="fa-solid fa-file-arrow-up"></i>
                                        </div>
                                        <p class="text-xs font-bold text-slate-400">Selecciona o arrastra un archivo
                                        </p>
                                        <p class="text-[11px] text-slate-500 max-w-xs">El contenido del PDF o imagen
                                            aparecerá aquí inmediatamente para que lo revises.</p>
                                    </div>
                                </template>

                                <template x-if="previewUrl && isImage">
                                    <img :src="previewUrl"
                                        class="max-h-[420px] max-w-full object-contain rounded-xl shadow-lg">
                                </template>

                                <template x-if="previewUrl && isPdf">
                                    <iframe :src="previewUrl + '#toolbar=1&view=FitH'"
                                        class="w-full h-[420px] rounded-xl border-0 bg-white shadow-inner"
                                        title="Lector en vivo PDF"></iframe>
                                </template>
                            </div>

                            <p class="text-[11px] text-slate-400 text-center mt-2">
                                Previsualización directa en el navegador sin descargas automáticas.
                            </p>
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-3 px-8 py-5 border-t border-slate-100 bg-slate-50">
                        <button wire:click="cerrarModalSubida"
                            class="px-5 py-3 text-xs font-bold text-slate-600 bg-gray-200 hover:bg-gray-300 rounded-2xl transition">Cancelar</button>
                        <button wire:click="guardarDocumento" wire:loading.attr="disabled"
                            wire:target="guardarDocumento"
                            class="px-6 py-3 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-2xl shadow-xl shadow-indigo-600/30 transition disabled:opacity-50 flex items-center gap-2">
                            <span wire:loading.remove wire:target="guardarDocumento"><i class="fa-solid fa-check"></i>
                                Guardar archivo</span>
                            <span wire:loading wire:target="guardarDocumento"><i
                                    class="fa-solid fa-circle-notch fa-spin"></i> Subiendo...</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/service-orders/detalle.blade.php ENDPATH**/ ?>