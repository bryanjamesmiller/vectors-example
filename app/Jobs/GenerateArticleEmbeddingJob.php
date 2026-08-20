<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Article;
use App\Services\Ai\EmbeddingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateArticleEmbeddingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(public Article $article) {}

    public function handle(EmbeddingService $embeddingService): void
    {
        $summary = $this->article->summary ? "**Summary:** {$this->article->summary}\n\n" : '';

        $textToEmbed = <<<MARKDOWN
        # {$this->article->title}
        **Target Audience:** {$this->article->audience->value}
        {$summary}## Content
        {$this->article->content}
        MARKDOWN;

        $vector = $embeddingService->generateEmbedding($textToEmbed);

        if (! empty($vector)) {
            $this->article->updateQuietly([
                'embedding' => $vector,
            ]);
        }
    }
}
