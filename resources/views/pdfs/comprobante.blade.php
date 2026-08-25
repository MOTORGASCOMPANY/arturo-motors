<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante {{ $orden->comprobante->folio }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .w-100 { width: 100%; }
        .w-50 { width: 50%; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* HEADER */
        .header-table td {
            vertical-align: top;
        }
        .logo {
            max-height: 55px;
            width: auto;
            margin-bottom: 6px;
        }
        .company-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }
        .company-info {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.3;
        }

        /* CUADRO RUC */
        .ruc-box {
            border: 2px solid #1e3a8a;
            border-radius: 8px;
            padding: 8px;
            text-align: center;
            background-color: #f8fafc;
            width: 100%;
        }
        .ruc-text {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
        }
        .ruc-type {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 4px 0;
        }
        .ruc-folio {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
        }

        /* SECCIÓN DATOS */
        .section-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-top: 10px;
            padding: 6px 8px;
            background-color: #ffffff;
        }
        .section-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
            margin-bottom: 6px;
        }
        .data-table td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 10px;
        }
        .label {
            font-weight: bold;
            color: #475569;
            width: 18%;
        }
        .val {
            color: #0f172a;
        }

        /* TABLA DE DETALLE */
        .items-table {
            margin-top: 12px;
            border: 1px solid #cbd5e1;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            font-size: 9px;
            padding: 5px;
            border-bottom: 1px solid #cbd5e1;
            border-right: 1px solid #cbd5e1;
        }
        .items-table td {
            padding: 6px 5px;
            font-size: 10px;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }
        .items-table th:last-child, .items-table td:last-child {
            border-right: none;
        }

        /* TOTALES Y FIRMA */
        .totals-section {
            margin-top: 8px;
        }
        .total-row {
            background-color: #f8fafc;
            font-size: 11px;
            font-weight: bold;
        }

        .footer-table {
            margin-top: 25px;
        }
        .notes-text {
            font-size: 8.5px;
            color: #64748b;
            line-height: 1.3;
        }
        .signature-line {
            border-bottom: 1px solid #94a3b8;
            width: 75%;
            margin: 0 auto 3px auto;
        }
    </style>
