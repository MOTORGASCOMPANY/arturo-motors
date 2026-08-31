<div wire:loading.class="opacity-50 pointer-events-none">
    <div class="max-w-4xl mx-auto py-12">
        <div class="bg-gray-200 p-8 rounded-xl w-full">
            <div class="px-2 w-full mb-4">
                <h2 class="text-gray-600 font-semibold text-2xl">
                    <i class="fas fa-box-open mr-2"></i>Kits en stock
                </h2>
                <span class="text-xs">Kits cerrados disponibles en Arturo Motors (Callao)</span>
            </div>

            @if ($kits->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal rounded-md overflow-hidden">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">Kit</th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase">N.° de lote / serie</th>
                                <th class="px-4 py-3 border-b-2 border-gray-200 bg-gray-100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kits as $kit)
                                <tr wire:key="kit-{{ $kit->id }}">
                                    <td class="px-4 py-3 font-medium border-b border-gray-200 bg-white text-sm">{{ $kit->producto->nombre }}</td>
                                    <td class="px-4 py-3 border-b border-gray-200 bg-white text-sm font-mono">{{ $kit->serie }}</td>
                                    <td class="px-4 py-3 text-right border-b border-gray-200 bg-white text-sm">
                                        <button wire:click="$dispatch('abrir-modal-abrir-kit', { itemId: {{ $kit->id }} })" type="button"
                                                class="bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                                            Abrir kit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200/60">{{ $kits->links() }}</div>
            @else
                <div class="px-6 py-4 text-center font-bold bg-indigo-200 rounded-md">
                    No hay kits cerrados en stock.
                </div>
            @endif
        </div>
    </div>

    <livewire:almacen.kits.abrir />
</div>