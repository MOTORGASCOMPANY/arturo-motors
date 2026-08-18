<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Nombre descriptivo
            $table->string('file_path');                     // Ruta del archivo en storage
            $table->string('file_type');                     // 'image', 'video', 'document'
            $table->string('mime_type')->nullable();         // 'image/jpeg', 'video/mp4', etc
            $table->unsignedBigInteger('file_size')->nullable(); // Bytes
            $table->string('alt_text')->nullable();          // Texto alternativo (accesibilidad + SEO)
            $table->string('caption')->nullable();           // Pie de foto
            $table->json('meta')->nullable();                // Dimensiones, duracion, etc
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
