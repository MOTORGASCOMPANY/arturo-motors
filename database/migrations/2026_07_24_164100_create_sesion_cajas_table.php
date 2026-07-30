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
        Schema::create('sesiones_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abierta_por')->constrained('users');
            $table->decimal('monto_apertura', 10, 2);
            $table->dateTime('abierta_en');
            $table->dateTime('cerrada_en')->nullable();
            $table->decimal('monto_cierre', 10, 2)->nullable();
            $table->decimal('monto_esperado', 10, 2)->nullable(); // calculado al cerrar
            $table->decimal('diferencia', 10, 2)->nullable(); // monto_cierre - monto_esperado
            $table->foreignId('cerrada_por')->nullable()->constrained('users');
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesion_cajas');
    }
};
