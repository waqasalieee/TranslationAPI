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
        Schema::table('translations', function (Blueprint $table) {
            $table->index('locale_id');
            $table->index('key');
            $table->index('value');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('translation_tag', function (Blueprint $table) {
            $table->index('translation_id');
            $table->index('tag_id');
            // Optional: Add a composite index if you frequently query by both columns
             $table->index(['translation_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('translations', function (Blueprint $table) {
            $table->dropIndex(['locale_id']);
            $table->dropIndex(['key']);
            $table->dropIndex(['value']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('translation_tag', function (Blueprint $table) {
            $table->dropIndex(['translation_id']);
            $table->dropIndex(['tag_id']);
            $table->dropIndex(['translation_id', 'tag_id']); // Drop the composite index as well
        });
    }
};
