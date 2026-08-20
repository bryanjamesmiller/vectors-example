<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\GenerateArticleEmbeddingJob;
use App\Models\Article;

class ArticleObserver
{
    /**
     * Handle the Article "saved" event.
     */
    public function saved(Article $article): void
    {
        if ($article->wasChanged(['title', 'content', 'summary', 'audience']) || is_null($article->embedding)) {
            GenerateArticleEmbeddingJob::dispatch($article);
        }
    }
}
