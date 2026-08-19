<div class="space-y-5">
    <!-- Bienvenida + Accesos Rápidos -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-900 to-blue-900 rounded-2xl shadow-lg p-5 sm:p-6 text-white flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight flex items-center gap-2">
                ¡Hola, <?php echo e(Auth::user()->name); ?>! 👋
            </h1>
            <p class="mt-1 text-slate-300 text-sm font-medium">
                Panel de control de Arturo Motors
            </p>
        </div>

        <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Vendedor|Jefe de Taller|Administrador del sistema')): ?>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?php echo e(route('ordenes.simple.crear')); ?>" 
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white text-xs font-semibold px-3.5 py-2 rounded-xl shadow-sm transition-all duration-150">
                    <i class="fas fa-plus text-xs"></i> Orden Simple
                </a>
                <a href="<?php echo e(route('conversiones.crear')); ?>" 
                   class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-500 active:bg-purple-700 text-white text-xs font-semibold px-3.5 py-2 rounded-xl shadow-sm transition-all duration-150">
                    <i class="fas fa-wrench text-xs"></i> Orden Conversión
                </a>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Grid Principal de Módulo de Caja y Métricas Operativas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">        
        <!-- Tarjeta Prominente: Caja (Ocupa 2 columnas en desktop) -->
        <a href="<?php echo e($sesionCaja ? route('caja.historial') : route('caja.abrir')); ?>"
           class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4 hover:shadow-md transition-all duration-150 flex flex-col justify-between relative overflow-hidden group">
            
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <i class="fas fa-cash-register text-sm"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Estado de Caja</span>
                        <span class="text-sm font-bold text-gray-800">Caja Chica Taller</span>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold <?php echo e($sesionCaja ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'); ?>">
                    <span class="w-1.5 h-1.5 rounded-full <?php echo e($sesionCaja ? 'bg-emerald-600 animate-pulse' : 'bg-rose-600'); ?>"></span>
                    <?php echo e($sesionCaja ? 'Abierta' : 'Cerrada'); ?>

                </span>
            </div>

            <div class="py-3">
                <!--[if BLOCK]><![endif]--><?php if($sesionCaja): ?>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-emerald-50/60 rounded-xl p-2.5 border border-emerald-100/60">
                            <span class="text-[11px] font-medium text-emerald-700 block">Ingresos hoy</span>
                            <span class="text-base font-extrabold text-emerald-700">S/ <?php echo e(number_format($ingresosHoy, 2)); ?></span>
                        </div>
                        <div class="bg-rose-50/60 rounded-xl p-2.5 border border-rose-100/60">
                            <span class="text-[11px] font-medium text-rose-700 block">Egresos hoy</span>
                            <span class="text-base font-extrabold text-rose-700">S/ <?php echo e(number_format($egresosHoy, 2)); ?></span>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2 flex items-center gap-1">
                        <i class="fas fa-user-circle"></i> Abierta por <?php echo e($sesionCaja->abiertaPor->name); ?>

                    </p>
                <?php else: ?>
                    <div class="py-2 text-center sm:text-left">
                        <p class="text-xs text-gray-500 font-medium">No hay ninguna sesión de caja activa en este momento.</p>
                        <span class="text-xs font-semibold text-indigo-600 group-hover:underline inline-flex items-center gap-1 mt-1">
                            Haz clic aquí para abrir caja <i class="fas fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </a>

        <!-- Órdenes de hoy -->
        <a href="<?php echo e(route('ordenes.listado')); ?>"
           class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4 hover:shadow-md transition-all duration-150 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Órdenes de hoy</span>
                <p class="text-2xl font-black text-gray-800 mt-1"><?php echo e($ordenesHoy); ?></p>
                <span class="text-[11px] text-gray-400 font-medium">Ver listado general</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i class="fas fa-file-alt text-lg"></i>
            </div>
        </a>

        <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Jefe de Taller|Administrador del sistema')): ?>
            <!-- Pendientes de asignar técnico -->
            <a href="<?php echo e(route('conversiones.asignar')); ?>"
               class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4 hover:shadow-md transition-all duration-150 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Por asignar</span>
                    <p class="text-2xl font-black <?php echo e($pendientesAsignarTecnico > 0 ? 'text-amber-600' : 'text-gray-800'); ?> mt-1">
                        <?php echo e($pendientesAsignarTecnico); ?>

                    </p>
                    <span class="text-[11px] text-gray-400 font-medium">Técnico pendiente</span>
                </div>
                <div class="w-11 h-11 rounded-2xl <?php echo e($pendientesAsignarTecnico > 0 ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-500'); ?> flex items-center justify-center shrink-0">
                    <i class="fas fa-user-clock text-lg"></i>
                </div>
            </a>
            <!-- Pendientes de entrega -->
            <a href="<?php echo e(route('conversiones.entregas-pendientes')); ?>"
               class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4 hover:shadow-md transition-all duration-150 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Listas entrega</span>
                    <p class="text-2xl font-black <?php echo e($pendientesEntrega > 0 ? 'text-amber-600' : 'text-gray-800'); ?> mt-1">
                        <?php echo e($pendientesEntrega); ?>

                    </p>
                    <span class="text-[11px] text-gray-400 font-medium">Vehículos listos</span>
                </div>
                <div class="w-11 h-11 rounded-2xl <?php echo e($pendientesEntrega > 0 ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-500'); ?> flex items-center justify-center shrink-0">
                    <i class="fas fa-key text-lg"></i>
                </div>
            </a>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Tecnico|Administrador del sistema')): ?>
            <!-- Mis conversiones -->
            <a href="<?php echo e(route('conversiones.mis-asignadas')); ?>"
               class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4 hover:shadow-md transition-all duration-150 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Mis conversiones</span>
                    <p class="text-2xl font-black <?php echo e($misPendientes > 0 ? 'text-amber-600' : 'text-gray-800'); ?> mt-1">
                        <?php echo e($misPendientes); ?>

                    </p>
                    <span class="text-[11px] text-gray-400 font-medium">Asignadas a mí</span>
                </div>
                <div class="w-11 h-11 rounded-2xl <?php echo e($misPendientes > 0 ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-500'); ?> flex items-center justify-center shrink-0">
                    <i class="fas fa-wrench text-lg"></i>
                </div>
            </a>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

        <!--[if BLOCK]><![endif]--><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Almacen|Administrador del sistema')): ?>
            <!-- Esperando equipos -->
            <a href="<?php echo e(route('conversiones.almacen-pendientes')); ?>"
               class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4 hover:shadow-md transition-all duration-150 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Almacén</span>
                    <p class="text-2xl font-black <?php echo e($pendientesAlmacen > 0 ? 'text-amber-600' : 'text-gray-800'); ?> mt-1">
                        <?php echo e($pendientesAlmacen); ?>

                    </p>
                    <span class="text-[11px] text-gray-400 font-medium">Esperando equipos</span>
                </div>
                <div class="w-11 h-11 rounded-2xl <?php echo e($pendientesAlmacen > 0 ? 'bg-amber-50 text-amber-600' : 'bg-gray-100 text-gray-500'); ?> flex items-center justify-center shrink-0">
                    <i class="fas fa-boxes text-lg"></i>
                </div>
            </a>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    </div>

    <!-- Conversiones: Resumen Estadístico Integrado -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-4">
        <div class="flex items-center gap-2 mb-3">
            <div class="w-2 h-2 rounded-full bg-purple-600"></div>
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Métricas de Conversiones</span>
        </div>
        <div class="grid grid-cols-3 gap-3 divide-x divide-gray-100 text-center">
            <div class="px-2">
                <span class="text-[11px] font-semibold text-gray-400 block uppercase">Hoy</span>
                <p class="text-xl sm:text-2xl font-black text-purple-700 mt-0.5"><?php echo e($conversionesHoy); ?></p>
            </div>
            <div class="px-2">
                <span class="text-[11px] font-semibold text-gray-400 block uppercase">Esta semana</span>
                <p class="text-xl sm:text-2xl font-black text-purple-700 mt-0.5"><?php echo e($conversionesSemana); ?></p>
            </div>
            <div class="px-2">
                <span class="text-[11px] font-semibold text-gray-400 block uppercase">Este mes</span>
                <p class="text-xl sm:text-2xl font-black text-purple-700 mt-0.5"><?php echo e($conversionesMes); ?></p>
            </div>
        </div>
    </div>

    <!-- Gráficos del Sistema (Lado a lado) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Gráfico 1: Ingresos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-5" wire:ignore>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-600"></i> Ingresos — Últimos 30 días
                </h3>
            </div>
            <div class="relative h-60 w-full">
                <canvas id="chartIngresos"></canvas>
            </div>
        </div>

        <!-- Gráfico 2: Conversiones -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200/80 p-5" wire:ignore>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-chart-bar text-purple-600"></i> Conversiones — Últimos 30 días
                </h3>
            </div>
            <div class="relative h-60 w-full">
                <canvas id="chartConversiones"></canvas>
            </div>
        </div>
    </div>

    <!-- Script Chart.js -->
    <script>
        function renderDashboardCharts() {
            // Gráfico 1: Ingresos
            const ctxIngresos = document.getElementById('chartIngresos');
            if (ctxIngresos) {
                if (window.chartIngresosInstance) window.chartIngresosInstance.destroy();
                
                const gradientBlue = ctxIngresos.getContext('2d').createLinearGradient(0, 0, 0, 200);
                gradientBlue.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
                gradientBlue.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

                window.chartIngresosInstance = new Chart(ctxIngresos, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($ingresosLabels, 15, 512) ?>,
                        datasets: [{
                            label: 'Ingresos (S/)',
                            data: <?php echo json_encode($ingresosData, 15, 512) ?>,
                            borderColor: '#2563eb',
                            borderWidth: 2.5,
                            backgroundColor: gradientBlue,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#2563eb',
                            pointRadius: 2,
                            pointHoverRadius: 5
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false } 
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                            y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } }
                        }
                    }
                });
            }

            // Gráfico 2: Conversiones
            const ctxConversiones = document.getElementById('chartConversiones');
            if (ctxConversiones) {
                if (window.chartConversionesInstance) window.chartConversionesInstance.destroy();
                
                window.chartConversionesInstance = new Chart(ctxConversiones, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($conversionesLabels, 15, 512) ?>,
                        datasets: [{
                            label: 'Conversiones',
                            data: <?php echo json_encode($conversionesData, 15, 512) ?>,
                            backgroundColor: '#8b5cf6',
                            borderRadius: 6,
                            borderSkipped: false
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false } 
                        },
                        scales: { 
                            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                            y: { 
                                grid: { color: '#f3f4f6' }, 
                                ticks: { stepSize: 1, font: { size: 10 } } 
                            } 
                        } 
                    }
                });
            }
        }

        document.addEventListener('livewire:navigated', renderDashboardCharts);
    </script>
</div><?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views/livewire/dashboard-arturo.blade.php ENDPATH**/ ?>