<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_info', function (Blueprint $table) {
            $table->id();
            $table->string('type');           // 'address', 'phone', 'schedule', 'whatsapp', 'map_iframe'
            $table->string('label');          // 'Direccion del Taller', 'Telefonos', etc.
            $table->text('value');            // El valor (texto, numero, iframe, link)
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_info');
    }
};
