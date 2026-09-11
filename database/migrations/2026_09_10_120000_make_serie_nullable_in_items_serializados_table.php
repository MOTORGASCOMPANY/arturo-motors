<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the unique index first, modify column, then re-add
        Schema::table('items_serializados', function (Blueprint $table) {
            $table->dropUnique('items_serializados_serie_unique');
            $table->string('serie')->nullable()->change();
            $table->unique('serie');
        });
    }

    public function down(): void
    {
        Schema::table('items_serializados', function (Blueprint $table) {
            // Note: Can't easily revert to NOT NULL without raw SQL
            // because we might have existing null values
        });
    }
};
