<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Recepción - Ficha Evaluación</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 2px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            width: 60%;
            height: 50px;
        }

        .section-title {
            background-color: #d1d1d1;
            padding: 5px;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .field-label {
            font-weight: bold;
        }

        /* Estilos para la tabla */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .info-table td,
        .info-table th {
            padding: 5px;
            vertical-align: top;
            border: none;
        }

        .info-table th {
            background-color: #e9e9e9;
            text-align: left;
        }

        /* Estilos para la tabla de accesorios (más compacta) */
        .accessories-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;

        }

        .accessories-table td,
        .accessories-table th {
            border: 1px solid #ccc;
            padding: 2px 4px;
            text-align: left;
            vertical-align: top;
        }

        .accessories-table th {
            background-color: #f5f5f5;
            text-align: center;
            font-weight: bold;
        }

        .accessories-table td.center-text {
            text-align: center;
        }

        .carro-esquema {
            padding: 0;
            background-repeat: no-repeat;
            background-position: center;
            background-size: 80% 100%;
            /* 🔑 deforma para encajar EXACTO */
            height: 240px;
            /* refuerza la altura esperada del td */

        }

        .check-icon {
            height: 12px;
            width: auto;
        }

        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>

<body>
    {{-- Encabezado con imagen --}}
    <div class="header">
        <img src="{{ public_path('images/header2.png') }}" alt="Encabezado">
    </div>

    {{-- Hoja de Recepción --}}
    <h2 style="text-align: center;">HOJA DE RECEPCION</h2>
    <table class="info-table">
        <tr>
            <td style="width: 50%;"><span class="field-label">Fecha de ingreso al taller:</span> {{ $fechaIngreso }}</td>
            <td style="width: 50%;"><span class="field-label">Fecha de salida al taller:</span> {{ $fechaSalida }}</td>
        </tr>
    </table>

    {{-- Datos del Dueño --}}
    <div class="section-title">DATOS DEL DUEÑO</div>
    <table class="info-table">
        <tr>
            <td style="width: 48%;"><span class="field-label">Nombre:</span> {{ $nombreCliente }}</td>
            <td style="width: 26%;"><span class="field-label">DNI:</span> {{ $dniCliente }}</td>
            <td style="width: 26%;"><span class="field-label">Teléfono:</span> {{ $telefonoCliente }}</td>
        </tr>
    </table>

    {{-- Datos y Características del Vehículo --}}
    <div class="section-title">DATOS Y CARACTERISTICAS DEL VEHICULO</div>
    <table class="info-table">
        <tr>
            <td style="width: 33.33%;"><span class="field-label">Placa Actual:</span> {{ $placaVehiculo }}</td>
            <td style="width: 33.33%;"><span class="field-label">Placa Anterior:</span> {{ $placaAnteriorVehiculo }}
            </td>
            <td style="width: 33.33%;"><span class="field-label">Marca:</span> {{ $marcaVehiculo }}</td>
        </tr>
        <tr>
            <td style="width: 33.33%;"><span class="field-label">Modelo:</span> {{ $modeloVehiculo }}</td>
            <td style="width: 33.33%;"><span class="field-label">N° Motor:</span> {{ $motorVehiculo }}</td>
            <td style="width: 33.33%;"><span class="field-label">Color:</span> {{ $colorVehiculo }}</td>
        </tr>
        <tr>
            <td style="width: 33.33%;"><span class="field-label">Año:</span> {{ $anioVehiculo }}</td>
            <td style="width: 33.33%;"><span class="field-label">Combustible:</span> {{ $combustibleVehiculo }}</td>
            <td style="width: 33.33%;"><span class="field-label">Kilometraje:</span> {{ $kilometrajeVehiculo }}</td>
        </tr>
    </table>

    {{-- Recepción del Vehículo --}}
    <div class="section-title">RECEPCION DEL VEHICULO</div>
    <table class="accessories-table">
        <thead>
            <tr>
                <th style="width: 49%;">Esquema de Daños</th>
                <th style="width: 20%;">Accesorios</th>
                <th class="center-text" style="width: 4%;">SI</th>
                <th class="center-text" style="width: 4%;">NO</th>
                <th style="width: 20%;">Accesorios</th>
                <th class="center-text" style="width: 4%;">SI</th>
                <th class="center-text" style="width: 4%;">NO</th>
            </tr>
        </thead>
        <tbody>
            @php
                $detalles = $detallesEvaluacion->getAttributes();
                unset($detalles['id'], $detalles['evaluacion_id'], $detalles['created_at'], $detalles['updated_at']);
                $column_count = 2;
                $items_per_column = ceil(count($detalles) / $column_count);
                $chunks = array_chunk($detalles, $items_per_column, true);

                // SVG en Base64 para el ícono de checkmark
                $svg =
                    'data:image/svg+xml;base64,' .
                    base64_encode(
                        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#000000"><path d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>',
                    );
            @endphp
            @for ($i = 0; $i < $items_per_column; $i++)
                <tr>
                    @if ($i === 0)
                        <td class="carro-esquema" rowspan="{{ $items_per_column }}"
                            style="background-image: url('{{ public_path('images/carro.png') }}');">
                        </td>
                    @endif
                    @foreach ($chunks as $column)
                        @php
                            $key = array_keys($column)[$i] ?? null;
                            $value = $key ? $column[$key] : null;
                            $nombre_accesorio = ucwords(str_replace('_', ' ', $key));
                        @endphp
                        @if ($key)
                            <td>{{ $nombre_accesorio }}</td>
                            <td class="center-text">
                                @if ($value === 1)
                                    <img src="{{ $svg }}" alt="Sí" class="check-icon">
                                @endif
                            </td>
                            <td class="center-text">
                                @if ($value === 0)
                                    <img src="{{ $svg }}" alt="No" class="check-icon">
                                @endif
                            </td>
                        @else
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Observaciones --}}
    <div class="section-title">OBSERVACIONES</div>
    <table class="info-table" style="">
        <tr>
            <td style="width:100%;">{{ $observaciones }}</td>
        </tr>
    </table>

    {{-- Autorización y Firmas --}}
    <p>
        Con la presente yo y/o en representación autorizo el trabajo a realizarse en mi vehículo.
    </p>
    <table class="info-table" style="margin-top:50px; text-align:center;">
        <tr>
            <td style="width:50%; padding-top:50px;">
                ___________________________<br>
                Firma del Cliente
            </td>
            <td style="width:50%; padding-top:50px;">
                ___________________________<br>
                Firma Representante del Taller
            </td>
        </tr>
    </table>

    <div class="footer">
        Documento generado por el <strong>Sistema de Gestión de Conversiones</strong>.
    </div>
</body>

</html>
