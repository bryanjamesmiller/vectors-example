<?php

declare(strict_types=1);

use App\Enums\Audience;
use App\Jobs\GenerateArticleEmbeddingJob;
use App\Models\Article;
use App\Services\Ai\EmbeddingService;
use Database\Seeders\ArticleSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    $this->seed(ArticleSeeder::class);
});

test('it retrieves semantically related articles based on vector proximity', function () {
    $toolGrantsArticle = Article::where('slug', 'applying-for-trade-tool-grants-fee-waivers')->firstOrFail();

    $related = $toolGrantsArticle->relatedArticles(3);

    // Must return related articles
    expect($related)->toHaveCount(3);

    // The current article must NOT be in its own related articles list
    expect($related->pluck('id'))->not->toContain($toolGrantsArticle->id);

    // Related articles must contain topically close guides like Emergency Student Aid
    $titles = $related->pluck('title')->all();
    expect($titles)->toContain('Emergency Student Aid & Housing Relief Grants');

    // Verify high similarity percentage
    expect($related->first()->getMatchPercentage())->toBeGreaterThan(70);
});

test('it excludes unpublished articles from related recommendations', function () {
    $safetyArticle = Article::where('slug', 'personal-protective-equipment-ppe-guidelines-for-welding-labs')->firstOrFail();

    // Create an unpublished article with identical embedding
    $unpublished = Article::factory()->create([
        'title' => 'Secret Unpublished Safety Rule',
        'is_published' => false,
        'embedding' => $safetyArticle->embedding,
    ]);

    $related = $safetyArticle->relatedArticles(5);

    expect($related->pluck('id'))->not->toContain($unpublished->id);
});

test('article casts audience to enum properly', function () {
    $article = Article::where('slug', 'applying-for-trade-tool-grants-fee-waivers')->firstOrFail();

    expect($article->audience)->toBe(Audience::Students);
});

test('homepage renders hero semantic search bar and quick search pills', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('Semantic Search')
        ->assertSee('Underwater Welding')
        ->assertSee('Solar Apprenticeship Grants')
        ->assertSee('Journeyman Electrical Exam');
});

test('articles index page renders successfully with seeded articles and example search pills', function () {
    $targetArticle = Article::where('is_published', true)->firstOrFail();
    $targetArticle->created_at = now()->addMinute();
    $targetArticle->save();

    $response = $this->get(route('articles.index'));

    $response->assertOk()
        ->assertSee('Trade School Articles')
        ->assertSee('Semantic Search')
        ->assertSee('Try searching:')
        ->assertSee('HVAC Diagnostics')
        ->assertSee(route('articles.index', ['q' => 'commercial refrigeration superheat diagnostics']))
        ->assertSee('Workshop PPE')
        ->assertSee(route('articles.index', ['q' => 'personal protective equipment ppe guidelines']))
        ->assertSee('Lockout/Tagout')
        ->assertSee(route('articles.index', ['q' => 'high voltage electrical lockout tagout procedures']))
        ->assertSee($targetArticle->title);
});

test('articles index page performs in-database semantic vector search and displays similarity match percentage', function () {
    $targetArticle = Article::where('slug', 'personal-protective-equipment-ppe-guidelines-for-welding-labs')->firstOrFail();

    $mockService = Mockery::mock(EmbeddingService::class);
    $mockService->shouldReceive('isCached')->with('hyperbaric welding safety protocols')->andReturn(false);
    $mockService->shouldReceive('generateEmbedding')
        ->with('hyperbaric welding safety protocols')
        ->once()
        ->andReturn($targetArticle->embedding->toArray());

    $this->app->instance(EmbeddingService::class, $mockService);

    $response = $this->get(route('articles.index', ['q' => 'hyperbaric welding safety protocols']));

    $response->assertOk()
        ->assertSee('AI vector similarity')
        ->assertSee('hyperbaric welding safety protocols')
        ->assertSee('100% Match')
        ->assertSee('personal-protective-equipment-ppe-guidelines-for-welding-labs');
});

test('articles index search with empty query returns all published articles', function () {
    $response = $this->get(route('articles.index', ['q' => '   ']));

    $response->assertOk()
        ->assertDontSee('AI vector similarity')
        ->assertSee('Trade School Articles');
});

test('articles index safely handles array-valued query parameters without throwing errors', function () {
    $response = $this->get('/articles?q[]=welding&q[]=safety');

    $response->assertOk()
        ->assertSee('Trade School Articles');
});

test('articles index falls back to text keyword search when embedding rate limit is reached', function () {
    $key = 'search-embedding:127.0.0.1';
    RateLimiter::clear($key);

    for ($i = 0; $i < 30; $i++) {
        RateLimiter::hit($key, 60);
    }

    $mockService = Mockery::mock(EmbeddingService::class);
    $mockService->shouldReceive('isCached')->with('Welding')->andReturn(false);
    $mockService->shouldNotReceive('generateEmbedding');
    $this->app->instance(EmbeddingService::class, $mockService);

    $response = $this->get(route('articles.index', ['q' => 'Welding']));

    $response->assertOk()
        ->assertSee('text keyword')
        ->assertDontSee('AI vector similarity')
        ->assertSee('Personal Protective Equipment (PPE) Guidelines for Welding Labs');
});

