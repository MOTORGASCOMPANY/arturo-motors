<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto','vendido','abierto','en_kit','defectuoso','consumido','completado','reemplazado') NOT NULL DEFAULT 'en_stock'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto','vendido','abierto','en_kit','defectuoso','consumido') NOT NULL DEFAULT 'en_stock'");
    }
};
