<div wire:poll.10s class="max-w-[1600px] mx-auto px-3 sm:px-4 py-4 sm:py-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-gray-900">
                <i class="fas fa-desktop mr-2 text-blue-600"></i>Conversiones Activas
            </h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Monitoreo en tiempo real</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative flex-1 sm:flex-none">
                <input type="text" wire:model.live="busqueda" placeholder="Buscar..."
                       class="w-full sm:w-56 px-3 py-2 sm:py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-gray-900 outline-none">
                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
            <span class="flex items-center gap-1.5 text-xs text-gray-400 shrink-0">
                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                <span class="hidden xs:inline sm:inline">Live</span>
            </span>
        </div>
    </div>

    @if($this->conversiones->isEmpty())
        <div class="bg-white rounded-lg border border-gray-200 p-8 sm:p-10 text-center">
            <i class="fas fa-inbox text-gray-300 text-3xl mb-3"></i>
            <p class="text-gray-500 font-medium">No hay conversiones activas</p>
        </div>
    @else
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">

            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-gray-50 border-b border-gray-200 text-left">
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Orden</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Cliente</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Placa</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Kit</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">T&eacute;cnico</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Inicio</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase">Fin</th>
                            <th class="px-3 py-2.5 font-semibold text-gray-600 text-xs uppercase text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($this->conversiones as $orden)
                            @php $r = $this->resumenConversion($orden); @endphp
                            <tr wire:click="abrirModal({{ $orden->id }})"
                                class="transition cursor-pointer {{ $orden->fecha_inicio_conversion ? 'hover:bg-gray-50' : 'bg-gray-50 cursor-not-allowed opacity-60' }}">
                                <td class="px-3 py-2.5 font-bold text-gray-900">#{{ $orden->id }}</td>
                                <td class="px-3 py-2.5 text-gray-700 whitespace-nowrap">{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</td>
                                <td class="px-3 py-2.5"><span class="font-mono font-semibold text-gray-900">{{ $orden->vehiculo->placa }}</span></td>
                                <td class="px-3 py-2.5 text-gray-700 text-xs">{{ $this->nombreKit($orden) }}</td>
                                <td class="px-3 py-2.5 text-gray-700 text-xs whitespace-nowrap">{{ $orden->tecnico->name ?? '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-500 text-xs whitespace-nowrap">{{ $orden->fecha_inicio_conversion ? $orden->fecha_inicio_conversion->format('d/m H:i') : '—' }}</td>
                                <td class="px-3 py-2.5 text-gray-500 text-xs whitespace-nowrap">{{ $orden->fecha_fin_conversion ? $orden->fecha_fin_conversion->format('d/m H:i') : '—' }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    @if($orden->fecha_inicio_conversion)
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">En conversi&oacute;n</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Sin iniciar</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-gray-100">
                @foreach($this->conversiones as $orden)
                    @php $r = $this->resumenConversion($orden); @endphp
                    <div wire:click="abrirModal({{ $orden->id }})"
                         class="p-3.5 flex flex-col gap-1.5 transition {{ $orden->fecha_inicio_conversion ? 'active:bg-gray-50 cursor-pointer' : 'bg-gray-50 cursor-not-allowed opacity-60' }}">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-gray-900 text-sm">#{{ $orden->id }}</span>
                            @if($orden->fecha_inicio_conversion)
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[11px] font-semibold rounded-full shrink-0">En conversi&oacute;n</span>
                            @else
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[11px] font-semibold rounded-full shrink-0">Sin iniciar</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between gap-2 text-xs text-gray-700">
                            <span class="truncate">{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</span>
                            <span class="font-mono font-semibold text-gray-900 shrink-0">{{ $orden->vehiculo->placa }}</span>
                        </div>
                        <div class="text-xs text-gray-500 truncate">{{ $this->nombreKit($orden) }}</div>
                        <div class="flex items-center justify-between gap-2 text-[11px] text-gray-400 mt-0.5">
                            <span class="truncate"><i class="fas fa-user-cog mr-1"></i>{{ $orden->tecnico->name ?? '—' }}</span>
                            <span class="shrink-0 text-right">
                                {{ $orden->fecha_inicio_conversion ? $orden->fecha_inicio_conversion->format('d/m H:i') : '—' }}
                                <i class="fas fa-arrow-right mx-0.5 text-[9px]"></i>
                                {{ $orden->fecha_fin_conversion ? $orden->fecha_fin_conversion->format('d/m H:i') : '—' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($this->conversiones->hasPages())
                <div class="px-3 sm:px-4 py-3 border-t border-gray-200 bg-gray-50 overflow-x-auto">{{ $this->conversiones->links('pagination::tailwind') }}</div>
            @endif
        </div>
    @endif

    @if($modalAbierto && $this->conversionSeleccionada)
        @php $orden = $this->conversionSeleccionada; @endphp
        <x-dialog-modal wire:model="modalAbierto" maxWidth="2xl">
            <x-slot name="title">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 sm:w-11 sm:h-11 bg-white/15 rounded-xl flex items-center justify-center backdrop-blur-sm shrink-0">
                        <i class="fas fa-car text-white text-sm sm:text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                            <span class="font-bold text-white text-sm sm:text-base">Orden #{{ $orden->id }}</span>
                            <span class="px-2 py-0.5 bg-white/20 text-white text-xs font-mono font-semibold rounded">{{ $orden->vehiculo->placa }}</span>
                            @if($this->generacionKit)
                                <span class="px-2 py-0.5 bg-purple-500 text-white text-[10px] font-bold rounded">{{ $this->generacionKit }}</span>
                            @endif
                        </div>
                        <span class="text-xs sm:text-sm text-blue-100 block truncate">
                            {{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}
                            &middot; T&eacute;c: {{ $orden->tecnico->name ?? '—' }}
                        </span>
                    </div>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="flex-1 min-h-0 flex flex-col lg:flex-row overflow-hidden">
                    <x-almacen.conversion-modal-piezas />
                    <x-almacen.conversion-modal-historial />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$set('modalAbierto', false)" class="w-full sm:w-auto">
                    Cerrar
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    @livewire('almacen.conversion-partes-generales-modal')
</div>