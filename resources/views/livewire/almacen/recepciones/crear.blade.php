<div>
    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-truck text-indigo-600"></i>
                        Recepción de Kits
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Registrar ingreso de kits al almacén</p>
                </div>
                <a href="{{ route('almacen.recepciones.listado') }}" class="text-sm text-gray-500 hover:text-gray-700">← Volver al historial</a>
            </div>

            <form wire:submit="guardar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <select wire:model="proveedorId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar proveedor...</option>
                            @foreach($this->proveedores as $proveedor)
                                <option value="{{ $proveedor }}">{{ $proveedor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sede destino</label>
                        <select wire:model="sedeId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(\App\Models\Sede::activas()->get() as $sede)
                                <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Kits --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-3">Kits a recibir</label>
                    <div class="space-y-3">
                        @foreach ($this->kitsDisponibles as $kit)
                            @php $gen = $kit->atributos['generacion'] ?? '—'; @endphp
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-indigo-300 transition-colors">
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
                                        {{-- Botón editar --}}
                                        <button type="button" wire:click="abrirEditarKit({{ $kit->id }})"
                                            class="w-8 h-8 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 transition"
                                            title="Editar kit">
                                            <i class="fas fa-pen text-xs"></i>
                                        </button>
                                        {{-- Botón eliminar --}}
                                        <button type="button" wire:click="eliminarKit({{ $kit->id }})"
                                            onclick="event.preventDefault(); if(confirm('Desactivar este kit?')) { @this.eliminarKit({{ $kit->id }}) }"
                                            class="w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 flex items-center justify-center text-red-600 transition"
                                            title="Eliminar kit">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                        {{-- Cantidad --}}
                                        <button type="button" wire:click="$set('cantidades.{{ $kit->id }}', Math.max(0, {{ $cantidades[$kit->id] ?? 0 }} - 1))"
                                            class="w-9 h-9 rounded-lg bg-gray-200 hover:bg-gray-300 flex items-center justify-center text-gray-600 font-bold transition-colors">−</button>
                                        <input type="number" min="0" max="99"
                                            wire:model.live="cantidades.{{ $kit->id }}"
                                            class="w-20 text-center text-lg font-bold border border-gray-300 rounded-lg py-1.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <button type="button" wire:click="$set('cantidades.{{ $kit->id }}', {{ $cantidades[$kit->id] ?? 0 }} + 1)"
                                            class="w-9 h-9 rounded-lg bg-indigo-100 hover:bg-indigo-200 flex items-center justify-center text-indigo-700 font-bold transition-colors">+</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Registrar kit nuevo --}}
                <div class="mb-6">
                    @if (!$mostrandoFormKit)
                        <button type="button" wire:click="toggleFormKit"
                            class="w-full flex items-center justify-center gap-2 p-3 border-2 border-dashed border-green-300 rounded-xl text-green-600 hover:bg-green-50 hover:border-green-400 transition font-semibold text-sm">
                            <i class="fas fa-plus-circle text-lg"></i>
                            Registrar kit nuevo
                        </button>
                    @else
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="text-sm font-bold text-green-800">Nuevo kit</h5>
                                <button type="button" wire:click="toggleFormKit" class="text-green-600 hover:text-green-800 text-sm">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Nombre del kit</label>
                                    <input type="text" wire:model.live="nuevoKitNombre" placeholder="Ej: Equipo 5TA"
                                           class="w-full text-sm border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Generación</label>
                                    <input type="text" wire:model.live="nuevoKitGeneracion" placeholder="Ej: 3ra Generación"
                                           class="w-full text-sm border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500">
                                </div>
                            </div>
                            <button type="button" wire:click="registrarKitNuevo" wire:loading.attr="disabled"
                                class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition disabled:opacity-50">
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

                @if ($this->total > 0)
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-box text-amber-600 text-sm"></i>
                            </div>
                            <p class="text-sm font-semibold text-amber-800">
                                Se recibirán <strong>{{ $this->total }}</strong> kit(s). Se abrirá un checklist para registrar componentes y series.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                    <a href="{{ route('almacen.recepciones.listado') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Cancelar</a>
                    <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="guardar">
                            <i class="fas fa-truck mr-1"></i> Recibir {{ $this->total > 0 ? $this->total . ' kit(s)' : '' }}
                        </span>
                        <span wire:loading wire:target="guardar">Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Componentes del kit                                 --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if ($modalAbierto)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" wire:click="cerrarModal"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl"
                         wire:click.away="cerrarModal">

                        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                        <i class="fas fa-clipboard-list"></i> Componentes del Kit
                                    </h3>
                                    <p class="text-sm text-indigo-100 mt-0.5">{{ $modalKitNombre }} — Recibiendo {{ $modalKitCantidad }} unidad(es)</p>
                                </div>
                                <button wire:click="cerrarModal" class="text-white/70 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
                            </div>
                        </div>

                        @if (count($colaKits) > 1)
                            <div class="px-6 pt-3 bg-indigo-50">
                                <div class="flex items-center justify-between text-xs text-indigo-600 font-medium mb-1">
                                    <span>Kit {{ $colaIndex + 1 }} de {{ count($colaKits) }}</span>
                                    <span>{{ round((($colaIndex + 1) / count($colaKits)) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-indigo-200 rounded-full h-1.5">
                                    <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-300"
                                         style="width: {{ round((($colaIndex + 1) / count($colaKits)) * 100) }}%"></div>
                                </div>
                            </div>
                        @endif

                        <div class="px-6 py-4 max-h-[55vh] overflow-y-auto">
                            @if (empty($modalComponentes))
                                <div class="text-center py-8 text-gray-400">
                                    <i class="fas fa-box-open text-3xl mb-2"></i>
                                    <p class="text-sm">No hay componentes definidos para este kit.</p>
                                </div>
                            @else
                                @php
                                    $serializados = collect($modalComponentes)->filter(fn($c) => $c['es_serializado']);
                                    $porCantidad = collect($modalComponentes)->filter(fn($c) => !$c['es_serializado']);
                                @endphp

                                @if ($serializados->isNotEmpty())
                                    <div class="mb-5">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                            <i class="fas fa-microchip text-indigo-500"></i> Serializados
                                        </h4>
                                        <div class="space-y-1.5">
                                            @foreach($modalComponentes as $idx => $comp)
                                                @if ($comp['es_serializado'])
                                                    <div class="flex items-center gap-2 p-2.5 rounded-xl border bg-indigo-50 border-indigo-200">
                                                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                                            <i class="fas fa-microchip text-xs"></i>
                                                        </div>
                                                        <p class="flex-1 text-sm font-semibold text-gray-800 truncate">{{ $comp['nombre'] }}</p>
                                                        <input type="text" wire:model.live="modalComponentes.{{ $idx }}.serie" placeholder="Serie..."
                                                               class="w-48 text-sm border-indigo-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 bg-white shrink-0">
                                                        <button type="button" wire:click="quitarComponenteModal({{ $idx }})"
                                                            class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center shrink-0 transition"
                                                            title="Quitar componente">
                                                            <i class="fas fa-times text-xs"></i>
                                                        </button>
                                                    </div>
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
                                            @foreach($modalComponentes as $idx => $comp)
                                                @if (!$comp['es_serializado'])
                                                    <div class="flex items-center gap-2 p-2.5 rounded-xl border bg-amber-50 border-amber-200">
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
                                                        <button type="button" wire:click="quitarComponenteModal({{ $idx }})"
                                                            class="w-7 h-7 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center shrink-0 transition"
                                                            title="Quitar componente">
                                                            <i class="fas fa-times text-xs"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- Seleccionar componente existente --}}
                            @php $disponibles = $this->productosDisponibles; @endphp
                            @if ($disponibles->isNotEmpty())
                                <div class="border-t border-gray-200 pt-4 mt-4">
                                    <div class="flex items-center gap-2">
                                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Agregar existente:</label>
                                        <select wire:model.live="productoExistenteId" class="flex-1 text-sm border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Seleccionar producto...</option>
                                            @foreach($disponibles as $p)
                                                <option value="{{ $p->id }}">{{ $p->nombre }} ({{ $p->categoria->es_serializado ? 'Serial' : 'Cantidad' }})</option>
                                            @endforeach
                                        </select>
                                        <button type="button" wire:click="agregarComponenteExistente"
                                            class="px-3 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition whitespace-nowrap">
                                            <i class="fas fa-plus mr-1"></i> Agregar
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Registrar componente nuevo --}}
                            <div class="border-t border-dashed border-gray-300 pt-4 mt-4">
                                @if (!$mostrandoFormNuevo)
                                    <button type="button" wire:click="toggleFormNuevo"
                                        class="w-full flex items-center justify-center gap-2 p-3 border-2 border-dashed border-green-300 rounded-xl text-green-600 hover:bg-green-50 hover:border-green-400 transition font-semibold text-sm">
                                        <i class="fas fa-plus-circle text-lg"></i> Registrar componente nuevo
                                    </button>
                                @else
                                    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <h5 class="text-sm font-bold text-green-800">Nuevo componente</h5>
                                            <button type="button" wire:click="toggleFormNuevo" class="text-green-600 hover:text-green-800 text-sm"><i class="fas fa-times"></i> Cancelar</button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                            <div class="sm:col-span-2">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Nombre del producto</label>
                                                <input type="text" wire:model.live="nuevoNombre" placeholder="Ej: Manómetro 150psi"
                                                       class="w-full text-sm border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Tipo</label>
                                                <select wire:model.live="nuevoTipo" class="w-full text-sm border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500">
                                                    <option value="serializado">Serializado</option>
                                                    <option value="cantidad">Por cantidad</option>
                                                </select>
                                            </div>
                                            @if ($nuevoTipo === 'cantidad')
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Cantidad</label>
                                                    <input type="number" min="1" max="99" wire:model.live="nuevaCantidad"
                                                           class="w-full text-sm border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500">
                                                </div>
                                            @endif
                                            @if ($nuevoTipo === 'serializado')
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-600 mb-1">Serie</label>
                                                    <input type="text" wire:model.live="nuevoSerie" placeholder="Ej: ABC-123"
                                                           class="w-full text-sm border-gray-300 rounded-lg focus:border-green-500 focus:ring-green-500">
                                                </div>
                                            @endif
                                        </div>
                                        <button type="button" wire:click="registrarComponenteNuevo" wire:loading.attr="disabled"
                                            class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                                            <span wire:loading.remove wire:target="registrarComponenteNuevo"><i class="fas fa-check mr-1"></i> Registrar y agregar</span>
                                            <span wire:loading wire:target="registrarComponenteNuevo">Guardando...</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t">
                            <button wire:click="cerrarModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancelar</button>
                            <button wire:click="confirmarModal" wire:loading.attr="disabled"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm disabled:opacity-50">
                                <span wire:loading.remove wire:target="confirmarModal">
                                    <i class="fas fa-check mr-1"></i>
                                    @if ($colaIndex < count($colaKits) - 1) Siguiente kit → @else Confirmar recepción @endif
                                </span>
                                <span wire:loading wire:target="confirmarModal">Guardando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: EDITAR KIT                                          --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if ($modalEditarKit)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" wire:click="cerrarEditarKit"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                         wire:click.away="cerrarEditarKit">

                        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <i class="fas fa-pen"></i> Editar Kit
                                </h3>
                                <button wire:click="cerrarEditarKit" class="text-white/70 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
                            </div>
                        </div>

                        <div class="px-6 py-4">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del kit</label>
                                    <input type="text" wire:model.live="editarKitNombre"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Generación</label>
                                    <input type="text" wire:model.live="editarKitGeneracion" placeholder="Ej: 3ra Generación"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t">
                            <button wire:click="cerrarEditarKit" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancelar</button>
                            <button wire:click="guardarEditarKit" wire:loading.attr="disabled"
                                class="px-6 py-2.5 text-sm font-bold text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition shadow-sm disabled:opacity-50">
                                <span wire:loading.remove wire:target="guardarEditarKit"><i class="fas fa-check mr-1"></i> Guardar cambios</span>
                                <span wire:loading wire:target="guardarEditarKit">Guardando...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SweetAlert2 listener --}}
    <script wire:script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('swal', (data) => {
                Swal.fire({
                    icon: data.tipo || 'info',
                    title: data.titulo || '',
                    text: data.mensaje || '',
                    confirmButtonColor: '#4F46E5',
                });
            });
        });
    </script>
</div>
