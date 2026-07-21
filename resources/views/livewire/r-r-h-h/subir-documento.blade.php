<div>
    <x-dialog-modal wire:model="mostrarModal">
        <x-slot name="title">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-full mr-3">
                    <i class="fas {{ $nombreTipo === 'Contrato Firmado' ? 'fa-pen-nib' : 'fa-file-upload' }} text-orange-600"></i>
                </div>
                <div>
                    <span class="block text-xl font-bold text-gray-800">
                        {{ $nombreTipo === 'Contrato Firmado' ? 'Firma de Contrato' : 'Cargar Documento' }}
                    </span>
                    <span class="text-sm font-normal text-white">{{ $nombreTipo }}</span>
                </div>
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-4">

                @if($nombreTipo === 'Contrato Firmado')
                    {{-- VISTA PARA CONTRATO --}}
                    <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-orange-500"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-orange-800 uppercase">Firma Digital Automática</h3>
                                <div class="mt-1 text-xs text-orange-700 leading-relaxed">
                                    Al procesar, el sistema generará el documento legal y estampará tu firma registrada automáticamente.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 mt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="autorizaFirma" class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500 h-5 w-5">
                            <span class="ml-3 text-sm font-bold text-gray-700 italic">
                                "Autorizo el estampado de mi firma digital en este contrato."
                            </span>
                        </label>
                        <x-input-error for="autorizaFirma" class="mt-1" />
                    </div>

                @else
                    {{-- VISTA PARA CARGA NORMAL --}}
                    <div class="col-span-1">
                        <x-label value="Seleccione el archivo (PDF, JPG o PNG)" class="mb-2" />
                        <div
                            class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 hover:bg-gray-50 transition-colors"
                            x-data="{ isUploading: false, progress: 0 }"
                            x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                        >
                            <input type="file" wire:model="archivo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".pdf,image/*">

                            <div class="text-center">
                                @if ($archivo)
                                    <i class="fas fa-file-pdf text-4xl text-red-500 mb-2"></i>
                                    <p class="text-sm font-medium text-gray-700">{{ $archivo->getClientOriginalName() }}</p>
                                @else
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-sm text-gray-500">Haz clic o arrastra un archivo aquí</p>
                                @endif
                            </div>

                            <div x-show="isUploading" class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2 text-indigo-600">
                                    <div class="bg-indigo-600 h-2 rounded-full transition-all" :style="`width: ${progress}%` text-indigo-600"></div>
                                </div>
                            </div>
                        </div>
                        <x-input-error for="archivo" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label value="Fecha de Emisión" />
                            <x-input type="date" class="w-full mt-1" wire:model="fecha_emision" />
                        </div>
                        <div>
                            <x-label value="Fecha de Expiración" />
                            <x-input type="date" class="w-full mt-1" wire:model="fecha_expiracion" />
                        </div>
                    </div>
                @endif

            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('mostrarModal', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-button class="ml-3 bg-orange-600 hover:bg-orange-700" wire:click="guardar" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="guardar">
                    <i class="fas {{ $nombreTipo === 'Contrato Firmado' ? 'fa-signature' : 'fa-save' }} mr-2"></i>
                    {{ $nombreTipo === 'Contrato Firmado' ? 'Firmar ahora' : 'Subir Documento' }}
                </span>
                <span wire:loading wire:target="guardar">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Procesando...
                </span>
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
