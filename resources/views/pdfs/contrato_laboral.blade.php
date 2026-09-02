<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato de Trabajo - {{ $contrato->user->name }}</title>
    <style>
        @page { margin: 1.2cm 1.5cm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; line-height: 1.4; color: #555; text-align: justify; margin: 0; padding: 0; }
        table.header { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .brand-title { font-size: 26px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1px; }
        .brand-subtitle { font-size: 10px; color: #999; margin: 2px 0 0 0; letter-spacing: 2px; text-transform: uppercase; }
        .ruc-cell { text-align: right; }
        .ruc-label { font-size: 10px; color: #999; }
        .ruc-value { font-size: 11px; font-weight: bold; color: #1565c0; }
        .rd-value { font-size: 9px; color: #999; }
        .doc-title { text-align: center; font-weight: bold; text-decoration: underline; margin: 15px 0 20px 0; font-size: 13px; color: #1a1a1a; }
        .content { margin-bottom: 8px; }
        .bold { font-weight: bold; color: #333; }
        ul { margin-top: 5px; padding-left: 20px; }
        li { margin-bottom: 5px; }
        .signatures-wrapper { margin-top: 35px; page-break-inside: avoid; }
        .signature-container { position: relative; width: 100%; height: 120px; text-align: center; }
        .firma-img { position: absolute; top: -85px; left: 50%; transform: translateX(-50%); width: 180px; z-index: 1; }
        .line { border-top: 1px solid #999; width: 80%; margin: 80px auto 5px auto; position: relative; z-index: 2; }
        .footer-note { margin-top: 30px; text-align: center; font-size: 8px; color: #aaa; }
    </style>
</head>
<body>
    <table width="100%" height="100%" style="position: fixed; top: 0; left: 0; z-index: -1;">
        <tr><td align="center" valign="middle"><img src="{{ public_path('images/icon.png') }}" style="width: 400px; opacity: 0.12;"></td></tr>
    </table>

    <table class="header">
        <tr>
            <td style="width: 15%;"><img src="{{ public_path('images/icon.png') }}" style="width: 65px; height: 65px;"></td>
            <td style="width: 55%;"><p class="brand-title">ARTURO MOTORS</p><p class="brand-subtitle">TECNOLOG&Iacute;A AUTOMOTRIZ</p></td>
            <td class="ruc-cell" style="width: 30%;"><div class="ruc-label">R.U.C.</div><div class="ruc-value">{{ $ruc ?? '20610295321' }}</div><div class="rd-value">R.D. N&deg; {{ $rd_numero ?? '0413-2023-MTC/17.03' }}</div></td>
        </tr>
    </table>

    <div class="doc-title">CONTRATO DE TRABAJO SUJETO A MODALIDAD POR NECESIDADES DEL MERCADO</div>

    <div class="content">Conste por el presente documento que celebramos de una parte la empresa <span class="bold">MOTOR GAS COMPANY S. A</span>, con RUC N&deg; 20472634501 con domicilio fiscal en Jr. San Pedro De Carabayllo N&deg; 180 Urbanizaci&oacute;n Santa Isabel Distrito de Carabayllo, Provincia y Departamento de Lima, representado por Don <span class="bold">Lopez Henriquez Spasoje Bratzo</span> identificado con DNI 09551431, a quien en adelante se denominar&aacute; EL CONTRATANTE <span class="bold">{{ $contrato->user->name }}</span> y por el otro parte identificado con DNI <span class="bold">{{ $contrato->user->dni }}</span> , domiciliada en {{ $contrato->user->direccion }}, quien en adelante se le denominar&aacute; EL CONTRATADO bajo los t&eacute;rminos siguientes:</div>

    <div class="content"><span class="bold">PRIMERO:</span> EL CONTRATANTE de conformidad con el Articulo 57 de la Ley de Productividad y Competitividad laboral se celebra el contrato por Naturaleza Temporal con el CONTRATADO por un periodo comprendido entre los d&iacute;as <span class="bold">{{ $contrato->fecha_inicio_contrato->translatedFormat('d \d\e F \d\e\l Y') }}</span> al <span class="bold">{{ $contrato->fecha_vencimiento->translatedFormat('d \d\e F \d\e\l Y') }}</span>.</div>

    <div class="content"><span class="bold">SEGUNDO:</span> EL CONTRATADO cumplir&aacute; las funciones de <span class="bold">{{ $contrato->cargo }}</span> en el proceso de Certificaciones Vehiculares de Conversiones a Gas Natural Vehicular &ndash; GNV, encargadas por el ministerio de Transportes y Comunicaciones con puntualidad, responsabilidad y cumplimiento con el horario de trabajo y todas las metas establecidas por la empresa y otras funciones que le designe el contratante.</div>

    <div class="content"><span class="bold">TERCERO:</span> EL CONTRATANTE se compromete a abonar por sus <span class="bold">HONORARIOS DE INSPECTOR</span> al CONTRATADO la suma de <span class="bold">S/ {{ number_format($contrato->sueldo_bruto, 2) }} ({{ $sueldo_letras }})</span>, en forma mensual por la integridad de los trabajos realizados previa presentaci&oacute;n del informe de labores.</div>

    <div class="content"><span class="bold">CUARTO: EL CONTRATADO</span> asume las siguientes obligaciones que est&aacute;n vinculadas a la naturaleza propia del servicio que presentar&aacute;.<ul><li>No realizar actividad alguna que pueda perjudicar a <span class="bold">MOTOR GAS COMPANY S.A.</span></li><li> Por la naturaleza propia de las labores que desempe&ntilde;a el contratado, deber&aacute; tener disposici&oacute;n para efectuar viajes por todas las distintas ciudades del Pa&iacute;s.</li></ul></div>

    <div class="content"><span class="bold">QUINTO: EL CONTRATADO</span> en caso de una renuncia voluntaria deber&aacute; avisar a la Entidad Certificadora con plazo m&iacute;nimo de 15 d&iacute;as y a su vez deber&aacute; guardar confidencialidad, no brindar informaci&oacute;n de la Entidad Certificadora a otras personas que no pertenecen a la empresa, en caso de no cumplir la cl&aacute;usula habr&aacute; una penalidad por parte de la Entidad Certificadora.</div>

    <div class="content" style="margin-top: 20px;">En caso de que el taller asignado dejar&aacute; de laboral con nosotros o solicita el cambio de su persona, por falta de taller; se terminar&aacute; el contrato. Ambas partes se ratifican en todos los extremos y firman en aceptaci&oacute;n del presente contrato a los <span class="bold">{{ $contrato->fecha_inicio_contrato->format('d') }}</span> d&iacute;as de <span class="bold">{{ $contrato->fecha_inicio_contrato->translatedFormat('F') }}</span> del a&ntilde;o <span class="bold">{{ $contrato->fecha_inicio_contrato->format('Y') }}</span>.</div>

    <div class="signatures-wrapper">
        <table style="width: 100%;">
            <tr>
                <td><div class="signature-container"><img src="{{ public_path('images/firmaing.jpeg') }}" class="firma-img"><div class="line"></div><span class="bold">Lopez Henriquez Spasoje Bratzo</span><br><span style="font-size: 10px;">DNI: 09551431</span><br><span style="font-size: 10px;">Gerente General</span></div></td>
                <td><div class="signature-container"><div class="line"></div><span class="bold">{{ $contrato->user->name }}</span><br><span style="font-size: 10px;">DNI: {{ $contrato->user->dni }}</span><br><span style="font-size: 10px;">EL TRABAJADOR</span></div></td>
            </tr>
        </table>
    </div>

    <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n Automotriz/Arturo Motors</p>
</body>
</html>
