<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_document_embeddings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->morphs('documentable'); // morphs to Student, ManualPayment, CompliancePolicy
            $table->text('content_chunk');

            if (DB::getDriverName() === 'pgsql') {
                $table->jsonb('metadata')->nullable();
                $table->vector('embedding', 512);
            } else {
                $table->json('metadata')->nullable();
                $table->json('embedding')->nullable();
            }

            $table->timestamps();

            $table->index(['school_id', 'documentable_type']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX school_doc_embeddings_hnsw_idx ON school_document_embeddings USING hnsw (embedding vector_cosine_ops)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_document_embeddings');
    }
};
