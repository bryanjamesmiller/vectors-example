<?php

declare(strict_types=1);

namespace App\Services\Ai;

use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Embeddings\CreateResponse;
use Throwable;

class EmbeddingService
{
    /**
     * Determine whether an embedding is already cached for the given text.
     */
    public function isCached(string $text): bool
    {
        $cleaned = trim((string) preg_replace('/\s+/', ' ', $text));
        if ($cleaned === '') {
            return false;
        }

        $model = (string) config('ai.embedding.model', 'text-embedding-3-small');
        $dimensions = (int) config('ai.embedding.dimensions', 512);
        $cacheKey = 'ai_embedding:'.hash('sha256', "{$model}:{$dimensions}:{$cleaned}");

        return Cache::has($cacheKey);
    }

    /**
     * Convert text into a 512-dimension float array using OpenAI embeddings with content-hash caching.
     *
     * @return list<float>
     */
    public function generateEmbedding(string $text): array
    {
        $result = $this->generateWithTelemetry($text);

        return $result['embedding'];
    }

    /**
     * Generate an embedding while capturing live provider, latency, and telemetry details.
     *
     * @return array{
     *     embedding: list<float>,
     *     provider: string,
     *     model: string,
     *     dimensions: int,
     *     latency_ms: float,
     *     is_cached: bool,
     *     endpoint: string,
     *     character_count: int,
     *     error: ?string
     * }
     */
    public function generateWithTelemetry(string $text, bool $bypassCache = false): array
    {
        $cleaned = trim((string) preg_replace('/\s+/', ' ', $text));
        $model = (string) config('ai.embedding.model', 'text-embedding-3-small');
        $dimensions = (int) config('ai.embedding.dimensions', 512);
        $baseUri = (string) (config('openai.base_uri') ?? 'https://api.openai.com/v1');

        $configuredProvider = config('ai.embedding.provider');
        $isOllama = str_contains($baseUri, 'localhost') || str_contains($baseUri, '11434') || str_contains($model, 'nomic');
        $providerName = $configuredProvider !== null && $configuredProvider !== ''
            ? (string) $configuredProvider
            : ($isOllama ? 'Ollama (Local Offline AI)' : 'OpenAI API (Cloud)');

        if ($cleaned === '') {
            return [
                'embedding' => [],
                'provider' => $providerName,
                'model' => $model,
                'dimensions' => $dimensions,
                'latency_ms' => 0.0,
                'is_cached' => false,
                'endpoint' => $baseUri,
                'character_count' => 0,
                'error' => 'Input text is empty.',
            ];
        }

        $cacheKey = 'ai_embedding:'.hash('sha256', "{$model}:{$dimensions}:{$cleaned}");
        $startTime = hrtime(true);

        if (! $bypassCache) {
            /** @var list<float>|null $cached */
            $cached = Cache::get($cacheKey);
            if (is_array($cached) && ! empty($cached)) {
                $endTime = hrtime(true);
                $latencyMs = round(($endTime - $startTime) / 1_000_000, 3);

                return [
                    'embedding' => $cached,
                    'provider' => $providerName,
                    'model' => $model,
                    'dimensions' => count($cached),
                    'latency_ms' => $latencyMs,
                    'is_cached' => true,
                    'endpoint' => $baseUri,
                    'character_count' => mb_strlen($cleaned),
                    'error' => null,
                ];
            }
        }

        try {
            /** @var CreateResponse $response */
            $response = retry(2, function () use ($model, $cleaned, $dimensions) {
                return OpenAI::embeddings()->create([
                    'model' => $model,
                    'input' => $cleaned,
                    'dimensions' => $dimensions,
                ]);
            }, 500, function (Throwable $e): bool {
                $msg = $e->getMessage();

                return ! str_contains($msg, 'Incorrect API key')
                    && ! str_contains($msg, 'invalid_api_key')
                    && ! str_contains($msg, 'invalid_request_error');
            });

            $endTime = hrtime(true);
            $latencyMs = round(($endTime - $startTime) / 1_000_000, 2);

            /** @var list<float> $embedding */
            $embedding = $response->embeddings[0]->embedding ?? [];

            if (! empty($embedding)) {
                Cache::forever($cacheKey, $embedding);
            }

            return [
                'embedding' => $embedding,
                'provider' => $providerName,
                'model' => $model,
                'dimensions' => count($embedding),
                'latency_ms' => $latencyMs,
                'is_cached' => false,
                'endpoint' => $baseUri,
                'character_count' => mb_strlen($cleaned),
                'error' => null,
            ];
        } catch (Throwable $e) {
            report($e);
            $endTime = hrtime(true);
            $latencyMs = round(($endTime - $startTime) / 1_000_000, 2);

            $errorMessage = $e->getMessage();
            if (str_contains(strtolower($errorMessage), 'rate limit') || str_contains(strtolower($errorMessage), 'quota')) {
                $errorMessage = 'OpenAI Rate Limit / Credit Quota Exceeded (HTTP 429). Please verify your OpenAI billing credit balance or wait a moment before retrying.';
            }

            return [
                'embedding' => [],
                'provider' => $providerName,
                'model' => $model,
                'dimensions' => $dimensions,
                'latency_ms' => $latencyMs,
                'is_cached' => false,
                'endpoint' => $baseUri,
                'character_count' => mb_strlen($cleaned),
                'error' => $errorMessage,
            ];
        }
    }
}
