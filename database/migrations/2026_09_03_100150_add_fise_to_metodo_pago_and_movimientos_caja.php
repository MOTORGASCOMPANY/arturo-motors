<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migración mínima para agregar 'fise' como método de pago.
     * 
     * 1. Agrega 'fise' al enum metodo_pago en comprobantes
     * 2. Agrega columna metodo_pago (nullable) a movimientos_caja
     * 3. Backfill: copia metodo_pago de comprobantes → movimientos_caja
     *
     * NO afecta datos existentes.
     */
    public function up(): void
    {
        // 1. Agregar 'fise' al enum de comprobantes (MySQL requires raw ALTER)
        DB::statement("ALTER TABLE comprobantes MODIFY COLUMN metodo_pago 
            ENUM('efectivo','tarjeta','transferencia','otro','fise') 
            NOT NULL DEFAULT 'efectivo'");

        // 2. Agregar columna metodo_pago a movimientos_caja (nullable para no romper egresos)
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->string('metodo_pago', 20)->nullable()->after('tipo');
        });

        // 3. Backfill: copiar metodo_pago desde comprobantes para movimientos con service_order
        DB::statement("
            UPDATE movimientos_caja mc
            INNER JOIN comprobantes c ON c.service_order_id = mc.service_order_id
            SET mc.metodo_pago = c.metodo_pago
            WHERE mc.service_order_id IS NOT NULL
        ");

        // 4. Los egresos (sin service_order) se quedan NULL — no tienen método de pago
    }

    public function down(): void
    {
        // Quitar columna
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });

        // Restaurar enum original
        DB::statement("ALTER TABLE comprobantes MODIFY COLUMN metodo_pago 
            ENUM('efectivo','tarjeta','transferencia','otro') 
            NOT NULL DEFAULT 'efectivo'");
    }
};
