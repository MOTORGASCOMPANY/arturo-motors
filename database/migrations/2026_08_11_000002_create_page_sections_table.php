<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->string('key');                  // 'hero', 'about', 'services', 'process', 'contact', 'footer'
            $table->string('title')->nullable();    // Titulo de la seccion
            $table->text('subtitle')->nullable();   // Subtitulo
            $table->text('description')->nullable(); // Texto largo
            $table->json('settings')->nullable();   // Config extra flexible (colores, links, etc)
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['page_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
