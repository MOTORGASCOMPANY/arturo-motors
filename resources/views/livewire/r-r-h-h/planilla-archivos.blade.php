<x-dialog-modal wire:model="open">
    <x-slot name="title">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold">Archivos de planilla: {{ $planilla->contrato->user->name ?? '' }}</h2>
        </div>
    </x-slot>
    <x-slot name="content">
        @if ($planilla)
            <div class="space-y-5">
                <!-- información básica del empleado -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-2 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-700 mb-1">Empleado</h3>
                    <p class="text-sm text-gray-900 font-semibold">{{ $planilla->contrato->user->name }}</p>
                    <p class="text-xs text-gray-500 italic font-medium">Documento: {{ $planilla->contrato->user->dni ?? 'No registrado' }}</p>
                </div>
                <!-- zona de carga de archivos -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-700 mb-3">Subir archivos</h3>
                    <!-- zona de arrastre y selección de archivos -->
                    <div x-data="{ isDropping: false }" @dragover.prevent="isDropping = true"
                        @dragleave.prevent="isDropping = false"
                        @drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                        class="relative border-2 border-dashed rounded-xl p-4 transition-all duration-200 flex flex-col items-center justify-center space-y-3"
                        :class="isDropping ? 'border-indigo-500 bg-blue-50' : 'border-indigo-400 bg-gray-50'">
                        <input type="file" x-ref="fileInput" wire:model="archivo" class="hidden" id="file-upload">
                        <p class="text-gray-500 text-sm font-medium text-center">Arrastra tus archivos aquí o haz clic
                            para seleccionarlos</p>
                        <label for="file-upload"
                            class="cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md text-sm font-bold transition-colors shadow-md">
                            Seleccionar archivos
                        </label>

                        @error('archivo')
                            <span class="text-red-500 text-xs font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- vista previa del archivo seleccionado -->
                    @if ($archivo)
                        <div
                            class="mt-4 p-4 border rounded-lg bg-indigo-50 border-indigo-200 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div
                                    class="w-16 h-16 bg-white rounded border overflow-hidden flex items-center justify-center shadow-sm">
                                    @if (in_array($archivo->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                                        <img src="{{ $archivo->temporaryUrl() }}" class="object-cover w-full h-full">
                                    @else
                                        <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-indigo-900 truncate w-64">
                                        {{ $archivo->getClientOriginalName() }}</p>
                                    <p class="text-xs text-indigo-700 font-medium italic">Preparado para subir...</p>

                                    <div class="mt-2 flex items-center space-x-2">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase">Categoría:</span>
                                        <select wire:model="tipo"
                                            class="text-[10px] border-gray-300 rounded p-1 h-7 bg-white font-bold">
                                            <option value="boleta">Boleta</option>
                                            <option value="comprobante">Comprobante</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button wire:click="$set('archivo', null)"
                                class="text-gray-400 hover:text-red-500 transition">
                                <i class="fas fa-times-circle text-xl"></i>
                            </button>
                        </div>
                    @endif
                </div>
                <!-- lista de archivos ya subidos -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-2 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-700 mb-4">Archivos guardados ({{ $planilla->archivos->count() }})</h3>
                    <div class="space-y-4">
                        @foreach (['comprobante', 'boleta', 'boleta_firmada'] as $t)
                            <div>
                                <h4 class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-2 border-b pb-1"> {{ $t }}</h4>
                                <div class="space-y-3">
                                    @forelse($planilla->archivos->where('tipo', $t) as $file)
                                        <div class="flex flex-wrap items-center justify-between p-2 border border-gray-100 rounded-lg bg-white hover:bg-white hover:border-blue-200 transition-all shadow-sm gap-3">
                                            <!-- vista previa del archivo -->
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-white border border-gray-200 rounded flex items-center justify-center shadow-sm text-lg overflow-hidden flex-shrink-0">
                                                    @if (in_array($file->extension, ['jpg', 'jpeg', 'png']))
                                                        <img src="{{ Storage::url($file->ruta) }}" alt="{{ $file->nombre }}" class="w-full h-full object-cover">
                                                    @else
                                                        <i class="fas fa-file-pdf"></i>
                                                    @endif
                                                </div>
                                                <div class="max-w-[180px]">
                                                    <p class="text-xs font-bold text-gray-800 truncate"
                                                        title="{{ $file->nombre }}">
                                                        {{ $file->nombre }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-400 font-medium italic">
                                                        Subido: {{ $file->created_at->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <!-- acciones para cada archivo -->
                                            <div class="flex items-center flex-wrap gap-2">
                                                <select class="text-[10px] border-gray-300 rounded-md p-1 h-8 bg-white font-bold text-gray-600 focus:ring-0 shadow-sm"
                                                    wire:change="cambiarTipo({{ $file->id }}, $event.target.value)">
                                                    <option value="comprobante" {{ $file->tipo == 'comprobante' ? 'selected' : '' }}>Comprobante</option>
                                                    <option value="boleta" {{ $file->tipo == 'boleta' ? 'selected' : '' }}>Boleta</option>
                                                    <option value="boleta_firmada" {{ $file->tipo == 'boleta_firmada' ? 'selected' : '' }}>Boleta Firmada</option>
                                                </select>
                                                <a href="{{ Storage::url($file->ruta) }}" target="_blank"
                                                    class="px-3 py-1.5 border border-gray-200 rounded text-[10px] font-bold text-gray-700 hover:bg-gray-100 uppercase transition">Ver</a>
                                                <button wire:click="descargarArchivo({{ $file->id }})"
                                                    class="px-3 py-1.5 border border-gray-200 rounded text-[10px] font-bold text-gray-700 hover:bg-gray-100 uppercase transition">Descargar</button>
                                                <button wire:click="eliminarArchivo({{ $file->id }})"
                                                    class="px-3 py-1.5 bg-red-500 text-white rounded text-[10px] font-bold hover:bg-red-600 uppercase transition shadow-sm">Eliminar</button>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-[11px] text-gray-400 italic px-2">Sin archivos en esta categoría.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="py-10 text-center">
                <i class="fas fa-spinner fa-spin text-gray-400 text-3xl mb-2"></i>
                <p class="text-gray-500 text-sm">Cargando información...</p>
            </div>
        @endif
    </x-slot>
    <x-slot name="footer">
        <div class="flex justify-end space-x-3 border-t pt-5 w-full">
            <button wire:click="$set('open', false)"
                class="px-6 py-2 border border-gray-300 rounded-md text-xs font-bold text-gray-700 hover:bg-gray-50 uppercase tracking-widest transition">
                Cerrar
            </button>
            <button wire:click="save" wire:loading.attr="disabled"
                class="px-6 py-2 bg-orange-500 text-white rounded-md text-xs font-bold hover:bg-orange-600 uppercase tracking-widest transition shadow-lg flex items-center">
                <span wire:loading.remove wire:target="save">Guardar</span>
                <span wire:loading wire:target="save">Procesando...</span>
            </button>
        </div>
    </x-slot>
</x-dialog-modal>
