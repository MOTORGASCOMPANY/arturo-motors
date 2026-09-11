<div wire:loading.class="opacity-50 pointer-events-none" class="max-w-3xl mx-auto py-12 space-y-6">

    {{-- Header --}}
    <div class="bg-gray-200 p-8 rounded-xl w-full flex items-center justify-between">
        <div>
            <h2 class="text-gray-600 font-semibold text-2xl"><i class="fas fa-truck mr-2"></i>Nuevo traslado</h2>
            <span class="text-xs">Enviar kit o piezas desde Arturo Motors hacia otra sede</span>
        </div>
        <a href="{{ route('almacen.traslados.listado') }}" wire:navigate
            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
            <i class="fas fa-history text-xs"></i> Historial
        </a>
    </div>

    <x-input-error for="general" />

    {{-- Paso 1: Configuración --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-6 h-6 bg-gray-900 text-white text-xs font-bold rounded-full flex items-center justify-center">1</span>
            <h3 class="font-semibold text-gray-800">Configuración del envío</h3>
        </div>

        <div class="space-y-3">
            <div>
                <x-label for="sedeDestinoId" value="Sede destino *" />
                <select wire:model="sedeDestinoId" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">-- Selecciona --</option>
                    @foreach ($this->sedes as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
                <x-input-error for="sedeDestinoId" class="mt-1" />
            </div>

            <div>
                <x-label for="observaciones" value="Observaciones (opcional)" />
                <x-input wire:model="observaciones" class="w-full rounded-lg border-gray-300" placeholder="Ej: para conversión pendiente en Ancon" />
            </div>
        </div>
    </div>

    {{-- Paso 2: Tipo de kit --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="w-6 h-6 {{ $tipoKit ? 'bg-green-600 text-white' : 'bg-gray-900 text-white' }} text-xs font-bold rounded-full flex items-center justify-center">2</span>
            <h3 class="font-semibold text-gray-800">Tipo de kit</h3>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button wire:click="seleccionarTipo('3RA')"
                class="p-4 rounded-xl border-2 text-center transition-all duration-150
                    {{ $tipoKit === '3RA'
                        ? 'border-purple-600 bg-purple-50 shadow-sm'
                        : 'border-gray-200 hover:border-purple-300 hover:bg-purple-50/50' }}">
                <p class="text-2xl font-bold {{ $tipoKit === '3RA' ? 'text-purple-700' : 'text-gray-600' }}">3RA</p>
                <p class="text-xs {{ $tipoKit === '3RA' ? 'text-purple-600' : 'text-gray-400' }} mt-1">23 componentes</p>
            </button>
            <button wire:click="seleccionarTipo('5TA')"
                class="p-4 rounded-xl border-2 text-center transition-all duration-150
                    {{ $tipoKit === '5TA'
                        ? 'border-blue-600 bg-blue-50 shadow-sm'
                        : 'border-gray-200 hover:border-blue-300 hover:bg-blue-50/50' }}">
                <p class="text-2xl font-bold {{ $tipoKit === '5TA' ? 'text-blue-700' : 'text-gray-600' }}">5TA</p>
                <p class="text-xs {{ $tipoKit === '5TA' ? 'text-blue-600' : 'text-gray-400' }} mt-1">25 componentes</p>
            </button>
        </div>
    </div>

    {{-- Paso 3: Modo de envío + selección --}}
    @if($tipoKit)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200/80 p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-6 h-6 {{ $modoEnvio ? 'bg-green-600 text-white' : 'bg-gray-900 text-white' }} text-xs font-bold rounded-full flex items-center justify-center">3</span>
                <h3 class="font-semibold text-gray-800">¿Qué vas a enviar?</h3>
            </div>

            {{-- Tabs --}}
            <div class="grid grid-cols-3 gap-2 mb-4">
                <button wire:click="seleccionarModo('completo')"
                    class="py-2.5 px-4 rounded-lg text-sm font-medium border-2 transition-all
                        {{ $modoEnvio === 'completo'
                            ? 'border-emerald-600 bg-emerald-50 text-emerald-800'
                            : 'border-gray-200 text-gray-500 hover:border-emerald-300' }}">
                    <i class="fas fa-box mr-1"></i> Completo
                </button>
                <button wire:click="seleccionarModo('incompleto')"
                    class="py-2.5 px-4 rounded-lg text-sm font-medium border-2 transition-all
                        {{ $modoEnvio === 'incompleto'
                            ? 'border-amber-600 bg-amber-50 text-amber-800'
                            : 'border-gray-200 text-gray-500 hover:border-amber-300' }}">
                    <i class="fas fa-puzzle-piece mr-1"></i> Incompleto
                </button>
                <button wire:click="seleccionarModo('cantidad')"
                    class="py-2.5 px-4 rounded-lg text-sm font-medium border-2 transition-all
                        {{ $modoEnvio === 'cantidad'
                            ? 'border-blue-600 bg-blue-50 text-blue-800'
                            : 'border-gray-200 text-gray-500 hover:border-blue-300' }}">
                    <i class="fas fa-cubes mr-1"></i> Por cantidad
                </button>
            </div>

            @if($modoEnvio)
                {{-- Buscador --}}
                <input type="text" wire:model.live.debounce.300ms="buscarItem"
                    placeholder="Buscar por serie o nombre..."
                    class="w-full rounded-lg border-gray-200 focus:border-gray-400 focus:ring-gray-400 text-sm mb-3">

                @if($modoEnvio === 'completo')
                    {{-- Kits completos (sellados) --}}
                    <div class="max-h-64 overflow-y-auto space-y-2">
                        @forelse ($this->kitsCompletos as $item)
                            <label class="flex items-center gap-3 border-2 rounded-xl px-4 py-3 cursor-pointer transition-all duration-150
                                        {{ isset($itemsSeleccionados[$item->id])
                                            ? 'border-emerald-500 bg-emerald-50 shadow-sm'
                                            : 'border-gray-100 hover:border-emerald-300 bg-white' }}">
                                <input type="checkbox" wire:click="toggleItem({{ $item->id }})"
                                    @checked(isset($itemsSeleccionados[$item->id]))
                                    class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">{{ $item->producto->nombre }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Caja #{{ $item->id }}</p>
                                </div>
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded-full flex-shrink-0">COMPLETO</span>
                            </label>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-box-open text-2xl mb-2"></i>
                                <p class="text-sm">No hay kits completos de tipo {{ $tipoKit }} disponibles</p>
                            </div>
                        @endforelse
                    </div>

                @elseif($modoEnvio === 'incompleto')
                    {{-- Kit incompleto (piezas sueltas) --}}
                    <div class="max-h-64 overflow-y-auto space-y-2">
                        @forelse ($this->piezasSueltas as $item)
                            <label class="flex items-center gap-3 border-2 rounded-xl px-4 py-3 cursor-pointer transition-all duration-150
                                        {{ isset($itemsSeleccionados[$item->id])
                                            ? 'border-amber-500 bg-amber-50 shadow-sm'
                                            : 'border-gray-100 hover:border-amber-300 bg-white' }}">
                                <input type="checkbox" wire:click="toggleItem({{ $item->id }})"
                                    @checked(isset($itemsSeleccionados[$item->id]))
                                    class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">{{ $item->producto->nombre }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        <i class="fas fa-barcode mr-1"></i>{{ $item->serie ?? 'Sin serie' }}
                                        <span class="mx-1">·</span>
                                        Kit: {{ $item->kitPadre->producto->nombre ?? '—' }}
                                    </p>
                                </div>
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full flex-shrink-0">INCOMPLETO</span>
                            </label>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-puzzle-piece text-2xl mb-2"></i>
                                <p class="text-sm">No hay piezas sueltas de kits {{ $tipoKit }} disponibles</p>
                            </div>
                        @endforelse
                    </div>

                @elseif($modoEnvio === 'cantidad')
                    {{-- Piezas por cantidad --}}
                    <div class="flex gap-2 mb-3">
                        <select wire:model="productoCantidadId" class="flex-1 rounded-lg border-gray-300 text-sm">
                            <option value="">-- Selecciona pieza --</option>
                            @foreach ($this->productosCantidad as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }} (disponible: {{ $p->stockEnSede(1) }})</option>
                            @endforeach
                        </select>
                        <input type="number" min="1" wire:model="cantidadPieza" class="w-20 rounded-lg border-gray-300 text-sm">
                        <button wire:click="agregarPiezaCantidad" type="button"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                            Agregar
                        </button>
                    </div>
                    <x-input-error for="cantidadPieza" class="mb-2" />

                    @if ($this->piezasCantidadCarrito->count())
                        <ul class="space-y-1">
                            @foreach ($this->piezasCantidadCarrito as $p)
                                <li class="flex justify-between items-center text-sm bg-blue-50 border border-blue-200 rounded-lg px-3 py-2">
                                    <span class="text-blue-800">{{ $p->nombre }} × {{ $p->cantidad_solicitada }}</span>
                                    <button wire:click="quitarPiezaCantidad({{ $p->id }})" type="button"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endif
            @endif
        </div>
    @endif

    {{-- Resumen del carrito --}}
    @unless($this->resumenVacio)
        <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-3"><i class="fas fa-list-check mr-1"></i> Resumen del envío</h3>

            <div class="space-y-1">
                @foreach($this->itemsCarrito as $item)
                    <div class="flex items-center justify-between text-sm py-1.5">
                        <div class="flex items-center gap-2">
                            @if($item->kit_padre_id)
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            @endif
                            <span class="text-gray-700">{{ $item->producto->nombre }}</span>
                            @if($item->kit_padre_id)
                                <span class="text-xs text-gray-400">{{ $item->serie ?? 's/serie' }}</span>
                            @else
                                <span class="text-xs text-gray-400">Caja #{{ $item->id }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach

                @foreach($this->piezasCantidadCarrito as $p)
                    <div class="flex items-center justify-between text-sm py-1.5">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                            <span class="text-gray-700">{{ $p->nombre }}</span>
                            <span class="text-xs text-gray-400">×{{ $p->cantidad_solicitada }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between text-sm">
                <span class="text-gray-500">Total:</span>
                <span class="font-bold text-gray-800">
                    {{ count($this->itemsSeleccionados) }} serializados
                    @if(count($this->cantidadSeleccionados))
                        + {{ count($this->cantidadSeleccionados) }} por cantidad
                    @endif
                </span>
            </div>
        </div>
    @endunless

    {{-- Confirmar --}}
    <button wire:click="confirmarTraslado" wire:loading.attr="disabled"
        class="w-full {{ $this->resumenVacio ? 'bg-gray-300 cursor-not-allowed' : 'bg-gray-900 hover:bg-gray-800' }} text-white rounded-xl py-3 font-semibold text-sm transition-colors">
        <i class="fas fa-truck mr-2"></i>Confirmar traslado
    </button>

    {{-- Modal checklist --}}
    @if($mostrarChecklist)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarChecklist"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-lg max-h-[85vh] overflow-hidden flex flex-col">

                <div class="px-6 py-4 border-b border-gray-100 {{ $esKitCompleto ? 'bg-emerald-50' : 'bg-amber-50' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold {{ $esKitCompleto ? 'text-emerald-800' : 'text-amber-800' }}">
                                <i class="fas fa-clipboard-check mr-1"></i> Confirmar envío
                            </h3>
                            <p class="text-sm {{ $esKitCompleto ? 'text-emerald-600' : 'text-amber-600' }} mt-0.5">
                                {{ $sedeDestinoId ? $this->sedes->firstWhere('id', $sedeDestinoId)?->nombre : '' }}
                                — {{ $tipoKit ?? '' }}
                                — {{ $esKitCompleto ? 'Kit completo' : 'Kit incompleto' }}
                            </p>
                        </div>
                        <button wire:click="cerrarChecklist" class="text-gray-400 hover:text-gray-600 transition">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 flex-1 overflow-y-auto">
                    <div class="space-y-2">
                        @forelse($checklistComponentes as $comp)
                            <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-emerald-200 bg-emerald-50">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-emerald-100">
                                    <i class="fas fa-check text-emerald-600 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold text-emerald-800">{{ $comp['nombre'] }}</span>
                                        @if($comp['tipo'] === 'kit_sellado')
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">COMPLETO</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700">INCOMPLETO</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-emerald-600">{{ $comp['detalle'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-box-open text-3xl mb-2"></i>
                                <p class="text-sm">No hay items en el envío</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex gap-3">
                    <button wire:click="cerrarChecklist" type="button"
                        class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-medium text-sm">
                        Volver
                    </button>
                    <button wire:click="confirmarEnvio" wire:loading.attr="disabled" type="button"
                        class="flex-1 px-4 py-2.5 {{ $esKitCompleto ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700' }} text-white rounded-xl transition font-semibold text-sm shadow-md">
                        <i class="fas fa-check mr-1"></i> Confirmar envío
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
