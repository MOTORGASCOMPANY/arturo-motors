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

    @if($mostrarDetalle && $trasladoSeleccionado)
        <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center sm:p-4" role="dialog" aria-modal="true" x-data>
            <div class="fixed inset-0 bg-gray-900/60" wire:click="cerrarDetalle"></div>

            <div class="relative flex w-full max-w-xl max-h-[92vh] flex-col overflow-hidden rounded-t-2xl sm:rounded-xl bg-white shadow-2xl border border-gray-200"
                 wire:click.away="cerrarDetalle">

                <header class="flex items-center gap-3 px-5 py-4 border-b border-gray-200">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-truck text-indigo-600"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-base font-bold text-gray-800 truncate">→ {{ $trasladoSeleccionado->sedeDestino->nombre }}</h3>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $trasladoSeleccionado->created_at->format('d/m/Y H:i') }} — {{ $trasladoSeleccionado->enviadoPor->name }}</p>
                    </div>
                    @if($trasladoSeleccionado->es_kit_completo)
                        <span class="shrink-0 px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">
                            <i class="fas fa-check-circle mr-0.5"></i> Completo
                        </span>
                    @else
                        <span class="shrink-0 px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                            <i class="fas fa-exclamation-triangle mr-0.5"></i> Incompleto
                        </span>
                    @endif
                    <button type="button" wire:click="cerrarDetalle" aria-label="Cerrar"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        <i class="fas fa-times"></i>
                    </button>
                </header>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Items enviados ({{ $trasladoSeleccionado->detalles->count() }})</p>

                    @forelse($trasladoSeleccionado->detalles as $d)
                        <div class="bg-white border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center gap-3">
                                @if($d->item_serializado_id)
                                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center shrink-0">
                                        <i class="fas fa-barcode text-green-500 text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $d->producto->nombre }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Serie: <span class="font-mono font-semibold">{{ $d->itemSerializado->serie ?? 'Sin serie' }}</span></p>
                                    </div>
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                        <i class="fas fa-cubes text-indigo-500 text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $d->producto->nombre }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Cantidad: <span class="font-bold">{{ $d->cantidad }}</span></p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <x-almacen.empty-state icon="fa-truck" message="Sin items en este traslado" />
                    @endforelse

                    @if($trasladoSeleccionado->observaciones)
                        <div class="mt-2 bg-amber-50/50 border border-amber-100 p-2.5 rounded-lg text-xs text-amber-800 flex gap-2 items-start">
                            <i class="fas fa-comment-alt mt-0.5 opacity-60"></i>
                            <span class="italic leading-relaxed">{{ $trasladoSeleccionado->observaciones }}</span>
                        </div>
                    @endif
                </div>

                <footer class="flex items-center gap-3 px-5 py-3 border-t border-gray-200 bg-gray-50">
                    <button type="button" wire:click="cerrarDetalle"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        Cerrar
                    </button>
                </footer>
            </div>
        </div>
    @endif
</div>