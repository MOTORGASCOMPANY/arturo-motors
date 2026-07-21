<div class="p-6">
    <!-- Encabezado: Información del Contrato -->
    <div class="bg-gray-200 rounded-lg shadow-md p-6 mb-6 border-l-4 border-indigo-500">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $contrato->user->name }}</h2>
                <p class="text-gray-500 uppercase text-xs font-semibold tracking-wider">
                    Cargo: {{ $contrato->cargo }} | DNI: {{ $contrato->user->dni }}
                </p>
                <div class="mt-2 flex gap-4">
                    <span class="text-sm">📅 <strong>Ingreso:</strong>
                        {{ $contrato->fecha_ingreso->format('d/m/Y') }}</span>
                    <span class="text-sm">⏳ <strong>Vencimiento:</strong>
                        {{ $contrato->fecha_vencimiento ? $contrato->fecha_vencimiento->format('d/m/Y') : 'Indeterminado' }}</span>
                </div>
            </div>
            <!-- Resumen de Saldos -->
            <div class="flex gap-4 text-center">
                <div class="bg-indigo-50 p-3 rounded-lg">
                    <p class="text-[10px] text-indigo-400 font-bold uppercase">Ganadas</p>
                    <p class="text-xl font-black text-indigo-700">{{ $contrato->vacaciones->dias_ganados ?? 0 }}</p>
                </div>
                <div class="bg-orange-50 p-3 rounded-lg">
                    <p class="text-[10px] text-orange-400 font-bold uppercase">Tomadas</p>
                    <p class="text-xl font-black text-orange-700">{{ $contrato->vacaciones->dias_tomados ?? 0 }}</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <p class="text-[10px] text-green-400 font-bold uppercase">Restantes</p>
                    <p class="text-xl font-black text-green-700">{{ $contrato->vacaciones->dias_restantes ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón para Nueva Asignación -->
    @hasanyrole('Administrador del sistema|administrador')
        <div class="flex justify-end mb-4">
            <button wire:click="$dispatch('abrir-asignar-vacacion', { idContrato: {{ $idContrato }} })"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition flex items-center">
                <i class="fas fa-calendar-plus mr-2"></i> Asignar Vacaciones
            </button>
        </div>
    @endhasanyrole

    <!-- Tabla de Historial (Vacaciones Asignadas) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-accent text-white uppercase text-xs">
                    <th class="px-5 py-3 border-b-2 text-left">F. Inicio</th>
                    <th class="px-5 py-3 border-b-2 text-left">Días Tomados</th>
                    <th class="px-5 py-3 border-b-2 text-left">Tipo / Razón</th>
                    <th class="px-5 py-3 border-b-2 text-left">Observación</th>
                    <th class="px-5 py-3 border-b-2 text-center">Estado</th>
                    @hasanyrole('Administrador del sistema|administrador')
                        <th class="px-5 py-3 border-b-2 text-center">Acciones</th>
                    @endhasanyrole
                </tr>
            </thead>
            <tbody>
                @forelse($asignaciones as $asig)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm">{{ $asig->f_inicio->format('d/m/Y') }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-orange-600">{{ $asig->d_tomados }} días</td>
                        <td class="px-5 py-4 text-sm">
                            <span class="block font-medium">{{ $asig->tipo }}</span>
                            <span class="text-xs text-gray-400">{{ $asig->razon }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-500">{{ $asig->observacion }}</td>
                        <td class="px-5 py-4 text-center">
                            @if ($asig->especial)
                                <span class="bg-indigo-400 text-white px-2 py-1 rounded-full text-[10px] font-bold">ESPECIAL</span>
                            @else
                                <span class="bg-accent text-white px-2 py-1 rounded-full text-[10px] font-bold">REGULAR</span>
                            @endif
                        </td>
                        @hasanyrole('Administrador del sistema|administrador')
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button wire:click="$dispatch('editar-asignacion', { id: {{ $asig->id }} })"
                                            class="py-2 px-3 rounded-md bg-lime-500 font-bold text-white hover:bg-lime-600 transition mr-1">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:confirm="¿Estás seguro de eliminar esta asignación? El saldo será devuelto al empleado."
                                            wire:click="eliminarAsignacion({{ $asig->id }})"
                                            class="py-2 px-3 rounded-md bg-red-500 font-bold text-white hover:bg-red-600 transition" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        @endhasanyrole
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-400 italic">
                            No hay vacaciones registradas en este periodo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $asignaciones->links() }}
        </div>
    </div>

    <!-- Modal para Asignar Vacaciones -->
    @livewire('r-r-h-h.asignar-vacacion')
</div>
