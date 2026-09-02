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
        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto','vendido','abierto') NOT NULL DEFAULT 'en_stock'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto','vendido') NOT NULL DEFAULT 'en_stock'");
    }
};
