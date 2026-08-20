<?php

declare(strict_types=1);

namespace App\Services\Ai;

use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

class EmbeddingService
{
    /**
     * Convert text into a 512-dimension float array using OpenAI embeddings with content-hash caching.
     *
     * @return array<int, float>
     */
    public function generateEmbedding(string $text): array
    {
        $cleaned = trim((string) preg_replace('/\s+/', ' ', $text));

        if ($cleaned === '') {
            return [];
        }

        $model = (string) config('ai.embedding.model', 'text-embedding-3-small');
        $dimensions = (int) config('ai.embedding.dimensions', 512);

        // Content-hash cache key incorporating model, dimensions, and text hash
        $cacheKey = 'ai_embedding:'.hash('sha256', "{$model}:{$dimensions}:{$cleaned}");

        /** @var array<int, float>|null $cached */
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && ! empty($cached)) {
            return $cached;
        }

        try {
            $response = OpenAI::embeddings()->create([
                'model' => $model,
                'input' => $cleaned,
                'dimensions' => $dimensions,
            ]);

            /** @var array<int, float> $embedding */
            $embedding = $response->embeddings[0]->embedding ?? [];

            if (! empty($embedding)) {
                Cache::forever($cacheKey, $embedding);
            }

            return $embedding;
        } catch (Throwable $e) {
            report($e);

            return [];
        }
    }
}
