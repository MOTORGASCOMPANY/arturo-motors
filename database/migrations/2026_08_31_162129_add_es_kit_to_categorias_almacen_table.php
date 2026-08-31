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
        Schema::table('categorias_almacen', function (Blueprint $table) {
            $table->boolean('es_kit')->default(false)->after('es_serializado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias_almacen', function (Blueprint $table) {
            $table->dropColumn('es_kit');
        });
    }
};
