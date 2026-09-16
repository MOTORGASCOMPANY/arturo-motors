<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-4xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-gray-600 font-semibold text-2xl"><i class="fas fa-truck mr-2"></i>Traslados a sedes</h2>
                    <span class="text-xs">Historial de envíos desde Arturo Motors</span>
                </div>
                <a href="{{ route('almacen.traslados.crear') }}" wire:navigate class="bg-indigo-500 px-5 py-3 rounded-md text-white font-semibold hover:bg-indigo-600 transition">
                    Nuevo traslado &nbsp;<i class="fas fa-plus"></i>
                </a>
            </div>

            @forelse ($traslados as $t)
                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-3">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm">→ {{ $t->sedeDestino->nombre }}</span>
                            @if($t->es_kit_completo === true)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <i class="fas fa-check-circle mr-0.5"></i> COMPLETO
                                </span>
                            @elseif($t->es_kit_completo === false)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                    <i class="fas fa-exclamation-triangle mr-0.5"></i> INCOMPLETO
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500">{{ $t->created_at->format('d/m/Y H:i') }} — {{ $t->enviadoPor->name }}</span>
                            <button wire:click="verDetalle({{ $t->id }})"
                                class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition"
                                title="Ver detalle">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="text-xs text-gray-600 space-y-1">
                        @foreach ($t->detalles as $d)
                            <div>
                                {{ $d->producto->nombre }}
                                @if ($d->item_serializado_id)
                                    (Serie: {{ $d->itemSerializado->serie ?? 'Sin serie' }})
                                @else
                                    × {{ $d->cantidad }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if ($t->observaciones)
                        <p class="text-xs text-gray-400 mt-2 italic">{{ $t->observaciones }}</p>
                    @endif
                </div>
            @empty
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">No hay traslados registrados.</div>
            @endforelse

            <div class="mt-4">{{ $traslados->links('pagination::tailwind') }}</div>
        </div>
    </div>

    {{-- Modal detalle --}}
    @if($mostrarDetalle && $trasladoSeleccionado)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="cerrarDetalle"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-md overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-5 bg-gray-900 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg font-bold">→ {{ $trasladoSeleccionado->sedeDestino->nombre }}</p>
                            <p class="text-xs text-gray-300 mt-0.5">{{ $trasladoSeleccionado->created_at->format('d/m/Y H:i') }} — {{ $trasladoSeleccionado->enviadoPor->name }}</p>
                        </div>
                        <button wire:click="cerrarDetalle" class="text-gray-400 hover:text-white transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- Badge tipo --}}
                <div class="px-6 py-3 border-b border-gray-100">
                    @if($trasladoSeleccionado->es_kit_completo)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">
                            <i class="fas fa-check-circle"></i> Kit completo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                            <i class="fas fa-exclamation-triangle"></i> Kit incompleto
                        </span>
                    @endif
                </div>

                {{-- Lista de items --}}
                <div class="px-6 py-4 max-h-64 overflow-y-auto">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Items enviados</p>
                    <div class="space-y-2">
                        @foreach($trasladoSeleccionado->detalles as $d)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                @if($d->item_serializado_id)
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-box text-emerald-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800">{{ $d->producto->nombre }}</p>
                                        <p class="text-xs text-gray-500">Serie: {{ $d->itemSerializado->serie ?? 'Sin serie' }}</p>
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-cubes text-blue-600 text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800">{{ $d->producto->nombre }}</p>
                                        <p class="text-xs text-gray-500">Cantidad: {{ $d->cantidad }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Observaciones --}}
                @if($trasladoSeleccionado->observaciones)
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Observaciones</p>
                        <p class="text-xs text-gray-600 italic">{{ $trasladoSeleccionado->observaciones }}</p>
                    </div>
                @endif

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    <button wire:click="cerrarDetalle" type="button"
                        class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition font-medium text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>