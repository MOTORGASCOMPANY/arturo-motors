<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

    <!-- Próximas citas -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Próximas Citas</h3>
        <ul>
            @foreach($citasProximas as $cita)
                <li class="border-b py-2">
                    {{ $cita->fecha_cita }} - Cliente: {{ $cita->cliente_id }}
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Vehículos recientes -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Vehículos Recientes</h3>
        <ul>
            @foreach($vehiculosRecientes as $vehiculo)
                <li class="border-b py-2">
                    {{ $vehiculo->placa }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Expedientes abiertos -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-2">Expedientes en Proceso</h3>
        <ul>
            @foreach($expedientesAbiertos as $expediente)
                <li class="border-b py-2">
                    ID: {{ $expediente->id }} - Cliente: {{ $expediente->cliente_id }}
                </li>
            @endforeach
        </ul>
    </div>

</div>
