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
        // 1. Modificar tabla clientes
        Schema::table('clientes', function (Blueprint $table) {
            $table->enum('tipo_persona', ['NATURAL', 'JURIDICA'])->default('NATURAL')->after('id');
            $table->string('nombre')->nullable()->change();
            $table->string('razon_social')->nullable()->after('apellido');
        });

        // 2. Crear tabla pivot cliente_vehiculo
        Schema::create('cliente_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->boolean('es_principal')->default(true);
            $table->string('relacion')->default('Propietario');
            $table->timestamps();
        });

        // 3. Eliminar la clave foránea y columna cliente_id de vehiculos
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropForeign(['cliente_id']);
            $table->dropColumn('cliente_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->cascadeOnDelete();
        });

        Schema::dropIfExists('cliente_vehiculo');

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['tipo_persona', 'razon_social']);
            $table->string('nombre')->nullable(false)->change();
        });
    }
};
