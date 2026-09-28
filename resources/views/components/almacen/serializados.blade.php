<div>
    <x-almacen.info-banner icon="fa-barcode" color="indigo"
        :title="'Registrando <strong>Productos serializados</strong>'"
        action-label="Volver" action-method="volverAProductos" />

    <div id="zona-series" class="space-y-5">
        @foreach ($this->productosPorCategoria as $categoria => $productos)
            <div wire:key="cat-{{ \Illuminate\Support\Str::slug($categoria) }}">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i class="fas fa-tag text-indigo-400"></i> {{ $categoria }}
                </h4>
                <div class="space-y-2">
                    @foreach ($productos as $producto)
                        <x-almacen.producto-serializado wire:key="prod-{{ $producto->id }}" :producto="$producto" />
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <x-almacen.notas class="mt-5" placeholder="Ej: Recepción parcial, faltan piezas..." />

    <x-almacen.total-banner :total="$this->totalProductos" icon="fa-barcode" bg="gray" icon-color="indigo"
        suffix="producto(s) en total." />

    <x-almacen.flow-actions method="guardarProductos" cancel-action="volverAProductos"
        :label="'Recibir ' . ($this->totalProductos > 0 ? $this->totalProductos . ' producto(s)' : '')"
        :data="[
            'data-total' => $this->totalProductos,
            'data-contenedor' => 'zona-series',
            'data-titulo' => '¿Recibir ' . $this->totalProductos . ' producto(s)?',
            'data-texto' => 'Se registrarán con las series capturadas.',
            'data-aviso' => 'Indica la cantidad de al menos un producto para continuar.',
        ]" />
</div>
