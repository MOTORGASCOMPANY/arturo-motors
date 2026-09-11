<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">

        <div class="p-6 border-b border-gray-100 bg-amber-50">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Solicitudes Pendientes</h2>
                    <p class="text-sm text-gray-600 mt-1">Técnicos que necesitan piezas de reemplazo</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-sm font-semibold">
                        {{ count($solicitudes) }} pendiente(s)
                    </span>
                    <button wire:click="cargarSolicitudes" 
                            class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if(empty($solicitudes))
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-green-500 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No hay solicitudes pendientes</p>
                    <p class="text-sm text-gray-400 mt-1">Las solicitudes de los técnicos aparecerán aquí</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($solicitudes as $solicitud)
                        <div class="border border-gray-200 rounded-xl overflow-hidden {{ $solicitudSeleccionada === $solicitud['id'] ? 'ring-2 ring-amber-500' : '' }}">
                            
                            <div wire:click="seleccionarSolicitud({{ $solicitud['id'] }})"
                                 class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-amber-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $solicitud['producto']['nombre'] ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Serie: <span class="font-mono">{{ $solicitud['serie'] }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">
                                        Orden #{{ $solicitud['service_order_id'] ?? 'N/A' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $solicitud['service_order']['cliente']['nombre'] ?? '' }} 
                                        {{ $solicitud['service_order']['cliente']['apellido'] ?? '' }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $solicitud['service_order']['vehiculo']['placa'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            @if($solicitudSeleccionada === $solicitud['id'])
                                <div class="border-t border-gray-100 bg-gray-50 p-4 space-y-4">
                                    
                                    <div class="bg-white border border-gray-200 rounded-lg p-3">
                                        <p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Pieza solicitada</p>
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $solicitud['producto']['nombre'] ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Categoría: {{ $solicitud['producto']['categoria']['nombre'] ?? 'N/A' }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">
                                            Kits disponibles para abrir
                                        </p>
                                        
                                        @if(empty($kitsDisponibles))
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                                <p class="text-sm text-red-700">
                                                    <i class="fas fa-times-circle mr-1"></i>
                                                    No hay kits disponibles para esta pieza
                                                </p>
                                            </div>
                                        @else
                                            <div class="space-y-2">
                                                @foreach($kitsDisponibles as $kit)
                                                    <div wire:click="$set('kitSeleccionadoId', {{ $kit['id'] }})"
                                                         class="flex items-center justify-between p-3 rounded-lg border-2 cursor-pointer transition-all
                                                                {{ $kitSeleccionadoId === $kit['id'] 
                                                                   ? 'border-amber-500 bg-amber-50' 
                                                                   : 'border-gray-200 hover:border-amber-300 bg-white' }}">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                                                                <i class="fas fa-box-open text-amber-600 text-sm"></i>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-semibold text-gray-800">{{ $kit['nombre'] }}</p>
                                                                <p class="text-xs text-gray-500">
                                                                    Serie: <span class="font-mono">{{ $kit['serie'] }}</span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-xs text-gray-500">
                                                                Proveedor: {{ $kit['proveedor'] }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex gap-3 pt-2">
                                        <button wire:click="rechazarSolicitud"
                                                wire:loading.attr="disabled"
                                                type="button"
                                                class="flex-1 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-medium text-sm">
                                            Rechazar
                                        </button>
                                        
                                        @if($kitSeleccionadoId)
                                            <button wire:click="abrirKit"
                                                    wire:loading.attr="disabled"
                                                    type="button"
                                                    class="flex-1 px-4 py-2.5 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition font-semibold text-sm shadow-md">
                                                <i class="fas fa-box-open mr-1"></i> Abrir Kit
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>