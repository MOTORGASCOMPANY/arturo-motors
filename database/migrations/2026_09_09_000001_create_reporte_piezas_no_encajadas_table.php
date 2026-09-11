<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporte_piezas_no_encajadas', function (Blueprint $table) {
            $table->id();
            
            // Relación con la orden de servicio
            $table->foreignId('service_order_id')->constrained('service_orders')->cascadeOnDelete();
            
            // Pieza que no encajó (la que estaba asignada)
            $table->foreignId('item_no_encajado_id')->constrained('items_serializados')->cascadeOnDelete();
            
            // Técnico que reporta
            $table->foreignId('tecnico_id')->constrained('users')->cascadeOnDelete();
            
            // Descripción del problema
            $table->text('motivo_no_encaja')->nullable();
            
            // Estado del reporte
            $table->enum('estado', [
                'pendiente',           // Recién creado
                'buscando_pieza',      // Buscando pieza compatible
                'pieza_encontrada',    // Se encontró pieza suelta
                'solicitando_almacen', // No hay sueltas, solicitando al almacén
                'kit_abierto',         // Almacén abrió kit
                'resuelto',            // Pieza cambiada
                'cancelado'            // Cancelado
            ])->default('pendiente');
            
            // Pieza nueva asignada
            $table->foreignId('item_nuevo_id')->nullable()->constrained('items_serializados')->nullOnDelete();
            
            // Almacén que atendió
            $table->foreignId('almacen_user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Observaciones
            $table->text('observaciones_almacen')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['service_order_id', 'estado']);
            $table->index(['tecnico_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte_piezas_no_encajadas');
    }
};
