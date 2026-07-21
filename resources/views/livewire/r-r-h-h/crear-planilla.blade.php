<div>
    <x-dialog-modal wire:model="abierto" maxWidth="6xl">
        <x-slot name="title">
            <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                <span class="font-extrabold text-gray-800 tracking-wider text-sm uppercase">
                    <i class="fas fa-layer-group mr-2 text-orange-500"></i>GENERACIÓN DE PLANILLA
                </span>
                <div class="flex items-center gap-3 bg-gray-100 p-1 px-3 rounded-full border border-gray-200">
                    <x-label value="FECHA PERIODO:" class="text-gray-600 text-sm font-bold" />
                    <x-input type="date" wire:model.live="periodo"
                        class="border-none bg-transparent text-gray-700 text-sm focus:ring-0 p-0 w-32 cursor-pointer shadow-none" />
                </div>
            </div>
        </x-slot>

        <x-slot name="content">
            @if($periodo && count($lista_planilla) > 0)
                <div class="px-4 py-3 bg-blue-50 border-l-4 border-blue-400 mt-2 rounded-r-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-calculator text-blue-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-[12px] text-blue-800 font-bold uppercase mb-1">
                                Lógica de Cálculo de Planilla (Periodos Quincenales/Mensuales)
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-[11px] text-blue-700 leading-tight">
                                <div>
                                    <p class="font-bold border-b border-blue-200 mb-1">Cálculo Banco (1ra Q / Finde):</p>
                                    <ul class="list-disc ml-4 space-y-1">
                                        <li><strong>Sueldo Base:</strong> (Sueldo real con descuentos AFP/ONP) / 2.</li>
                                        <li><strong>BANCO 1Q:</strong> Base + Asignación + Horas Extras + Otros - Descuentos.</li>
                                    </ul>
                                </div>
                                <div>
                                    <p class="font-bold border-b border-blue-200 mb-1">Cálculo Efectivo y Consolidado:</p>
                                    <ul class="list-disc ml-4 space-y-1">
                                        <li><strong>EFECTIVO:</strong> Corresponde únicamente al monto de Movilidad.</li>
                                        <li><strong>TOTAL:</strong> Banco + Efectivo (Suma de todos los conceptos y beneficios).</li>
                                    </ul>
                                </div>
                            </div>
                            <p class="text-[10px] text-blue-700 italic">
                                * Nota: La asignación familiar se calcula internamente según ley vigente aplicada al periodo.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 shadow-sm rounded-xl border border-gray-200 overflow-hidden">
                    <table class="min-w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-accent text-xs font-bold text-white uppercase tracking-wider">
                            <tr>
                                {{-- Empleado: Ancho reducido --}}
                                <th class="w-auto px-4 py-3 text-left">EMPLEADO</th>

                                {{-- Columnas Numéricas con anchos específicos --}}
                                <th class="w-24 px-1 py-3 text-center bg-blue-50/50 text-blue-700">S. BASE</th>
                                <th class="w-20 px-1 py-3 text-center">H.EXT</th>
                                <th class="w-20 px-1 py-3 text-center">MOVIL</th>
                                <th class="w-20 px-1 py-3 text-center">OTROS</th>
                                <th class="w-20 px-1 py-3 text-center text-red-600">DCTOS.</th>

                                {{-- Observación: Espacio ampliado --}}
                                <th class="w-72 px-2 py-3 text-left">OBSERVACIONES</th>

                                <th class="w-20 px-1 py-3 text-center bg-green-800">TOTAL</th>

                                <th class="w-20 px-1 py-3 text-center bg-yellow-600">BANCO 1Q</th>
                                <th class="w-20 px-1 py-3 text-center bg-green-700">EFECTIVO</th>

                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 text-sm">
                            @foreach($lista_planilla as $index => $item)
                                <tr wire:key="fila-{{ $index }}" class="hover:bg-gray-50 transition-colors">
                                    <!-- Empleado -->
                                    <td class="px-4 py-1 font-semibold text-gray-700">
                                        <div class="" title="{{ $item['nombre'] }}">
                                            {{ $item['nombre'] }}
                                        </div>
                                    </td>
                                    <!-- Sueldo Base -->
                                    <td class="px-1 py-1 bg-blue-50/30 text-center">
                                        <x-input type="number" step="0.01"
                                            class="w-full text-right py-1 px-1 border-none shadow-none bg-transparent focus:ring-0 font-bold text-blue-800"
                                            wire:model.live.blur="lista_planilla.{{ $index }}.sueldo_base" />
                                    </td>
                                    <!-- Horas Extras -->
                                    <td class="px-1 py-1 text-center">
                                        <x-input type="number" step="0.01"
                                            class="w-full text-right py-1 px-1 border-gray-100 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-none transition-all"
                                            wire:model.live.blur="lista_planilla.{{ $index }}.horas_extras" />
                                    </td>
                                    <!-- Movilidad -->
                                    <td class="px-1 py-1 text-center">
                                        <x-input type="number" step="0.01"
                                            class="w-full text-right py-1 px-1 border-gray-100 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-none transition-all"
                                            wire:model.live.blur="lista_planilla.{{ $index }}.movilidad" />
                                    </td>
                                    <!-- Otros Ingresos -->
                                    <td class="px-1 py-1 text-center">
                                        <x-input type="number" step="0.01"
                                            class="w-full text-right py-1 px-1 border-gray-100 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-none transition-all"
                                            wire:model.live.blur="lista_planilla.{{ $index }}.otros_ingresos" />
                                    </td>
                                    <!-- Otros Descuentos -->
                                    <td class="px-1 py-1 text-center">
                                        <x-input type="number" step="0.01"
                                            class="w-full text-right py-1 px-1 border-red-50 text-red-600 focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50 rounded-md shadow-none transition-all"
                                            wire:model.live.blur="lista_planilla.{{ $index }}.otros_descuentos" />
                                    </td>
                                    <!-- Observaciones -->
                                    <td class="px-2 py-1">
                                        <x-input type="text" placeholder="Observaciones..."
                                            class="w-full text-sm py-1 px-2 border-gray-100 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-none italic text-gray-500"
                                            wire:model.live.blur="lista_planilla.{{ $index }}.observacion"  />
                                    </td>
                                    <!-- Total Calculado -->
                                    <td class="px-4 py-1 text-center font-black text-white bg-green-800 italic">
                                        {{ number_format($item['total'], 2) }}
                                    </td>
                                    <td class="px-4 py-1 text-center font-black text-white bg-yellow-600 italic">
                                        {{ number_format($item['banco'], 2) }}
                                    </td>
                                    <td class="px-4 py-1 text-center font-black text-white bg-green-700 italic">
                                        {{ number_format($item['efectivo'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 mt-2">
                    <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-4"></i>
                    <p class="text-sm font-black text-gray-400 uppercase tracking-[0.1em]">Seleccione una fecha para cargar registros</p>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex items-center justify-between w-full">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-widest italic">
                    @if(count($lista_planilla) > 0)
                        PROCESANDO: {{ count($lista_planilla) }} EMPLEADOS
                    @endif
                </span>
                <div class="flex gap-3">
                    <x-secondary-button wire:click="$set('abierto', false)" class="!text-xs tracking-widest">
                        CANCELAR
                    </x-secondary-button>
                    @if(count($lista_planilla) > 0)
                        <x-button wire:click="guardarMasivo"
                            class="!bg-orange-500 hover:!bg-orange-600 !text-xs tracking-widest">
                            <i class="fas fa-save mr-2"></i>GUARDAR PLANILLA
                        </x-button>
                    @endif
                </div>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
