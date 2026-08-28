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
        Schema::table('articles', function (Blueprint $table) {
            // Drop standalone boolean index (subsumed by composite indexes)
            $table->dropIndex(['is_published']);

            // 1. Semantic search & related recommendations filtered by audience
            $table->index(['is_published', 'audience']);

            // 2. Main catalog pagination: WHERE is_published = true ORDER BY created_at DESC, id DESC
            $table->index(['is_published', 'created_at', 'id']);

            // 3. Duplicate check in Vector Lab: WHERE title = ?
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['is_published', 'created_at', 'id']);
            $table->dropIndex(['is_published', 'audience']);
            $table->index('is_published');
        });
    }
};
