<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fise_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained('service_orders')->cascadeOnDelete();
            $table->decimal('monto_total', 10, 2);        // precio_final de la orden
            $table->decimal('monto_pagado', 10, 2)->default(0); // cuánto pagó en este registro
            $table->date('fecha_pago');                     // cuándo pagó
            $table->foreignId('pagado_por')->constrained('users'); // quién registró
            $table->enum('estado', ['pendiente', 'parcial', 'pagado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['estado', 'service_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fise_pagos');
    }
};
