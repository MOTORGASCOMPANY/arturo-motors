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
            <div class="bg-white rounded-xl border border-gray-200 p-5 mb-4 shadow-sm hover:shadow-md transition duration-200">
                {{-- Cabecera de la tarjeta --}}
                <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-100">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-800 text-base">
                                <i class="fas fa-map-marker-alt text-indigo-400 mr-1"></i> {{ $t->sedeDestino->nombre }}
                            </span>
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
                        <p class="text-xs text-gray-500">
                            <i class="far fa-clock mr-1"></i>{{ $t->created_at->format('d/m/Y H:i') }} 
                            <span class="mx-1">•</span> 
                            <i class="far fa-user mr-1"></i>{{ $t->enviadoPor->name }}
                        </p>
                    </div>
                    
                    <button wire:click="verDetalle({{ $t->id }})"
                        class="w-9 h-9 flex items-center justify-center text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white rounded-lg transition-colors"
                        title="Ver detalle completo">
                        <i class="fas fa-eye text-sm"></i>
                    </button>
                </div>
        
                {{-- Lista de items (Vista previa limitada) --}}
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">
                        Contenido del traslado ({{ $t->detalles->count() }} items)
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @php $detallesPreview = $t->detalles->take(4); @endphp
                        @foreach ($detallesPreview as $d)
                            <div class="flex items-center justify-between text-xs bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <i class="fas {{ $d->item_serializado_id ? 'fa-barcode text-gray-400' : 'fa-box text-gray-400' }} text-[10px]"></i>
                                    <span class="truncate font-medium text-gray-700">{{ $d->producto->nombre }}</span>
                                </div>
                                @if ($d->item_serializado_id)
                                    <span class="text-gray-500 font-mono text-[10px] bg-gray-200/70 px-1.5 py-0.5 rounded border border-gray-200 whitespace-nowrap">
                                        SN: {{ $d->itemSerializado->serie ?? 'N/A' }}
                                    </span>
                                @else
                                    <span class="text-gray-600 font-bold bg-gray-200/70 px-1.5 py-0.5 rounded border border-gray-200">
                                        x{{ $d->cantidad }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    @if($t->detalles->count() > 4)
                        <button wire:click="verDetalle({{ $t->id }})" class="w-full mt-2 py-1.5 text-xs text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 rounded font-medium transition text-center">
                            Ver los {{ $t->detalles->count() - 4 }} productos restantes <i class="fas fa-chevron-right text-[9px] ml-1"></i>
                        </button>
                    @endif
                </div>
        
                {{-- Observaciones --}}
                @if ($t->observaciones)
                    <div class="mt-4 bg-amber-50/50 border border-amber-100 p-2.5 rounded-lg text-xs text-amber-800 flex gap-2 items-start">
                        <i class="fas fa-comment-alt mt-0.5 opacity-60"></i> 
                        <span class="italic leading-relaxed">{{ $t->observaciones }}</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-12 px-4 bg-white rounded-xl border border-dashed border-gray-300">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-truck text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium">No hay traslados registrados aún.</p>
            </div>
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