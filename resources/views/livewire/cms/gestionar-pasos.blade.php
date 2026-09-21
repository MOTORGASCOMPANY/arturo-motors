<div>
    <x-cms.layout
        title="Proceso de Trabajo"
        description="Administra los pasos del proceso que se muestran en el landing page"
        headerIcon='<i class="fa-solid fa-route text-white"></i>'
    >
        <div class="px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ count($steps) }} pasos</h2>
                    <p class="text-gray-500 text-sm">Gestiona los pasos del proceso de trabajo</p>
                </div>
                <button wire:click="$dispatch('abrir-crear-paso', { total: {{ count($steps) }} })"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold shadow-sm shadow-blue-200 hover:bg-blue-700 hover:shadow-md hover:shadow-blue-300 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Nuevo Paso
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                @forelse ($steps as $step)
                    <x-cms.card wire:key="step-{{ $step->id }}" class="flex flex-col h-full group" style="animation: cardEntry 0.4s ease-out {{ $loop->index * 0.06 }}s both">
                        <div class="p-6 flex-1 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto mb-4 border border-blue-100">
                                <span class="text-2xl font-bold text-blue-600">{{ $step->step_number }}</span>
                            </div>
                            <h5 class="font-bold text-gray-900 text-lg mb-2">{{ $step->title }}</h5>
                            <p class="text-gray-500 text-sm">{{ $step->description }}</p>
                        </div>
                        <div class="border-t border-gray-100 bg-gray-50/80 px-6 py-3.5 flex items-center justify-center gap-1">
                            <x-cms.action-button icon="fa-solid fa-pen" variant="warning" wireClick="$dispatch('abrir-editar-paso', { id: {{ $step->id }} })" title="Editar" />
                            <x-cms.action-button icon="fa-solid fa-{{ $step->is_active ? 'eye-slash' : 'eye' }}" variant="{{ $step->is_active ? 'ghost' : 'success' }}" onclick="confirmToggleStep({{ $step->id }})" title="{{ $step->is_active ? 'Desactivar' : 'Activar' }}" />
                            <x-cms.action-button icon="fa-solid fa-trash" variant="danger" onclick="confirmDeleteStep({{ $step->id }})" title="Eliminar" />
                        </div>
                    </x-cms.card>
                @empty
                    <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center" style="animation: emptyPulse 3s ease-in-out infinite">
                        <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
                            <i class="fa-solid fa-route text-2xl text-white"></i>
                        </div>
                        <p class="text-gray-500 font-medium mb-1">No hay pasos definidos</p>
                        <p class="text-gray-400 text-sm">Usa el botón <span class="font-semibold text-blue-600">"Nuevo Paso"</span> de arriba para crear el primero</p>
                    </div>
                @endforelse
            </div>

            <style>
                @keyframes cardEntry { from { opacity: 0; transform: translateY(20px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
                @keyframes emptyPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
            </style>

            <script>
                function confirmDeleteStep(id) {
                    Swal.fire({
                        title: '¿Eliminar paso?',
                        text: 'Esta acción no se puede deshacer.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: { popup: 'rounded-xl shadow-xl border border-gray-200', confirmButton: 'rounded-lg px-5 py-2 font-semibold text-sm', cancelButton: 'rounded-lg px-5 py-2 font-semibold text-sm' }
                    }).then((result) => {
                        if (result.isConfirmed) { @this.call('delete', id) }
                    });
                }
                function confirmToggleStep(id) {
                    Swal.fire({
                        title: '¿Cambiar estado?',
                        text: 'Se activará o desactivará este paso.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar',
                        customClass: { popup: 'rounded-xl shadow-xl border border-gray-200', confirmButton: 'rounded-lg px-5 py-2 font-semibold text-sm', cancelButton: 'rounded-lg px-5 py-2 font-semibold text-sm' }
                    }).then((result) => {
                        if (result.isConfirmed) { @this.call('toggleActive', id) }
                    });
                }
            </script>
        </div>
    </x-cms.layout>

    <livewire:cms.pasos.form-paso />
</div>
