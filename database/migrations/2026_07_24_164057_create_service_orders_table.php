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
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('cita_id')->nullable()->constrained('citas');

            $table->string('estado')->default('creada');
            // simples: creada, entregada, cancelada
            // conversion: en_evaluacion, evaluacion_rechazada, aprobado_conversion,
            //             en_conversion, conversion_completada, en_control_calidad,
            //             listo_para_entrega, entregado, cancelado

            $table->decimal('precio_lista', 10, 2);
            $table->decimal('precio_final', 10, 2);
            $table->string('descuento_motivo')->nullable();
            $table->foreignId('descuento_autorizado_por')->nullable()->constrained('users');

            // Evaluación (solo conversión)
            $table->json('checklist_evaluacion')->nullable();
            $table->boolean('evaluacion_aprobada')->nullable();
            $table->text('evaluacion_observaciones')->nullable();
            $table->foreignId('evaluado_por')->nullable()->constrained('users');
            $table->dateTime('evaluado_en')->nullable();

            // Conversión
            $table->foreignId('tecnico_id')->nullable()->constrained('users');
            $table->dateTime('fecha_inicio_conversion')->nullable();
            $table->dateTime('fecha_fin_conversion')->nullable();

            $table->foreignId('creado_por')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};
