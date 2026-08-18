<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();           // Clase FontAwesome, ej: 'fa-solid fa-gas-pump'
            $table->json('features')->nullable();         // Lista de features: ["Equipos italianas", "Garantia 1 año"]
            $table->string('cta_text')->nullable();       // Texto del boton CTA
            $table->string('cta_link')->nullable();       // Link del boton (WhatsApp con mensaje predefinido)
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_services');
    }
};
