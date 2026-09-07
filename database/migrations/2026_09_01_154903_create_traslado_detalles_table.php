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
        Schema::create('traslado_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_id')->constrained('traslados')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('item_serializado_id')->nullable()->constrained('items_serializados');
            $table->integer('cantidad')->nullable(); // null si es serializado (siempre es 1 unidad identificada por su serie)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traslado_detalles');
    }
};
