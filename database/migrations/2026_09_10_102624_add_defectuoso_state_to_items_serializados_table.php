<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items_serializados', function (Blueprint $table) {
            $table->enum('estado', [
                'en_stock', 'asignado', 'instalado', 'devuelto', 'vendido', 'abierto', 'en_kit', 'defectuoso'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items_serializados', function (Blueprint $table) {
            $table->enum('estado', [
                'en_stock', 'asignado', 'instalado', 'devuelto', 'vendido', 'abierto', 'en_kit'
            ])->change();
        });
    }
};
