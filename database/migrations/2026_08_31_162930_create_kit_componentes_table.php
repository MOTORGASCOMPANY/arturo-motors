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
        Schema::create('kit_componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_kit_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('producto_componente_id')->constrained('productos')->cascadeOnDelete();
            $table->integer('cantidad_esperada')->default(1);
            $table->timestamps();

            $table->unique(['producto_kit_id', 'producto_componente_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kit_componentes');
    }
};
