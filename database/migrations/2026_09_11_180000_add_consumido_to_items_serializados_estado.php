<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto','vendido','abierto','en_kit','defectuoso','consumido') NOT NULL");
    }

    public function down(): void
    {
        // Before removing 'consumido', need to reset any items using it
        DB::table('items_serializados')
            ->where('estado', 'consumido')
            ->update(['estado' => 'abierto']);

        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto','vendido','abierto','en_kit','defectuoso') NOT NULL");
    }
};