test('articles index allows cached vector searches even when rate limit is exceeded', function () {
    $targetArticle = Article::where('slug', 'personal-protective-equipment-ppe-guidelines-for-welding-labs')->firstOrFail();

    $key = 'search-embedding:127.0.0.1';
    RateLimiter::clear($key);

    for ($i = 0; $i < 30; $i++) {
        RateLimiter::hit($key, 60);
    }

    $mockService = Mockery::mock(EmbeddingService::class);
    $mockService->shouldReceive('isCached')->with('hyperbaric welding safety')->andReturn(true);
    $mockService->shouldReceive('generateEmbedding')->with('hyperbaric welding safety')->once()->andReturn($targetArticle->embedding->toArray());
    $this->app->instance(EmbeddingService::class, $mockService);

    $response = $this->get(route('articles.index', ['q' => 'hyperbaric welding safety']));

    $response->assertOk()
        ->assertSee('AI vector similarity')
        ->assertSee('100% Match')
        ->assertDontSee('text keyword');
});

test('articles index displays distinct empty states for empty search results versus empty database catalog', function () {
    // 1. Search with no keyword matches (when embedding is unavailable or returns no keyword matches)
    $mockService = Mockery::mock(EmbeddingService::class);
    $mockService->shouldReceive('isCached')->andReturn(false);
    $mockService->shouldReceive('generateEmbedding')
        ->andReturn([]);
    $this->app->instance(EmbeddingService::class, $mockService);

    $response = $this->get(route('articles.index', ['q' => 'nonexistentquery123xyz']));
    $response->assertOk()
        ->assertSee('No articles matched your search query.')
        ->assertSee('Clear Search');

    // 2. Empty catalog with no published articles
    Article::query()->delete();
    $emptyCatalogResponse = $this->get(route('articles.index'));
    $emptyCatalogResponse->assertOk()
        ->assertSee('No published articles found.')
        ->assertDontSee('Clear Search');
});

test('article show page renders article and related recommendations', function () {
    $article = Article::where('slug', 'applying-for-trade-tool-grants-fee-waivers')->firstOrFail();

    $response = $this->get(route('articles.show', $article));

    $response->assertOk()
        ->assertSee($article->title)
        ->assertSee('Related Articles')
        ->assertSee('Auto-matched via pgvector')
        ->assertSee('Emergency Student Aid &amp; Housing Relief Grants', false);
});

test('saving an article dispatches GenerateArticleEmbeddingJob', function () {
    Queue::fake();

    $article = Article::create([
        'title' => 'Electrical Lockout/Tagout Safety',
        'slug' => 'electrical-loto-safety-test',
        'audience' => Audience::TeachingAssistants,
        'summary' => 'Summary test',
        'content' => 'Content test',
        'is_published' => true,
    ]);

    Queue::assertPushed(GenerateArticleEmbeddingJob::class, function ($job) use ($article) {
        return $job->article->id === $article->id;
    });
});

test('GenerateArticleEmbeddingJob calls EmbeddingService and saves vector to article', function () {
    $article = Article::factory()->create([
        'title' => 'HVAC Duct Sizing',
        'slug' => 'hvac-duct-sizing',
        'audience' => Audience::Students,
        'embedding' => null,
    ]);

    $fakeVector = array_fill(0, 512, 0.042);

    $mockEmbedder = Mockery::mock(EmbeddingService::class);
    $mockEmbedder->shouldReceive('generateEmbedding')
        ->once()
        ->andReturn($fakeVector);

    $job = new GenerateArticleEmbeddingJob($article);
    $job->handle($mockEmbedder);

    $article->refresh();
    expect($article->embedding)->not->toBeNull()
        ->and($article->embedding->toArray())->toHaveCount(512);
});

test('articles:re-embed artisan command dispatches jobs for all articles', function () {
    Queue::fake();

    $this->artisan('articles:re-embed')
        ->assertSuccessful();

    $count = Article::count();
    Queue::assertPushed(GenerateArticleEmbeddingJob::class, $count);
});

test('EmbeddingService uses content-hash cache for identical text', function () {
    $text = 'Sample trade article content for cache verification.';
    $model = (string) config('ai.embedding.model', 'text-embedding-3-small');
    $dimensions = (int) config('ai.embedding.dimensions', 512);
    $cacheKey = 'ai_embedding:'.hash('sha256', "{$model}:{$dimensions}:{$text}");
    $mockVector = array_fill(0, 512, 0.088);

    Cache::put($cacheKey, $mockVector);

    $service = new EmbeddingService;
    $result = $service->generateEmbedding($text);

    expect($result)->toBe($mockVector);
});

test('articles:export-fixtures exports JSON file correctly', function () {
    $tempPath = storage_path('framework/testing/test_articles_fixtures.json');

    $this->artisan('articles:export-fixtures', ['--path' => $tempPath])
        ->assertSuccessful();

    expect(file_exists($tempPath))->toBeTrue();

    $content = json_decode((string) file_get_contents($tempPath), true);
    expect($content)->toBeArray()
        ->and(count($content))->toBeGreaterThan(0);

    @unlink($tempPath);
});
