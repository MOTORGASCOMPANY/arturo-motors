<div>
    <x-almacen.info-banner icon="fa-microchip" color="indigo"
        :title="'Registrando <strong>Productos</strong>'"
        action-label="Cambiar" action-method="volverAEleccion" />

    <label class="block text-sm font-bold text-gray-700 mb-3">¿Qué tipo de producto vas a recibir?</label>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-almacen.choice-card icon="fa-barcode" color="indigo" title="Serializados"
            subtitle="Cada pieza tiene serie propia"
            wire-click="abrirSubSeccion('serializados')" />
        <x-almacen.choice-card icon="fa-cubes" color="amber" title="Por cantidad"
            subtitle="Repuestos, mangueras, piezas sueltas"
            wire-click="abrirSubSeccion('cantidad')" />
    </div>
</div>
