<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        table.datos {
            width: 100%;
            margin-bottom: 15px;
        }

        table.datos td {
            padding: 3px 0;
        }

        .grupo-titulo {
            font-weight: bold;
            background: #f3f4f6;
            padding: 4px 8px;
            margin-top: 12px;
        }

        table.checklist {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table.checklist td {
            width: 25%;
            padding: 3px 8px;
            font-size: 11px;
        }

        .ok {
            color: #059669;
        }

        .no {
            color: #9ca3af;
        }

        .observaciones {
            margin-top: 15px;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .resultado {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            margin-top: 15px;
        }

        .resultado.apto {
            background: #d1fae5;
            color: #065f46;
        }

        .resultado.no-apto {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Ficha de Evaluación Técnica Previa</h2>
        <p>Orden #{{ $orden->id }}</p>
    </div>

    <table class="datos">
        <tr>
            <td width="15%"><strong>Cliente:</strong></td>
            <td>{{ $orden->cliente->nombre }} {{ $orden->cliente->apellido }} — {{ $orden->cliente->documento }}</td>
        </tr>
        <tr>
            <td><strong>Vehículo:</strong></td>
            <td>{{ $orden->vehiculo->placa }} — {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }}</td>
        </tr>
        <tr>
            <td><strong>Servicio:</strong></td>
            <td>{{ $orden->service->nombre }}</td>
        </tr>
        <tr>
            <td><strong>Evaluado por:</strong></td>
            <td>{{ $orden->evaluadoPor->name }} — {{ $orden->evaluado_en->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    @foreach ($checklistGrupos as $grupo => $items)
        <div class="grupo-titulo">{{ $grupo }}</div>
        <table class="checklist">
            <tr>
                @foreach ($items as $clave => $label)
                    <td class="{{ $orden->checklist_evaluacion[$clave] ?? false ? 'ok' : 'no' }}">
                        {{ $orden->checklist_evaluacion[$clave] ?? false ? '[X]' : '[ ]' }} {{ $label }}
                    </td>
                    @if ($loop->iteration % 4 == 0)
            </tr>
            <tr>
    @endif
    @endforeach
    </tr>
    </table>
    @endforeach

    @if ($orden->evaluacion_observaciones)
        <div class="observaciones">
            <strong>Observaciones:</strong><br>{{ $orden->evaluacion_observaciones }}
        </div>
    @endif

    <div class="resultado {{ $orden->evaluacion_aprobada ? 'apto' : 'no-apto' }}">
        {{ $orden->evaluacion_aprobada ? 'APTO PARA CONVERSIÓN' : 'NO APTO PARA CONVERSIÓN' }}
    </div>
</body>

</html>
