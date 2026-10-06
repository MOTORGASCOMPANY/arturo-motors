<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Manual de Conversión GNV</title>
    @include('pdfs._marca')
    <style>
        table.resumen { width: 100%; border-collapse: collapse; margin-bottom: 4px; border-top: 0.75px solid #c7d7ee; border-bottom: 0.75px solid #c7d7ee; }
        table.resumen td { padding: 5px 8px; text-align: center; border-right: 0.75px solid #e2e6ea; }
        table.resumen td:last-child { border-right: none; }
        .resumen-label { font-size: 7px; color: #8a8a8a; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 2px 0; }
        .resumen-value { font-size: 11px; font-weight: bold; color: #1565c0; margin: 0; }

        ul.recomendaciones { margin: 3px 0 0 0; padding-left: 14px; }
        ul.recomendaciones li { font-size: 8.5px; margin-bottom: 2px; }
    </style>
</head>
<body>

    <table class="watermark">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/LOGOFINAL.jpg') }}"></td></tr>
    </table>

    <!-- ENCABEZADO -->
    <table class="header">
        <tr>
            <td class="logo-cell"><img src="{{ public_path('images/LOGOFINAL.jpg') }}"></td>
            <td class="brand-cell">
                <p class="brand-title">ARTURO MOTORS</p>
                <p class="brand-subtitle">Tecnología Automotriz</p>
            </td>
            <td class="ruc-cell">
                <div class="ruc-label">R.U.C.</div>
                <div class="ruc-value">{{ $ruc ?? '20610295321' }}</div>
                <div class="rd-value">R.D. N° {{ $rd_numero ?? '0413-2023-MTC/17.03' }}</div>
            </td>
        </tr>
    </table>
    <hr class="header-divider">

    <!-- TÍTULO -->
    <table class="doc-title-box">
        <tr>
            <td style="width: 70%;">
                <p class="doc-title-main">Manual de Conversión a GNV</p>
                <p class="doc-title-sub">Ficha técnica del vehículo y propietario</p>
            </td>
            <td class="doc-title-code" style="width: 30%;">
                <span>Placa</span>
                {{ $vehiculo->placa }}
            </td>
        </tr>
    </table>

    <!-- FICHA RÁPIDA -->
    <table class="resumen">
        <tr>
            <td><p class="resumen-label">Marca / Modelo</p><p class="resumen-value">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</p></td>
            <td><p class="resumen-label">Año</p><p class="resumen-value">{{ $vehiculo->anio ?? '---' }}</p></td>
            <td><p class="resumen-label">Placa</p><p class="resumen-value">{{ $vehiculo->placa }}</p></td>
            <td><p class="resumen-label">Combustible</p><p class="resumen-value">{{ $vehiculo->combustible ?? '---' }}</p></td>
        </tr>
    </table>

    <!-- 01 PROPIETARIO -->
    <div class="section-title"><span>01</span>Datos del Propietario</div>
    <table class="info">
        <tr><th>Apellidos / Nombres</th><td colspan="3">{{ $propietario->nombre ?? '---' }} {{ $propietario->apellido ?? '' }}</td></tr>
        <tr>
            <th>Tipo de documento</th><td>{{ $propietario->tipo_documento ?? '---' }}</td>
            <th>N° de documento</th><td>{{ $propietario->documento ?? '---' }}</td>
        </tr>
        <tr><th>Dirección</th><td colspan="3">{{ $propietario->direccion ?? '---' }}</td></tr>
        <tr>
            <th>Teléfono</th><td>{{ $propietario->telefono ?? '---' }}</td>
            <th>Email</th><td>{{ $propietario->email ?? '---' }}</td>
        </tr>
    </table>

    <!-- 02 VEHÍCULO -->
    <div class="section-title"><span>02</span>Datos del Vehículo</div>
    <table class="info">
        <tr>
            <th>Marca</th><td>{{ $vehiculo->marca }}</td>
            <th>Modelo</th><td>{{ $vehiculo->modelo }}</td>
        </tr>
        <tr>
            <th>Año</th><td>{{ $vehiculo->anio ?? '---' }}</td>
            <th>Placa</th><td>{{ $vehiculo->placa }}</td>
        </tr>
        <tr>
            <th>Color</th><td>{{ $vehiculo->color ?? '---' }}</td>
            <th>Combustible</th><td>{{ $vehiculo->combustible ?? '---' }}</td>
        </tr>
        <tr><th>N° Serie / VIN</th><td colspan="3">{{ $vehiculo->serie ?? '---' }}</td></tr>
    </table>

    <!-- 03 HISTORIAL -->
    <div class="section-title"><span>03</span>Historial de Servicios</div>
    @if($servicios->count() > 0)
        <table class="lista">
            <tr>
                <th style="width: 13%;">Fecha</th>
                <th style="width: 21%;">Servicio</th>
                <th style="width: 15%;">Estado</th>
                <th style="width: 15%;">Técnico</th>
                <th style="width: 13%;">Monto</th>
                <th style="width: 23%;">Observaciones</th>
            </tr>
            @foreach($servicios->sortByDesc('created_at')->take(5) as $s)
                <tr>
                    <td>{{ $s->created_at->format('d/m/Y') }}</td>
                    <td>{{ $s->service?->nombre ?? 'Servicio' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $s->estado)) }}</td>
                    <td>{{ $s->tecnico?->name ?? '---' }}</td>
                    <td>S/ {{ number_format($s->precio_final ?? 0, 2) }}</td>
                    <td>{{ $s->evaluacion_observaciones ?? '---' }}</td>
                </tr>
            @endforeach
        </table>
        @if($servicios->count() > 5)
            <p class="empty" style="margin: 3px 0 0 0; font-size: 7.5px;">Se muestran los 5 servicios más recientes de {{ $servicios->count() }} registrados.</p>
        @endif
    @else
        <p class="empty">No hay servicios registrados para este vehículo.</p>
    @endif

    <!-- 04 MANTENIMIENTOS -->
    <div class="section-title"><span>04</span>Próximos Mantenimientos Sugeridos</div>
    <table class="lista">
        <tr><th style="width: 22%;">Frecuencia</th><th>Actividad</th></tr>
        <tr><td><strong>Cada 6 meses</strong></td><td>Revisión general del sistema GNV (fugas, conexiones, regulador).</td></tr>
        <tr><td><strong>Cada 12 meses</strong></td><td>Revisión de cilindro, válvulas y prueba de hermeticidad.</td></tr>
        <tr><td><strong>Cada 24 meses</strong></td><td>Prueba hidrostática de cilindro (según normativa vigente).</td></tr>
        <tr><td><strong>Próxima revisión</strong></td><td>{{ $proximoMantenimiento ?? 'Según última cita: ' . ($ultimaCita?->fecha_cita?->format('d/m/Y') ?? 'Pendiente') }}</td></tr>
    </table>

    <!-- 05 RECOMENDACIONES -->
    <div class="section-title"><span>05</span>Recomendaciones de Uso y Seguridad</div>
    <ul class="recomendaciones">
        <li>Ante olor a gas, cierre la válvula del cilindro, ventile el vehículo y acuda al taller. No encienda llamas.</li>
        <li>No manipule el sistema GNV fuera de un taller autorizado.</li>
        <li>Cumpla los mantenimientos indicados para conservar la garantía. Presente este manual en cada revisión.</li>
    </ul>

    <!-- FIRMAS -->
    <table class="firmas">
        <tr>
            <td>
                <div class="firma-linea"></div>
                <strong>Sello y Firma del Taller</strong>
                <span>ARTURO MOTORS</span>
            </td>
            <td>
                <div class="firma-linea"></div>
                <strong>Firma del Cliente</strong>
                <span>Conformidad de Servicio</span>
            </td>
        </tr>
    </table>

    <p class="footer-note">DOCUMENTO GENERADO POR EL SISTEMA DE GESTIÓN DE ARTURO MOTORS — {{ $fechaEmision }}</p>

</body>
</html>