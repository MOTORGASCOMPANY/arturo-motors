<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traslados', function (Blueprint $table) {
            $table->boolean('es_kit_completo')->nullable()->after('observaciones');
            $table->text('componentes_faltantes')->nullable()->after('es_kit_completo');
        });
    }

    public function down(): void
    {
        Schema::table('traslados', function (Blueprint $table) {
            $table->dropColumn(['es_kit_completo', 'componentes_faltantes']);
        });
    }
};
