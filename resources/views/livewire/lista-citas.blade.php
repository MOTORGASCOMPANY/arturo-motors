<!-- resources>views>livewire>lista-citas.blade.php -->
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creación
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones
                                    </th>
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
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $cita->fecha_cita->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @php
                                                $colors = [
                                                    'aceptada' => 'bg-green-100 text-green-800',
                                                    'rechazada' => 'bg-red-100 text-red-800',
                                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                                ];
                                            @endphp
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$cita->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $cita->estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $cita->motivo }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $cita->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="text-center">
                                            <div class="flex justify-center items-center space-x-2">
                                                <button onclick="confirmarAceptacion({{ $cita->id }})"
                                                    type="button"
                                                    class="group flex py-2 px-2 text-center items-center rounded-md bg-green-700 font-bold text-white cursor-pointer hover:bg-green-800 hover:animate-pulse">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    <span
                                                        class="group-hover:opacity-100 transition-opacity bg-gray-800 px-1 text-sm text-gray-100 rounded-md absolute  translate-y-full opacity-0 m-4 mx-auto z-50">
                                                        Aceptar
                                                    </span>
                                                </button>
                                                <button onclick="confirmarRechazo({{ $cita->id }})" type="button"
                                                    class="group flex py-2 px-2 text-center items-center rounded-md bg-red-500 font-bold text-white cursor-pointer hover:bg-red-700 hover:animate-pulse">
                                                    <i class="fa-solid fa-ban"></i>
                                                    <span
                                                        class="group-hover:opacity-100 transition-opacity bg-gray-800 px-1 text-sm text-gray-100 rounded-md absolute  translate-y-full opacity-0 m-4 mx-auto z-50">
                                                        Rechazar
                                                    </span>
                                                </button>
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
            <h1 class="text-xl font-bold">Crear Nueva Cita</h1>
        </x-slot>

        <x-slot name="content">
            <!-- Cliente -->
            <div class="bg-gray-50 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-blue-800 border-b pb-1 mb-3">👤 Datos del Cliente</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input label="Nombres" type="text" class="w-full" placeholder="Nombre completo"
                            wire:model="nombre" />
                        <x-input-error for="nombre" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Apellidos" type="text" class="w-full" placeholder="Apellido completo"
                            wire:model="apellido" />
                        <x-input-error for="apellido" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Documento" type="text" class="w-full" placeholder="DNI o documento"
                            wire:model="documento" />
                        <x-input-error for="documento" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Teléfono" type="tel" class="w-full" placeholder="Número de contacto"
                            wire:model="telefono" />
                        <x-input-error for="telefono" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Correo" type="email" class="w-full" placeholder="correo@ejemplo.com"
                            wire:model="email" />
                        <x-input-error for="email" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Dirección" type="text" class="w-full" placeholder="Dirección completa"
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
                        <x-input label="Marca" placeholder="Ej. Toyota" class="w-full" type="text"
                            wire:model="marca" />
                        <x-input-error for="marca" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Modelo" placeholder="Ej. Corolla" class="w-full" type="text"
                            wire:model="modelo" />
                        <x-input-error for="modelo" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Año" placeholder="Ej. 2022" class="w-full" type="number"
                            wire:model="anio" />
                        <x-input-error for="anio" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Placa" placeholder="ABC123" class="w-full" type="text"
                            wire:model="placa" />
                        <x-input-error for="placa" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Serie" placeholder="N° Motor" class="w-full" type="text"
                            wire:model="serie" />
                        <x-input-error for="serie" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Color" placeholder="Ej. Rojo" class="w-full" type="text"
                            wire:model="color" />
                        <x-input-error for="color" class="mt-1" />
                    </div>
                </div>
                <div class="mt-3">
                    <x-input label="Combustible" placeholder="Combustible" type="text" class="w-full"
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
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-input label="Fecha de Cita" type="datetime-local" class="w-full"
                            wire:model="fecha_cita" />
                        <x-input-error for="fecha_cita" class="mt-1" />
                    </div>
                    <div>
                        <x-input label="Motivo" placeholder="Motivo de la cita" class="w-full" type="text"
                            wire:model="motivo" />
                        <x-input-error for="motivo" class="mt-1" />
                    </div>
                </div>
                <!-- Toggle: Asesor externo -->
                <div class="mt-4 mb-2">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="is_externo"
                            class="w-4 h-4 text-slate-600 bg-slate-100 border-gray-300 rounded outline-none  focus:ring-slate-600">
                        <span class="ml-3 text-gray-700">La cita la registra un <strong>vendedor/asesor externo
                                ?</strong></span>
                    </label>
                </div>
                <!-- Si ES externo: mostramos select con asesores externos -->
                @if ($is_externo)
                    <div>
                        <select wire:model="asesor_externo_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Seleccione asesor externo...</option>
                            @foreach ($asesores as $asesor)
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

    {{-- JS --}}
    @push('js')
        {{--
        <script>
            Livewire.on('marcarRechazada', ({
                id
            }) => {
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

                        //Livewire.emitTo('lista-citas', 'marcarRechazada', id);
                        // Si el usuario confirma, emite el evento de Livewire
                        // En Livewire 3, se usa `Livewire.dispatch`
                        Livewire.dispatch('marcarRechazada', {
                            id: id
                        });

                        Swal.fire(
                            'Listo!',
                            'Cita rechazada correctamente.',
                            'success'
                        )
                    }
                })
            });
        </script>
        --}}
        <script>
            // Esta es la función que será llamada desde el botón
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
                        // Si el usuario confirma, ahora sí, emite el evento al componente Livewire
                        Livewire.dispatch('marcarCitaComoRechazada', {
                            id: id
                        });
                    }
                });
            }
            // Este listener se encarga de recibir la confirmación del backend para mostrar el mensaje final.
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('citaRechazada', () => {
                    Swal.fire(
                        '¡Cita Cancelada!',
                        'La cita ha sido rechazada correctamente.',
                        'success'
                    );
                });
            });
        </script>
        <script>
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
                        Livewire.dispatch('marcarCitaComoAceptada', {
                            id: id
                        });
                    }
                });
            }
            // Este listener se encarga de recibir la confirmación del backend para mostrar el mensaje final.
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('citaAceptada', () => {
                    Swal.fire(
                        '¡Cita aceptada!',
                        'La cita ha sido aceptada y el expediente se creó correctamente.',
                        'success'
                    );
                });
            });
        </script>
    @endpush

</div>
