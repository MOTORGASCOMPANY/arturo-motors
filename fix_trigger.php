<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Drop the old trigger that only counts efectivo
DB::unprepared('DROP TRIGGER IF EXISTS trg_sesiones_caja_before_update');

// Recreate with ALL payment methods
DB::unprepared("
CREATE TRIGGER trg_sesiones_caja_before_update
BEFORE UPDATE ON sesiones_caja
FOR EACH ROW
BEGIN
    IF NEW.estado = 'cerrada' AND OLD.estado = 'abierta' THEN
        SET NEW.monto_esperado = (
            SELECT COALESCE(SUM(CASE
                WHEN tipo = 'ingreso' THEN monto
                WHEN tipo = 'egreso' THEN -monto
                ELSE 0
            END), 0) + NEW.monto_apertura
            FROM movimientos_caja
            WHERE sesion_caja_id = NEW.id
        );
        SET NEW.diferencia = NEW.monto_cierre - NEW.monto_esperado;

        IF ABS(NEW.diferencia) > 50 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Descuadre mayor a S/50. Verifica el monto de cierre.';
        END IF;
    END IF;
END
");

echo "✅ Trigger actualizado: monto_esperado = apertura + TODOS los ingresos - TODOS los egresos\n";
