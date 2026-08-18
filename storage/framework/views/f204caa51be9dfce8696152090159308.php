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
        <p>Orden #<?php echo e($orden->id); ?></p>
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
        <tr>
            <td><strong>Evaluado por:</strong></td>
            <td><?php echo e($orden->evaluadoPor->name); ?> — <?php echo e($orden->evaluado_en->format('d/m/Y H:i')); ?></td>
        </tr>
    </table>

    <?php $__currentLoopData = $checklistGrupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="grupo-titulo"><?php echo e($grupo); ?></div>
        <table class="checklist">
            <tr>
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clave => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td class="<?php echo e($orden->checklist_evaluacion[$clave] ?? false ? 'ok' : 'no'); ?>">
                        <?php echo e($orden->checklist_evaluacion[$clave] ?? false ? '[X]' : '[ ]'); ?> <?php echo e($label); ?>

                    </td>
                    <?php if($loop->iteration % 4 == 0): ?>
            </tr>
            <tr>
    <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tr>
    </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if($orden->evaluacion_observaciones): ?>
        <div class="observaciones">
            <strong>Observaciones:</strong><br><?php echo e($orden->evaluacion_observaciones); ?>

        </div>
    <?php endif; ?>

    <div class="resultado <?php echo e($orden->evaluacion_aprobada ? 'apto' : 'no-apto'); ?>">
        <?php echo e($orden->evaluacion_aprobada ? 'APTO PARA CONVERSIÓN' : 'NO APTO PARA CONVERSIÓN'); ?>

    </div>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\Arturo\resources\views/pdfs/evaluacion.blade.php ENDPATH**/ ?>