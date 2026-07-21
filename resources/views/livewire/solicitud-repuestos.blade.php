<!-- resources>views>livewire>solicitud-repuestos.blade.php -->
<div class="rounded-xl m-4 bg-white p-8 mx-auto max-w-max shadow-lg">
    <h2 class="text-gray-600 font-semibold">Solicitud para Conversión # {{ $conversionId }}</h2>
    <div class="flex flex-col md:flex-row items-baseline gap-4 mb-2">
        <p class="text-sm text-gray-500">
            Cliente: {{ $conversion->expediente->cliente->nombre ?? 'N/A' }}
            {{ $conversion->expediente->cliente->apellido ?? '' }}
        </p>
        <p class="text-sm text-gray-500">
            Vehículo: {{ $conversion->expediente->vehiculo->placa ?? '' }}
        </p>
    </div>

    <div class="flex flex-col md:flex-row items-end gap-4 mb-6">
        <!-- Repuesto -->
        <div class="w-full">
            <x-label for="repuesto_id" value="Repuesto:" />
            <select id="repuesto_id" class="bg-white border-gray-300 rounded-md outline-none ml-1 block w-full"
                wire:model="repuesto_id">
                <option value="">Seleccione un repuesto</option>
                @foreach ($repuestosDisponibles as $repuesto)
                    <option value="{{ $repuesto['id'] }}">{{ $repuesto['nombre'] }}</option>
                @endforeach
            </select>
        </div>
        <!-- Cantidad -->
        <div class="w-full">
            <x-label for="cantidad" value="Cantidad:" />
            <x-input id="cantidad" class="mt-1 block w-full" type="number" wire:model="cantidad" />
        </div>
        {{-- Botón para agregar --}}
        <div class="md:shrink-0">
            <button wire:click="addRepuesto"
                class="bg-amber-400 px-5 py-3 rounded-md text-white font-semibold tracking-wide hover:bg-amber-600">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>

    @if (count($repuestos) > 0)
        <h2 class="text-gray-600 font-semibold">Repuestos en la solicitud:</h2>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:mx-0.5">
                <div class="py-2 inline-block min-w-full ">
                    <div class="overflow-hidden rounded-lg">
                        <table class="min-w-full">
                            <thead class="bg-slate-600 border-b">
                                <tr>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-4 py-2 text-left">
                                        #
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-4 py-2 text-left">
                                        Repuesto
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-4 py-2 text-left">
                                        Cantidad
                                    </th>
                                    <th scope="col"
                                        class="text-sm font-medium font-semibold text-white px-4 py-2 text-left">
                                        Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($repuestos as $index => $item)
                                    <tr class="bg-gray-100 border-b">
                                        <td class="text-sm text-gray-900 font-light px-4 py-2 whitespace-nowrap">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="text-sm text-gray-900 font-light px-4 py-2 whitespace-nowrap">
                                            {{ $item['nombre'] }}
                                        </td>
                                        <td class="text-sm text-gray-900 font-light px-4 py-2 whitespace-nowrap">
                                            {{ $item['cantidad'] }}
                                        </td>
                                        <td class="text-sm text-gray-900 font-light px-4 py-2 whitespace-nowrap">
                                            <a wire:click="removeRepuesto({{ $index }})"
                                                class="hover: cursor-pointer p-4">
                                                <i class="fa-solid fa-trash hover:text-red-500"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    @else
        <p class="text-gray-500 text-sm italic text-center">No hay repuestos en la lista.</p>
    @endif

    {{-- Botón para guardar la solicitud --}}
    <div class="mt-6 flex items-center justify-center gap-2">
        <button class="p-3 bg-teal-600 rounded-xl text-white text-sm hover:font-bold hover:bg-teal-700"
            wire:click="saveSolicitud" wire:loading.attr="disabled" wire:target="saveSolicitud">
            <i class="fa-solid fa-floppy-disk"></i> Guardar
        </button>
        @if ($showButtons) 
            <button class="p-3 bg-red-600 rounded-xl text-white text-sm hover:font-bold hover:bg-red-700"
                wire:click="redirectToRegresar">
                <i class="fa-solid fa-rotate-left"></i> Regresar
            </button>
            <button class="p-3 bg-yellow-500 rounded-xl text-white text-sm hover:font-bold hover:bg-yellow-600"
                wire:click="openPdf">
                <i class="fa-solid fa-clipboard-list"></i> Orden Repuests
            </button>
        @endif
    </div>
</div>

{{-- Script para escuchar el evento de la alerta y abrir el PDF --}}
@script
    <script>
        Livewire.on('open-pdf', (params) => {
            window.open(params.url, '_blank');
        });
    </script>
@endscript
{{-- Script para escuchar el evento de la alerta y redirigir 
@script
    <script>
        Livewire.on('solicitudGuardada', (params) => {
            Swal.fire({
                title: params.titulo,
                text: params.mensaje,
                icon: params.icono,
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('ListaConversiones') }}";
                }
            });
        });
    </script>
@endscript --}}
