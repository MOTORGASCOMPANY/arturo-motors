<!-- resources/views/livewire/expediente-modal.blade.php -->
<div>
    <x-dialog-modal wire:model="open" maxWidth="4xl">
        <x-slot name="title">
            <h2 class="text-lg font-bold text-center">FICHA DE RECEPCIÓN DEL VEHÍCULO</h2>
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-2 text-sm mb-4">
                <label class="flex items-center space-x-1">
                    <input type="checkbox"
                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600"
                        wire:model="instalacion">
                    <span>Instalación</span>
                </label>
                <label class="flex items-center space-x-1">
                    <input type="checkbox"
                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600"
                        wire:model="cambio_tanque">
                    <span>Cambio de tanque</span>
                </label>
                <label class="flex items-center space-x-1">
                    <input type="checkbox"
                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600"
                        wire:model="revision">
                    <span>Revisión quinquenal</span>
                </label>
                <label class="flex items-center space-x-1">
                    <input type="checkbox"
                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600"
                        wire:model="certificacion">
                    <span>Certificación</span>
                </label>
                <label class="flex items-center space-x-1">
                    <input type="checkbox"
                        class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600"
                        wire:model="servicio">
                    <span>Servicio</span>
                </label>
            </div>
            <div class="border-t border-gray-300 pt-4"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm mb-2">
                <div class="flex items-center">
                    <span class="w-36">Cliente / Propietario:</span>
                    <x-input wire:model="cliente" class="flex-1" />
                </div>
                <div class="flex items-center">
                    <span class="w-36">DNI o RUC:</span>
                    <x-input wire:model="dni" class="flex-1" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[36%_36%_22%] gap-6">
                <div class="space-y-2">
                    <div class="flex items-center">
                        <span class="w-24">Teléf. Fijo:</span>
                        <x-input wire:model="telefono_fijo" class="flex-1" />
                    </div>
                    <div class="flex items-center">
                        <span class="w-24">Placa Actual:</span>
                        <x-input wire:model="placa_actual" class="flex-1" />
                    </div>
                    <div class="flex items-center">
                        <span class="w-24">Marca:</span>
                        <x-input wire:model="marca" class="flex-1" />
                    </div>
                    <div class="flex items-center">
                        <span class="w-24">Modelo:</span>
                        <x-input wire:model="modelo" class="flex-1" />
                    </div>
                    <div class="flex items-center">
                        <span class="w-24">Año:</span>
                        <x-input wire:model="anio" class="flex-1" />
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <span class="w-24">Teléf. Móvil:</span>
                        <x-input wire:model="telefono_movil" class="flex-1" />
                    </div>
                    <div class="flex items-center">
                        <span class="w-24">Placa Anterior:</span>
                        <x-input wire:model="placa_anterior" class="flex-1" />
                    </div>
                    <div class="flex items-center">
                        <span class="w-24">N° Motor:</span>
                        <x-input wire:model="motor" class="flex-1" />
                    </div>
                    <div class="flex items-center">
                        <span class="w-24">Color:</span>
                        <x-input wire:model="color" class="flex-1" />
                    </div>
                    <div class="flex items-center">
                        <span class="w-24">Combustible:</span>
                        <x-input wire:model="combustible" class="flex-1" />
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="border border-gray-300 rounded p-3 text-sm">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="inyectado"
                                class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                            <span>Inyectado</span>
                        </label>
                        <label class="mt-2 flex items-center space-x-2">
                            <input type="checkbox" wire:model="carburado"
                                class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                            <span>Carburado</span>
                        </label>
                        <label class="mt-2 flex items-center space-x-2">
                            <input type="checkbox" wire:model="monopunto"
                                class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                            <span>Monopunto</span>
                        </label>
                        <div class="">
                            <span>Motor de:</span>
                            <x-input wire:model="motor_tipo" class="w-24" />
                        </div>
                        <div class="mt-2 flex items-center space-x-2">
                            <span>3CIL</span> <input type="checkbox" wire:model="cil3"
                                class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                            <input type="checkbox" wire:model=""
                                class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600"><span>.........CIL</span>
                            <input type="checkbox" wire:model=""
                                class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                        </div>
                    </div>
                    <div class="">
                        <span class="w-24">Kilometraje:</span>
                        <x-input wire:model="kilometraje" class="flex-1" />
                    </div>
                </div>
            </div>

            <!-- Tabla con 3 columnas -->
            <div class="flex flex-col md:flex-row mt-4 border rounded-lg">
                <!-- 1° Columna -->
                <div class="flex-1 border-r border-gray-300 flex flex-col justify-between text-center">
                    <div class="p-4">
                        <h2 class="text-lg font-bold mb-4">ORDEN DE TRABAJO</h2>
                        <p>Nº ..........................................</p>
                    </div>
                    <div class="mt-4 border-t border-gray-300"></div>
                    <div class="p-4">
                        <p class="text-sm mt-8">Con la presente yo y/o en representación autorizo el trabajo a
                            realizarse en mi vehículo</p>
                    </div>
                    <div class="mt-auto p-4">
                        <div class="mt-8 border-b border-gray-500 pb-2"></div>
                        <p class="text-xs mt-2">FIRMA AUTORIZADA<br>(CLIENTE)</p>
                        <div class="mt-28 border-b border-gray-500 pb-2"></div>
                        <p class="text-xs mt-2">RECIBÍ CONFORME<br>(CLIENTE)</p>
                    </div>
                </div>
                <!-- 2° Columna -->
                <div class="flex-1 p-4 border-r border-gray-300 flex flex-col items-center justify-between">
                    <div class="mt-16">
                        <img src="{{ asset('images/carro.png') }}" alt="Medidor de gasolina"
                            class="">
                    </div>
                </div>
                <!-- 3° Columna -->
                <div class="flex-1">
                    <div class="grid grid-cols-[3fr_1fr_1fr] text-xs border-b border-gray-300">
                        <div class="text-left px-4"></div>
                        <div class="text-center border-l border-r border-gray-300">SI</div>
                        <div class="text-center">NO</div>
                    </div>

                    @php
                        $accesorios = [
                            'Tarjeta de propiedad',
                            'SOAT',
                            'Llave de contacto',
                            'Espejos',
                            'Antena',
                            'Plumillas',
                            'Vasos',
                            'Emblemas',
                            'Tapa Combustible',
                            'Batería',
                            'Seguro de Batería',
                            'Claxon',
                            'Tapa Aceite',
                            'Tapa Radiador',
                            'Barita de Capot',
                            'Espejo Anterior',
                            'Tapasoles',
                            'Radio',
                            'Reproductor de CD',
                            'Parlantes',
                            'Cenicero',
                            'Encendedor',
                            'Pisos',
                            'Fundas o Forros',
                            'Cinturones',
                            'Llanta de Repuesto',
                            'Gata y Palanca',
                            'Llave de Ruedas',
                            'Triángulo',
                            'Extintor',
                            'Linterna',
                            'Herramientas',
                            'Botiquín',
                        ];
                    @endphp

                    @foreach ($accesorios as $accesorio)
                        <div class="grid grid-cols-[3fr_1fr_1fr] text-xs border-b border-gray-200 last:border-b-0">
                            <div class="px-4">{{ $accesorio }}</div>
                            <div class="text-center border-l border-r border-gray-300">
                                <input type="checkbox"
                                    class="w-3 h-3 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                            </div>
                            <div class="text-center">
                                <input type="checkbox"
                                    class="w-3 h-3 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open', false)">Cancelar</x-secondary-button>
            <x-button class="ml-2" wire:click="guardar">Guardar</x-button>
        </x-slot>
    </x-dialog-modal>
</div>
