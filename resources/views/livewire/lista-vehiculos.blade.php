<!-- resources/views/livewire/lista-vehiculos.blade.php -->
<div class="flex box-border" x-data="visorArchivos">

    {{-- Preload del script del visor una sola vez --}}
    @once
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('visorArchivos', () => ({
                    show: false,
                    everOpened: false,
                    url: '',
                    title: '',
                    status: 'loading',
                    isImage: false,
                    zoom: 1,
                    panning: false,
                    panStartX: 0,
                    panStartY: 0,
                    scrollStartX: 0,
                    scrollStartY: 0,
                    moved: false,
                    cache: {},

                    async abrir(url, title) {
                        this.everOpened = true;
                        this.url = url;
                        this.title = title;
                        this.status = 'loading';
                        this.isImage = false;
                        this.zoom = 1;
                        this.show = true;

                        if (this.cache[url]) {
                            this.status = 'ok';
                            this.isImage = this.cache[url] === 'image';
                            return;
                        }
                        try {
                            const r = await fetch(url, {
                                method: 'HEAD',
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const ct = r.headers.get('content-type') || '';
                            if (ct.includes('pdf')) {
                                this.cache[url] = 'pdf';
                                this.status = 'ok';
                            } else if (ct.includes('image')) {
                                this.cache[url] = 'image';
                                this.status = 'ok';
                                this.isImage = true;
                            } else {
                                this.status = 'error';
                            }
                        } catch {
                            this.status = 'error';
                        }
                    },
                    zoomIn() { this.zoom = Math.min(3, +(this.zoom + 0.25).toFixed(2)); },
                    zoomOut() { this.zoom = Math.max(0.5, +(this.zoom - 0.25).toFixed(2)); },
                    zoomReset() { this.zoom = 1; },
                    cerrar() {
                        this.show = false;
                        setTimeout(() => {
                            this.url = '';
                            this.status = 'loading';
                            this.isImage = false;
                            this.zoom = 1;
                        }, 150);
                    },
                    startPan(e) {
                        if (this.zoom <= 1) return;
                        const c = this.$refs.vp;
                        if (!c) return;
                        this.panning = true;
                        this.moved = false;
                        this.panStartX = e.clientX;
                        this.panStartY = e.clientY;
                        this.scrollStartX = c.scrollLeft;
                        this.scrollStartY = c.scrollTop;
                    },
                    movePan(e) {
                        if (!this.panning) return;
                        const c = this.$refs.vp;
                        if (!c) return;
                        const dx = e.clientX - this.panStartX, dy = e.clientY - this.panStartY;
                        if (Math.abs(dx) > 3 || Math.abs(dy) > 3) this.moved = true;
                        c.scrollLeft = this.scrollStartX - dx;
                        c.scrollTop = this.scrollStartY - dy;
                    },
                    stopPan() { this.panning = false; },
                }));
            });
        </script>
    @endonce

    <div class="container mx-auto py-4">
        <x-table-vehiculos>
            @if ($vehiculos->count())
                <table class="w-full whitespace-nowrap table-auto">
                    <thead class="bg-slate-600 font-bold text-white">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left">#</th>
                            <th scope="col" class="px-6 py-4 text-left">Cliente</th>
                            <th scope="col" class="px-6 py-4 text-left">Taller</th>
                            <th scope="col" class="px-6 py-4 text-left">Placa</th>
                            <th scope="col" class="px-6 py-4 text-left">Marca</th>
                            <th scope="col" class="px-6 py-4 text-left">Modelo</th>
                            <th scope="col" class="px-6 py-4 text-left">Documentos</th>
                            <th scope="col" class="px-6 py-4 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($vehiculos as $veh)
                            @php
                                // --- Documentos del sistema (PDFs generados) ---
                                $docsSistema = collect();
                                $docsSistema->push([
                                    'label'   => 'Carta Garantía',
                                    'url'     => route('vehiculo.pdf', $veh->id),
                                    'icon'    => 'fa-solid fa-shield-halved',
                                    'color'   => 'bg-emerald-500',
                                    'tipo'    => 'sistema',
                                ]);
                                $docsSistema->push([
                                    'label'   => 'Manual Conversión',
                                    'url'     => route('manual.pdf', $veh->id),
                                    'icon'    => 'fa-solid fa-book-open',
                                    'color'   => 'bg-amber-500',
                                    'tipo'    => 'sistema',
                                ]);

                                // --- Documentos subidos (desde service_orders -> documentos) ---
                                $docsSubidos = $veh->serviceOrders->flatMap->documentos->map(function ($d) {
                                    $nombreLimpio = ucfirst(str_replace('_', ' ', $d->tipo));
                                    return [
                                        'label'   => $nombreLimpio,
                                        'url'     => $d->url,
                                        'icon'    => 'fa-solid fa-file-pdf',
                                        'color'   => 'bg-indigo-500',
                                        'tipo'    => 'subido',
                                    ];
                                });

                                $todosDocs = $docsSistema->merge($docsSubidos);
                                $totalDocs = $todosDocs->count();
                            @endphp
                            <tr tabindex="0" class="focus:outline-none bg-white h-16 hover:bg-gray-100">
                                <td class="px-6 py-4 text-left">
                                    <div class="flex items-center">
                                        <div class="bg-indigo-200 rounded-md w-7 h-7 flex flex-shrink-0 justify-center items-center text-indigo-900">
                                            {{ $loop->iteration }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p class="text-sm font-medium leading-none text-gray-600">
                                        @php
                                            $cliente = $veh->clientes->first();
                                            $nombreCliente = $cliente
                                                ? ($cliente->nombre
                                                    ? $cliente->nombre . ' ' . $cliente->apellido
                                                    : $cliente->razon_social)
                                                : '—';
                                        @endphp
                                        {{ $nombreCliente }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p class="text-sm leading-none text-gray-600">
                                        Lifegas Company
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p class="text-sm leading-none text-gray-600 p-2 bg-blue-200 rounded-full inline-block">
                                        {{ $veh->placa }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p class="text-sm font-medium leading-none text-gray-600">
                                        {{ $veh->marca }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <p class="text-sm font-medium leading-none text-gray-600">
                                        {{ $veh->modelo }}
                                    </p>
                                </td>

                                {{-- ===================== COLUMNA DOCUMENTOS ===================== --}}
                                <td class="px-6 py-4 text-left">
                                    @if ($totalDocs > 0)
                                        {{-- Dropdown trigger con contador --}}
                                        <div class="relative" x-data="{ open: false }">
                                            <button type="button"
                                                x-on:click="open = !open"
                                                x-on:keydown.escape.window="open = false"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all
                                                       bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900
                                                       focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1"
                                                aria-haspopup="true"
                                                :aria-expanded="open.toString()">
                                                <i class="fa-solid fa-file-lines text-slate-500"></i>
                                                <span>{{ $totalDocs }}</span>
                                                <span class="hidden sm:inline">doc{{ $totalDocs > 1 ? 's' : '' }}</span>
                                                <svg class="w-3 h-3 ml-0.5 text-slate-400 transition-transform duration-150"
                                                    :class="open ? 'rotate-180' : ''"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            {{-- Panel del dropdown --}}
                                            <div x-show="open"
                                                x-on:click.away="open = false"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95"
                                                class="absolute left-0 mt-2 w-72 rounded-xl shadow-xl bg-white ring-1 ring-black/5 divide-y divide-gray-100 focus:outline-none z-50"
                                                role="menu" aria-orientation="vertical">

                                                {{-- Header del dropdown --}}
                                                <div class="px-4 py-2.5 flex items-center justify-between">
                                                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Documentos</span>
                                                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">{{ $totalDocs }}</span>
                                                </div>

                                                {{-- Lista de documentos --}}
                                                <div class="py-1 max-h-64 overflow-y-auto" role="none">
                                                    @foreach ($todosDocs as $doc)
                                                        <button type="button"
                                                            x-on:click="open = false; abrir('{{ $doc['url'] }}', '{{ $doc['label'] }}')"
                                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm hover:bg-gray-50 transition-colors group"
                                                            role="menuitem">
                                                            <div class="w-8 h-8 rounded-lg {{ $doc['color'] }} text-white flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-sm">
                                                                <i class="{{ $doc['icon'] }} text-xs"></i>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <p class="text-xs font-bold text-gray-800 truncate">{{ $doc['label'] }}</p>
                                                                <p class="text-[10px] text-gray-400 font-medium">
                                                                    {{ $doc['tipo'] === 'sistema' ? 'Generado por sistema' : 'Documento subido' }}
                                                                </p>
                                                            </div>
                                                            <i class="fa-solid fa-eye text-[10px] text-gray-300 group-hover:text-indigo-500 transition-colors shrink-0"></i>
                                                        </button>
                                                    @endforeach
                                                </div>

                                                {{-- Footer --}}
                                                <div class="px-4 py-2 bg-gray-50 rounded-b-xl">
                                                    <p class="text-[10px] text-gray-400 font-medium text-center">
                                                        <i class="fa-solid fa-info-circle mr-1"></i> Haz clic para previsualizar
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Estado vacío --}}
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-400 border border-dashed border-gray-200">
                                            <i class="fa-solid fa-folder-open text-[10px]"></i>
                                            <span>Sin docs</span>
                                        </div>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <div class="relative group">
                                            <a wire:click="edit({{ $veh->id }})"
                                                class="py-1 px-2 text-center rounded-md bg-amber-300 font-bold text-black cursor-pointer hover:bg-amber-400">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <span class="absolute bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                                                Editar
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{-- Sección de paginación mejorada --}}
                @if ($vehiculos->hasPages())
                    <div class="py-4 px-2 bg-white">
                        {{ $vehiculos->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-4 text-center font-bold bg-blue-100 rounded-md">
                    No se encontró ningún registro.
                </div>
            @endif
        </x-table-vehiculos>
    </div>

    {{-- ===================== MODAL DE PREVISUALIZACIÓN ===================== --}}
    <template x-if="everOpened">
        <template x-teleport="body">
            <div x-show="show" x-cloak @keydown.escape.window="cerrar()" class="fixed inset-0 z-[90]" style="display:none;">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="cerrar()"></div>

                {{-- Panel del visor --}}
                <div class="absolute inset-4 sm:inset-8 md:inset-12 bg-white rounded-3xl shadow-2xl flex flex-col overflow-hidden border border-white/20"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white shrink-0">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid" :class="isImage ? 'fa-image text-emerald-500 text-lg' : 'fa-file-pdf text-red-500 text-lg'"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-base font-extrabold text-gray-800 truncate block" x-text="title"></span>
                                <span class="text-[11px] text-gray-400 font-medium">Previsualización de contenido en pantalla completa</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Controles de zoom (solo para imágenes) --}}
                            <template x-if="isImage && status === 'ok'">
                                <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 mr-2 border border-gray-200 shadow-inner">
                                    <button @click="zoomOut()" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-600 hover:bg-white shadow-sm transition">
                                        <i class="fa-solid fa-minus text-xs"></i>
                                    </button>
                                    <span class="text-xs font-bold text-gray-700 w-10 text-center select-none" x-text="Math.round(zoom*100)+'%'"></span>
                                    <button @click="zoomIn()" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-600 hover:bg-white shadow-sm transition">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                    <div class="w-px h-4 bg-gray-300 mx-0.5"></div>
                                    <button @click="zoomReset()" class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-600 hover:bg-white shadow-sm transition" title="Restablecer zoom">
                                        <i class="fa-solid fa-expand text-xs"></i>
                                    </button>
                                </div>
                            </template>
                            {{-- Abrir en pestaña --}}
                            <button @click="window.open(url, '_blank')" x-show="status==='ok'"
                                class="px-3 py-2 text-xs font-bold text-indigo-600 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition hidden sm:inline-flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Abrir en pestaña
                            </button>
                            {{-- Cerrar --}}
                            <button @click="cerrar()"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Contenido --}}
                    <div class="flex-1 bg-slate-900 relative overflow-hidden flex items-center justify-center">
                        {{-- Loading --}}
                        <div x-show="status==='loading'" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white z-10">
                            <div class="w-10 h-10 rounded-full border-4 border-indigo-100 border-t-indigo-600 animate-spin"></div>
                            <span class="text-xs font-bold text-gray-600">Cargando contenido del documento...</span>
                        </div>
                        {{-- Error --}}
                        <div x-show="status==='error'" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-white z-10">
                            <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <p class="text-sm font-bold text-gray-700">No se pudo cargar el contenido</p>
                            <p class="text-xs text-gray-500 max-w-xs text-center">El archivo puede estar dañado o no estar disponible en este momento.</p>
                            <button @click="cerrar()" class="text-xs font-bold text-gray-600 bg-gray-100 px-4 py-2 rounded-xl hover:bg-gray-200 transition mt-2">Cerrar visor</button>
                        </div>
                        {{-- PDF --}}
                        <template x-if="status==='ok' && !isImage">
                            <div class="w-full h-full p-2 sm:p-4 flex items-center justify-center bg-slate-950">
                                <iframe :src="url + '#toolbar=1&view=FitH'" class="w-full h-full rounded-2xl border-0 shadow-2xl bg-white" title="Previsualizador de contenido PDF" loading="lazy"></iframe>
                            </div>
                        </template>
                        {{-- Imagen --}}
                        <template x-if="status==='ok' && isImage">
                            <div class="w-full h-full overflow-auto p-6 select-none flex items-center justify-center"
                                :class="zoom <= 1 ? 'flex items-center justify-center' : ''" x-ref="vp"
                                @mousedown="startPan($event)"
                                @mousemove.window.throttle.16ms="movePan($event)"
                                @mouseup.window="stopPan()" @mouseleave="stopPan()">
                                <img :src="url" :style="zoom > 1 ? `width:${zoom*100}%` : ''"
                                    @click="if(moved){moved=false}else{zoom=zoom===1?1.5:1}"
                                    @dragstart.prevent
                                    :class="zoom <= 1 ? 'max-w-full max-h-full object-contain cursor-zoom-in' : 'block mx-auto max-w-none cursor-grab'"
                                    class="rounded-2xl shadow-2xl transition-[width] duration-150 bg-white"
                                    draggable="false" loading="lazy" alt="Previsualización de imagen">
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </template>

    {{-- ===================== MODAL EDITAR VEHÍCULO ===================== --}}
    <x-dialog-modal wire:model="open" wire:loading.attr="disabled" wire:target="open">
        <x-slot name="title">
            Editar Vehiculo
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-label for="placa" value="Placa" />
                    <x-input id="placa" type="text" class="mt-1 block w-full" wire:model.live="placa" placeholder="Ej: ABC-123" />
                    @error('placa')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="marca" value="Marca" />
                    <x-input id="marca" type="text" class="mt-1 block w-full" wire:model.live="marca" placeholder="Ej: Toyota, Hyundai" />
                    @error('marca')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="modelo" value="Modelo" />
                    <x-input id="modelo" type="text" class="mt-1 block w-full" wire:model.live="modelo" placeholder="Ej: Corolla, Accent" />
                    @error('modelo')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="anio" value="Año" />
                    <x-input id="anio" type="number" min="1900" max="2099" class="mt-1 block w-full" wire:model.live="anio" placeholder="Ej: 2024" />
                    @error('anio')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="combustible" value="Combustible" />
                    <x-input id="combustible" type="text" class="mt-1 block w-full"
                        wire:model.live="combustible" placeholder="Ej: Gasolina, Gas, Híbrido" />
                    @error('combustible')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="serie" value="Serie" />
                    <x-input id="serie" type="text" class="mt-1 block w-full" wire:model.live="serie" placeholder="Ej: N° de serie del motor" />
                    @error('serie')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="color" value="Color" />
                    <x-input id="color" type="text" class="mt-1 block w-full" wire:model.live="color" placeholder="Ej: Blanco, Negro, Plata" />
                    @error('color')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open', false)" class="mx-2">
                Cerrar
            </x-secondary-button>
            <x-button wire:click="updateVehiculo" wire:loading.attr="disabled" wire:target="updateVehiculo">
                Actualizar
            </x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- ===================== MODAL CREAR VEHÍCULO ===================== --}}
    <x-dialog-modal wire:model="openCreate" wire:loading.attr="disabled" wire:target="openCreate">
        <x-slot name="title">
            Agregar Nuevo Vehículo
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-label for="cliente_id" value="Cliente" />
                    <select id="cliente_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.live="cliente_id">
                        <option value="">Seleccione un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellido }} - {{ $cliente->documento }}</option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="createPlaca" value="Placa" />
                    <x-input id="createPlaca" type="text" class="mt-1 block w-full" wire:model.live="createPlaca" placeholder="Ej: ABC-123" />
                    @error('createPlaca')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="createMarca" value="Marca" />
                    <x-input id="createMarca" type="text" class="mt-1 block w-full" wire:model.live="createMarca" placeholder="Ej: Toyota, Hyundai" />
                    @error('createMarca')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="createModelo" value="Modelo" />
                    <x-input id="createModelo" type="text" class="mt-1 block w-full" wire:model.live="createModelo" placeholder="Ej: Corolla, Accent" />
                    @error('createModelo')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="createAnio" value="Año" />
                    <x-input id="createAnio" type="number" min="1900" max="2099" class="mt-1 block w-full" wire:model.live="createAnio" placeholder="Ej: 2024" />
                    @error('createAnio')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="createCombustible" value="Combustible" />
                    <x-input id="createCombustible" type="text" class="mt-1 block w-full" wire:model.live="createCombustible" placeholder="Ej: Gasolina, Gas, Híbrido" />
                    @error('createCombustible')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="createSerie" value="Serie" />
                    <x-input id="createSerie" type="text" class="mt-1 block w-full" wire:model.live="createSerie" placeholder="Ej: N° de serie del motor" />
                    @error('createSerie')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <x-label for="createColor" value="Color" />
                    <x-input id="createColor" type="text" class="mt-1 block w-full" wire:model.live="createColor" placeholder="Ej: Blanco, Negro, Plata" />
                    @error('createColor')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('openCreate', false)" class="mx-2">
                Cerrar
            </x-secondary-button>
            <x-button wire:click="storeVehiculo" wire:loading.attr="disabled" wire:target="storeVehiculo">
                Guardar
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
