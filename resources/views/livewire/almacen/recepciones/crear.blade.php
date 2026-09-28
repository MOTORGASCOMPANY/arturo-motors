<div x-data
     x-on:swal.window="recepcionSwal.desdeServidor($event.detail, $wire)"
     x-on:swal-init.window="recepcionSwal.desdeServidor($event.detail, $wire)"
     x-on:swal-kit.window="recepcionSwal.desdeServidor($event.detail, $wire)">

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

            <x-almacen.recepcion.header />

            <x-almacen.recepcion.stepper />

            @if ($modalKitAbierto)
                <x-almacen.recepcion.componentes />

            @elseif ($seccion === 'elegir')
                <x-almacen.recepcion.elegir />

            @elseif ($seccion === 'kits')
                <x-almacen.recepcion.kits />

            @elseif ($seccion === 'productos')
                @if ($subSeccionProductos === '')
                    <x-almacen.recepcion.tipo-producto />
                @elseif ($subSeccionProductos === 'serializados')
                    <x-almacen.recepcion.serializados />
                @elseif ($subSeccionProductos === 'cantidad')
                    <x-almacen.recepcion.cantidad />
                @endif
            @endif

        </div>
    </div>

    <script src="{{ asset('js/components/recepcion-swal.js') }}"></script>
</div>
