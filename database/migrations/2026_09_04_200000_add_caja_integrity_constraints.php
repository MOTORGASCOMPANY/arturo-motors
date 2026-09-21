<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── movimientos_caja ───────────────────────────────────────────
        // 1. metodo_pago: convertir de string a ENUM con valores permitidos
        DB::statement("
            ALTER TABLE movimientos_caja
            MODIFY COLUMN metodo_pago
            ENUM('efectivo','tarjeta','transferencia','fise','otro')
            NULL DEFAULT NULL
            COMMENT 'Solo ingresos llevan metodo_pago. Egresos = NULL.'
        ");

        // 2. monto: debe ser siempre positivo (> 0)
        DB::statement("
            ALTER TABLE movimientos_caja
            ADD CONSTRAINT chk_monto_positivo
            CHECK (monto > 0)
        ");

        // 3. Ingresos DEBEN tener metodo_pago; egresos NO deben tenerlo
        //    (MySQL CHECK no soporta IF, así que lo manejamos en el modelo)
        //    Pero sí podemos crear un trigger para rechazar datos inválidos:
        DB::statement("
            CREATE TRIGGER trg_movimientos_caja_before_insert
            BEFORE INSERT ON movimientos_caja
            FOR EACH ROW
            BEGIN
                -- Ingresos deben tener metodo_pago
                IF NEW.tipo = 'ingreso' AND NEW.metodo_pago IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Los ingresos deben tener un metodo_pago válido.';
                END IF;
                -- Egresos NO deben tener metodo_pago
                IF NEW.tipo = 'egreso' AND NEW.metodo_pago IS NOT NULL THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Los egresos no deben tener metodo_pago.';
                END IF;
            END
        ");

        DB::statement("
            CREATE TRIGGER trg_movimientos_caja_before_update
            BEFORE UPDATE ON movimientos_caja
            FOR EACH ROW
            BEGIN
                IF NEW.tipo = 'ingreso' AND NEW.metodo_pago IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Los ingresos deben tener un metodo_pago válido.';
                END IF;
                IF NEW.tipo = 'egreso' AND NEW.metodo_pago IS NOT NULL THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Los egresos no deben tener metodo_pago.';
                END IF;
            END
        ");

        // ─── sesiones_caja ─────────────────────────────────────────────
        // 4. monto_apertura: no puede ser negativo
        DB::statement("
            ALTER TABLE sesiones_caja
            ADD CONSTRAINT chk_apertura_no_negativa
            CHECK (monto_apertura >= 0)
        ");

        // 5. monto_cierre: cuando no es NULL, debe ser >= 0
        DB::statement("
            ALTER TABLE sesiones_caja
            ADD CONSTRAINT chk_cierre_no_negativo
            CHECK (monto_cierre IS NULL OR monto_cierre >= 0)
        ");

        // 6. diferencia: trigger que calcula automáticamente al cerrar
        //    y previene cierres con diferencia > 50 (alerta de descuadre grave)
        DB::statement("
            CREATE TRIGGER trg_sesiones_caja_before_update
            BEFORE UPDATE ON sesiones_caja
            FOR EACH ROW
            BEGIN
                -- Solo aplica cuando se está cerrando la sesión
                IF NEW.estado = 'cerrada' AND OLD.estado = 'abierta' THEN
                    -- El monto_esperado se calcula: apertura + efectivo - egresos
                    -- Lo calculamos aquí como安全 net, pero el modelo ya lo hace
                    SET NEW.monto_esperado = (
                        SELECT COALESCE(SUM(CASE
                            WHEN tipo = 'ingreso' AND metodo_pago = 'efectivo' THEN monto
                            WHEN tipo = 'egreso' THEN -monto
                            ELSE 0
                        END), 0) + NEW.monto_apertura
                        FROM movimientos_caja
                        WHERE sesion_caja_id = NEW.id
                    );
                    SET NEW.diferencia = NEW.monto_cierre - NEW.monto_esperado;

                    -- Prevenir cierres con descuadre mayor a S/50
                    IF ABS(NEW.diferencia) > 50 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Descuadre mayor a S/50. Verifica el monto de cierre.';
                    END IF;
                END IF;
            END
        ");
    }

    public function down(): void
    {
        // Eliminar triggers
        DB::statement("DROP TRIGGER IF EXISTS trg_movimientos_caja_before_insert");
        DB::statement("DROP TRIGGER IF EXISTS trg_movimientos_caja_before_update");
        DB::statement("DROP TRIGGER IF EXISTS trg_sesiones_caja_before_update");

        // Eliminar check constraints
        DB::statement("ALTER TABLE movimientos_caja DROP CONSTRAINT IF EXISTS chk_monto_positivo");
        DB::statement("ALTER TABLE sesiones_caja DROP CONSTRAINT IF EXISTS chk_apertura_no_negativa");
        DB::statement("ALTER TABLE sesiones_caja DROP CONSTRAINT IF EXISTS chk_cierre_no_negativo");

        // Restaurar metodo_pago a string
        DB::statement("
            ALTER TABLE movimientos_caja
            MODIFY COLUMN metodo_pago VARCHAR(20) NULL DEFAULT NULL
        ");
    }
};
