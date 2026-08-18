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
                <td><?php echo e($vehiculo->cliente->nombre ?? '---'); ?></td>
            </tr>
            <tr>
                <th>DNI / CE / RUC:</th>
                <td><?php echo e($vehiculo->cliente->documento ?? '---'); ?></td>
            </tr>
            <tr>
                <th>Dirección:</th>
                <td><?php echo e($vehiculo->cliente->direccion ?? '---'); ?></td>
            </tr>
            <tr>
                <th>Marca / Modelo / Año:</th>
                <td><?php echo e($vehiculo->marca); ?> / <?php echo e($vehiculo->modelo); ?> / <?php echo e($vehiculo->anio); ?></td>
            </tr>
            <tr>
                <th>Cilindrada:</th>
                <td><?php echo e($vehiculo->cilindrada); ?></td>
            </tr>
            <tr>
                <th>Placa:</th>
                <td><?php echo e($vehiculo->placa); ?></td>
            </tr>
            <tr>
                <th>Color:</th>
                <td><?php echo e($vehiculo->color); ?></td>
            </tr>
            <tr>
                <th>Alimentación:</th>
                <td>Dual Gasolina – <?php echo e($vehiculo->alimentacion ?? '---'); ?></td>
            </tr>
            <tr>
                <th>Tanque (GLP/GNV):</th>
                <td><?php echo e($vehiculo->tanque ?? '---'); ?></td>
            </tr>
            <tr>
                <th>Marca / Modelo / N° Serie:</th>
                <td><?php echo e($vehiculo->Tanquemarca); ?> / <?php echo e($vehiculo->Tanquemodelo); ?> / <?php echo e($vehiculo->Tanqueserie); ?></td>
            </tr>
            <tr>
                <th>N° Constancia de Aprobación / N° Expediente:</th>
                <td><?php echo e($vehiculo->Tanqueaprobacion); ?> / <?php echo e($vehiculo->Tanqueexpediente); ?></td>
            </tr>
            <tr>
                <th>Multiválvulas Tipo y Medida:</th>
                <td><?php echo e($vehiculo->multivalvulas ?? '---'); ?></td>
            </tr>
            <tr>
                <th>Toma de carga Tipo:</th>
                <td><?php echo e($vehiculo->toma ?? '---'); ?></td>
            </tr>
            <tr>
                <th>Tipo de equipo:</th>
                <td><?php echo e($vehiculo->equipo ?? '---'); ?></td>
            </tr>
            <tr>
                <th>Reductor Marca / Tipo / N° Serie:</th>
                <td><?php echo e($vehiculo->Equipomarca); ?> / <?php echo e($vehiculo->Equipomodelo); ?> / <?php echo e($vehiculo->Equiposerie); ?></td>
            </tr>
            <tr>
                <th>N° Constancia de Aprobación / N° Expediente:</th>
                <td><?php echo e($vehiculo->Equipoaprobacion); ?> / <?php echo e($vehiculo->Equipoexpediente); ?></td>
            </tr>
            <tr>
                <th>Central de Control conmutación tipo / Electrónica adicional:</th>
                <td><?php echo e($vehiculo->central); ?> / <?php echo e($vehiculo->electronica); ?></td>
            </tr>
            <tr>
                <th>Otros dispositivos instalados:</th>
                <td><?php echo e($vehiculo->otros); ?></td>
            </tr>
            <tr>
                <th>Fecha de Instalación:</th>
                <td><?php echo e($vehiculo->expediente->created_at->format('d/m/Y') ?? '---'); ?></td>
            </tr>
            <tr>
                <th>Kilometraje Actual:</th>
                <td><?php echo e($vehiculo->kilometraje ?? '---'); ?></td>
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
<?php /**PATH C:\xampp\htdocs\Lifegas\resources\views/pdfs/manual.blade.php ENDPATH**/ ?>