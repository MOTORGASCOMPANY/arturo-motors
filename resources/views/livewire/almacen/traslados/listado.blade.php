<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-4xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-gray-600 font-semibold text-2xl"><i class="fas fa-truck mr-2"></i>Traslados a sedes</h2>
                    <span class="text-xs">Historial de envíos desde Arturo Motors</span>
                </div>
                <a href="{{ route('almacen.traslados.crear') }}" class="bg-indigo-500 px-5 py-3 rounded-md text-white font-semibold hover:bg-indigo-600 transition">
                    Nuevo traslado &nbsp;<i class="fas fa-plus"></i>
                </a>
            </div>

            @forelse ($traslados as $t)
                <div class="bg-white rounded-lg border border-gray-200 p-4 mb-3">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-sm">→ {{ $t->sedeDestino->nombre }}</span>
                        <span class="text-xs text-gray-500">{{ $t->created_at->format('d/m/Y H:i') }} — {{ $t->enviadoPor->name }}</span>
                    </div>
                    <div class="text-xs text-gray-600 space-y-1">
                        @foreach ($t->detalles as $d)
                            <div>
                                {{ $d->producto->nombre }}
                                @if ($d->item_serializado_id)
                                    (Serie: {{ $d->itemSerializado->serie }})
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

            <div class="mt-4">{{ $traslados->links() }}</div>
        </div>
    </div>
</div>