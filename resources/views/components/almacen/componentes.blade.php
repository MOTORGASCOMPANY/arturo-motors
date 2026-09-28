@php
    $serializados = collect($this->modalComponentes)->filter(fn ($c) => $c['es_serializado']);
    $porCantidad  = collect($this->modalComponentes)->filter(fn ($c) => ! $c['es_serializado']);
    $totalCola    = count($this->colaKits);
    $esUltimoKit  = $this->colaIndex >= $totalCola - 1;
    $porcentaje   = $totalCola > 0 ? round((($this->colaIndex + 1) / $totalCola) * 100) : 100;
    $disponibles  = $this->productosDisponiblesModal;
@endphp

<div>
    <div class="flex items-center justify-between mb-5 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
        <p class="text-sm text-indigo-900">
            <i class="fas fa-clipboard-list mr-1.5"></i>
            Componentes de <strong>{{ $this->modalKitNombre }}</strong> — Recibiendo <strong>{{ $this->modalKitCantidad }}</strong> unidad(es)
        </p>
        <button type="button" x-on:click="recepcionSwal.cancelarComponentes($wire)"
                class="text-xs font-semibold text-indigo-600 hover:underline whitespace-nowrap">
            <i class="fas fa-times mr-1"></i> Cancelar
        </button>
    </div>

    @if ($totalCola > 1)
        <div class="mb-5">
            <div class="flex items-center justify-between text-xs text-gray-500 font-medium mb-1">
                <span>Kit {{ $this->colaIndex + 1 }} de {{ $totalCola }}</span>
                <span>{{ $porcentaje }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300" style="width: {{ $porcentaje }}%"></div>
            </div>
        </div>
    @endif

    <div id="zona-componentes">
        @if (empty($this->modalComponentes))
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-box-open text-3xl mb-2"></i>
                <p class="text-sm">Este kit no tiene componentes definidos. Agrega uno abajo.</p>
            </div>
        @else
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

            @if ($serializados->isNotEmpty())
                <div class="mb-5">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i class="fas fa-microchip text-indigo-500"></i> Serializados
                    </h4>
                    <div class="space-y-3">
                        @foreach ($this->modalComponentes as $idx => $comp)
                            @if ($comp['es_serializado'])
                                <x-almacen.componente-serializado :idx="$idx" :comp="$comp" />
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($porCantidad->isNotEmpty())
                <div>
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                        <i class="fas fa-cubes text-amber-500"></i> Por cantidad
                    </h4>
                    <div class="space-y-1.5">
                        @foreach ($this->modalComponentes as $idx => $comp)
                            @if (! $comp['es_serializado'])
                                <x-almacen.componente-cantidad :idx="$idx" :comp="$comp" />
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>

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

    <x-almacen.nuevo-componente />

    <x-almacen.flow-actions method="confirmarModal" align="between" icon="fa-check"
        :label="$esUltimoKit ? 'Confirmar recepción' : 'Siguiente kit →'"
        :data="[
            'data-contenedor' => 'zona-componentes',
            'data-titulo' => $esUltimoKit ? '¿Confirmar la recepción?' : '¿Pasar al siguiente kit?',
            'data-texto' => $esUltimoKit
                ? 'Se registrarán los componentes y series de este kit y se cerrará la recepción.'
                : 'Se guardarán los componentes de este kit y continuarás con el siguiente.',
            'data-boton' => $esUltimoKit ? 'Sí, confirmar' : 'Sí, continuar',
        ]" />
</div>
