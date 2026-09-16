<div>
@if ($maxImages > 0 && count($mediaItems) > 0)
    <div class="mb-3">
        <p class="text-xs font-semibold text-gray-500 mb-2 flex justify-between items-center">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-images text-gray-400"></i>
                Galería de Imágenes
            </span>
            <span class="bg-blue-50 text-blue-600 px-2.5 py-0.5 rounded-full text-[10px] font-semibold">
                {{ count($mediaItems) }}/{{ $maxImages }}
            </span>
        </p>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @foreach($mediaItems as $pm)
                <div class="relative group rounded-lg overflow-hidden border border-gray-200 bg-white hover:shadow-md transition-shadow">
                    <img src="{{ asset('storage/' . $pm['media']['file_path']) }}" class="w-full h-16 object-cover">
                    <div class="absolute inset-0 bg-gray-900/60 opacity-0 group-hover:opacity-100 transition-all flex flex-col items-center justify-center gap-1 p-2">
                        <a href="{{ asset('storage/' . $pm['media']['file_path']) }}" target="_blank"
                           class="bg-white text-gray-800 text-[10px] font-semibold px-2 py-1 rounded hover:bg-gray-100 flex items-center gap-1 w-full justify-center">
                            <i class="fa-solid fa-expand"></i> Ver
                        </a>
                        <button type="button" onclick="confirmDeleteImage({{ $pm['id'] }})"
                                class="bg-red-500 text-white text-[10px] font-semibold px-2 py-1 rounded hover:bg-red-600 flex items-center gap-1 w-full justify-center">
                            <i class="fa-solid fa-trash"></i> Borrar
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if ($maxImages > 0)
    <div class="mb-3">
        @if($maxImages > 0 && count($mediaItems) < $maxImages)
            <div x-data="{ dragOver: false }"
                 @dragover.prevent="dragOver = true"
                 @dragleave.prevent="dragOver = false"
                 @drop.prevent="dragOver = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                 :class="dragOver ? 'border-blue-400 bg-blue-50' : 'border-gray-200 bg-white'"
                 class="rounded-lg p-2.5 border transition-colors">
                <input type="file" x-ref="fileInput" accept="image/*" class="hidden"
                       wire:model="uploadFile" wire:change="uploadMedia">
                <div class="flex flex-col sm:flex-row gap-2 items-center">
                    <button type="button" onclick="this.previousElementSibling.click()"
                        class="w-full sm:flex-1 text-xs border-2 border-dashed border-gray-300 rounded-lg px-3 py-1.5 bg-gray-50 text-left text-gray-600 hover:bg-gray-100 hover:border-blue-400 transition-all cursor-pointer flex items-center truncate">
                        <i class="fa-solid fa-image text-gray-400 mr-2 shrink-0"></i>
                        <span class="truncate">Arrastrá una imagen o hacé clic acá...</span>
                    </button>
                    <button type="button" wire:click="uploadMedia"
                        class="w-full sm:w-auto bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 font-semibold transition-all flex justify-center items-center gap-2 text-xs shrink-0">
                        <i class="fa-solid fa-upload"></i> Subir
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 mt-1"><i class="fa-solid fa-circle-info mr-1"></i>JPG, PNG, WebP. Máx: 5MB.</p>
            </div>
        @else
            <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200 text-center flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check text-gray-400"></i>
                <p class="text-gray-500 text-xs font-semibold">Límite alcanzado ({{ $maxImages }})</p>
            </div>
        @endif
    </div>
@endif
</div>
