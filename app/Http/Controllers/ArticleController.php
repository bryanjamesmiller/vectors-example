<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Article;
use App\Services\Ai\EmbeddingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Pgvector\Laravel\Vector;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles with optional in-database vector similarity search.
     */
    public function index(Request $request, EmbeddingService $embeddingService): View
    {
        $q = $request->query('q');
        $legacy = $request->query('search');
        $rawSearch = is_string($q) ? $q : (is_string($legacy) ? $legacy : '');
        $search = trim(mb_substr($rawSearch, 0, 255));
        $isVectorSearch = false;

        if ($search !== '') {
            $rateLimitKey = 'search-embedding:'.($request->ip() ?? 'unknown');
            $queryEmbedding = [];

            if ($embeddingService->isCached($search)) {
                $queryEmbedding = $embeddingService->generateEmbedding($search);
            } elseif (! RateLimiter::tooManyAttempts($rateLimitKey, 30)) {
                RateLimiter::hit($rateLimitKey, 60);
                $queryEmbedding = $embeddingService->generateEmbedding($search);
            }

            if (! empty($queryEmbedding)) {
                $isVectorSearch = true;
                $articles = Article::query()
                    ->where('is_published', true)
                    ->whereNotNull('embedding')
                    ->selectRaw('articles.*, (articles.embedding <=> ?) as neighbor_distance', [new Vector($queryEmbedding)])
                    ->orderBy('neighbor_distance')
                    ->paginate(9)
                    ->withQueryString();
            } else {
                $articles = Article::query()
                    ->where('is_published', true)
                    ->where(function ($query) use ($search): void {
                        $query->whereLike('title', "%{$search}%", caseSensitive: false)
                            ->orWhereLike('summary', "%{$search}%", caseSensitive: false)
                            ->orWhereLike('content', "%{$search}%", caseSensitive: false);
                    })
                    ->latest('created_at')
                    ->latest('id')
                    ->paginate(9)
                    ->withQueryString();
            }
        } else {
            $articles = Article::query()
                ->where('is_published', true)
                ->latest('created_at')
                ->latest('id')
                ->paginate(9);
        }

        return view('articles.index', [
            'articles' => $articles,
            'search' => $search,
            'isVectorSearch' => $isVectorSearch,
        ]);
    }

    /**
     * Display the specified article along with vector-related recommendations.
     */
    public function show(Article $article): View
    {
        abort_if(! $article->is_published, 404);

        $filterByAudience = request()->boolean('filter_audience');
        $audienceFilter = $filterByAudience ? $article->audience : null;

        $relatedArticles = $article->relatedArticles(3, $audienceFilter);

        return view('articles.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'filterByAudience' => $filterByAudience,
        ]);
    }
}
