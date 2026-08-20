<?php

declare(strict_types=1);

use App\Enums\Audience;
use App\Jobs\GenerateArticleEmbeddingJob;
use App\Models\Article;
use App\Services\Ai\EmbeddingService;
use Database\Seeders\ArticleSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

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

test('articles index page renders successfully with seeded articles', function () {
    $targetArticle = Article::where('is_published', true)->firstOrFail();
    $targetArticle->created_at = now()->addMinute();
    $targetArticle->save();

    $response = $this->get(route('articles.index'));

    $response->assertOk()
        ->assertSee('Trade School Articles')
        ->assertSee('Semantic Search')
        ->assertSee($targetArticle->title);
});

test('articles index page performs in-database semantic vector search and displays similarity match percentage', function () {
    $targetArticle = Article::where('slug', 'personal-protective-equipment-ppe-guidelines-for-welding-labs')->firstOrFail();

    $mockService = Mockery::mock(EmbeddingService::class);
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
