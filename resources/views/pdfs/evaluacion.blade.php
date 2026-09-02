<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 50px 60px 60px 60px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #555; line-height: 1.5; margin: 0; padding: 0; }
    table.header { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .brand-title { font-size: 26px; font-weight: bold; color: #1565c0; margin: 0; letter-spacing: 1px; }
    .brand-subtitle { font-size: 10px; color: #999; margin: 2px 0 0 0; letter-spacing: 2px; text-transform: uppercase; }
    .ruc-cell { text-align: right; }
    .ruc-label { font-size: 10px; color: #999; }
    .ruc-value { font-size: 11px; font-weight: bold; color: #1565c0; }
    .rd-value { font-size: 9px; color: #999; }
    h1.titulo { text-align: center; font-size: 16px; font-weight: bold; color: #1a1a1a; margin: 20px 0 25px 0; text-decoration: underline; }
    table.datos { width: 100%; margin-bottom: 15px; border: 1px solid #eee; }
    table.datos td { padding: 4px 8px; border-bottom: 1px solid #f5f5f5; color: #555; }
    table.datos td strong { color: #333; }
    .grupo-titulo { font-weight: bold; background: #e0e0e0; color: #333; padding: 4px 8px; margin-top: 12px; font-size: 11px; }
    table.checklist { width: 100%; border-collapse: collapse; margin-top: 5px; border: 1px solid #eee; }
    table.checklist td { width: 25%; padding: 3px 8px; font-size: 10px; border-bottom: 1px solid #f5f5f5; color: #555; }
    .ok { color: #333; font-weight: bold; }
    .no { color: #aaa; }
    .observaciones { margin-top: 15px; padding: 10px; background: #f9f9f9; border: 1px solid #eee; color: #555; }
    .resultado { text-align: center; font-size: 14px; font-weight: bold; padding: 10px; margin-top: 15px; }
    .resultado.apto { background: #e0e0e0; color: #333; }
    .resultado.no-apto { background: #f5f5f5; color: #666; }
    .footer-note { margin-top: 40px; text-align: center; font-size: 8px; color: #aaa; }
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

    <h1 class="titulo">FICHA DE EVALUACI&Oacute;N T&Eacute;CNICA PREVIA</h1>
    <p style="text-align: right; font-size: 10px; color: #999;">Orden #{{ $orden->id }}</p>

    <table class="datos">
        <tr><td width="15%"><strong>Cliente:</strong></td><td>{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }} &mdash; {{ $orden->cliente->documento }}</td></tr>
        <tr><td><strong>Veh&iacute;culo:</strong></td><td>{{ $orden->vehiculo->placa }} &mdash; {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</td></tr>
        <tr><td><strong>Servicio:</strong></td><td>{{ $orden->service->nombre }}</td></tr>
        <tr><td><strong>Evaluado por:</strong></td><td>{{ $orden->evaluadoPor->name }} &mdash; {{ $orden->evaluado_en->format('d/m/Y H:i') }}</td></tr>
    </table>

    @foreach ($checklistGrupos as $grupo => $items)
        <div class="grupo-titulo">{{ $grupo }}</div>
        <table class="checklist">
            <tr>
                @foreach ($items as $clave => $label)
                    <td class="{{ $orden->checklist_evaluacion[$clave] ?? false ? 'ok' : 'no' }}">{{ $orden->checklist_evaluacion[$clave] ?? false ? '[X]' : '[ ]' }} {{ $label }}</td>
                    @if ($loop->iteration % 4 == 0)</tr><tr>@endif
                @endforeach
            </tr>
        </table>
    @endforeach

    @if ($orden->evaluacion_observaciones)
        <div class="observaciones"><strong>Observaciones:</strong><br>{{ $orden->evaluacion_observaciones }}</div>
    @endif

    <div class="resultado {{ $orden->evaluacion_aprobada ? 'apto' : 'no-apto' }}">{{ $orden->evaluacion_aprobada ? 'APTO PARA CONVERSI&Oacute;N' : 'NO APTO PARA CONVERSI&Oacute;N' }}</div>

    <p class="footer-note">Documento generado por el Sistema de Gesti&oacute;n Automotriz/Arturo Motors</p>
</body>
</html>
