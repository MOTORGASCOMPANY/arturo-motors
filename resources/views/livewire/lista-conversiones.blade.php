<!-- resources>views>livewire>lista-conversiones.blade.php -->
<div class="flex box-border">
    <div class="container mx-auto py-4">
        <x-custom-table>
            <x-slot name="titulo">
                <h2 class="text-gray-600 font-semibold text-2xl">Conversiones</h2>
                <span class="text-xs text-gray-500">Todos los registros de conversión</span>
            </x-slot>
            <x-slot name="btnAgregar">
                <button wire:click=""
                    class="bg-slate-600 px-6 py-4 rounded-md text-white font-semibold tracking-wide cursor-pointer">Agregar</button>
            </x-slot>
            <x-slot name="contenido">
                <!-- Contenido de la tabla aquí -->
                @if (count($conversiones))
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehículo</th>
                                    <!--<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asesor</th>-->
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tecnico</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Fin</th>                                    
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documentos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creación</th>
                                    <th class="px-8 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($conversiones as $conve)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 text-sm text-gray-500">{{ $conve->id ?? null }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $conve->expediente->cliente->nombre . ' ' . $conve->expediente->cliente->apellido }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $conve->expediente->vehiculo->placa ?? null }}
                                        </td>
                                        <!--td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $conve->expediente->cita->asesor->name ?? null }}
                                        </td-->
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $conve->expediente->tecnico->name ?? 'No Asignado' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @php
                                                $colors = [
                                                    'en_proceso' => 'bg-yellow-100 text-yellow-800',
                                                    'completado' => 'bg-blue-200 text-blue-700',
                                                    'certificado' => 'bg-green-200 text-green-700',
                                                ];
                                                $labels = [
                                                    'en_proceso' => 'En Proceso',
                                                    'completado' => 'Completado',
                                                    'certificado' => 'Certficado',
                                                ];
                                            @endphp
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colors[$conve->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $labels[$conve->estado] ?? 'Desconocido' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ $conve->fecha_inicio }}
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ $conve->fecha_fin }}
                                        </td>                                        
                                        <td class="px-4 py-4 text-sm">
                                            <div class="relative text-center" x-data="{ menu: false }">
                                                <!-- Boton -->
                                                <button type="button" x-on:click="menu = ! menu" id="menu-button"
                                                    aria-expanded="true" aria-haspopup="true" data-te-ripple-init
                                                    data-te-ripple-color="light"
                                                    class="hover:animate-pulse inline-block rounded-full bg-teal-500 p-2 uppercase leading-normal text-white shadow-md transition duration-150 ease-in-out hover:bg-teal-700 hover:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:bg-teal-600 focus:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)] focus:outline-none focus:ring-0 active:bg-teal-700 active:shadow-[0_8px_9px_-4px_rgba(59,113,202,0.3),0_4px_18px_0_rgba(59,113,202,0.2)]">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        fill="currentColor" class="h-4 w-4">
                                                        <path fill-rule="evenodd" d="M19.5 21a3 3 0 003-3V9a3 3 0 00-3-3h-5.379a.75.75 0 01-.53-.22L11.47 3.66A2.25 2.25 0 009.879 3H4.5a3 3 0 00-3 3v12a3 3 0 003 3h15zm-6.75-10.5a.75.75 0 00-1.5 0v4.19l-1.72-1.72a.75.75 0 00-1.06 1.06l3 3a.75.75 0 001.06 0l3-3a.75.75 0 10-1.06-1.06l-1.72 1.72V10.5z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                                <!-- Opciones de Boton -->
                                                <div x-show="menu" x-on:click.away="menu = false"
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95"
                                                    class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-gray-300 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-40"
                                                    role="menu" aria-orientation="vertical" aria-labelledby="menu-button"
                                                    tabindex="-1">
                                                    <div class="" role="none">
                                                        <!-- Carta garantia muestra datos de vehiculo -->
                                                        <a href="{{ route('vehiculo.pdf', $conve->expediente->vehiculo->id) }}" target="__blank"
                                                            rel="noopener noreferrer"
                                                            class="flex px-4 py-2 text-sm text-blue-600 hover:bg-slate-600 hover:text-white justify-between items-center hover:cursor-pointer">
                                                            <i class="fa-solid fa-clipboard-list"></i>
                                                            <span>Carta de Garantia</span>
                                                        </a>
                                                        <!-- Carta garantia muestra datos de vehiculo -->
                                                        <a href="{{ route('manual.pdf', $conve->expediente->vehiculo->id) }}" target="__blank"
                                                            rel="noopener noreferrer"
                                                            class="flex px-4 py-2 text-sm text-blue-600 hover:bg-slate-600 hover:text-white justify-between items-center rounded-t-md hover:cursor-pointer">
                                                            <i class="fa-solid fa-clipboard-list"></i>
                                                            <span>Manual y Mantenimiento</span>
                                                        </a>
                                                        <!-- Si pasamos id de Conversion ya que conversion esta relacionado con repuestos -->
                                                        <a href="{{ route('ordenRepuestos.pdf', ['id' => $conve->id]) }}" target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="flex px-4 py-2 text-sm text-blue-600 hover:bg-slate-600 hover:text-white justify-between items-center rounded-t-md hover:cursor-pointer">
                                                            <i class="fa-solid fa-clipboard-list"></i>
                                                            <span>Ordn Repuestos y Acces</span>
                                                        </a>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $conve->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-8 py-4">
                                            <div class="flex justify-center items-center space-x-2">
                                                <!-- Botones de acción aquí -->
                                                <div class="relative group">
                                                    <a wire:click="editConversion({{ $conve->id }})"
                                                        class="py-1 px-2 text-center rounded-md bg-amber-400 font-bold text-black cursor-pointer hover:bg-amber-500">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <span
                                                        class="absolute bottom-full  mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-2 whitespace-nowrap z-10">
                                                        Editar
                                                    </span>
                                                </div>
                                                <!-- Repuestos -->
                                                @hasanyrole('Administrador del sistema|Tecnico')
                                                    <div class="relative group">
                                                        <a wire:click="redirectToSolicitudRepuestos({{ $conve->id }})"
                                                            class="py-1 px-2 text-center rounded-md bg-green-200 font-bold text-black cursor-pointer hover:bg-green-300">
                                                            <i class="fa-solid fa-cart-plus"></i>
                                                        </a>
                                                        <span
                                                            class="absolute bottom-full  mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded py-1 px-1 whitespace-nowrap z-10">
                                                            Repuestos
                                                        </span>
                                                    </div>
                                                @endhasanyrole
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if ($conversiones->hasPages())
                        <div class="mt-2 bg-white px-5 py-5 border-t rounded-lg">
                            {{ $conversiones->links() }}
                        </div>
                    @endif
                @else
                    <div class="px-6 py-4 text-center font-bold bg-teal-100 rounded-md">
                        No se encontró ningún registro.
                    </div>
                @endif
            </x-slot>
        </x-custom-table>
    </div>

    <!-- Dialog Modal para actualizar -->
    <x-dialog-modal wire:model="open" wire:loading.attr="disabled" wire:target="">
        <x-slot name="title">
            Editar Conversión
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <!-- Campo Técnico -->
                <div>
                    <x-label for="tecnico_id" value="Técnico Asignado" />
                    <select id="tecnico_id" wire:model="tecnico_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="">-- Seleccionar Técnico --</option>
                        @foreach ($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="tecnico_id" class="mt-2" />
                </div>

                <!-- Campo Fecha de Inicio -->
                <div>
                    <x-label for="fecha_inicio" value="Fecha de Inicio" />
                    <x-input id="fecha_inicio" type="date" class="mt-1 block w-full"
                        wire:model="fecha_inicio" />
                    <x-input-error for="fecha_inicio" class="mt-2" />
                </div>

                <!-- Campo Fecha de Fin -->
                <div>
                    <x-label for="fecha_fin" value="Fecha de Fin" />
                    <x-input id="fecha_fin" type="date" class="mt-1 block w-full"
                        wire:model="fecha_fin" />
                    <x-input-error for="fecha_fin" class="mt-2" />
                </div>

                <!-- Campo Estado -->
                <div>
                    <x-label for="estado" value="Estado" />
                    <select id="estado" wire:model="estado"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="en_proceso">En Proceso</option>
                        <option value="completado">Completado</option>
                        <option value="certificado">Certificado</option>
                    </select>
                    <x-input-error for="estado" class="mt-2" />
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open', false)" class="mx-2">
                Cerrar
            </x-secondary-button>

            <x-button wire:click="updateConversion" wire:loading.attr="disabled" wire:target="updateConversion">
                Actualizar
            </x-button>
        </x-slot>
    </x-dialog-modal>
</div>
