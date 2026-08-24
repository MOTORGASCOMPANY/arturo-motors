<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Manual y Mantenimiento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11.3pt;
            /* un poco más grande que 11pt */
            margin: 22px 30px;
            /* margen equilibrado */
            line-height: 1.35;
            /* menos espaciado que 1.5 */
        }

        .section-title {
            background-color: #d1d5db;
            padding: 5px;
            font-size: 13pt;
            /* ligeramente más grande */
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table th {
            text-align: left;
            width: 40%;
            padding: 5px 8px;
            font-weight: bold;
            background-color: #f3f4f6;
            border-bottom: 1px solid #ddd;
            font-size: 10.8pt;
        }

        .info-table td {
            padding: 5px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10.8pt;
        }

        .firma {
            margin-top: 100px;
            text-align: center;
        }

        .firma .linea {
            margin-top: 35px;
            border-top: 1px solid #000;
            width: 40%;
            margin-left: auto;
            margin-right: auto;
        }

        .firma p {
            margin-top: 6px;
            font-weight: bold;
            font-size: 10.8pt;
        }
    </style>


</head>

<body>
    <main>
        <!-- Título -->
        <div class="section-title">DATOS DEL CLIENTE Y VEHÍCULO</div>

        <!-- Tabla de datos -->
        <table class="info-table">
            <tr>
                <th>Apellidos / Nombres o Razón Social:</th>
                <td>{{ $vehiculo->clientes->first()?->nombre ?? '---' }}</td>
            </tr>
            <tr>
                <th>DNI / CE / RUC:</th>
                <td>{{ $vehiculo->clientes->first()?->documento ?? '---' }}</td>
            </tr>
            <tr>
                <th>Dirección:</th>
                <td>{{ $vehiculo->clientes->first()?->direccion ?? '---' }}</td>
            </tr>
            <tr>
                <th>Marca / Modelo / Año:</th>
                <td>{{ $vehiculo->marca }} / {{ $vehiculo->modelo }} / {{ $vehiculo->anio }}</td>
            </tr>
            <tr>
                <th>Cilindrada:</th>
                <td>{{ $vehiculo->cilindrada }}</td>
            </tr>
            <tr>
                <th>Placa:</th>
                <td>{{ $vehiculo->placa }}</td>
            </tr>
            <tr>
                <th>Color:</th>
                <td>{{ $vehiculo->color }}</td>
            </tr>
            <tr>
                <th>Alimentación:</th>
                <td>Dual Gasolina – {{ $vehiculo->alimentacion ?? '---' }}</td>
            </tr>
            <tr>
                <th>Tanque (GLP/GNV):</th>
                <td>{{ $vehiculo->tanque ?? '---' }}</td>
            </tr>
            <tr>
                <th>Marca / Modelo / N° Serie:</th>
                <td>{{ $vehiculo->Tanquemarca }} / {{ $vehiculo->Tanquemodelo }} / {{ $vehiculo->Tanqueserie }}</td>
            </tr>
            <tr>
                <th>N° Constancia de Aprobación / N° Expediente:</th>
                <td>{{ $vehiculo->Tanqueaprobacion }} / {{ $vehiculo->Tanqueexpediente }}</td>
            </tr>
            <tr>
                <th>Multiválvulas Tipo y Medida:</th>
                <td>{{ $vehiculo->multivalvulas ?? '---' }}</td>
            </tr>
            <tr>
                <th>Toma de carga Tipo:</th>
                <td>{{ $vehiculo->toma ?? '---' }}</td>
            </tr>
            <tr>
                <th>Tipo de equipo:</th>
                <td>{{ $vehiculo->equipo ?? '---' }}</td>
            </tr>
            <tr>
                <th>Reductor Marca / Tipo / N° Serie:</th>
                <td>{{ $vehiculo->Equipomarca }} / {{ $vehiculo->Equipomodelo }} / {{ $vehiculo->Equiposerie }}</td>
            </tr>
            <tr>
                <th>N° Constancia de Aprobación / N° Expediente:</th>
                <td>{{ $vehiculo->Equipoaprobacion }} / {{ $vehiculo->Equipoexpediente }}</td>
            </tr>
            <tr>
                <th>Central de Control conmutación tipo / Electrónica adicional:</th>
                <td>{{ $vehiculo->central }} / {{ $vehiculo->electronica }}</td>
            </tr>
            <tr>
                <th>Otros dispositivos instalados:</th>
                <td>{{ $vehiculo->otros }}</td>
            </tr>
            <tr>
                <th>Fecha de Instalación:</th>
                <td>{{ $vehiculo->expediente->created_at->format('d/m/Y') ?? '---' }}</td>
            </tr>
            <tr>
                <th>Kilometraje Actual:</th>
                <td>{{ $vehiculo->kilometraje ?? '---' }}</td>
            </tr>
        </table>

        <!-- Firma -->
        <div class="firma">
            <div class="linea"></div>
            <p>Sello y firma del taller</p>
        </div>
    </main>
</body>

</html>
