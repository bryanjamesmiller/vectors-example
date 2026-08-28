<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Models\Article;
use Generator;
use Illuminate\Database\Eloquent\Collection;
use OpenAI\Laravel\Facades\OpenAI;
use Pgvector\Laravel\Vector;
use Throwable;

class RagChatService
{
    public function __construct(
        protected EmbeddingService $embeddingService
    ) {}

    /**
     * Retrieve relevant knowledge base articles for a query using pgvector cosine distance.
     *
     * @return array{
     *     articles: list<array{
     *         id: int,
     *         title: string,
     *         slug: string,
     *         audience: string,
     *         summary: ?string,
     *         content: string,
     *         distance: float,
     *         similarity: float,
     *         match_percentage: int
     *     }>,
     *     grounded: bool,
     *     latency_ms: float,
     *     min_threshold: float
     * }
     */
    public function retrieveContext(string $query, int $limit = 5): array
    {
        $startTime = hrtime(true);
        $minThreshold = (float) config('ai.embedding.min_similarity_threshold', 0.60);

        $queryEmbedding = $this->embeddingService->generateEmbedding($query);
        if (empty($queryEmbedding)) {
            $endTime = hrtime(true);

            return [
                'articles' => [],
                'grounded' => false,
                'latency_ms' => round(($endTime - $startTime) / 1_000_000, 2),
                'min_threshold' => $minThreshold,
            ];
        }

        /** @var Collection<int, Article> $records */
        $records = Article::query()
            ->where('is_published', true)
            ->whereNotNull('embedding')
            ->select([
                'articles.id',
                'articles.title',
                'articles.slug',
                'articles.audience',
                'articles.summary',
                'articles.content',
            ])
            ->selectRaw('(articles.embedding <=> ?) as cosine_distance', [new Vector($queryEmbedding)])
            ->orderBy('cosine_distance')
            ->take($limit)
            ->get();

        $endTime = hrtime(true);
        $latencyMs = round(($endTime - $startTime) / 1_000_000, 2);

        $articles = [];
        foreach ($records as $article) {
            $distance = (float) ($article->getAttribute('cosine_distance') ?? 1.0);
            $similarity = 1.0 - $distance;
            $percentage = (int) max(0, min(100, round($similarity * 100)));

            if ($similarity >= $minThreshold) {
                $articles[] = [
                    'id' => $article->id,
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'audience' => $article->audience->value,
                    'summary' => $article->summary,
                    'content' => $article->content,
                    'distance' => round($distance, 4),
                    'similarity' => round($similarity, 4),
                    'match_percentage' => $percentage,
                ];
            }
        }

        return [
            'articles' => $articles,
            'grounded' => count($articles) > 0,
            'latency_ms' => $latencyMs,
            'min_threshold' => $minThreshold,
        ];
    }

    /**
     * Assemble the system prompt incorporating retrieved context and grounding guidelines.
     *
     * @param  list<array{
     *     id: int,
     *     title: string,
     *     slug: string,
     *     audience: string,
     *     summary: ?string,
     *     content: string,
     *     distance: float,
     *     similarity: float,
     *     match_percentage: int
     * }>  $articles
     */
    public function buildSystemPrompt(array $articles): string
    {
        $contextBlocks = [];
        foreach ($articles as $idx => $article) {
            $sourceNum = $idx + 1;
            $summaryText = $article['summary'] ? "\nSummary: {$article['summary']}" : '';
            $truncatedContent = mb_strlen($article['content']) > 1500
                ? mb_substr($article['content'], 0, 1500).'... [truncated]'
                : $article['content'];

            $contextBlocks[] = <<<CONTEXT
            [Source {$sourceNum}: "{$article['title']}"] (Audience: {$article['audience']}){$summaryText}
            Content:
            {$truncatedContent}
            CONTEXT;
        }

        $contextString = implode("\n\n---\n\n", $contextBlocks);

        return <<<PROMPT
        You are a helpful, knowledgeable, and welcoming AI advisor for our vocational trade school academy.
        Your mission is to assist prospective recruits, students, alumni, and faculty by answering questions strictly and accurately using the official trade school knowledge base provided below.

        STRICT RAG GROUNDING RULES:
        1. Base your answers solely on the provided trade school articles below. Do not fabricate programs, certifications, tuition details, or policies not mentioned in the context.
        2. Cite your sources in your answers when referencing specific facts or guidelines, e.g. [Source 1: "Title"].
        3. If the context does not provide sufficient information to answer the question, clearly state that our current trade school documentation does not cover that specific topic, and invite them to speak with admissions or campus student services.
        4. Maintain an encouraging, career-focused, professional tone. Use concise plain text with short paragraphs and hyphen-prefixed lists for easy reading.

        OFFICIAL TRADE SCHOOL KNOWLEDGE BASE CONTEXT:
        {$contextString}
        PROMPT;
    }

    /**
     * Assemble the baseline system prompt for raw LLM inference without database knowledge grounding.
     */
    public function buildRawSystemPrompt(): string
    {
        return <<<'PROMPT'
        You are a general academic assistant for a vocational trade school academy.
        You do NOT have access to our private school database, internal tuition records, or campus policy documents.
        Answer the user's questions relying solely on your general pre-trained knowledge.
        Maintain a helpful, encouraging tone, and use concise plain text with short paragraphs.
        PROMPT;
    }

    /**
     * Construct the full message array for OpenAI chat completions, including conversation history.
     *
     * @param  list<array{role: string, content: string}>  $history
     * @return list<array{role: string, content: string}>
     */
    public function buildMessages(string $systemPrompt, array $history, string $currentQuestion, int $maxHistoryTurns = 10): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
        ];

        // Slice to the last N complete conversation turns (user + assistant)
        $recentHistory = array_slice($history, -($maxHistoryTurns * 2));
        foreach ($recentHistory as $turn) {
            if (in_array($turn['role'], ['user', 'assistant'], true) && ! empty($turn['content'])) {
                $messages[] = [
                    'role' => $turn['role'],
                    'content' => $turn['content'],
                ];
            }
        }

        $messages[] = [
            'role' => 'user',
            'content' => $currentQuestion,
        ];

        return $messages;
    }

    /**
     * Stream the chat completion token-by-token from OpenAI.
     *
     * @param  list<array{role: string, content: string}>  $messages
     * @return Generator<int, string>
     */
    public function streamChatResponse(array $messages): Generator
    {
        $model = (string) config('ai.chat.model', 'gpt-4o-mini');
        $temperature = (float) config('ai.chat.temperature', 0.1);

        try {
            $stream = OpenAI::chat()->createStreamed([
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
                'max_completion_tokens' => 800,
            ]);

            foreach ($stream as $response) {
                $delta = $response->choices[0]->delta->content ?? '';
                if ($delta !== '') {
                    yield $delta;
                }
            }
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Complete the chat response non-streamed (useful for fallback or testing).
     *
     * @param  list<array{role: string, content: string}>  $messages
     */
    public function completeChatResponse(array $messages): string
    {
        $model = (string) config('ai.chat.model', 'gpt-4o-mini');
        $temperature = (float) config('ai.chat.temperature', 0.1);

        try {
            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
                'max_completion_tokens' => 800,
            ]);

            return $response->choices[0]->message->content ?? '';
        } catch (Throwable $e) {
            report($e);

            return 'Unable to complete response from AI service.';
        }
    }
}
