<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(): View
    {
        $articles = Article::query()
            ->where('is_published', true)
            ->latest('created_at')
            ->latest('id')
            ->paginate(9);

        return view('articles.index', [
            'articles' => $articles,
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
