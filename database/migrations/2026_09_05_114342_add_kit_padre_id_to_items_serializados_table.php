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
            $table->foreignId('kit_padre_id')->nullable()->after('producto_id')
                ->constrained('items_serializados')->nullOnDelete();
        });

        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto','vendido','abierto','en_kit') NOT NULL DEFAULT 'en_stock'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items_serializados', function (Blueprint $table) {
            $table->dropForeign(['kit_padre_id']);
            $table->dropColumn('kit_padre_id');
        });
        DB::statement("ALTER TABLE items_serializados MODIFY estado ENUM('en_stock','asignado','instalado','devuelto','vendido','abierto') NOT NULL DEFAULT 'en_stock'");
    }
};
