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
        Schema::table('pages', function (Blueprint $table) {
            // Customization fields stored as JSON in meta column
            // We'll add a dedicated meta column if it doesn't exist, or ensure it can store all customization data
            if (! Schema::hasColumn('pages', 'meta')) {
                $table->json('meta')->nullable()->after('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'meta')) {
                $table->dropColumn('meta');
            }
        });
    }
};
