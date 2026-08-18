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
        // Pages table indexes
        Schema::table('pages', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'pages_active_sort_idx');
            $table->index('slug', 'pages_slug_idx');
        });

        // Page sections table indexes
        Schema::table('page_sections', function (Blueprint $table) {
            $table->index(['page_id', 'is_active', 'sort_order'], 'page_sections_page_active_sort_idx');
            $table->index(['page_id', 'key'], 'page_sections_page_key_idx');
            $table->index('is_active', 'page_sections_active_idx');
        });

        // Media table indexes
        Schema::table('media', function (Blueprint $table) {
            $table->index('file_type', 'media_type_idx');
            $table->index('mime_type', 'media_mime_idx');
        });

        // Page media table indexes
        Schema::table('page_media', function (Blueprint $table) {
            $table->index(['page_section_id', 'usage', 'sort_order'], 'page_media_section_usage_sort_idx');
            $table->index(['page_section_id', 'is_active'], 'page_media_section_active_idx');
        });

        // Site services table indexes
        Schema::table('site_services', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'site_services_active_sort_idx');
        });

        // Process steps table indexes
        Schema::table('process_steps', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'process_steps_active_sort_idx');
        });

        // Why cards table indexes
        Schema::table('why_cards', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'why_cards_active_sort_idx');
        });

        // Contact info table indexes
        Schema::table('contact_info', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'contact_info_active_sort_idx');
            $table->index(['type', 'is_active'], 'contact_info_type_active_idx');
        });

        // Social links table indexes
        Schema::table('social_links', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'social_links_active_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex('pages_active_sort_idx');
            $table->dropIndex('pages_slug_idx');
        });

        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropIndex('page_sections_page_active_sort_idx');
            $table->dropIndex('page_sections_page_key_idx');
            $table->dropIndex('page_sections_active_idx');
        });

        Schema::table('media', function (Blueprint $table) {
            $table->dropIndex('media_type_idx');
            $table->dropIndex('media_mime_idx');
        });

        Schema::table('page_media', function (Blueprint $table) {
            $table->dropIndex('page_media_section_usage_sort_idx');
            $table->dropIndex('page_media_section_active_idx');
        });

        Schema::table('site_services', function (Blueprint $table) {
            $table->dropIndex('site_services_active_sort_idx');
        });

        Schema::table('process_steps', function (Blueprint $table) {
            $table->dropIndex('process_steps_active_sort_idx');
        });

        Schema::table('why_cards', function (Blueprint $table) {
            $table->dropIndex('why_cards_active_sort_idx');
        });

        Schema::table('contact_info', function (Blueprint $table) {
            $table->dropIndex('contact_info_active_sort_idx');
            $table->dropIndex('contact_info_type_active_idx');
        });

        Schema::table('social_links', function (Blueprint $table) {
            $table->dropIndex('social_links_active_sort_idx');
        });
    }
};
