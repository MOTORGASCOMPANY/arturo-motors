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
        // 1. Tabla Contratos
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->date('fecha_ingreso');
            $table->date('fecha_inicio_contrato');
            $table->date('fecha_vencimiento')->nullable();

            $table->string('cargo', 150);
            $table->string('tipo_contrato', 100)->default('Plazo Fijo');

            $table->decimal('sueldo_bruto', 10, 2)->default(0.00);
            $table->decimal('sueldo_neto', 10, 2)->default(0.00);

            $table->enum('status', ['Activo', 'Vencido', 'Finalizado'])->default('Activo');
            $table->string('contrato_path', 255)->nullable();
            $table->timestamps();
        });

        // 2. Tabla Vacaciones
        Schema::create('vacaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idContrato')->constrained('contratos')->cascadeOnDelete();
            $table->integer('dias_ganados')->default(0);
            $table->integer('dias_tomados')->default(0);
            $table->integer('dias_restantes')->default(0);
            $table->timestamps();
        });

        // 3. Tabla Vacacion Asignada
        Schema::create('vacacion_asignada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idVacacion')->constrained('vacaciones')->cascadeOnDelete();
            $table->string('tipo')->nullable();
            $table->text('razon')->nullable();
            $table->integer('d_tomados')->nullable();
            $table->date('f_inicio')->nullable();
            $table->text('observacion')->nullable();
            $table->boolean('especial')->default(false);
            $table->timestamps();
        });

        // 4. Tabla Tipo Documentos
        Schema::create('tipo_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->boolean('requerido')->default(false);
            $table->boolean('vencible')->default(false);
            $table->timestamps();
        });

        // 5. Tabla Documentos Usuarios (Legajo Digital)
        Schema::create('documentos_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tipo_documento_id')->constrained('tipo_documentos');
            $table->string('nombre', 255)->nullable();
            $table->string('ruta', 255);
            $table->string('extension', 10)->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_expiracion')->nullable();
            $table->enum('estado', ['Pendiente', 'Aprobado', 'Rechazado'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // 6. Tabla Planillas
        Schema::create('planillas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnDelete();
            $table->date('periodo');

            $table->decimal('sueldo_base', 10, 2)->default(0.00);
            $table->decimal('asignacion_familiar', 10, 2)->default(0.00);
            $table->decimal('horas_extras', 10, 2)->default(0.00);
            $table->decimal('movilidad', 10, 2)->default(0.00);
            $table->decimal('otros_ingresos', 10, 2)->default(0.00);
            $table->decimal('otros_descuentos', 10, 2)->default(0.00);

            // Columna calculada virtual nativa en Laravel
            $table->decimal('total_pagado', 10, 2)
                ->virtualAs('sueldo_base + asignacion_familiar + horas_extras + movilidad + otros_ingresos - otros_descuentos');

            $table->string('planilla', 255)->nullable();
            $table->boolean('estado_pago')->default(false)->comment('0 = Pendiente, 1 = Pagado');
            $table->date('fecha_pago')->nullable();
            $table->string('numero_cuenta', 50)->nullable();
            $table->text('observacion')->nullable();

            $table->timestamps();
        });

        // 7. Tabla Planilla Archivos
        Schema::create('planilla_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planilla_id')->constrained('planillas')->cascadeOnDelete();
            $table->enum('tipo', ['boleta', 'comprobante', 'otros'])->default('boleta');
            $table->string('nombre', 255);
            $table->string('ruta', 255);
            $table->string('extension', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planilla_archivos');
        Schema::dropIfExists('planillas');
        Schema::dropIfExists('documentos_usuarios');
        Schema::dropIfExists('tipo_documentos');
        Schema::dropIfExists('vacacion_asignada');
        Schema::dropIfExists('vacaciones');
        Schema::dropIfExists('contratos');
    }
};
