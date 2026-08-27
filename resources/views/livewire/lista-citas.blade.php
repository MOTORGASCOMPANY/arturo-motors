<div class="flex box-border">
    <div class="container mx-auto py-4">
        <x-custom-table>
            <x-slot name="titulo">
                <h2 class="text-gray-600 font-semibold text-2xl">Citas</h2>
                <span class="text-xs text-gray-500">Todos las citas programadas</span>
            </x-slot>

            <x-slot name="btnAgregar">
                <x-button wire:click="$toggle('open')"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Agregar
                </x-button>                
            </x-slot>

            <x-slot name="contenido">
                @if (count($citas))
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sede</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creación</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($citas as $cita)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $cita->id }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $cita->cliente->nombre . ' ' . $cita->cliente->apellido }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $cita->vehiculo->placa }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 font-semibold">
                                            {{ $cita->sede->nombre ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $cita->fecha_cita ? \Carbon\Carbon::parse($cita->fecha_cita)->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @php
                                                $colors = [
                                                    'aceptada' => 'bg-green-100 text-green-800',
                                                    'rechazada' => 'bg-red-100 text-red-800',
                                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                ];
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$cita->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($cita->estado) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $cita->motivo ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $cita->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center items-center space-x-2">
                                                @if($cita->estado === 'pendiente')
                                                    <button {{--onclick="confirmarAceptacion({{ $cita->id }})"--}} wire:click="abrirModalAceptar({{ $cita->id }})" type="button"
                                                        class="group flex py-2 px-2 text-center items-center rounded-md bg-green-700 font-bold text-white cursor-pointer hover:bg-green-800 hover:animate-pulse">
                                                        <i class="fa-solid fa-circle-check"></i>
                                                        <span class="group-hover:opacity-100 transition-opacity bg-gray-800 px-1 text-sm text-gray-100 rounded-md absolute  translate-y-full opacity-0 m-4 mx-auto z-50">
                                                            Aceptar
                                                        </span>
                                                    </button>
                                                    <button onclick="confirmarRechazo({{ $cita->id }})" type="button"
                                                        class="group flex py-2 px-2 text-center items-center rounded-md bg-red-500 font-bold text-white cursor-pointer hover:bg-red-700 hover:animate-pulse">
                                                        <i class="fa-solid fa-ban"></i>
                                                        <span class="group-hover:opacity-100 transition-opacity bg-gray-800 px-1 text-sm text-gray-100 rounded-md absolute  translate-y-full opacity-0 m-4 mx-auto z-50">
                                                            Rechazar
                                                        </span>
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">Sin acciones</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if ($citas->hasPages())
                        <div class="mt-2 bg-white px-5 py-5 border-t rounded-lg">
                            {{ $citas->links() }}
                        </div>
                    @endif
                @else
                    <div class="px-6 py-4 text-center font-bold bg-blue-100 rounded-md">
                        No se encontró ningún registro.
                    </div>
                @endif
            </x-slot>

        </x-custom-table>
    </div>

    <!-- Dialog modal para crear cita con cliente y vehiculo -->
    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            <h1 class="text-xl font-bold">Programar Nueva Cita</h1>
        </x-slot>
        <x-slot name="content">
            <!-- Cliente -->
            <div class="bg-gray-50 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-blue-800 border-b pb-1 mb-3">👤 Datos del Cliente</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-label value="Nombres" />
                        <x-input type="text" class="w-full mt-1" placeholder="Nombre completo"
                            wire:model="nombre" />
                        <x-input-error for="nombre" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Apellidos" />
                        <x-input type="text" class="w-full mt-1" placeholder="Apellido completo"
                            wire:model="apellido" />
                        <x-input-error for="apellido" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Documento" />
                        <x-input type="text" class="w-full mt-1" placeholder="DNI o documento"
                            wire:model="documento" maxlength="8" />
                        <x-input-error for="documento" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Teléfono" />
                        <x-input type="tel" class="w-full mt-1" placeholder="Número de contacto"
                            wire:model="telefono" maxlength="9"/>
                        <x-input-error for="telefono" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Correo" />
                        <x-input type="email" class="w-full mt-1" placeholder="correo@ejemplo.com"
                            wire:model="email" />
                        <x-input-error for="email" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Dirección" />
                        <x-input type="text" class="w-full mt-1" placeholder="Dirección completa"
                            wire:model="direccion" />
                        <x-input-error for="direccion" class="mt-1" />
                    </div>
                </div>
            </div>
            <!-- Vehículo -->
            <div class="bg-gray-50 p-4 rounded-lg shadow mt-4">
                <h3 class="text-lg font-semibold text-green-800 border-b pb-1 mb-3">🚗 Datos del Vehículo</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-label value="Marca" />
                        <x-input placeholder="Ej. Toyota" class="w-full mt-1" type="text"
                            wire:model="marca" />
                        <x-input-error for="marca" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Modelo" />
                        <x-input placeholder="Ej. Corolla" class="w-full mt-1" type="text"
                            wire:model="modelo" />
                        <x-input-error for="modelo" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Año" />
                        <x-input placeholder="Ej. 2022" class="w-full mt-1" type="number"
                            wire:model="anio" />
                        <x-input-error for="anio" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Placa" />
                        <x-input placeholder="ABC123" class="w-full mt-1" type="text"
                            wire:model="placa" />
                        <x-input-error for="placa" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Serie" />
                        <x-input placeholder="N° Motor" class="w-full mt-1" type="text"
                            wire:model="serie" />
                        <x-input-error for="serie" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Color" />
                        <x-input placeholder="Ej. Rojo" class="w-full mt-1" type="text"
                            wire:model="color" />
                        <x-input-error for="color" class="mt-1" />
                    </div>
                </div>
                <div class="mt-3">
                    <x-label value="Combustible" />
                    <x-input placeholder="Combustible" type="text" class="w-full mt-1"
                        wire:model="combustible" list="items" />
                    <datalist id="items">
                        <option value="GASOLINA">GASOLINA</option>
                        <option value="BI-COMBUSTIBLE GNV">BI-COMBUSTIBLE GNV</option>
                        <option value="BI-COMBUSTIBLE GLP">BI-COMBUSTIBLE GLP</option>
                        <option value="GNV">GNV</option>
                        <option value="GLP">GLP</option>
                        <option value="DIESEL">DIESEL</option>
                    </datalist>
                    <x-input-error for="combustible" class="mt-1" />
                </div>
            </div>
            <!-- Cita -->
            <div class="bg-gray-50 p-4 rounded-lg shadow mt-4">
                <h3 class="text-lg font-semibold text-yellow-800 border-b pb-1 mb-3">📅 Datos de la Cita</h3>
                <!-- Fecha y motivo -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <x-label value="Sede de Atención" class="mb-1" />
                        <select wire:model="sede_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="">Seleccione una sede...</option>
                            @foreach ($sedes as $sede)
                                <option value="{{ $sede->id }}">
                                    {{ $sede->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="sede_id" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Fecha de Cita" />
                        <x-input type="datetime-local" class="w-full mt-1" wire:model="fecha_cita" />
                        <x-input-error for="fecha_cita" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Motivo" />
                        <x-input placeholder="Motivo de la cita" class="w-full mt-1" type="text" wire:model="motivo" />
                        <x-input-error for="motivo" class="mt-1" />
                    </div>
                </div>
                <!-- Toggle: Asesor externo -->
                <div class="mt-4 mb-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="is_externo"
                            class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none focus:ring-slate-600">
                        <span class="ml-3 text-gray-700">La cita la registra un <strong>vendedor/asesor externo?</strong></span>
                    </label>
                </div>
                <!-- Si ES externo: mostramos select con asesores externos -->
                @if ($is_externo)
                    <div>
                        <x-label value="Asesor Externo" class="mb-1" />
                        <select wire:model="asesor_externo_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <option value="">Seleccione asesor externo...</option>
                            @foreach ($asesoresExternos as $asesor)
                                <option value="{{ $asesor->id }}">
                                    {{ $asesor->nombre }}{{ $asesor->telefono ? ' • ' . $asesor->telefono : '' }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="asesor_externo_id" class="mt-1" />
                    </div>
                @endif
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open',false)" class="mx-2">
                Cancelar
            </x-secondary-button>
            <x-button wire:click="crearCita" wire:loading.attr="disabled" wire:target="crearCita">
                Guardar
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Modal 2: Dialog Modal para Aceptar Cita y Generar Orden -->
    <x-dialog-modal wire:model="openAceptarModal">
        <x-slot name="title">
            <h1 class="text-xl font-bold">Aceptar Cita & Generar Orden de Servicio</h1>
        </x-slot>

        <x-slot name="content">
            @if ($citaSeleccionada)
                <!-- Resumen de Cliente y Vehículo -->
                <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-900 grid grid-cols-2 gap-2">
                    <div>
                        <span class="font-bold">Cliente:</span> {{ $citaSeleccionada->cliente->nombre ?? '' }} {{ $citaSeleccionada->cliente->apellido ?? '' }}
                    </div>
                    <div>
                        <span class="font-bold">Vehículo / Placa:</span> {{ $citaSeleccionada->vehiculo->placa ?? '' }} ({{ $citaSeleccionada->vehiculo->marca ?? '' }} {{ $citaSeleccionada->vehiculo->modelo ?? '' }})
                    </div>
                </div>

                <!-- Selección de Tipo de Servicio -->
                <div>
                    <x-label value="Selecciona el Tipo de Servicio" class="mb-2 text-gray-700 font-medium" /> 
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-56 overflow-y-auto pr-1">
                        @foreach ($servicios as $s)
                            <div wire:click="seleccionarServicio({{ $s->id }})"
                                 class="border rounded-xl p-4 cursor-pointer transition-all flex items-center justify-between {{ $serviceId === $s->id ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-500/20 shadow-sm' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50/50' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 flex items-center justify-center {{ $serviceId === $s->id ? 'border-blue-600 bg-blue-600' : '' }}">
                                        @if($serviceId === $s->id)
                                            <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                                        @endif
                                    </div>
                                    <span class="font-medium text-gray-800 text-sm">{{ $s->nombre }}</span>
                                </div>
                                <span class="font-bold text-gray-900 text-sm">S/ {{ number_format($s->precio_base, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <x-input-error for="serviceId" class="mt-2" />
                </div>

                <!-- Ajuste de Precio -->
                @if ($serviceId)
                    <div class="mt-4 p-4 bg-gray-50 border border-gray-200/80 rounded-xl grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-label for="precioFinal" value="Precio Acordado (S/)" class="text-gray-700 font-medium" />
                            <x-input id="precioFinal" type="number" step="0.01" wire:model="precioFinal" class="w-full font-semibold text-gray-900 mt-1 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg" />
                            <span class="text-xs text-gray-500 mt-1 block">Precio base sugerido: S/ {{ number_format($precioLista, 2) }}</span>
                            <x-input-error for="precioFinal" class="mt-1" />
                        </div>
                        <div>
                            <x-label for="descuentoMotivo" value="Motivo del Ajuste / Descuento" class="text-gray-700 font-medium" />
                            <x-input id="descuentoMotivo" type="text" wire:model="descuentoMotivo" placeholder="Ej: Descuento por kit en oferta" class="w-full mt-1 border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg" />
                            <x-input-error for="descuentoMotivo" class="mt-1" />
                        </div>
                    </div>
                @endif
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('openAceptarModal', false)" class="mx-2">
                Cancelar
            </x-secondary-button>
            <x-button wire:click="procesarAceptacionCita" wire:loading.attr="disabled" class="bg-green-600 hover:bg-green-700">
                <span wire:loading.remove wire:target="procesarAceptacionCita">Aceptar y Crear Orden</span>
                <span wire:loading wire:target="procesarAceptacionCita">Procesando...</span>
            </x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- JS --}}
    @push('js')
        <script>
            function confirmarRechazo(id) {
                Swal.fire({
                    title: '¿Estás seguro de cancelar esta cita?',
                    text: '¡Esta acción no se puede revertir!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, cancelar cita',
                    cancelButtonText: 'No, mantener'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('marcarCitaComoRechazada', { id: id });
                    }
                });
            }

            function confirmarAceptacion(id) {
                Swal.fire({
                    title: '¿Aceptar esta cita?',
                    text: 'Se creará automáticamente un expediente asociado.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, aceptar',
                    cancelButtonText: 'No, cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('marcarCitaComoAceptada', { id: id });
                    }
                });
            }

            document.addEventListener('livewire:initialized', () => {
                Livewire.on('citaRechazada', () => {
                    Swal.fire(
                        '¡Cita Cancelada!',
                        'La cita ha sido rechazada correctamente.',
                        'success'
                    );
                });

                Livewire.on('citaAceptada', () => {
                    Swal.fire(
                        '¡Cita aceptada!',
                        'La cita se aceptó y se creó la orden de servicio exitosamente.',
                        'success'
                    );
                });
            });
        </script>
    @endpush

</div>
