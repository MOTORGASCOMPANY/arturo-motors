<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items_serializados', function (Blueprint $table) {
            $table->foreignId('sede_id')->default(1)->after('estado')->constrained('sedes');
            $table->foreignId('vehiculo_instalado_id')->nullable()->after('service_order_id')->constrained('vehiculos');
            $table->dateTime('fecha_instalacion_reportada')->nullable()->after('vehiculo_instalado_id');
        });

        // MySQL no permite agregar un valor a un ENUM con Schema::table, se modifica con SQL directo
        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto','vendido') NOT NULL DEFAULT 'en_stock'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items_serializados', function (Blueprint $table) {
            $table->dropForeign(['sede_id']);
            $table->dropForeign(['vehiculo_instalado_id']);
            $table->dropColumn(['sede_id', 'vehiculo_instalado_id', 'fecha_instalacion_reportada']);
        });

        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto') NOT NULL DEFAULT 'en_stock'");
    }
};
