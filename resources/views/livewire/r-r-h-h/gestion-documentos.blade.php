
<div class="p-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fas fa-folder-open mr-2 text-orange-600"></i> Legajo Digital: {{ $usuario->name }}
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($documentosRequeridos as $tipo)
                @php
                    $docSubido = $usuario->documentos->where('tipo_documento_id', $tipo->id)->first();
                @endphp

                <div class="border rounded-lg p-4 flex items-center justify-between bg-gray-50">
                    <div class="flex items-center">
                        <div class="mr-4">
                            @if($docSubido)
                                @if($docSubido->estado == 'Aprobado')
                                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                                @elseif($docSubido->estado == 'Rechazado')
                                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                                @else
                                    <i class="fas fa-clock text-orange-500 text-2xl"></i>
                                @endif
                            @else
                                <i class="fas fa-file-upload text-gray-300 text-2xl"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-sm text-gray-700">{{ $tipo->nombre }}</p>
                            <p class="text-xs text-gray-500">
                                @if($docSubido)
                                    <span class="capitalize">Estado: {{ $docSubido->estado }}</span>
                                @else
                                    <span class="text-red-400">Pendiente de subir</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        @if($docSubido)
                            @hasanyrole('Administrador del sistema|administrador')
                                @if($docSubido->estado == 'Pendiente')
                                    <button wire:click="cambiarEstado({{ $docSubido->id }}, 'Aprobado')"
                                            class="p-2 bg-green-100 text-green-600 rounded-md hover:bg-green-200" title="Aprobar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button wire:click="cambiarEstado({{ $docSubido->id }}, 'Rechazado')"
                                            class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200" title="Rechazar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            @endhasanyrole

                            {{-- Botón para Ver/Descargar --}}
                            <a href="{{ Storage::url($docSubido->ruta) }}" target="_blank"
                            class="p-2 bg-indigo-100 text-indigo-600 rounded-md hover:bg-indigo-200" title="Ver documento">
                                <i class="fas fa-eye"></i>
                            </a>

                            @hasanyrole('Administrador del sistema|administrador')
                                <button wire:confirm="¿Estás seguro de eliminar este documento?"
                                        wire:click="eliminarDocumento({{ $docSubido->id }})"
                                        class="p-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endhasanyrole
                        @endif

                        {{-- Botón para Subir (Solo el dueño o admin si es necesario) --}}
                        @if(!$docSubido || $docSubido->estado == 'Rechazado')
                            <button wire:click="$dispatch('abrir-modal-subir', {tipoId: {{ $tipo->id }}, nombreTipo: '{{ $tipo->nombre }}', userId: {{ $usuarioId }}})"
                                    class="p-2 bg-orange-100 text-orange-600 rounded-md hover:bg-orange-200">
                                <i class="fas fa-upload"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Aquí incluiremos el modal para subir archivos --}}
        @livewire('r-r-h-h.subir-documento', ['usuarioId' => $usuarioId])
    </div>
</div>