</head>
<body>

    <!-- Marca de agua -->
    <table width="100%" height="100%" style="position: fixed; top: 0; left: 0; z-index: -1;">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/icon.png') }}" style="width: 400px; opacity: 0.12;"></td></tr>
    </table>

    <!-- CABECERA PRINCIPAL -->
    <table class="header-table w-100">
        <tr>
            <!-- Columna Izquierda: Logo y Empresa -->
            <td style="width: 58%; padding-right: 10px;">
                <img src="{{ public_path('images/arturo3.png') }}" class="logo">
                <div class="company-title uppercase">ARTURO MOTORS — ASESOR AUTOMOTRIZ</div>
                <div class="company-title uppercase" style="color: #1e3a8a;">CENTRO DE INSPECCIÓN TÉCNICA VEHICULAR</div>
                
                <div class="company-info" style="margin-top: 4px;">
                    <strong>DIRECCIÓN:</strong> Av. Perú N° 5176 - Callao, Perú<br>
                    <strong>TELÉFONO:</strong> 987 288 504 / 943 694 464<br>
                    <strong>EMAIL:</strong> contacto@empresa.com.pe
                </div>
            </td>

            <!-- Columna Derecha: Cuadro RUC -->
            <td style="width: 42%; vertical-align: top;">
                <div class="ruc-box">
                    <div class="ruc-text">R.U.C. N° 20123456789</div>
                    <div class="ruc-type uppercase">CONSTANCIA DE SERVICIO</div>
                    <div class="ruc-folio">{{ $orden->comprobante->folio }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- INFORMACIÓN DE RECEPCIÓN (CLIENTE Y VEHÍCULO) -->
    <div class="section-box">
        <div class="section-title uppercase">Información de la Recepción</div>
        <table class="data-table w-100">
            <tr>
                <td class="label">Cliente:</td>
                <td class="val" style="width: 32%;">{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }}</td>
                <td class="label">Fecha / Hora:</td>
                <td class="val" style="width: 32%;">{{ $orden->comprobante->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Documento:</td>
                <td class="val">{{ $orden->cliente->documento ?? '74859612' }}</td>
                <td class="label">Método Pago:</td>
                <td class="val">{{ ucfirst($orden->comprobante->metodo_pago) }}</td>
            </tr>
            <tr>
                <td class="label">Placa Vehículo:</td>
                <td class="val"><strong style="font-size: 11px; color: #1e3a8a;">{{ strtoupper($orden->vehiculo->placa) }}</strong></td>
                <td class="label">Marca/Modelo:</td>
                <td class="val">{{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</td>
            </tr>
            <tr>
                <td class="label">Atendido por:</td>
                <td class="val">Sistema de Servicio</td>
                <td class="label">Estado Pago:</td>
                <td class="val"><strong style="color: #16a34a;">PAGADO</strong></td>
            </tr>
        </table>
    </div>

    <!-- TABLA DE DETALLE DE SERVICIOS -->
    <table class="items-table w-100">
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">ITEM</th>
                <th style="width: 52%;" class="text-left">DESCRIPCIÓN DEL SERVICIO</th>
                <th style="width: 10%;" class="text-center">CANT.</th>
                <th style="width: 15%;" class="text-right">P. UNITARIO</th>
                <th style="width: 15%;" class="text-right">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">01</td>
                <td>
                    <strong>{{ $orden->service->nombre }}</strong>
                    @if(!empty($orden->descuento_motivo))
                        <br><span style="font-size: 8.5px; color: #64748b;">Ajuste: {{ $orden->descuento_motivo }}</span>
                    @endif
                </td>
                <td class="text-center">1</td>
                <td class="text-right">S/ {{ number_format($orden->comprobante->monto, 2) }}</td>
                <td class="text-right">S/ {{ number_format($orden->comprobante->monto, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TOTALES E IMPORTE EN LETRAS -->
    <table class="w-100 totals-section">
        <tr>
            <!-- Columna Izquierda: Son X Soles -->
            <td style="width: 55%; vertical-align: top; padding-top: 6px;">
                <div style="font-size: 9.5px; color: #334155;">
                    <strong>IMPORTE EN LETRAS:</strong><br>
                    Son {{ number_format($orden->comprobante->monto, 2) }} Soles.
                </div>
            </td>

            <!-- Columna Derecha: Desglose Op. Gravada, IGV, Total -->
            <td style="width: 45%;">
                <table class="w-100">
                    <tr>
                        <td class="text-right font-bold" style="color: #475569; padding: 2px 4px;">Op. Gravada:</td>
                        <td class="text-right" style="width: 40%; padding: 2px 4px;">S/ {{ number_format($orden->comprobante->monto / 1.18, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-right font-bold" style="color: #475569; padding: 2px 4px;">I.G.V. (18%):</td>
                        <td class="text-right" style="padding: 2px 4px;">S/ {{ number_format($orden->comprobante->monto - ($orden->comprobante->monto / 1.18), 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="text-right font-bold" style="padding: 5px 4px; color: #0f172a;">TOTAL A PAGAR:</td>
                        <td class="text-right font-bold" style="padding: 5px 4px; color: #1e3a8a;">S/ {{ number_format($orden->comprobante->monto, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- PIE DE PÁGINA: NOTAS Y FIRMA -->
    <table class="w-100 footer-table">
        <tr>
            <td style="width: 68%; vertical-align: bottom;">
                <div class="notes-text">
                    * Este documento representa un comprobante de atención e inspección interna.<br>
                    * Conserve esta constancia para el retiro o verificación de su vehículo.
                </div>
            </td>
            <td style="width: 32%; text-align: center; vertical-align: bottom;">
                <div class="signature-line"></div>
                <span style="font-size: 8px; color: #64748b; font-weight: bold;" class="uppercase">Firma / Sello Autorizado</span>
            </td>
        </tr>
    </table>

</body>
</html>