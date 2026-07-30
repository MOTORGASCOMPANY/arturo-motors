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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias_almacen')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('marca')->nullable();
            $table->json('atributos')->nullable(); // valores según esquema de la categoría
            $table->decimal('precio_referencial', 10, 2)->nullable();
            $table->integer('stock')->default(0); // solo relevante si categoria.es_serializado = false
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
