<div class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h2 class="text-2xl font-extrabold text-gray-700 tracking-tight">Mis Boletas de Pago</h2>
            <p class="text-gray-600">Historial de pagos y comprobantes electrónicos.</p>
        </div>

        @forelse ($planillas as $year => $meses)
            <div x-data="{ openYear: {{ $loop->first ? 'true' : 'false' }} }" class="mb-6 shadow-sm">
                <button @click="openYear = !openYear"
                        class="flex items-center justify-between w-full p-2 bg-white border border-gray-200 rounded-t-xl hover:bg-gray-50 transition">
                    <div class="flex items-center">
                        <span class="w-2 h-8 bg-indigo-600 rounded mr-3"></span>
                        <span class="text-xl font-bold text-gray-800">{{ $year }}</span>
                    </div>
                    <i class="fas text-gray-400 transition-transform duration-300" :class="openYear ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>

                <div x-show="openYear" x-collapse class="bg-white border-x border-b border-gray-200 rounded-b-xl p-4 sm:p-6 space-y-8">
                    @foreach ($meses as $mes => $periodos)
                        <div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4 flex items-center">
                                <span class="bg-gray-100 px-2 py-1 rounded">{{ $mes }}</span>
                                <div class="flex-grow border-t border-gray-100 ml-3"></div>
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($periodos as $p)
                                    <div class="relative p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-md transition duration-200">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <p class="text-[10px] font-bold text-indigo-500 uppercase">
                                                    {{ $p->periodo->day <= 15 ? 'Primera Quincena' : 'Fin de Mes' }}
                                                </p>
                                                <p class="text-xs text-gray-500 font-medium">
                                                    Pago: {{ $p->fecha_pago ? $p->fecha_pago->format('d/m/Y') : 'Pendiente' }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-gray-900">S/ {{ number_format($p->total_calculado, 2) }}</p>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            @forelse ($p->archivos as $file)
                                                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-gray-100 group">
                                                    <div class="flex items-center space-x-3 overflow-hidden">
                                                        <div class="w-8 h-8 flex-shrink-0 bg-gray-50 rounded flex items-center justify-center border">
                                                            @if(in_array($file->extension, ['jpg', 'jpeg', 'png']))
                                                                <img src="{{ Storage::url($file->ruta) }}" class="w-full h-full object-cover rounded">
                                                            @else
                                                                <i class="fas fa-file-pdf text-red-500 text-sm"></i>
                                                            @endif
                                                        </div>
                                                        <span class="text-xs font-bold text-gray-600 truncate uppercase">
                                                            {{ $file->tipo }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center space-x-1">
                                                        <a href="{{ Storage::url($file->ruta) }}" target="_blank"
                                                           class="p-2 text-gray-400 hover:text-blue-600 transition" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button wire:click="descargar('{{ $file->id }}')"
                                                                class="p-2 text-gray-400 hover:text-indigo-600 transition" title="Descargar">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                        @php
                                                            $yaFirmado = $p->archivos->where('tipo', 'boleta_firmada')->count() > 0;
                                                        @endphp
                                                        @if($file->tipo == 'boleta' && !$yaFirmado)
                                                            <button wire:click="$dispatch('abrir-modal-firma', { id: {{ $file->id }} })" title="Firmar boleta"
                                                                    class="w-6 h-6 flex items-center justify-center bg-white border border-green-500 hover:bg-green-50 rounded-full transition shadow-sm mr-1 group/btn">
                                                                <i class="fas fa-plus text-green-600 text-[10px] group-hover/btn:scale-110 transition-transform"></i>
                                                            </button>
                                                        @elseif($file->tipo == 'boleta_firmada')
                                                            <span class="text-[10px] bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">
                                                                <i class="fas fa-check mr-1"></i> FIRMADO
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-2 border-2 border-dashed border-gray-200 rounded-lg">
                                                    <p class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">Sin archivos adjuntos</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center bg-white p-16 rounded-2xl border shadow-sm">
                <i class="fas fa-receipt text-gray-200 text-6xl mb-4"></i>
                <p class="text-gray-500 font-bold">No hay registros de planillas aún.</p>
            </div>
        @endforelse
    </div>

    @livewire('r-r-h-h.firmar-boleta')
</div>
