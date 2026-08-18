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

        table.datos {
            width: 100%;
            margin-bottom: 15px;
        }

        table.datos td {
            padding: 3px 0;
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
    </style>
</head>

<body>
    <div class="header">
        <h2>Ficha Técnica de Conversión</h2>
        <p>Orden #<?php echo e($orden->id); ?></p>
    </div>

    <table class="datos">
        <tr>
            <td width="15%"><strong>Cliente:</strong></td>
            <td><?php echo e($orden->cliente->nombre); ?> <?php echo e($orden->cliente->apellido); ?> — <?php echo e($orden->cliente->documento); ?></td>
        </tr>
        <tr>
            <td><strong>Vehículo:</strong></td>
            <td><?php echo e($orden->vehiculo->placa); ?> — <?php echo e($orden->vehiculo->marca); ?> <?php echo e($orden->vehiculo->modelo); ?>

                (<?php echo e($orden->vehiculo->anio); ?>)</td>
        </tr>
        <tr>
            <td><strong>Tipo de conversión:</strong></td>
            <td><?php echo e($orden->service->nombre); ?></td>
        </tr>
        <tr>
            <td><strong>Técnico responsable:</strong></td>
            <td><?php echo e($orden->tecnico->name); ?></td>
        </tr>
        <tr>
            <td><strong>Fecha de inicio:</strong></td>
            <td><?php echo e($orden->fecha_inicio_conversion?->format('d/m/Y H:i') ?? '—'); ?></td>
        </tr>
        <tr>
            <td><strong>Fecha de finalización:</strong></td>
            <td><?php echo e($orden->fecha_fin_conversion?->format('d/m/Y H:i') ?? '—'); ?></td>
        </tr>
    </table>

    <h3>Equipos instalados</h3>
    <table class="equipos">
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Producto</th>
                <th>Marca</th>
                <th>N.° de serie</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $orden->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item->producto->categoria->nombre); ?></td>
                    <td><?php echo e($item->producto->nombre); ?></td>
                    <td><?php echo e($item->producto->marca); ?></td>
                    <td><strong><?php echo e($item->serie); ?></strong></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php if($orden->movimientosStock->count()): ?>
        <h3>Repuestos utilizados</h3>
        <table class="equipos">
            <thead>
                <tr>
                    <th>Repuesto</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $orden->movimientosStock; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($mov->producto->nombre); ?></td>
                        <td><?php echo e($mov->cantidad); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\Arturo\resources\views/pdfs/ficha-tecnica.blade.php ENDPATH**/ ?>