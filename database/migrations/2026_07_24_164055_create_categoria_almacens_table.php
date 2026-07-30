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
        Schema::create('categorias_almacen', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('es_serializado')->default(false);
            $table->json('esquema_atributos')->nullable(); // ej: ["serie","marca","generacion"]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_almacens');
    }
};
