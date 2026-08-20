<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Embedding Configuration
    |--------------------------------------------------------------------------
    |
    | We use OpenAI's 'text-embedding-3-small' model.
    |
    | Dimension Reduction (Matryoshka Embeddings):
    | Default native output is 1,536 dimensions. We configure dimensions to 512.
    | OpenAI's text-embedding-3-small natively supports dimension truncation
    | down to 512 with less than 2% loss in retrieval quality, while saving
    | ~66% of disk storage and RAM footprint for PostgreSQL HNSW vector indexes.
    |
    */
    'embedding' => [
        'provider' => env('AI_EMBEDDING_PROVIDER', null),
        'model' => env('AI_EMBEDDING_MODEL', 'text-embedding-3-small'),
        'dimensions' => (int) env('AI_EMBEDDING_DIMENSIONS', 512),
        'chunk_size' => (int) env('AI_CHUNK_SIZE', 500),
        'chunk_overlap' => (int) env('AI_CHUNK_OVERLAP', 50),
        'min_similarity_threshold' => (float) env('AI_MIN_SIMILARITY_THRESHOLD', 0.60),
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Chat & RAG Synthesis Configuration
    |--------------------------------------------------------------------------
    |
    | Model used by the School AI Assistant (Lumion AI) for RAG context synthesis.
    | Low temperature (0.1) enforces deterministic, factual grounding based
    | strictly on retrieved trade school records and policies.
    |
    */
    'chat' => [
        'model' => env('AI_CHAT_MODEL', 'gpt-4o-mini'),
        'temperature' => (float) env('AI_CHAT_TEMPERATURE', 0.1),
    ],

];
