<div class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sede destino</label>
        <select wire:model="sedeId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @foreach (\App\Models\Sede::activas()->get() as $sede)
                <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-bold text-gray-700 mb-3">¿Qué vas a registrar?</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-almacen.choice-card icon="fa-box" color="indigo" title="Kits"
                subtitle="Equipos completos por generación"
                alpine-click="almacenAcciones.elegir($wire, 'kits')" />
            <x-almacen.choice-card icon="fa-microchip" color="indigo" title="Productos"
                subtitle="Piezas, repuestos y componentes"
                alpine-click="almacenAcciones.elegir($wire, 'productos')" />
        </div>
    </div>
</div>
