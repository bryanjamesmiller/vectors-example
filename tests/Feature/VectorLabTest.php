<?php

declare(strict_types=1);

use App\Enums\Audience;
use App\Livewire\VectorLab;
use App\Models\Article;
use App\Services\Ai\EmbeddingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Pgvector\Laravel\Vector;

test('vector lab page is publicly accessible without login', function () {
    $response = $this->get(route('vector-lab'));

    $response->assertOk()
        ->assertSee('Interactive AI Vector Lab')
        ->assertSee('Underwater Welding');
});

test('presets can be loaded into the vector lab component', function () {
    Livewire::test(VectorLab::class)
        ->call('loadPreset', 'electrical')
        ->assertSet('title', 'Journeyman Electrical Master Licensing Exam Tactics')
        ->assertSet('audience', 'alumni')
        ->assertSet('telemetry', null)
        ->assertSet('generatedVector', null);
});

test('embedding can be generated with live telemetry, auto-saved to database, and queries nearest neighbors', function () {
    $sampleVector = array_fill(0, 512, 0.05);

    // Mock EmbeddingService
    $mockService = Mockery::mock(EmbeddingService::class);
    $mockService->shouldReceive('generateWithTelemetry')
        ->once()
        ->andReturn([
            'embedding' => $sampleVector,
            'provider' => 'OpenAI API (Cloud)',
            'model' => 'text-embedding-3-small',
            'dimensions' => 512,
            'latency_ms' => 195.4,
            'is_cached' => false,
            'endpoint' => 'https://api.openai.com/v1',
            'character_count' => 150,
            'error' => null,
        ]);

    $this->app->instance(EmbeddingService::class, $mockService);

    // Create a target article in the database with matching vector
    Article::create([
        'title' => 'Welding Lab Safety Standards',
        'slug' => 'welding-lab-safety-standards',
        'audience' => Audience::Students,
        'summary' => 'Welding safety overview.',
        'content' => 'Full markdown content.',
        'is_published' => true,
        'embedding' => new Vector($sampleVector),
    ]);

    Livewire::test(VectorLab::class)
        ->set('title', 'Advanced Pipe Welding Safety')
        ->set('audience', 'students')
        ->set('content', 'Detailed hyperbaric welding content.')
        ->call('generateEmbedding')
        ->assertSet('telemetry.provider', 'OpenAI API (Cloud)')
        ->assertSet('telemetry.latency_ms', 195.4)
        ->assertSet('telemetry.dimensions', 512)
        ->assertSet('isPublished', true)
        ->assertSet('publishedArticleSlug', 'advanced-pipe-welding-safety')
        ->assertCount('nearestMatches', 1);

    $this->assertDatabaseHas('articles', [
        'title' => 'Advanced Pipe Welding Safety',
        'slug' => 'advanced-pipe-welding-safety',
        'audience' => Audience::Students->value,
        'is_published' => true,
    ]);
});

test('calculated vector lab article appears first on the articles index page', function () {
    $sampleVector = array_fill(0, 512, 0.05);

    $mockService = Mockery::mock(EmbeddingService::class);
    $mockService->shouldReceive('generateWithTelemetry')
        ->once()
        ->andReturn([
            'embedding' => $sampleVector,
            'provider' => 'OpenAI API (Cloud)',
            'model' => 'text-embedding-3-small',
            'dimensions' => 512,
            'latency_ms' => 100.0,
            'is_cached' => false,
            'endpoint' => 'https://api.openai.com/v1',
            'character_count' => 150,
            'error' => null,
        ]);

    $this->app->instance(EmbeddingService::class, $mockService);

    // Seed an older article
    Article::create([
        'title' => 'Old Seeded Article',
        'slug' => 'old-seeded-article',
        'audience' => Audience::Students,
        'content' => 'Old content.',
        'is_published' => true,
        'embedding' => new Vector($sampleVector),
    ]);

    Livewire::test(VectorLab::class)
        ->set('title', 'Brand New Vector Lab Article')
        ->set('audience', 'students')
        ->set('content', 'Brand new content.')
        ->call('generateEmbedding');

    $response = $this->get(route('articles.index'));
    $response->assertOk();

    /** @var LengthAwarePaginator<Article> $articles */
    $articles = $response->viewData('articles');
    expect($articles->first()->title)->toBe('Brand New Vector Lab Article');
});

