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

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            {{-- ═══ CABECERA ═══ --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-truck text-indigo-600"></i>
                        Recepción
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Registrar ingreso de kits y productos</p>
                </div>
                <a href="{{ route('almacen.recepciones.listado') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver al historial</a>
            </div>

            {{-- ═══ INDICADOR DE PASOS ═══ --}}
            <ol class="flex items-center mb-8 text-xs font-semibold">
                @foreach ($pasosFlujo as $i => $nombrePaso)
                    @php
                        $n      = $i + 1;
                        $hecho  = $n < $pasoActual;
                        $activo = $n === $pasoActual;
                    @endphp
                    <li wire:key="paso-{{ $n }}" class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                        <span class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center {{ $hecho ? 'bg-green-500 text-white' : ($activo ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                                @if ($hecho)
                                    <i class="fas fa-check text-[10px]"></i>
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

            {{-- ═══════════════════════════════════════════════════════
                 PASO 3 — COMPONENTES DEL KIT (antes era un modal)
                 ═══════════════════════════════════════════════════════ --}}
            @if ($enComponentes)
                @php
                    $serializados = collect($modalComponentes)->filter(fn ($c) => $c['es_serializado']);
                    $porCantidad  = collect($modalComponentes)->filter(fn ($c) => ! $c['es_serializado']);
                    $totalCola    = count($colaKits);
                    $esUltimoKit  = $colaIndex >= $totalCola - 1;
                    $porcentaje   = $totalCola > 0 ? round((($colaIndex + 1) / $totalCola) * 100) : 100;
                    $disponibles  = $this->productosDisponiblesModal;
                @endphp

                <div>
                    <div class="flex items-center justify-between mb-5 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                        <p class="text-sm text-indigo-900">
                            <i class="fas fa-clipboard-list mr-1.5"></i>
                            Componentes de <strong>{{ $modalKitNombre }}</strong> — Recibiendo <strong>{{ $modalKitCantidad }}</strong> unidad(es)
                        </p>
                        <button type="button" x-on:click="recepcionSwal.cancelarComponentes($wire)"
                                class="text-xs font-semibold text-indigo-600 hover:underline whitespace-nowrap">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </button>
                    </div>

                    @if ($totalCola > 1)
                        <div class="mb-5">
                            <div class="flex items-center justify-between text-xs text-gray-500 font-medium mb-1">
                                <span>Kit {{ $colaIndex + 1 }} de {{ $totalCola }}</span>
                                <span>{{ $porcentaje }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div id="zona-componentes">
                        @if (empty($modalComponentes))
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-box-open text-3xl mb-2"></i>
                                <p class="text-sm">Este kit no tiene componentes definidos. Agrega uno abajo.</p>
                            </div>
                        @else
                            {{-- Produce compartido --}}
                            @if ($serializados->isNotEmpty())
                                <div class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-xl">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-tag text-indigo-500 text-sm"></i>
                                        <label class="text-xs font-bold text-indigo-700">Produce (opcional, aplica a todos los componentes)</label>
                                    </div>
                                    <input type="text" wire:model.live="kitProduce" placeholder="Ej: 370-2024"
                                           class="mt-2 w-full max-w-xs text-sm border-indigo-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 bg-white">
                                </div>
                            @endif

                            {{-- Serializados --}}
                            @if ($serializados->isNotEmpty())
                                <div class="mb-5">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                        <i class="fas fa-microchip text-indigo-500"></i> Serializados
                                    </h4>
                                    <div class="space-y-3">
                                        @foreach ($modalComponentes as $idx => $comp)
                                            @if ($comp['es_serializado'])
                                                <div wire:key="comp-ser-{{ $idx }}" class="p-3 rounded-xl border bg-indigo-50 border-indigo-200">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                                            <i class="fas fa-microchip text-xs"></i>
                                                        </div>
                                                        <p class="flex-1 text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</p>
                                                        <span class="text-[10px] px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded font-semibold">{{ count($comp['unidades'] ?? []) }} uds</span>
                                                        <button type="button"
                                                                data-idx="{{ $idx }}" data-nombre="{{ $comp['nombre'] }}"
                                                                x-on:click="recepcionSwal.quitarComponente($wire, $el.dataset)"
                                                                class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center shrink-0 transition" title="Quitar componente">
                                                            <i class="fas fa-times text-xs"></i>
                                                        </button>
                                                    </div>
                                                    <div class="space-y-2 ml-9">
                                                        @foreach (($comp['unidades'] ?? []) as $uIdx => $unidad)
                                                            <div wire:key="comp-{{ $idx }}-u-{{ $uIdx }}" class="flex flex-wrap items-center gap-2 p-2 bg-white rounded-lg border border-indigo-100">
                                                                <span class="text-[10px] font-bold text-indigo-400 w-6">#{{ $uIdx + 1 }}</span>
                                                                @foreach ($comp['campos_esquema'] ?? ['serie'] as $campo)
                                                                    @php
                                                                        $label = match ($campo) {
                                                                            'capacidad' => 'Capac.',
                                                                            default     => ucfirst(str_replace('_', ' ', $campo)),
                                                                        };
                                                                        $esSerie = $campo === 'serie';
                                                                    @endphp
                                                                    <label class="flex items-center gap-1 text-xs text-gray-500">
                                                                        <span class="text-[10px] {{ $esSerie ? 'font-bold text-indigo-700' : '' }}">{{ $label }}</span>
                                                                        <input type="text"
                                                                            wire:model.live="modalComponentes.{{ $idx }}.unidades.{{ $uIdx }}.{{ $campo }}"
                                                                            @if ($esSerie) data-serie data-etiqueta="{{ $comp['nombre'] }} #{{ $uIdx + 1 }}" @endif
                                                                            placeholder="{{ $label }}"
                                                                            class="text-sm border {{ $esSerie ? 'border-indigo-400 bg-white font-semibold' : 'border-indigo-200 bg-white' }} rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-indigo-500"
                                                                            maxlength="50"
                                                                            style="min-width: {{ $esSerie ? '140px' : '100px' }};">
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Por cantidad --}}
                            @if ($porCantidad->isNotEmpty())
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                        <i class="fas fa-cubes text-amber-500"></i> Por cantidad
                                    </h4>
                                    <div class="space-y-1.5">
                                        @foreach ($modalComponentes as $idx => $comp)
                                            @if (! $comp['es_serializado'])
                                                <div wire:key="comp-cant-{{ $idx }}" class="flex items-center gap-2 p-2.5 rounded-xl border bg-amber-50 border-amber-200">
                                                    <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                                                        <i class="fas fa-cubes text-xs"></i>
                                                    </div>
                                                    <p class="flex-1 text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</p>
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        <button type="button" wire:click="$set('modalComponentes.{{ $idx }}.cantidad', Math.max(1, {{ $comp['cantidad'] }} - 1))"
                                                                class="w-7 h-7 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 font-bold text-sm">−</button>
                                                        <input type="number" min="1" max="99" wire:model.live="modalComponentes.{{ $idx }}.cantidad"
                                                               class="w-14 text-center text-sm font-bold border border-amber-300 rounded-lg py-1 focus:ring-2 focus:ring-amber-500">
                                                        <button type="button" wire:click="$set('modalComponentes.{{ $idx }}.cantidad', {{ $comp['cantidad'] }} + 1)"
                                                                class="w-7 h-7 rounded-lg bg-amber-200 hover:bg-amber-300 flex items-center justify-center text-amber-700 font-bold text-sm">+</button>
                                                    </div>
                                                    <button type="button"
                                                            data-idx="{{ $idx }}" data-nombre="{{ $comp['nombre'] }}"
                                                            x-on:click="recepcionSwal.quitarComponente($wire, $el.dataset)"
                                                            class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center shrink-0 transition" title="Quitar componente">
                                                        <i class="fas fa-times text-xs"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Agregar existente --}}
                    @if ($disponibles->isNotEmpty())
                        <div class="border-t border-gray-200 pt-4 mt-5">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Agregar existente:</label>
                                <select wire:model.live="productoExistenteId" class="flex-1 text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccionar producto...</option>
                                    @foreach ($disponibles as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->categoria->es_serializado ? 'Serial' : 'Cantidad' }})</option>
                                    @endforeach
                                </select>
                                <button type="button"
                                    x-on:click="recepcionSwal.validarYLlamar($wire, [['productoExistenteId', 'El producto a agregar']], 'agregarComponenteExistente')"
                                    class="px-3 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition whitespace-nowrap">
                                    <i class="fas fa-plus mr-1"></i> Agregar
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Registrar componente nuevo (formulario en línea) --}}
                    <div class="border-t border-dashed border-gray-300 pt-4 mt-4">
                        @if (! $mostrandoFormNuevo)
                            <button type="button" wire:click="toggleFormNuevo"
                                class="w-full flex items-center justify-center gap-2 p-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 hover:border-gray-400 transition font-semibold text-sm">
                                <i class="fas fa-plus-circle text-lg"></i> Registrar componente nuevo
                            </button>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="text-sm font-bold text-gray-800">Nuevo componente</h5>
                                    <button type="button" wire:click="toggleFormNuevo" class="text-gray-500 hover:text-gray-700 text-sm"><i class="fas fa-times"></i> Cancelar</button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Nombre del producto</label>
                                        <input type="text" wire:model.live="nuevoNombre" placeholder="Ej: Manómetro 150psi"
                                               class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                                        <select wire:model.live="nuevoTipo" class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="serializado">Serializado</option>
                                            <option value="cantidad">Por cantidad</option>
                                        </select>
                                    </div>
                                    @if ($nuevoTipo === 'serializado')
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Categoría *</label>
                                            <select wire:model.live="nuevoCategoriaId" class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">Seleccionar categoría...</option>
                                                @foreach ($this->categoriasSerializadas as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                                            <input type="text" wire:model.live="nuevoSerie" placeholder="Ej: ABC-123"
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    @endif
                                    @if ($nuevoTipo === 'cantidad')
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Cantidad</label>
                                            <input type="number" min="1" max="99" wire:model.live="nuevaCantidad"
                                                   class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    @endif
                                </div>
                                <button type="button" wire:loading.attr="disabled"
                                    x-on:click="recepcionSwal.registrarComponenteNuevo($wire)"
                                    class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="registrarComponenteNuevo"><i class="fas fa-check mr-1"></i> Registrar y agregar</span>
                                    <span wire:loading wire:target="registrarComponenteNuevo">Guardando...</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Pie del paso --}}
                    <div class="flex justify-between items-center gap-3 mt-6 pt-4 border-t">
                        <button type="button" x-on:click="recepcionSwal.cancelarComponentes($wire)"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
                        <button type="button" wire:loading.attr="disabled"
                            data-metodo="confirmarModal"
                            data-contenedor="zona-componentes"
                            data-titulo="{{ $esUltimoKit ? '¿Confirmar la recepción?' : '¿Pasar al siguiente kit?' }}"
                            data-texto="{{ $esUltimoKit ? 'Se registrarán los componentes y series de este kit y se cerrará la recepción.' : 'Se guardarán los componentes de este kit y continuarás con el siguiente.' }}"
                            data-boton="{{ $esUltimoKit ? 'Sí, confirmar' : 'Sí, continuar' }}"
                            x-on:click="recepcionSwal.accionConfirmada($wire, $el.dataset)"
                            class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm disabled:opacity-50">
                            <span wire:loading.remove wire:target="confirmarModal">
                                <i class="fas fa-check mr-1"></i>
                                {{ $esUltimoKit ? 'Confirmar recepción' : 'Siguiente kit →' }}
                            </span>
                            <span wire:loading wire:target="confirmarModal">Guardando...</span>
                        </button>
                    </div>
                </div>

            {{-- ═══════════════════════════════════════════════════════
                 PASO 1 — ELEGIR proveedor, sede y qué registrar
                 ═══════════════════════════════════════════════════════ --}}
            @elseif ($seccion === 'elegir')
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor *</label>
                            <div class="flex gap-2">
                                <select wire:model="proveedorId" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccionar proveedor...</option>
                                    @foreach ($this->proveedores as $proveedor)
                                        <option value="{{ $proveedor }}">{{ $proveedor }}</option>
                                    @endforeach
                                </select>
                                <button type="button" x-on:click="recepcionSwal.nuevoProveedor($wire)"
                                    class="shrink-0 w-10 h-10 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-600 flex items-center justify-center transition font-bold text-lg" title="Agregar proveedor">
                                    +
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sede destino</label>
                            <select wire:model="sedeId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach (\App\Models\Sede::activas()->get() as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">¿Qué vas a registrar?</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <button type="button"
                                x-on:click="recepcionSwal.elegir($wire, 'kits')"
                                class="flex items-center gap-4 p-5 rounded-xl border-2 border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition text-left">
                                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-box text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-gray-800">Kits</p>
                                    <p class="text-sm text-gray-500">Equipos completos por generación</p>
                                </div>
                            </button>
                            <button type="button"
                                x-on:click="recepcionSwal.elegir($wire, 'productos')"
                                class="flex items-center gap-4 p-5 rounded-xl border-2 border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition text-left">
                                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-microchip text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-gray-800">Productos</p>
                                    <p class="text-sm text-gray-500">Piezas, repuestos y componentes</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

            {{-- ═══════════════════════════════════════════════════════
                 PASO 2A — KITS
                 ═══════════════════════════════════════════════════════ --}}
            @elseif ($seccion === 'kits')
                <div>
                    <x-almacen.info-banner icon="fa-box" color="indigo"
                        :title="'Registrando <strong>Kits</strong> — Proveedor: <strong>' . $proveedorId . '</strong>'"
                        action-label="Cambiar" action-method="volverAEleccion" />

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Kits a recibir</label>
                        <div class="space-y-3">
                            @forelse ($this->kitsDisponibles as $kit)
                                @php $gen = $kit->atributos['generacion'] ?? '—'; @endphp
                                <div wire:key="kit-{{ $kit->id }}" class="p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                                                <i class="fas fa-box text-indigo-600 text-lg"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800">{{ $kit->nombre }}</p>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded-full">{{ $gen }}</span>
                                                    <span class="text-xs text-gray-400">En stock: {{ $kit->stockTotal() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                data-id="{{ $kit->id }}" data-nombre="{{ $kit->nombre }}" data-gen="{{ $gen === '—' ? '' : $gen }}"
                                                x-on:click="recepcionSwal.editarKit($wire, $el.dataset)"
                                                class="w-8 h-8 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition" title="Editar kit">
                                                <i class="fas fa-pen text-xs"></i>
                                            </button>
                                            <button type="button"
                                                data-id="{{ $kit->id }}" data-nombre="{{ $kit->nombre }}"
                                                x-on:click="recepcionSwal.eliminarKit($wire, $el.dataset)"
                                                class="w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 flex items-center justify-center text-red-600 transition" title="Desactivar kit">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                            <x-almacen.quantity-stepper :model="'cantidades.' . $kit->id" color="indigo" />
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm text-center py-6">Todavía no hay kits registrados. Crea el primero abajo.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Registrar kit nuevo --}}
                    <div class="mb-6">
                        @if (! $mostrandoFormKit)
                            <button type="button" wire:click="toggleFormKit"
                                class="w-full flex items-center justify-center gap-2 p-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-600 hover:bg-gray-50 hover:border-gray-400 transition font-semibold text-sm">
                                <i class="fas fa-plus-circle text-lg"></i>
                                Registrar kit nuevo
                            </button>
                        @else
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="text-sm font-bold text-gray-800">Nuevo kit</h5>
                                    <button type="button" wire:click="toggleFormKit" class="text-gray-500 hover:text-gray-700 text-sm">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Nombre del kit *</label>
                                        <input type="text" wire:model.live="nuevoKitNombre" placeholder="Ej: Equipo 5TA"
                                               class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Generación</label>
                                        <input type="text" wire:model.live="nuevoKitGeneracion" placeholder="Ej: 3ra Generación"
                                               class="w-full text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                                <button type="button" wire:loading.attr="disabled"
                                    x-on:click="recepcionSwal.validarYLlamar($wire, [['nuevoKitNombre', 'El nombre del kit']], 'registrarKitNuevo')"
                                    class="w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition disabled:opacity-50">
                                    <span wire:loading.remove wire:target="registrarKitNuevo"><i class="fas fa-check mr-1"></i> Registrar kit</span>
                                    <span wire:loading wire:target="registrarKitNuevo">Guardando...</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                        <textarea wire:model="notas" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Kit incompleto, falta válvula..."></textarea>
                    </div>

                    @if ($this->totalKits > 0)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-box text-indigo-600 text-sm"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">
                                    Se recibirán <strong>{{ $this->totalKits }}</strong> kit(s). En el siguiente paso registrarás los componentes y series.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                        <button type="button" wire:click="volverAEleccion" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
                        <button type="button" wire:loading.attr="disabled"
                            data-metodo="guardar"
                            data-total="{{ $this->totalKits }}"
                            data-titulo="¿Continuar con {{ $this->totalKits }} kit(s)?"
                            data-texto="Proveedor: {{ $proveedorId }}. Después registrarás los componentes y series de cada kit."
                            data-aviso="Selecciona la cantidad de al menos un kit para continuar."
                            x-on:click="recepcionSwal.accionConfirmada($wire, $el.dataset)"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="guardar">
                                <i class="fas fa-truck mr-1"></i> Recibir {{ $this->totalKits > 0 ? $this->totalKits . ' kit(s)' : '' }}
                            </span>
                            <span wire:loading wire:target="guardar">Guardando...</span>
                        </button>
                    </div>
                </div>

            {{-- ═══════════════════════════════════════════════════════
                 PASO 2B — PRODUCTOS
                 ═══════════════════════════════════════════════════════ --}}
            @elseif ($seccion === 'productos')

                {{-- Intermedio: elegir tipo --}}
                @if ($subSeccionProductos === '')
                    <div>
                        <x-almacen.info-banner icon="fa-microchip" color="indigo"
                            :title="'Registrando <strong>Productos</strong> — Proveedor: <strong>' . $proveedorId . '</strong>'"
                            action-label="Cambiar" action-method="volverAEleccion" />

                        <label class="block text-sm font-bold text-gray-700 mb-3">¿Qué tipo de producto vas a recibir?</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <button type="button" wire:click="abrirSubSeccion('serializados')"
                                class="flex items-center gap-4 p-5 rounded-xl border-2 border-gray-200 hover:border-indigo-400 hover:bg-indigo-50 transition text-left">
                                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-barcode text-indigo-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-gray-800">Serializados</p>
                                    <p class="text-sm text-gray-500">Cada pieza tiene serie propia</p>
                                </div>
                            </button>
                            <button type="button" wire:click="abrirSubSeccion('cantidad')"
                                class="flex items-center gap-4 p-5 rounded-xl border-2 border-gray-200 hover:border-amber-400 hover:bg-amber-50 transition text-left">
                                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                                    <i class="fas fa-cubes text-amber-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-base font-bold text-gray-800">Por cantidad</p>
                                    <p class="text-sm text-gray-500">Repuestos, mangueras, piezas sueltas</p>
                                </div>
                            </button>
                        </div>
                    </div>

                {{-- Serializados --}}
                @elseif ($subSeccionProductos === 'serializados')
                    <div>
                        <x-almacen.info-banner icon="fa-barcode" color="indigo"
                            :title="'Registrando <strong>Productos serializados</strong> — Proveedor: <strong>' . $proveedorId . '</strong>'"
                            action-label="Volver" action-method="volverAProductos" />

                        <div id="zona-series" class="space-y-5">
                            @foreach ($this->productosPorCategoria as $categoria => $productos)
                                <div wire:key="cat-{{ \Illuminate\Support\Str::slug($categoria) }}">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <i class="fas fa-tag text-indigo-400"></i> {{ $categoria }}
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

                                            <div wire:key="prod-{{ $producto->id }}">
                                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors">
                                                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                                        <i class="fas fa-barcode text-indigo-600 text-sm"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $producto->nombre }}</p>
                                                        <span class="text-[10px] px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded font-semibold">Serializado</span>
                                                    </div>
                                                    <div class="flex items-center gap-2 shrink-0">
                                                        <x-almacen.quantity-stepper :model="'cantidades.' . $producto->id" color="indigo" />
                                                    </div>
                                                </div>

                                                {{-- Campo compartido: Produce --}}
                                                @if ($tieneProduce && $cantProducto > 0)
                                                    <div class="mt-2 ml-14 flex items-center gap-2">
                                                        <label class="flex items-center gap-1 text-xs text-gray-500">
                                                            <span class="text-[10px] font-semibold text-indigo-600">Produce</span>
                                                            <input type="text"
                                                                wire:model.live="produces.{{ $producto->id }}"
                                                                placeholder="Ej: 370-2024"
                                                                class="text-sm border border-indigo-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-indigo-500 bg-indigo-50"
                                                                maxlength="50"
                                                                style="min-width: 140px;">
                                                        </label>
                                                        <span class="text-[10px] text-gray-400 italic">— aplica a todas las unidades</span>
                                                    </div>
                                                @endif

                                                {{-- Campos por unidad --}}
                                                @if ($cantProducto > 0 && count($camposUnidad) > 0)
                                                    <div class="mt-2 ml-14 space-y-1 border-l-2 border-indigo-200 pl-3">
                                                        @for ($i = 1; $i <= $cantProducto; $i++)
                                                            <div wire:key="prod-{{ $producto->id }}-u-{{ $i }}" class="flex flex-wrap items-center gap-2">
                                                                <span class="text-xs text-gray-400 w-8">#{{ $i }}</span>
                                                                @foreach ($camposUnidad as $campo)
                                                                    @php
                                                                        $placeholder = ucfirst(str_replace('_', ' ', $campo));
                                                                        $label = match ($campo) {
                                                                            'capacidad' => 'Capac.',
                                                                            default     => ucfirst($campo),
                                                                        };
                                                                    @endphp
                                                                    <label class="flex items-center gap-1 text-xs text-gray-500 w-auto">
                                                                        <span class="w-auto text-[10px]">{{ $label }}</span>
                                                                        <input type="text"
                                                                            wire:model.live="series.{{ $producto->id }}.{{ $i - 1 }}.{{ $campo }}"
                                                                            @if ($campo === 'serie') data-serie data-etiqueta="{{ $producto->nombre }} #{{ $i }}" @endif
                                                                            placeholder="{{ $placeholder }}"
                                                                            class="text-sm border border-gray-300 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-indigo-500"
                                                                            maxlength="50"
                                                                            style="min-width: 120px;">
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @endfor
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-6 mt-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                            <textarea wire:model="notas" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Recepción parcial, faltan piezas..."></textarea>
                        </div>

                        @if ($this->totalProductos > 0)
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-barcode text-indigo-600 text-sm"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">
                                        Se recibirán <strong>{{ $this->totalProductos }}</strong> producto(s) en total.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                            <button type="button" wire:click="volverAProductos" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
                            <button type="button" wire:loading.attr="disabled"
                                data-metodo="guardarProductos"
                                data-total="{{ $this->totalProductos }}"
                                data-contenedor="zona-series"
                                data-titulo="¿Recibir {{ $this->totalProductos }} producto(s)?"
                                data-texto="Proveedor: {{ $proveedorId }}. Se registrarán con las series capturadas."
                                data-aviso="Indica la cantidad de al menos un producto para continuar."
                                x-on:click="recepcionSwal.accionConfirmada($wire, $el.dataset)"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="guardarProductos">
                                    <i class="fas fa-truck mr-1"></i> Recibir {{ $this->totalProductos > 0 ? $this->totalProductos . ' producto(s)' : '' }}
                                </span>
                                <span wire:loading wire:target="guardarProductos">Guardando...</span>
                            </button>
                        </div>
                    </div>

                {{-- Por cantidad --}}
                @elseif ($subSeccionProductos === 'cantidad')
                    <div>
                        <x-almacen.info-banner icon="fa-cubes" color="amber"
                            :title="'Registrando <strong>Productos por cantidad</strong> — Proveedor: <strong>' . $proveedorId . '</strong>'"
                            action-label="Volver" action-method="volverAProductos" />

                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2 flex-1 min-w-[200px] max-w-sm">
                                <i class="fas fa-search text-gray-400 text-sm mr-2"></i>
                                <input class="bg-transparent outline-none text-sm w-full border-none focus:ring-0"
                                    type="text" wire:model.live="buscarCantidad" placeholder="Buscar producto...">
                            </div>
                            <button type="button" wire:click="toggleFormNuevoCantidad"
                                class="ml-3 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2 shrink-0">
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
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nombre *</label>
                                        <input type="text" wire:model="nuevoCantidadNombre"
                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500" placeholder="Ej: Tuerca M8">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Categoría *</label>
                                        <select wire:model="nuevoCantidadCategoriaId"
                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500">
                                            <option value="">Seleccionar...</option>
                                            @foreach ($this->categoriasCantidad as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Stock inicial *</label>
                                        <input type="number" min="1" max="999" wire:model="nuevoCantidadStockInicial"
                                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500">
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-3">
                                    <button type="button" wire:click="toggleFormNuevoCantidad" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                                    <button type="button"
                                        x-on:click="recepcionSwal.validarYLlamar($wire, [['nuevoCantidadNombre', 'El nombre'], ['nuevoCantidadCategoriaId', 'La categoría'], ['nuevoCantidadStockInicial', 'El stock inicial']], 'crearProductoCantidad')"
                                        class="px-4 py-1.5 text-xs font-bold text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition">
                                        <i class="fas fa-plus mr-1"></i> Crear y agregar
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-2">
                            @forelse ($this->filtradosCantidad as $producto)
                                <div wire:key="cant-{{ $producto->id }}" class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 hover:border-amber-300 transition-colors">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                                        <i class="fas fa-cubes text-amber-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $producto->nombre }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $producto->categoria->nombre ?? '—' }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <x-almacen.quantity-stepper :model="'cantidadesCantidad.' . $producto->id" color="amber" />
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-400 text-sm text-center py-6">No hay productos por cantidad registrados. Usa "Crear nuevo" para agregar el primero.</p>
                            @endforelse
                        </div>

                        <div class="mb-6 mt-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notas (opcional)</label>
                            <textarea wire:model="notas" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Recepción parcial, faltan piezas..."></textarea>
                        </div>

                        @if ($this->totalCantidad > 0)
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-cubes text-amber-600 text-sm"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">
                                        Se recibirán <strong>{{ $this->totalCantidad }}</strong> unidad(es) en total.
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                            <button type="button" wire:click="volverAProductos" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancelar</button>
                            <button type="button" wire:loading.attr="disabled"
                                data-metodo="guardarCantidad"
                                data-total="{{ $this->totalCantidad }}"
                                data-titulo="¿Recibir {{ $this->totalCantidad }} unidad(es)?"
                                data-texto="Proveedor: {{ $proveedorId }}. Se sumarán al stock de la sede."
                                data-aviso="Indica la cantidad de al menos un producto para continuar."
                                x-on:click="recepcionSwal.accionConfirmada($wire, $el.dataset)"
                                class="px-6 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
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
    </div>

    {{-- ═══ SweetAlert2: helpers del flujo ═══ --}}
    <script src="{{ asset('js/components/recepcion-swal.js') }}"></script>
</div>