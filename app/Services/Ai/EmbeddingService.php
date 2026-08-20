<?php

declare(strict_types=1);

namespace App\Services\Ai;

use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

class EmbeddingService
{
    /**
     * Convert text into a 512-dimension float array using OpenAI embeddings.
     *
     * @return array<int, float>
     */
    public function generateEmbedding(string $text): array
    {
        $cleaned = trim((string) preg_replace('/\s+/', ' ', $text));

        if ($cleaned === '') {
            return [];
        }

        try {
            $response = OpenAI::embeddings()->create([
                'model' => (string) config('ai.embedding.model', 'text-embedding-3-small'),
                'input' => $cleaned,
                'dimensions' => (int) config('ai.embedding.dimensions', 512),
            ]);

            /** @var array<int, float> $embedding */
            $embedding = $response->embeddings[0]->embedding ?? [];

            return $embedding;
        } catch (Throwable $e) {
            report($e);

            return [];
        }
    }
}
