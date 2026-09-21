<div x-data
     x-on:swal.window="recepcionSwal.desdeServidor($event.detail, $wire)"
     x-on:swal-init.window="recepcionSwal.desdeServidor($event.detail, $wire)"
     x-on:swal-kit.window="recepcionSwal.desdeServidor($event.detail, $wire)">

    @php
        // El paso 3 (componentes) reutiliza el flag $modalKitAbierto que ya maneja tu componente,
        // pero ahora se dibuja como una sección de la página, no como un modal.
        $enComponentes = (bool) $modalKitAbierto;
        $pasosFlujo    = $seccion === 'productos' ? ['Datos', 'Productos'] : ['Datos', 'Kits', 'Componentes'];
        $pasoActual    = $seccion === 'elegir' && ! $enComponentes ? 1 : ($enComponentes ? 3 : 2);
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-5">

        {{-- ═══════════════════════════════════════════════════════════════
             CABECERA + PASOS: una sola barra, siempre a la vista
        ═══════════════════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4">

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-truck text-indigo-600"></i>
                        Recepción
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5">Registrar ingreso de kits y productos</p>
                </div>

                <a href="{{ route('almacen.recepciones.listado') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 px-3 py-2 rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Volver al historial
                </a>
            </div>

            <ol class="flex items-center mt-4 text-sm font-semibold" aria-label="Pasos de la recepción">
                @foreach ($pasosFlujo as $i => $nombrePaso)
                    @php
                        $n      = $i + 1;
                        $hecho  = $n < $pasoActual;
                        $activo = $n === $pasoActual;
                    @endphp
                    <li wire:key="paso-{{ $n }}" @if ($activo) aria-current="step" @endif class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                        <span class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs {{ $hecho ? 'bg-green-500 text-white' : ($activo ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                                @if ($hecho)
                                    <i class="fas fa-check text-xs"></i>
                                @else
                                    {{ $n }}
                                @endif
                            </span>
                            <span class="{{ $activo ? 'text-indigo-700' : 'text-gray-500' }}">{{ $nombrePaso }}</span>
                        </span>
                        @unless ($loop->last)
                            <span class="flex-1 h-px mx-3 {{ $hecho ? 'bg-green-400' : 'bg-gray-200' }}"></span>
                        @endunless
                    </li>
                @endforeach
            </ol>
        </div>


        {{-- ═══════════════════════════════════════════════════════════════
             PASO 3 — COMPONENTES DEL KIT
             Izquierda: lo que se captura. Derecha: agregar componentes
             (siempre visible mientras se baja por la lista).
        ═══════════════════════════════════════════════════════════════ --}}
        @if ($enComponentes)
            @php
                $serializados = collect($modalComponentes)->filter(fn ($c) => $c['es_serializado']);
                $porCantidad  = collect($modalComponentes)->filter(fn ($c) => ! $c['es_serializado']);
                $totalCola    = count($colaKits);
                $esUltimoKit  = $colaIndex >= $totalCola - 1;
                $porcentaje   = $totalCola > 0 ? round((($colaIndex + 1) / $totalCola) * 100) : 100;
                $disponibles  = $this->productosDisponiblesModal;
            @endphp

            <div class="space-y-4">

                {{-- Qué kit estoy completando --}}
                <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm text-indigo-900">
                            <i class="fas fa-clipboard-list mr-1.5"></i>
                            Componentes de <strong>{{ $modalKitNombre }}</strong>
                            <span class="text-indigo-300 mx-1">|</span>
                            Recibiendo <strong>{{ $modalKitCantidad }}</strong> unidad(es)
                        </p>
                        @if ($totalCola > 1)
                            <span class="text-sm font-semibold text-indigo-700 tabular-nums">Kit {{ $colaIndex + 1 }} de {{ $totalCola }}</span>
                        @endif
                    </div>

                    @if ($totalCola > 1)
                        <div class="mt-2 w-full bg-indigo-100 rounded-full h-1.5" role="progressbar" aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

                    {{-- ── Columna principal: captura ── --}}
                    <div class="lg:col-span-2 space-y-4">

                        <div id="zona-componentes" class="space-y-4">
                            @if (empty($modalComponentes))
                                <div class="bg-white rounded-xl border border-gray-200 text-center py-10 text-gray-400">
                                    <i class="fas fa-box-open text-3xl mb-2"></i>
                                    <p class="text-sm">Este kit no tiene componentes definidos. Agrégalos desde el panel de la derecha.</p>
                                </div>
                            @else

                                {{-- Produce compartido --}}
                                @if ($serializados->isNotEmpty())
                                    <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3">
                                        <label for="kit-produce" class="flex items-center gap-2 text-sm font-semibold text-indigo-800">
                                            <i class="fas fa-tag text-indigo-500"></i>
                                            Produce <span class="font-normal text-indigo-600">(opcional, aplica a todos los componentes)</span>
                                        </label>
                                        <input id="kit-produce" type="text" wire:model.live="kitProduce" placeholder="Ej: 370-2024"
                                               class="mt-2 w-full max-w-xs text-sm border-indigo-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                    </div>
                                @endif

                                {{-- Serializados --}}
                                @if ($serializados->isNotEmpty())
                                    <section>
                                        <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                                            <i class="fas fa-microchip text-indigo-500"></i> Con serie
                                            <span class="font-normal text-gray-400">({{ $serializados->count() }})</span>
                                        </h4>
                                        <div class="space-y-3">
                                            @foreach ($modalComponentes as $idx => $comp)
                                                @if ($comp['es_serializado'])
                                                    <div wire:key="comp-ser-{{ $idx }}" class="rounded-xl border border-indigo-200 bg-white overflow-hidden">

                                                        <div class="flex items-center gap-2 px-3 py-2.5 bg-indigo-50 border-b border-indigo-100">
                                                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                                                <i class="fas fa-microchip text-xs"></i>
                                                            </div>
                                                            <p class="flex-1 text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</p>
                                                            <span class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full font-semibold">{{ count($comp['unidades'] ?? []) }} uds</span>
                                                            <button type="button"
                                                                    data-idx="{{ $idx }}" data-nombre="{{ $comp['nombre'] }}"
                                                                    x-on:click="recepcionSwal.quitarComponente($wire, $el.dataset)"
                                                                    class="w-8 h-8 rounded-lg text-gray-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center shrink-0 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                                                                    title="Quitar componente" aria-label="Quitar {{ $comp['nombre'] }}">
                                                                <i class="fas fa-times text-sm"></i>
                                                            </button>
                                                        </div>

                                                        <div class="divide-y divide-gray-100">
                                                            @foreach (($comp['unidades'] ?? []) as $uIdx => $unidad)
                                                                <div wire:key="comp-{{ $idx }}-u-{{ $uIdx }}" class="flex flex-wrap items-end gap-x-3 gap-y-2 px-3 py-2.5">
                                                                    <span class="text-xs font-bold text-indigo-400 w-6 pb-2.5">#{{ $uIdx + 1 }}</span>
                                                                    @foreach ($comp['campos_esquema'] ?? ['serie'] as $campo)
                                                                        @php
                                                                            $label = match ($campo) {
                                                                                'capacidad' => 'Capac.',
                                                                                default     => ucfirst(str_replace('_', ' ', $campo)),
                                                                            };
                                                                            $esSerie = $campo === 'serie';
                                                                        @endphp
                                                                        <label class="block text-xs text-gray-500">
                                                                            <span class="block mb-0.5 {{ $esSerie ? 'font-bold text-indigo-700' : '' }}">{{ $label }}</span>
                                                                            <input type="text"
                                                                                wire:model.live="modalComponentes.{{ $idx }}.unidades.{{ $uIdx }}.{{ $campo }}"
                                                                                @if ($esSerie) data-serie data-etiqueta="{{ $comp['nombre'] }} #{{ $uIdx + 1 }}" @endif
                                                                                placeholder="{{ $label }}"
                                                                                class="text-sm border {{ $esSerie ? 'border-indigo-400 font-semibold' : 'border-gray-300' }} bg-white rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-indigo-500"
                                                                                maxlength="50"
                                                                                style="min-width: {{ $esSerie ? '160px' : '110px' }};">
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </section>
                                @endif

                                {{-- Por cantidad --}}
                                @if ($porCantidad->isNotEmpty())
                                    <section>
                                        <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                                            <i class="fas fa-cubes text-amber-500"></i> Por cantidad
                                            <span class="font-normal text-gray-400">({{ $porCantidad->count() }})</span>
                                        </h4>
                                        <div class="space-y-2">
                                            @foreach ($modalComponentes as $idx => $comp)
                                                @if (! $comp['es_serializado'])
                                                    <div wire:key="comp-cant-{{ $idx }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl border border-amber-200 bg-amber-50">
                                                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                                            <i class="fas fa-cubes text-xs"></i>
                                                        </div>
                                                        <p class="flex-1 min-w-0 text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</p>
                                                        <div class="flex items-center gap-1.5 shrink-0">
                                                            <button type="button" wire:click="$set('modalComponentes.{{ $idx }}.cantidad', Math.max(1, {{ $comp['cantidad'] }} - 1))"
                                                                    aria-label="Restar una unidad de {{ $comp['nombre'] }}"
                                                                    class="w-9 h-9 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 flex items-center justify-center text-gray-600 font-bold">−</button>
                                                            <input type="number" min="1" max="99" wire:model.live="modalComponentes.{{ $idx }}.cantidad"
                                                                   aria-label="Cantidad de {{ $comp['nombre'] }}"
                                                                   class="w-16 text-center text-base font-bold border border-amber-300 rounded-lg py-1.5 focus:ring-2 focus:ring-amber-500">
                                                            <button type="button" wire:click="$set('modalComponentes.{{ $idx }}.cantidad', {{ $comp['cantidad'] }} + 1)"
                                                                    aria-label="Sumar una unidad de {{ $comp['nombre'] }}"
                                                                    class="w-9 h-9 rounded-lg bg-amber-200 hover:bg-amber-300 flex items-center justify-center text-amber-800 font-bold">+</button>
                                                        </div>
                                                        <button type="button"
                                                                data-idx="{{ $idx }}" data-nombre="{{ $comp['nombre'] }}"
                                                                x-on:click="recepcionSwal.quitarComponente($wire, $el.dataset)"
                                                                class="w-8 h-8 rounded-lg text-gray-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center shrink-0 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                                                                title="Quitar componente" aria-label="Quitar {{ $comp['nombre'] }}">
                                                            <i class="fas fa-times text-sm"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </section>
                                @endif

                            @endif
                        </div>
                    </div>

                    {{-- ── Columna lateral: agregar componentes ── --}}
                    <aside class="lg:sticky lg:top-4 space-y-4">

                        {{-- Agregar existente --}}
                        @if ($disponibles->isNotEmpty())
                            <section class="bg-white rounded-xl border border-gray-200 p-4">
                                <label for="producto-existente" class="block text-sm font-bold text-gray-700 mb-2">Agregar existente</label>
                                <select id="producto-existente" wire:model.live="productoExistenteId" class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccionar producto...</option>
                                    @foreach ($disponibles as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->categoria->es_serializado ? 'Serial' : 'Cantidad' }})</option>
                                    @endforeach
                                </select>
                                <button type="button"
                                    x-on:click="recepcionSwal.validarYLlamar($wire, [['productoExistenteId', 'El producto a agregar']], 'agregarComponenteExistente')"
                                    class="mt-2 w-full px-3 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                    <i class="fas fa-plus mr-1"></i> Agregar al kit
                                </button>
                            </section>
                        @endif

                        {{-- Registrar componente nuevo --}}
                        <section class="bg-white rounded-xl border border-gray-200 p-4">
                            @if (! $mostrandoFormNuevo)
                                <button type="button" wire:click="toggleFormNuevo"
                                    class="w-full flex items-center justify-center gap-2 p-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 hover:border-gray-400 transition font-semibold text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                    <i class="fas fa-plus-circle text-lg"></i> Registrar componente nuevo
                                </button>
                            @else
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="text-sm font-bold text-gray-800">Nuevo componente</h5>
                                    <button type="button" wire:click="toggleFormNuevo" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i> Cancelar</button>
                                </div>
                                <div class="space-y-3 mb-3">
                                    <div>
                                        <label for="nuevo-nombre" class="block text-xs font-medium text-gray-600 mb-1">Nombre del producto</label>
                                        <input id="nuevo-nombre" type="text" wire:model.live="nuevoNombre" placeholder="Ej: Manómetro 150psi"
                                               class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label for="nuevo-tipo" class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                                        <select id="nuevo-tipo" wire:model.live="nuevoTipo" class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="serializado">Serializado</option>
                                            <option value="cantidad">Por cantidad</option>
                                        </select>
                                    </div>
                                    @if ($nuevoTipo === 'serializado')
                                        <div>
                                            <label for="nuevo-categoria" class="block text-xs font-medium text-gray-600 mb-1">Categoría *</label>
                                            <select id="nuevo-categoria" wire:model.live="nuevoCategoriaId" class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">Seleccionar categoría...</option>
                                                @foreach ($this->categoriasSerializadas as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="nuevo-serie" class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                                            <input id="nuevo-serie" type="text" wire:model.live="nuevoSerie" placeholder="Ej: ABC-123"
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    @endif
                                    @if ($nuevoTipo === 'cantidad')
                                        <div>
                                            <label for="nueva-cantidad" class="block text-xs font-medium text-gray-600 mb-1">Cantidad</label>
                                            <input id="nueva-cantidad" type="number" min="1" max="99" wire:model.live="nuevaCantidad"
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    @endif
                                </div>
                                <button type="button" wire:loading.attr="disabled"
                                    x-on:click="recepcionSwal.registrarComponenteNuevo($wire)"
                                    class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                    <span wire:loading.remove wire:target="registrarComponenteNuevo"><i class="fas fa-check mr-1"></i> Registrar y agregar</span>
                                    <span wire:loading wire:target="registrarComponenteNuevo">Guardando...</span>
                                </button>
                            @endif
                        </section>
                    </aside>
                </div>

                {{-- Acciones fijas al pie --}}
                <div class="sticky bottom-3 z-10 flex items-center gap-3 rounded-xl border border-gray-200 bg-white/95 backdrop-blur px-4 py-3 shadow-lg">
                    <button type="button" x-on:click="recepcionSwal.cancelarComponentes($wire)"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">Cancelar</button>
                    <p class="hidden sm:block flex-1 text-sm text-gray-500 text-right">
                        {{ $esUltimoKit ? 'Es el último kit: al confirmar se cierra la recepción.' : 'Al continuar pasas al siguiente kit.' }}
                    </p>
                    <button type="button" wire:loading.attr="disabled"
                        data-metodo="confirmarModal"
                        data-contenedor="zona-componentes"
                        data-titulo="{{ $esUltimoKit ? '¿Confirmar la recepción?' : '¿Pasar al siguiente kit?' }}"
                        data-texto="{{ $esUltimoKit ? 'Se registrarán los componentes y series de este kit y se cerrará la recepción.' : 'Se guardarán los componentes de este kit y continuarás con el siguiente.' }}"
                        data-boton="{{ $esUltimoKit ? 'Sí, confirmar' : 'Sí, continuar' }}"
                        x-on:click="recepcionSwal.accionConfirmada($wire, $el.dataset)"
                        class="ml-auto sm:ml-0 px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                        <span wire:loading.remove wire:target="confirmarModal">
                            <i class="fas fa-check mr-1"></i>
                            {{ $esUltimoKit ? 'Confirmar recepción' : 'Siguiente kit →' }}
                        </span>
                        <span wire:loading wire:target="confirmarModal">Guardando...</span>
                    </button>
                </div>
            </div>

        {{-- ═══════════════════════════════════════════════════════════════
             PASO 1 — DATOS + QUÉ REGISTRAR (lado a lado)
        ═══════════════════════════════════════════════════════════════ --}}
        @elseif ($seccion === 'elegir')
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-start">

                <section class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                    <h2 class="text-base font-bold text-gray-800">Datos de la recepción</h2>

                    <div>
                        <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-1">Proveedor *</label>
                        <div class="flex gap-2">
                            <select id="proveedor" wire:model="proveedorId" class="flex-1 min-w-0 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccionar proveedor...</option>
                                @foreach ($this->proveedores as $proveedor)
                                    <option value="{{ $proveedor }}">{{ $proveedor }}</option>
                                @endforeach
                            </select>
                            <button type="button" x-on:click="recepcionSwal.nuevoProveedor($wire)"
                                class="shrink-0 w-10 h-10 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-600 flex items-center justify-center transition font-bold text-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                title="Agregar proveedor" aria-label="Agregar proveedor">
                                +
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="sede" class="block text-sm font-medium text-gray-700 mb-1">Sede destino</label>
                        <select id="sede" wire:model="sedeId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (\App\Models\Sede::activas()->get() as $sede)
                                <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </section>

                <section class="lg:col-span-3 bg-white rounded-xl border border-gray-200 p-5">
                    <h2 class="text-base font-bold text-gray-800">¿Qué vas a registrar?</h2>
                    <p class="text-sm text-gray-500 mt-0.5 mb-4">Elige una opción para continuar.</p>

                    <div class="space-y-3">
                        <button type="button"
                            x-on:click="recepcionSwal.elegir($wire, 'kits')"
                            class="w-full flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-box text-indigo-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-base font-bold text-gray-800">Kits</p>
                                <p class="text-sm text-gray-500">Equipos completos por generación</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </button>

                        <button type="button"
                            x-on:click="recepcionSwal.elegir($wire, 'productos')"
                            class="w-full flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                <i class="fas fa-microchip text-indigo-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-base font-bold text-gray-800">Productos</p>
                                <p class="text-sm text-gray-500">Piezas, repuestos y componentes</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300"></i>
                        </button>
                    </div>
                </section>
            </div>

        {{-- ═══════════════════════════════════════════════════════════════
             PASO 2A — KITS
        ═══════════════════════════════════════════════════════════════ --}}
        @elseif ($seccion === 'kits')
            <div class="space-y-4">

                <div class="flex items-center justify-between gap-3 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                    <p class="text-sm text-indigo-900 min-w-0">
                        <i class="fas fa-box mr-1.5"></i>
                        Registrando <strong>Kits</strong>
                        <span class="text-indigo-300 mx-1">|</span>
                        Proveedor: <strong>{{ $proveedorId }}</strong>
                    </p>
                    <button type="button" wire:click="volverAEleccion" class="shrink-0 text-sm font-semibold text-indigo-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded">
                        <i class="fas fa-arrow-left mr-1"></i> Cambiar
                    </button>
                </div>

                <section class="bg-white rounded-xl border border-gray-200 p-5">

                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div>
                            <h2 class="text-base font-bold text-gray-800">Kits a recibir</h2>
                            <p class="text-sm text-gray-500">Indica cuántas unidades de cada kit llegaron.</p>
                        </div>

                        @if (! $mostrandoFormKit)
                            <button type="button" wire:click="toggleFormKit"
                                class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                <i class="fas fa-plus-circle"></i>
                                Registrar kit nuevo
                            </button>
                        @endif
                    </div>

                    {{-- Registrar kit nuevo (arriba, junto a la lista) --}}
                    @if ($mostrandoFormKit)
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="text-sm font-bold text-gray-800">Nuevo kit</h5>
                                <button type="button" wire:click="toggleFormKit" class="text-sm text-gray-500 hover:text-gray-700">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label for="nuevo-kit-nombre" class="block text-xs font-medium text-gray-600 mb-1">Nombre del kit *</label>
                                    <input id="nuevo-kit-nombre" type="text" wire:model.live="nuevoKitNombre" placeholder="Ej: Equipo 5TA"
                                           class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="nuevo-kit-gen" class="block text-xs font-medium text-gray-600 mb-1">Generación</label>
                                    <input id="nuevo-kit-gen" type="text" wire:model.live="nuevoKitGeneracion" placeholder="Ej: 3ra Generación"
                                           class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            <button type="button" wire:loading.attr="disabled"
                                x-on:click="recepcionSwal.validarYLlamar($wire, [['nuevoKitNombre', 'El nombre del kit']], 'registrarKitNuevo')"
                                class="w-full sm:w-auto px-5 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                                <span wire:loading.remove wire:target="registrarKitNuevo"><i class="fas fa-check mr-1"></i> Registrar kit</span>
                                <span wire:loading wire:target="registrarKitNuevo">Guardando...</span>
                            </button>
                        </div>
                    @endif

                    <div class="space-y-2">
                        @forelse ($this->kitsDisponibles as $kit)
                            @php
                                $gen = $kit->atributos['generacion'] ?? '—';
                                $cantKit = (int) ($cantidades[$kit->id] ?? 0);
                            @endphp
                            <div wire:key="kit-{{ $kit->id }}"
                                 class="flex flex-wrap items-center gap-3 p-3 rounded-xl border transition-colors {{ $cantKit > 0 ? 'border-indigo-300 bg-indigo-50/60' : 'border-gray-200 bg-white hover:border-indigo-200' }}">

                                <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-box text-indigo-600"></i>
                                </div>

                                <div class="flex-1 min-w-[10rem]">
                                    <p class="text-sm font-bold text-gray-800">{{ $kit->nombre }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">{{ $gen }}</span>
                                        <span class="text-xs text-gray-500">En stock: {{ $kit->stockTotal() }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1">
                                    <button type="button"
                                        data-id="{{ $kit->id }}" data-nombre="{{ $kit->nombre }}" data-gen="{{ $gen === '—' ? '' : $gen }}"
                                        x-on:click="recepcionSwal.editarKit($wire, $el.dataset)"
                                        class="w-8 h-8 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 flex items-center justify-center transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                        title="Editar kit" aria-label="Editar {{ $kit->nombre }}">
                                        <i class="fas fa-pen text-xs"></i>
                                    </button>
                                    <button type="button"
                                        data-id="{{ $kit->id }}" data-nombre="{{ $kit->nombre }}"
                                        x-on:click="recepcionSwal.eliminarKit($wire, $el.dataset)"
                                        class="w-8 h-8 rounded-lg text-gray-400 hover:bg-red-100 hover:text-red-600 flex items-center justify-center transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500"
                                        title="Desactivar kit" aria-label="Desactivar {{ $kit->nombre }}">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" wire:click="$set('cantidades.{{ $kit->id }}', Math.max(0, {{ $cantidades[$kit->id] ?? 0 }} - 1))"
                                        aria-label="Restar una unidad de {{ $kit->nombre }}"
                                        class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">−</button>
                                    <input type="number" min="0" max="99"
                                        wire:model.live="cantidades.{{ $kit->id }}"
                                        aria-label="Cantidad de {{ $kit->nombre }}"
                                        class="w-16 text-center text-lg font-bold border border-gray-300 rounded-lg py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button type="button" wire:click="$set('cantidades.{{ $kit->id }}', {{ $cantidades[$kit->id] ?? 0 }} + 1)"
                                        aria-label="Sumar una unidad de {{ $kit->nombre }}"
                                        class="w-10 h-10 rounded-lg bg-indigo-100 hover:bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">+</button>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center py-8">Todavía no hay kits registrados. Usa “Registrar kit nuevo” para crear el primero.</p>
                        @endforelse
                    </div>
                </section>

                <section class="bg-white rounded-xl border border-gray-200 p-5">
                    <label for="notas-kits" class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                    <textarea id="notas-kits" wire:model="notas" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Kit incompleto, falta válvula..."></textarea>
                </section>

                {{-- Acciones fijas al pie --}}
                <div class="sticky bottom-3 z-10 flex items-center gap-3 rounded-xl border border-gray-200 bg-white/95 backdrop-blur px-4 py-3 shadow-lg">
                    <button type="button" wire:click="volverAEleccion" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">Cancelar</button>

                    <p class="hidden sm:block flex-1 text-sm text-gray-600 text-right">
                        @if ($this->totalKits > 0)
                            Se recibirán <strong>{{ $this->totalKits }}</strong> kit(s). Luego registrarás componentes y series.
                        @else
                            Aún no elegiste ningún kit.
                        @endif
                    </p>

                    <button type="button" wire:loading.attr="disabled"
                        data-metodo="guardar"
                        data-total="{{ $this->totalKits }}"
                        data-titulo="¿Continuar con {{ $this->totalKits }} kit(s)?"
                        data-texto="Proveedor: {{ $proveedorId }}. Después registrarás los componentes y series de cada kit."
                        data-aviso="Selecciona la cantidad de al menos un kit para continuar."
                        x-on:click="recepcionSwal.accionConfirmada($wire, $el.dataset)"
                        class="ml-auto sm:ml-0 px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                        <span wire:loading.remove wire:target="guardar">
                            <i class="fas fa-truck mr-1"></i> Recibir {{ $this->totalKits > 0 ? $this->totalKits . ' kit(s)' : '' }}
                        </span>
                        <span wire:loading wire:target="guardar">Guardando...</span>
                    </button>
                </div>
            </div>

        {{-- ═══════════════════════════════════════════════════════════════
             PASO 2B — PRODUCTOS
        ═══════════════════════════════════════════════════════════════ --}}
        @elseif ($seccion === 'productos')

            {{-- Intermedio: elegir tipo --}}
            @if ($subSeccionProductos === '')
                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                        <p class="text-sm text-indigo-900 min-w-0">
                            <i class="fas fa-microchip mr-1.5"></i>
                            Registrando <strong>Productos</strong>
                            <span class="text-indigo-300 mx-1">|</span>
                            Proveedor: <strong>{{ $proveedorId }}</strong>
                        </p>
                        <button type="button" wire:click="volverAEleccion" class="shrink-0 text-sm font-semibold text-indigo-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded">
                            <i class="fas fa-arrow-left mr-1"></i> Cambiar
                        </button>
                    </div>

                    <section class="bg-white rounded-xl border border-gray-200 p-5">
                        <h2 class="text-base font-bold text-gray-800">¿Qué tipo de producto vas a recibir?</h2>
                        <p class="text-sm text-gray-500 mt-0.5 mb-4">Elige una opción para continuar.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <button type="button" wire:click="abrirSubSeccion('serializados')"
                                class="flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-barcode text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-gray-800">Serializados</p>
                                    <p class="text-sm text-gray-500">Cada pieza tiene serie propia</p>
                                </div>
                            </button>
                            <button type="button" wire:click="abrirSubSeccion('cantidad')"
                                class="flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 hover:border-amber-400 hover:bg-amber-50 transition text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-cubes text-amber-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-gray-800">Por cantidad</p>
                                    <p class="text-sm text-gray-500">Repuestos, mangueras, piezas sueltas</p>
                                </div>
                            </button>
                        </div>
                    </section>
                </div>

            {{-- Serializados --}}
            @elseif ($subSeccionProductos === 'serializados')
                <div class="space-y-4">

                    <div class="flex items-center justify-between gap-3 rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                        <p class="text-sm text-indigo-900 min-w-0">
                            <i class="fas fa-barcode mr-1.5"></i>
                            Registrando <strong>Productos serializados</strong>
                            <span class="text-indigo-300 mx-1">|</span>
                            Proveedor: <strong>{{ $proveedorId }}</strong>
                        </p>
                        <button type="button" wire:click="volverAProductos" class="shrink-0 text-sm font-semibold text-indigo-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </button>
                    </div>

                    <div id="zona-series" class="space-y-4">
                        @foreach ($this->productosPorCategoria as $categoria => $productos)
                            @php
                                $totalCategoria = $productos->sum(fn ($pr) => (int) ($cantidades[$pr->id] ?? 0));
                            @endphp
                            <section wire:key="cat-{{ \Illuminate\Support\Str::slug($categoria) }}" class="bg-white rounded-xl border border-gray-200 p-4">
                                <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-1.5">
                                    <i class="fas fa-tag text-indigo-400"></i> {{ $categoria }}
                                    @if ($totalCategoria > 0)
                                        <span class="ml-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full tabular-nums">{{ $totalCategoria }}</span>
                                    @endif
                                </h4>

                                <div class="space-y-2">
                                    @foreach ($productos as $producto)
                                        @php
                                            $esquema       = $producto->categoria->esquema_atributos ?? ['serie'];
                                            $campos        = is_string($esquema) ? json_decode($esquema, true) : $esquema;
                                            $camposUnidad  = \App\Livewire\Almacen\Recepciones\Crear::camposPorUnidad($campos);
                                            $tieneProduce  = in_array('produce', $campos);
                                            $cantProducto  = (int) ($cantidades[$producto->id] ?? 0);
                                        @endphp

                                        <div wire:key="prod-{{ $producto->id }}"
                                             class="rounded-xl border transition-colors {{ $cantProducto > 0 ? 'border-indigo-300 bg-indigo-50/40' : 'border-gray-200 bg-white hover:border-indigo-200' }}">

                                            <div class="flex items-center gap-3 p-3">
                                                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                                    <i class="fas fa-barcode text-indigo-600 text-sm"></i>
                                                </div>
                                                <p class="flex-1 min-w-0 text-sm font-bold text-gray-800 truncate">{{ $producto->nombre }}</p>
                                                <div class="flex items-center gap-1.5 shrink-0">
                                                    <button type="button" wire:click="$set('cantidades.{{ $producto->id }}', Math.max(0, {{ $cantProducto }} - 1))"
                                                        aria-label="Restar una unidad de {{ $producto->nombre }}"
                                                        class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">−</button>
                                                    <input type="number" min="0" max="99"
                                                        wire:model.live="cantidades.{{ $producto->id }}"
                                                        aria-label="Cantidad de {{ $producto->nombre }}"
                                                        class="w-16 text-center text-lg font-bold border border-gray-300 rounded-lg py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                    <button type="button" wire:click="$set('cantidades.{{ $producto->id }}', {{ $cantProducto }} + 1)"
                                                        aria-label="Sumar una unidad de {{ $producto->nombre }}"
                                                        class="w-10 h-10 rounded-lg bg-indigo-100 hover:bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">+</button>
                                                </div>
                                            </div>

                                            @if ($cantProducto > 0 && ($tieneProduce || count($camposUnidad) > 0))
                                                <div class="border-t border-indigo-100 px-3 py-3 space-y-3">

                                                    {{-- Campo compartido: Produce --}}
                                                    @if ($tieneProduce)
                                                        <label class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-500">
                                                            <span class="text-sm font-semibold text-indigo-700">Produce</span>
                                                            <input type="text"
                                                                wire:model.live="produces.{{ $producto->id }}"
                                                                placeholder="Ej: 370-2024"
                                                                class="text-sm border border-indigo-300 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-indigo-500 bg-white"
                                                                maxlength="50"
                                                                style="min-width: 160px;">
                                                            <span class="text-gray-400">aplica a todas las unidades</span>
                                                        </label>
                                                    @endif

                                                    {{-- Campos por unidad --}}
                                                    @if (count($camposUnidad) > 0)
                                                        <div class="space-y-2">
                                                            @for ($i = 1; $i <= $cantProducto; $i++)
                                                                <div wire:key="prod-{{ $producto->id }}-u-{{ $i }}" class="flex flex-wrap items-end gap-x-3 gap-y-2">
                                                                    <span class="text-xs font-bold text-indigo-400 w-6 pb-2.5">#{{ $i }}</span>
                                                                    @foreach ($camposUnidad as $campo)
                                                                        @php
                                                                            $placeholder = ucfirst(str_replace('_', ' ', $campo));
                                                                            $label = match ($campo) {
                                                                                'capacidad' => 'Capac.',
                                                                                default     => ucfirst($campo),
                                                                            };
                                                                        @endphp
                                                                        <label class="block text-xs text-gray-500">
                                                                            <span class="block mb-0.5">{{ $label }}</span>
                                                                            <input type="text"
                                                                                wire:model.live="series.{{ $producto->id }}.{{ $i - 1 }}.{{ $campo }}"
                                                                                @if ($campo === 'serie') data-serie data-etiqueta="{{ $producto->nombre }} #{{ $i }}" @endif
                                                                                placeholder="{{ $placeholder }}"
                                                                                class="text-sm border border-gray-300 bg-white rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-indigo-500"
                                                                                maxlength="50"
                                                                                style="min-width: 130px;">
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>

                    <section class="bg-white rounded-xl border border-gray-200 p-5">
                        <label for="notas-serializados" class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                        <textarea id="notas-serializados" wire:model="notas" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Recepción parcial, faltan piezas..."></textarea>
                    </section>

                    {{-- Acciones fijas al pie --}}
                    <div class="sticky bottom-3 z-10 flex items-center gap-3 rounded-xl border border-gray-200 bg-white/95 backdrop-blur px-4 py-3 shadow-lg">
                        <button type="button" wire:click="volverAProductos" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">Cancelar</button>

                        <p class="hidden sm:block flex-1 text-sm text-gray-600 text-right">
                            @if ($this->totalProductos > 0)
                                Se recibirán <strong>{{ $this->totalProductos }}</strong> producto(s) en total.
                            @else
                                Aún no indicaste ninguna cantidad.
                            @endif
                        </p>

                        <button type="button" wire:loading.attr="disabled"
                            data-metodo="guardarProductos"
                            data-total="{{ $this->totalProductos }}"
                            data-contenedor="zona-series"
                            data-titulo="¿Recibir {{ $this->totalProductos }} producto(s)?"
                            data-texto="Proveedor: {{ $proveedorId }}. Se registrarán con las series capturadas."
                            data-aviso="Indica la cantidad de al menos un producto para continuar."
                            x-on:click="recepcionSwal.accionConfirmada($wire, $el.dataset)"
                            class="ml-auto sm:ml-0 px-6 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1">
                            <span wire:loading.remove wire:target="guardarProductos">
                                <i class="fas fa-truck mr-1"></i> Recibir {{ $this->totalProductos > 0 ? $this->totalProductos . ' producto(s)' : '' }}
                            </span>
                            <span wire:loading wire:target="guardarProductos">Guardando...</span>
                        </button>
                    </div>
                </div>

            {{-- Por cantidad --}}
            @elseif ($subSeccionProductos === 'cantidad')
                <div class="space-y-4">

                    <div class="flex items-center justify-between gap-3 rounded-xl border border-amber-100 bg-amber-50 px-4 py-3">
                        <p class="text-sm text-amber-900 min-w-0">
                            <i class="fas fa-cubes mr-1.5"></i>
                            Registrando <strong>Productos por cantidad</strong>
                            <span class="text-amber-300 mx-1">|</span>
                            Proveedor: <strong>{{ $proveedorId }}</strong>
                        </p>
                        <button type="button" wire:click="volverAProductos" class="shrink-0 text-sm font-semibold text-amber-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 rounded">
                            <i class="fas fa-arrow-left mr-1"></i> Volver
                        </button>
                    </div>

                    <section class="bg-white rounded-xl border border-gray-200 p-5">

                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <label class="flex items-center bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 flex-1 min-w-[200px] max-w-sm focus-within:ring-2 focus-within:ring-amber-500">
                                <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                                <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0 p-0"
                                    type="text" wire:model.live="buscarCantidad" placeholder="Buscar producto...">
                            </label>
                            <button type="button" wire:click="toggleFormNuevoCantidad"
                                class="ml-auto px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-1">
                                <i class="fas fa-plus text-xs"></i> Crear nuevo
                            </button>
                        </div>

                        @if ($mostrandoFormNuevoCantidad)
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-4">
                                <h4 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-box text-amber-600"></i> Nuevo producto
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label for="nuevo-cant-nombre" class="block text-xs font-semibold text-gray-600 mb-1">Nombre *</label>
                                        <input id="nuevo-cant-nombre" type="text" wire:model="nuevoCantidadNombre"
                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500" placeholder="Ej: Tuerca M8">
                                    </div>
                                    <div>
                                        <label for="nuevo-cant-categoria" class="block text-xs font-semibold text-gray-600 mb-1">Categoría *</label>
                                        <select id="nuevo-cant-categoria" wire:model="nuevoCantidadCategoriaId"
                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500">
                                            <option value="">Seleccionar...</option>
                                            @foreach ($this->categoriasCantidad as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="nuevo-cant-stock" class="block text-xs font-semibold text-gray-600 mb-1">Stock inicial *</label>
                                        <input id="nuevo-cant-stock" type="number" min="1" max="999" wire:model="nuevoCantidadStockInicial"
                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-3">
                                    <button type="button" wire:click="toggleFormNuevoCantidad" class="px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                                    <button type="button"
                                        x-on:click="recepcionSwal.validarYLlamar($wire, [['nuevoCantidadNombre', 'El nombre'], ['nuevoCantidadCategoriaId', 'La categoría'], ['nuevoCantidadStockInicial', 'El stock inicial']], 'crearProductoCantidad')"
                                        class="px-4 py-2 text-sm font-bold text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-1">
                                        <i class="fas fa-plus mr-1"></i> Crear y agregar
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-2">
                            @forelse ($this->filtradosCantidad as $producto)
                                @php $cantP = (int) ($cantidadesCantidad[$producto->id] ?? 0); @endphp
                                <div wire:key="cant-{{ $producto->id }}"
                                     class="flex items-center gap-3 p-3 rounded-xl border transition-colors {{ $cantP > 0 ? 'border-amber-300 bg-amber-50/60' : 'border-gray-200 bg-white hover:border-amber-200' }}">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                                        <i class="fas fa-cubes text-amber-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $producto->nombre }}</p>
                                        <p class="text-xs text-gray-500">{{ $producto->categoria->nombre ?? '—' }}</p>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button" wire:click="$set('cantidadesCantidad.{{ $producto->id }}', Math.max(0, {{ $cantidadesCantidad[$producto->id] ?? 0 }} - 1))"
                                            aria-label="Restar una unidad de {{ $producto->nombre }}"
                                            class="w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">−</button>
                                        <input type="number" min="0" max="999"
                                            wire:model.live="cantidadesCantidad.{{ $producto->id }}"
                                            aria-label="Cantidad de {{ $producto->nombre }}"
                                            class="w-20 text-center text-lg font-bold border border-gray-300 rounded-lg py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                        <button type="button" wire:click="$set('cantidadesCantidad.{{ $producto->id }}', {{ $cantidadesCantidad[$producto->id] ?? 0 }} + 1)"
                                            aria-label="Sumar una unidad de {{ $producto->nombre }}"
                                            class="w-10 h-10 rounded-lg bg-amber-100 hover:bg-amber-200 flex items-center justify-center text-amber-700 font-bold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">+</button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm text-center py-8">No hay productos por cantidad registrados. Usa “Crear nuevo” para agregar el primero.</p>
                            @endforelse
                        </div>
                    </section>

                    <section class="bg-white rounded-xl border border-gray-200 p-5">
                        <label for="notas-cantidad" class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                        <textarea id="notas-cantidad" wire:model="notas" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Recepción parcial, faltan piezas..."></textarea>
                    </section>

                    {{-- Acciones fijas al pie --}}
                    <div class="sticky bottom-3 z-10 flex items-center gap-3 rounded-xl border border-gray-200 bg-white/95 backdrop-blur px-4 py-3 shadow-lg">
                        <button type="button" wire:click="volverAProductos" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">Cancelar</button>

                        <p class="hidden sm:block flex-1 text-sm text-gray-600 text-right">
                            @if ($this->totalCantidad > 0)
                                Se recibirán <strong>{{ $this->totalCantidad }}</strong> unidad(es) en total.
                            @else
                                Aún no indicaste ninguna cantidad.
                            @endif
                        </p>

                        <button type="button" wire:loading.attr="disabled"
                            data-metodo="guardarCantidad"
                            data-total="{{ $this->totalCantidad }}"
                            data-titulo="¿Recibir {{ $this->totalCantidad }} unidad(es)?"
                            data-texto="Proveedor: {{ $proveedorId }}. Se sumarán al stock de la sede."
                            data-aviso="Indica la cantidad de al menos un producto para continuar."
                            x-on:click="recepcionSwal.accionConfirmada($wire, $el.dataset)"
                            class="ml-auto sm:ml-0 px-6 py-2.5 bg-amber-600 text-white text-sm font-bold rounded-lg hover:bg-amber-700 transition disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-1">
                            <span wire:loading.remove wire:target="guardarCantidad">
                                <i class="fas fa-truck mr-1"></i> Recibir {{ $this->totalCantidad > 0 ? $this->totalCantidad . ' unidad(es)' : '' }}
                            </span>
                            <span wire:loading wire:target="guardarCantidad">Guardando...</span>
                        </button>
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- ═══ SweetAlert2: helpers del flujo (sin modales propios) — SIN CAMBIOS ═══ --}}
    <script wire:script>
        window.recepcionSwal = window.recepcionSwal || (function () {
            const base = {
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280',
                denyButtonColor: '#D97706',
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                reverseButtons: true,
            };

            const construirLista = (items) => {
                const ul = document.createElement('ul');
                ul.style.cssText = 'text-align:left;margin:.5rem auto 0;max-width:24rem;list-style:disc;padding-left:1.25rem;font-size:.9rem;';
                items.forEach((t) => {
                    const li = document.createElement('li');
                    li.textContent = t;
                    ul.appendChild(li);
                });
                return ul;
            };

            // Alerta simple: el flujo sigue cuando la persona presiona OK.
            const alerta = ({ tipo = 'info', titulo = '', mensaje = '', lista = [], boton = 'OK' } = {}) => {
                const opts = { ...base, icon: tipo, title: titulo, confirmButtonText: boton };
                if (lista.length) {
                    const cont = document.createElement('div');
                    if (mensaje) {
                        const p = document.createElement('p');
                        p.textContent = mensaje;
                        cont.appendChild(p);
                    }
                    cont.appendChild(construirLista(lista));
                    opts.html = cont;
                } else if (mensaje) {
                    opts.text = mensaje;
                }
                return Swal.fire(opts);
            };

            const confirmar = async ({ titulo, texto = '', boton = 'Sí, continuar', tipo = 'question' }) => {
                const r = await Swal.fire({ ...base, icon: tipo, title: titulo, text: texto, showCancelButton: true, confirmButtonText: boton });
                return r.isConfirmed;
            };

            // Eventos que despacha Livewire: swal, swal-init, swal-kit.
            // Si el evento trae "continuar", se llama ese método del componente después del OK.
            const desdeServidor = async (d, $wire) => {
                d = Array.isArray(d) ? (d[0] || {}) : (d || {});
                await alerta({
                    tipo: d.tipo || 'info',
                    titulo: d.titulo || '',
                    mensaje: d.mensaje || '',
                    lista: Array.isArray(d.lista) ? d.lista : [],
                });
                if (d.continuar && $wire) await $wire[d.continuar]();
            };

            // Revisa las series vacías dentro de un contenedor. Devuelve true si se puede seguir.
            const revisarSeries = async (contenedorId) => {
                const cont = document.getElementById(contenedorId);
                if (!cont) return true;
                const vacios = [...cont.querySelectorAll('input[data-serie]')].filter((i) => !i.value.trim());
                if (!vacios.length) return true;

                const items = vacios.slice(0, 8).map((i) => i.dataset.etiqueta || 'Serie sin nombre');
                if (vacios.length > 8) items.push(`… y ${vacios.length - 8} más`);
                const html = document.createElement('div');
                const p = document.createElement('p');
                p.textContent = `Faltan ${vacios.length} serie(s) por llenar:`;
                html.appendChild(p);
                html.appendChild(construirLista(items));

                const r = await Swal.fire({
                    ...base,
                    icon: 'warning',
                    title: 'Hay series sin llenar',
                    html,
                    showDenyButton: true,
                    confirmButtonText: 'Completar series',
                    denyButtonText: 'Continuar sin serie',
                });
                if (r.isConfirmed) {
                    const primero = vacios[0];
                    primero.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    primero.focus();
                    return false;
                }
                return r.isDenied;
            };

            // Botón principal de cada paso: valida totales y series, confirma y ejecuta.
            const accionConfirmada = async ($wire, ds) => {
                if (ds.total !== undefined && (parseInt(ds.total, 10) || 0) < 1) {
                    return alerta({ tipo: 'warning', titulo: 'Falta la cantidad', mensaje: ds.aviso || 'Indica al menos una unidad para continuar.' });
                }
                if (ds.contenedor && !(await revisarSeries(ds.contenedor))) return;
                const ok = await confirmar({ titulo: ds.titulo || '¿Continuar?', texto: ds.texto || '', boton: ds.boton || 'Sí, continuar' });
                if (ok) await $wire[ds.metodo]();
            };

            // Valida campos obligatorios del estado de Livewire antes de llamar al método.
            const validarYLlamar = async ($wire, campos, metodo) => {
                const faltan = campos
                    .filter(([prop]) => {
                        const v = $wire[prop];
                        return v === null || v === undefined || String(v).trim() === '';
                    })
                    .map(([, etiqueta]) => `${etiqueta} es obligatorio`);
                if (faltan.length) {
                    return alerta({ tipo: 'warning', titulo: 'Faltan datos', mensaje: 'Completa lo siguiente y presiona OK para continuar:', lista: faltan });
                }
                await $wire[metodo]();
            };

            const registrarComponenteNuevo = async ($wire) => {
                const campos = [['nuevoNombre', 'El nombre del producto']];
                if ($wire.nuevoTipo === 'serializado') campos.push(['nuevoCategoriaId', 'La categoría']);
                await validarYLlamar($wire, campos, 'registrarComponenteNuevo');
            };

            // Paso 1: exige proveedor antes de pasar de sección.
            const elegir = async ($wire, destino) => {
                if (!$wire.proveedorId) {
                    return alerta({ tipo: 'warning', titulo: 'Falta el proveedor', mensaje: 'Selecciona un proveedor para continuar. Si no aparece, agrégalo con el botón +.' });
                }
                await $wire.elegirSeccion(destino);
            };

            const nuevoProveedor = async ($wire) => {
                const r = await Swal.fire({
                    ...base,
                    title: 'Nuevo proveedor',
                    text: 'Se agregará a la lista de proveedores.',
                    input: 'text',
                    inputPlaceholder: 'Nombre del proveedor',
                    inputAttributes: { maxlength: 100, autocomplete: 'off' },
                    showCancelButton: true,
                    confirmButtonText: 'Agregar',
                    inputValidator: (v) => (!v || !v.trim() ? 'Escribe el nombre del proveedor' : undefined),
                });
                if (r.isConfirmed) await $wire.agregarProveedor(r.value.trim());
            };

            const editarKit = async ($wire, ds) => {
                const r = await Swal.fire({
                    ...base,
                    title: 'Editar kit',
                    html:
                        '<div style="text-align:left">' +
                        '<label for="swal-kit-nombre" style="font-size:.85rem;font-weight:600">Nombre del kit</label>' +
                        '<input id="swal-kit-nombre" class="swal2-input" style="margin:.25rem 0 1rem;width:100%" maxlength="100" autocomplete="off">' +
                        '<label for="swal-kit-gen" style="font-size:.85rem;font-weight:600">Generación</label>' +
                        '<input id="swal-kit-gen" class="swal2-input" style="margin:.25rem 0 0;width:100%" maxlength="50" placeholder="Ej: 3ra Generación" autocomplete="off">' +
                        '</div>',
                    showCancelButton: true,
                    confirmButtonText: 'Guardar cambios',
                    didOpen: () => {
                        document.getElementById('swal-kit-nombre').value = ds.nombre || '';
                        document.getElementById('swal-kit-gen').value = ds.gen || '';
                    },
                    preConfirm: () => {
                        const nombre = document.getElementById('swal-kit-nombre').value.trim();
                        const gen = document.getElementById('swal-kit-gen').value.trim();
                        if (!nombre) {
                            Swal.showValidationMessage('El nombre del kit es obligatorio');
                            return false;
                        }
                        return { nombre, gen };
                    },
                });
                if (r.isConfirmed) await $wire.actualizarKit(Number(ds.id), r.value.nombre, r.value.gen);
            };

            const eliminarKit = async ($wire, ds) => {
                const ok = await confirmar({ tipo: 'warning', titulo: '¿Desactivar este kit?', texto: `Se desactivará "${ds.nombre}".`, boton: 'Sí, desactivar' });
                if (ok) await $wire.eliminarKit(Number(ds.id));
            };

            const quitarComponente = async ($wire, ds) => {
                const ok = await confirmar({ tipo: 'warning', titulo: '¿Quitar componente?', texto: `Se quitará "${ds.nombre}" de este kit.`, boton: 'Sí, quitar' });
                if (ok) await $wire.quitarComponenteModal(Number(ds.idx));
            };

            const cancelarComponentes = async ($wire) => {
                const ok = await confirmar({ tipo: 'warning', titulo: '¿Salir del registro de componentes?', texto: 'Se perderá lo capturado en este paso.', boton: 'Sí, salir' });
                if (ok) await $wire.cerrarModal();
            };

            // Errores de servidor (500 / sesión vencida): alerta con OK en lugar del visor por defecto.
            const engancharErrores = () => {
                if (window.__recepcionSwalHook || !window.Livewire || typeof Livewire.hook !== 'function') return;
                window.__recepcionSwalHook = true;
                Livewire.hook('request', (ctx) => {
                    if (!ctx || typeof ctx.fail !== 'function') return;
                    ctx.fail(({ status, content, preventDefault }) => {
                        if (status === 419) {
                            preventDefault();
                            alerta({ tipo: 'warning', titulo: 'Tu sesión expiró', mensaje: 'Recarga la página para continuar.', boton: 'Recargar' }).then(() => location.reload());
                        } else if (status >= 500) {
                            preventDefault();
                            console.error('Error de servidor en Livewire:', content);
                            alerta({ tipo: 'error', titulo: 'No se pudo completar la acción', mensaje: 'Ocurrió un error inesperado. Presiona OK y vuelve a intentarlo.' });
                        }
                    });
                });
            };
            if (window.Livewire) engancharErrores();
            else document.addEventListener('livewire:init', engancharErrores);

            return {
                alerta, confirmar, desdeServidor, accionConfirmada, validarYLlamar, registrarComponenteNuevo,
                elegir, nuevoProveedor, editarKit, eliminarKit, quitarComponente, cancelarComponentes,
            };
        })();
    </script>
</div>