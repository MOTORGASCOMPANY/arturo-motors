<?php
/**
 * Script to update MovimientoCaja records with timestamps and concepts
 * Run via: php update_movimientos.php
 */
require __DIR__.'/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'laravel',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

Capsule::setAsGlobal();
Capsule::bootEloquent();

// Update records with timestamps and concepts
$records = [
    ['id' => 1, 'created_at' => '2026-09-02 10:30:00', 'concepto' => 'Apertura de caja'],
    ['id' => 2, 'created_at' => '2026-09-02 14:45:00', 'concepto' => 'Venta con tarjeta'],
    ['id' => 3, 'created_at' => '2026-09-02 09:15:00', 'concepto' => 'Depósito en efectivo'],
    ['id' => 4, 'created_at' => '2026-09-02 16:20:00', 'concepto' => 'Retiro de egresos'],
];

foreach ($records as $record) {
    $model = \App\Models\MovimientoCaja::find($record['id']);
    if ($model) {
        $model->created_at = $record['created_at'];
        $model->concepto = $record['concepto'];
        $model->save();
        echo "Actualizado ID {$record['id']}: {$record['concepto']} a las {$record['created_at']}\n";
    } else {
        echo "No se encontró ID {$record['id']}\n";
    }
}

echo "\n¡Actualización completada!\n";