test('an interviewer can publish the tested article from the vector lab to the database', function () {
    $sampleVector = array_fill(0, 512, 0.08);

    Livewire::test(VectorLab::class)
        ->set('title', 'Hyperbaric Chamber Pressure Testing')
        ->set('audience', 'students')
        ->set('summary', 'Hyperbaric safety summary.')
        ->set('content', 'Hyperbaric chamber technical guide.')
        ->set('generatedVector', $sampleVector)
        ->call('publishArticle')
        ->assertSet('isPublished', true)
        ->assertSet('publishedArticleSlug', 'hyperbaric-chamber-pressure-testing');

    $this->assertDatabaseHas('articles', [
        'title' => 'Hyperbaric Chamber Pressure Testing',
        'slug' => 'hyperbaric-chamber-pressure-testing',
        'audience' => Audience::Students->value,
    ]);
});

test('publishing without generating a vector first displays an error', function () {
    Livewire::test(VectorLab::class)
        ->set('title', 'Un-vectorized Article')
        ->set('audience', 'students')
        ->set('content', 'Some content.')
        ->set('generatedVector', null)
        ->call('publishArticle')
        ->assertSet('isPublished', false);

    $this->assertDatabaseMissing('articles', [
        'title' => 'Un-vectorized Article',
    ]);
});

test('rate limiter blocks excessive uncached embedding generations in vector lab', function () {
    $sampleVector = array_fill(0, 512, 0.05);

    $mockService = Mockery::mock(EmbeddingService::class);
    $mockService->shouldReceive('generateWithTelemetry')
        ->andReturn([
            'embedding' => $sampleVector,
            'provider' => 'OpenAI API (Cloud)',
            'model' => 'text-embedding-3-small',
            'dimensions' => 512,
            'latency_ms' => 120.0,
            'is_cached' => false,
            'endpoint' => 'https://api.openai.com/v1',
            'character_count' => 150,
            'error' => null,
        ]);

    $this->app->instance(EmbeddingService::class, $mockService);

    $key = 'vector-lab-live:127.0.0.1';
    RateLimiter::clear($key);

    for ($i = 0; $i < 15; $i++) {
        RateLimiter::hit($key, 60);
    }

    Livewire::test(VectorLab::class)
        ->set('title', 'Unique Text '.uniqid())
        ->set('audience', 'students')
        ->set('content', 'Content '.uniqid())
        ->call('generateEmbedding')
        ->assertSet('telemetry', null);
});

test('calculating vector for an article with an existing title does not create duplicate rows', function () {
    $sampleVector = array_fill(0, 512, 0.05);

    $mockService = Mockery::mock(EmbeddingService::class);
    $mockService->shouldReceive('generateWithTelemetry')
        ->twice()
        ->andReturn([
            'embedding' => $sampleVector,
            'provider' => 'OpenAI API (Cloud)',
            'model' => 'text-embedding-3-small',
            'dimensions' => 512,
            'latency_ms' => 100.0,
            'is_cached' => false,
            'endpoint' => 'https://api.openai.com/v1',
            'character_count' => 150,
            'error' => null,
        ]);

    $this->app->instance(EmbeddingService::class, $mockService);

    // Initial calculation creates the article
    Livewire::test(VectorLab::class)
        ->set('title', 'Duplicate Prevention Test Guide')
        ->set('audience', 'students')
        ->set('content', 'Initial article content.')
        ->call('generateEmbedding')
        ->assertSet('isDuplicateTitle', false)
        ->assertSet('isPublished', true);

    expect(Article::where('title', 'Duplicate Prevention Test Guide')->count())->toBe(1);

    // Second calculation with the exact same title calculates telemetry but prevents duplicate insert
    Livewire::test(VectorLab::class)
        ->set('title', 'Duplicate Prevention Test Guide')
        ->set('audience', 'students')
        ->set('content', 'Modified article content.')
        ->call('generateEmbedding')
        ->assertSet('isDuplicateTitle', true)
        ->assertSet('isPublished', true);

    expect(Article::where('title', 'Duplicate Prevention Test Guide')->count())->toBe(1);
});
