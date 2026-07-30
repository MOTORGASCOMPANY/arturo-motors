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
        Schema::create('items_serializados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('serie')->unique();
            $table->json('atributos')->nullable(); // datos propios de esa unidad física
            $table->enum('estado', ['en_stock', 'asignado', 'instalado', 'devuelto'])->default('en_stock');
            $table->foreignId('service_order_id')->nullable()->constrained('service_orders');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_serializados');
    }
};
