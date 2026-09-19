<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kit_piezas_extraidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_serializado_id')
                  ->constrained('items_serializados')
                  ->onDelete('cascade')
                  ->comment('Kit padre del que se extrajo la pieza');
            $table->foreignId('producto_componente_id')
                  ->constrained('productos')
                  ->onDelete('cascade')
                  ->comment('Producto de la pieza extraída');
            $table->unsignedInteger('cantidad_extraida')->default(1);
            $table->foreignId('service_order_id')
                  ->nullable()
                  ->constrained('service_orders')
                  ->onDelete('set null')
                  ->comment('Orden de servicio que solicitó la pieza');
            $table->foreignId('extraida_por')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamp('extraida_en');
            $table->timestamps();

            $table->index(['item_serializado_id', 'producto_componente_id'], 'kpe_kit_producto_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kit_piezas_extraidas');
    }
};
