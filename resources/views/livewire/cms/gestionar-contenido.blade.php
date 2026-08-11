<div>
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200 m-4">
        <h4 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fa-solid fa-edit text-blue-600"></i>Gestionar Contenido
        </h4>
        <span class="bg-blue-600 text-white text-xs font-semibold px-4 py-2 rounded-full">{{ $pageTitle }}</span>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-3.5 rounded-xl mb-6 flex justify-between items-center shadow-sm">
            <span class="flex items-center gap-2 font-medium"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <div class="space-y-6 m-4">
        @foreach ($sections as $section)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex flex-col lg:flex-row">

                    {{-- IZQUIERDA: Preview de cómo se ve en la web --}}
                    <div class="lg:w-1/2 p-6 border-b lg:border-b-0 lg:border-r border-gray-100 bg-gray-50/50">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-2 rounded-full bg-green-400"></div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Así se ve en la página</p>
                        </div>
                        <x-section-preview :section="$section" />
                    </div>

                    {{-- DERECHA: Info + imágenes + acciones --}}
                    <div class="lg:w-1/2 p-6">
                        {{-- Header --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                                    <i class="fa-solid fa-layer-group"></i>
                                </div>
                                <div>
                                    <h6 class="font-bold text-gray-900">{{ $section['title'] }}</h6>
                                    <p class="text-gray-400 text-xs">Clave: <code class="bg-gray-100 px-1.5 py-0.5 rounded font-mono">{{ $section['key'] }}</code></p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $section['is_active'] ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-gray-50 text-gray-400 border border-gray-200' }}">
                                {{ $section['is_active'] ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>

                        {{-- Info --}}
                        @if($section['subtitle'] || $section['description'])
                            <div class="mb-4 space-y-1">
                                @if($section['subtitle'])
                                    <p class="text-sm"><span class="font-medium text-gray-700">Subtítulo:</span> <span class="text-gray-500">{{ Str::limit($section['subtitle'], 80) }}</span></p>
                                @endif
                                @if($section['description'])
                                    <p class="text-sm"><span class="font-medium text-gray-700">Descripción:</span> <span class="text-gray-500">{{ Str::limit($section['description'], 80) }}</span></p>
                                @endif
                            </div>
                        @endif

                        {{-- Imágenes actuales --}}
                        @if(count($section['media_items'] ?? []) > 0)
                            <div class="mb-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Imágenes alojadas ({{ count($section['media_items']) }})</p>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($section['media_items'] as $pm)
                                        <div class="relative group rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                                            <img src="{{ asset('storage/' . $pm['media']['file_path']) }}" class="w-full h-20 object-cover">
                                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1">
                                                <a href="{{ asset('storage/' . $pm['media']['file_path']) }}" target="_blank" class="bg-white text-gray-700 text-[10px] font-semibold px-2 py-1 rounded-md hover:bg-gray-100 flex items-center gap-1">
                                                    <i class="fa-solid fa-expand"></i>Ver
                                                </a>
                                                <button wire:click="removeMedia({{ $pm['id'] }})" wire:confirm="¿Eliminar esta imagen?" class="bg-red-500 text-white text-[10px] font-semibold px-2 py-1 rounded-md hover:bg-red-600 flex items-center gap-1">
                                                    <i class="fa-solid fa-trash"></i>Borrar
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mb-4 bg-gray-50 border border-dashed border-gray-300 rounded-xl p-4 text-center">
                                <i class="fa-solid fa-image text-xl text-gray-300 mb-1"></i>
                                <p class="text-gray-400 text-xs">Sin imágenes</p>
                            </div>
                        @endif

                        {{-- Upload --}}
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 mb-4">
                            <div class="flex gap-2">
                                <input type="file" class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="uploadFile" accept="image/*">
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-semibold transition-all shadow-sm whitespace-nowrap" wire:click="uploadMedia({{ $section['id'] }})" wire:loading.attr="disabled">
                                    <i class="fa-solid fa-upload mr-1"></i>Subir
                                </button>
                            </div>
                        </div>

                        {{-- Editar --}}
                        <button class="w-full bg-amber-50 border border-amber-200 text-amber-600 px-4 py-2.5 rounded-xl hover:bg-amber-100 font-semibold transition-all" wire:click="editSection({{ $section['id'] }})">
                            <i class="fa-solid fa-pen mr-1.5"></i>Editar Textos
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($editingSection)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-start justify-center z-50 overflow-y-auto py-8 px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg my-auto">
                <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center rounded-t-2xl z-10">
                    <h5 class="text-lg font-bold text-gray-900">Editar Sección</h5>
                    <button wire:click="$set('editingSection', null)" class="w-9 h-9 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título</label>
                        <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="sectionTitle">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subtítulo</label>
                        <input type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" wire:model="sectionSubtitle">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descripción</label>
                        <textarea class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" rows="4" wire:model="sectionDescription"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 rounded-lg focus:ring-blue-500 border-gray-300" wire:model="sectionActive">
                            <span class="text-sm font-medium text-gray-700">Sección Activa</span>
                        </label>
                    </div>
                </div>
                <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 font-semibold transition-all" wire:click="$set('editingSection', null)">Cancelar</button>
                    <button type="button" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 font-semibold transition-all shadow-sm" wire:click="saveSection">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
