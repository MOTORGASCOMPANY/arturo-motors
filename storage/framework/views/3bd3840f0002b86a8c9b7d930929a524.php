<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        table.datos {
            width: 100%;
            margin-bottom: 15px;
        }

        table.datos td {
            padding: 3px 0;
        }

        .terminos {
            margin-top: 15px;
        }

        .terminos ul {
            padding-left: 18px;
        }

        table.equipos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.equipos th {
            background: #f3f4f6;
            text-align: left;
            padding: 6px 8px;
            font-size: 11px;
        }

        table.equipos td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .firmas {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .firma {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .firma .linea {
            border-top: 1px solid #333;
            margin: 40px 20px 5px 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Carta de Garantía</h2>
        <p>Orden #<?php echo e($orden->id); ?> — <?php echo e(now()->format('d/m/Y')); ?></p>
    </div>

    <table class="datos">
        <tr>
            <td width="15%"><strong>Cliente:</strong></td>
            <td><?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?> — <?php echo e($orden->cliente->documento); ?></td>
        </tr>
        <tr>
            <td><strong>Vehículo:</strong></td>
            <td><?php echo e($orden->vehiculo->placa); ?> — <?php echo e($orden->vehiculo->marca); ?> <?php echo e($orden->vehiculo->modelo); ?></td>
        </tr>
        <tr>
            <td><strong>Servicio:</strong></td>
            <td><?php echo e($orden->service->nombre); ?></td>
        </tr>
    </table>

    <h3>Equipos garantizados</h3>
    <table class="equipos">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Marca</th>
                <th>N.° de serie</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $orden->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->producto->nombre); ?></td>
                    <td><?php echo e($item->producto->marca); ?></td>
                    <td><strong><?php echo e($item->serie); ?></strong></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="terminos">
        <h3>Términos de la garantía</h3>
        <ul>
            <li>El taller garantiza el correcto funcionamiento de los equipos e instalación detallados arriba por un
                periodo de [ __ ] meses o [ __ ] kilómetros, lo que ocurra primero, contados desde la fecha de entrega
                del vehículo.</li>
            <li>La garantía cubre defectos de fabricación de los equipos y errores de instalación atribuibles al taller.
            </li>
            <li>La garantía no cubre daños derivados de mal uso, manipulación por terceros no autorizados, accidentes, o
                falta de mantenimiento del sistema de conversión.</li>
            <li>Para hacer válida la garantía, el cliente debe presentar este documento junto con el comprobante de
                pago.</li>
        </ul>
    </div>

    <div class="firmas">
        <div class="firma">
            <div class="linea"></div>
            Firma del taller
        </div>
        <div class="firma">
            <div class="linea"></div>
            Firma del cliente
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\Arturo\resources\views/pdfs/garantia.blade.php ENDPATH**/ ?>