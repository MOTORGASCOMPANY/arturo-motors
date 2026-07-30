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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos');
            $table->foreignId('asesor_id')->nullable()->constrained('users'); // vendedor interno
            $table->foreignId('asesor_externo_id')->nullable()->constrained('asesores_externos');
            $table->dateTime('fecha_cita');
            $table->string('motivo')->nullable();
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada', 'cancelada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
