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
    
    <div class="header">
        <img src="<?php echo e(public_path('images/header2.png')); ?>" alt="Encabezado">
    </div>

    
    <h2 style="text-align: center;">HOJA DE RECEPCION</h2>
    <table class="info-table">
        <tr>
            <td style="width: 50%;"><span class="field-label">Fecha de ingreso al taller:</span> <?php echo e($fechaIngreso); ?></td>
            <td style="width: 50%;"><span class="field-label">Fecha de salida al taller:</span> <?php echo e($fechaSalida); ?></td>
        </tr>
    </table>

    
    <div class="section-title">DATOS DEL DUEÑO</div>
    <table class="info-table">
        <tr>
            <td style="width: 48%;"><span class="field-label">Nombre:</span> <?php echo e($nombreCliente); ?></td>
            <td style="width: 26%;"><span class="field-label">DNI:</span> <?php echo e($dniCliente); ?></td>
            <td style="width: 26%;"><span class="field-label">Teléfono:</span> <?php echo e($telefonoCliente); ?></td>
        </tr>
    </table>

    
    <div class="section-title">DATOS Y CARACTERISTICAS DEL VEHICULO</div>
    <table class="info-table">
        <tr>
            <td style="width: 33.33%;"><span class="field-label">Placa Actual:</span> <?php echo e($placaVehiculo); ?></td>
            <td style="width: 33.33%;"><span class="field-label">Placa Anterior:</span> <?php echo e($placaAnteriorVehiculo); ?>

            </td>
            <td style="width: 33.33%;"><span class="field-label">Marca:</span> <?php echo e($marcaVehiculo); ?></td>
        </tr>
        <tr>
            <td style="width: 33.33%;"><span class="field-label">Modelo:</span> <?php echo e($modeloVehiculo); ?></td>
            <td style="width: 33.33%;"><span class="field-label">N° Motor:</span> <?php echo e($motorVehiculo); ?></td>
            <td style="width: 33.33%;"><span class="field-label">Color:</span> <?php echo e($colorVehiculo); ?></td>
        </tr>
        <tr>
            <td style="width: 33.33%;"><span class="field-label">Año:</span> <?php echo e($anioVehiculo); ?></td>
            <td style="width: 33.33%;"><span class="field-label">Combustible:</span> <?php echo e($combustibleVehiculo); ?></td>
            <td style="width: 33.33%;"><span class="field-label">Kilometraje:</span> <?php echo e($kilometrajeVehiculo); ?></td>
        </tr>
    </table>

    
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
            <?php
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
            ?>
            <?php for($i = 0; $i < $items_per_column; $i++): ?>
                <tr>
                    <?php if($i === 0): ?>
                        <td class="carro-esquema" rowspan="<?php echo e($items_per_column); ?>"
                            style="background-image: url('<?php echo e(public_path('images/carro.png')); ?>');">
                        </td>
                    <?php endif; ?>
                    <?php $__currentLoopData = $chunks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $key = array_keys($column)[$i] ?? null;
                            $value = $key ? $column[$key] : null;
                            $nombre_accesorio = ucwords(str_replace('_', ' ', $key));
                        ?>
                        <?php if($key): ?>
                            <td><?php echo e($nombre_accesorio); ?></td>
                            <td class="center-text">
                                <?php if($value === 1): ?>
                                    <img src="<?php echo e($svg); ?>" alt="Sí" class="check-icon">
                                <?php endif; ?>
                            </td>
                            <td class="center-text">
                                <?php if($value === 0): ?>
                                    <img src="<?php echo e($svg); ?>" alt="No" class="check-icon">
                                <?php endif; ?>
                            </td>
                        <?php else: ?>
                            <td></td>
                            <td></td>
                            <td></td>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    
    <div class="section-title">OBSERVACIONES</div>
    <table class="info-table" style="">
        <tr>
            <td style="width:100%;"><?php echo e($observaciones); ?></td>
        </tr>
    </table>

    
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
<?php /**PATH C:\xampp\htdocs\arturo-motors\resources\views\pdfs\ficha-evaluacion.blade.php ENDPATH**/ ?>