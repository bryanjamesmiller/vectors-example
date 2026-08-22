<?php

declare(strict_types=1);

use App\Enums\Audience;
use App\Livewire\VectorLab;
use App\Models\Article;
use App\Services\Ai\EmbeddingService;
use App\Services\Ai\ScenarioService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use OpenAI\Laravel\Facades\OpenAI;
use Pgvector\Laravel\Vector;

test('vector lab page is publicly accessible without login', function () {
    $response = $this->get(route('vector-lab'));

    $response->assertOk()
        ->assertSee('Interactive AI Vector Lab')
        ->assertSee('Preset articles:')
        ->assertSee('Or generate article with AI:')
        ->assertSee('Generate article with OpenAI API');
});

test('vector lab generates a randomized scenario on mount', function () {
    $mockScenarioService = Mockery::mock(ScenarioService::class);
    $mockScenarioService->shouldReceive('generateRandomScenario')
        ->once()
        ->andReturn([
            'title' => 'Custom CNC Milling Speeds',
            'audience' => 'students',
            'summary' => 'CNC machining overview.',
            'content' => 'High speed milling parameters.',
        ]);

    $this->app->instance(ScenarioService::class, $mockScenarioService);

    Livewire::test(VectorLab::class)
        ->assertSet('title', 'Custom CNC Milling Speeds')
        ->assertSet('audience', 'students')
        ->assertSet('summary', 'CNC machining overview.')
        ->assertSet('content', 'High speed milling parameters.');
});

test('vector lab randomizeScenario action re-rolls fresh scenario', function () {
    $mockScenarioService = Mockery::mock(ScenarioService::class);
    $mockScenarioService->shouldReceive('generateRandomScenario')
        ->twice()
        ->andReturn(
            [
                'title' => 'Initial Scenario',
                'audience' => 'recruits',
                'summary' => 'Initial summary',
                'content' => 'Initial content',
            ],
            [
                'title' => 'Re-rolled Scenario',
                'audience' => 'teachers',
                'summary' => 'Re-rolled summary',
                'content' => 'Re-rolled content',
            ]
        );

    $this->app->instance(ScenarioService::class, $mockScenarioService);

    Livewire::test(VectorLab::class)
        ->assertSet('title', 'Initial Scenario')
        ->call('randomizeScenario')
        ->assertSet('title', 'Re-rolled Scenario')
        ->assertSet('audience', 'teachers');
});

test('vector lab randomizeScenario falls back to curated scenario when rate limited', function () {
    $key = 'vector-lab-scenario:127.0.0.1';
    RateLimiter::clear($key);

    for ($i = 0; $i < 10; $i++) {
        RateLimiter::hit($key, 60);
    }

    $mockScenarioService = Mockery::mock(ScenarioService::class);
    $mockScenarioService->shouldReceive('getRandomFallbackScenario')
        ->once()
        ->andReturn([
            'title' => 'Fallback Scenario',
            'audience' => 'students',
            'summary' => 'Fallback summary',
            'content' => 'Fallback content',
        ]);
    $mockScenarioService->shouldNotReceive('generateRandomScenario');

    $this->app->instance(ScenarioService::class, $mockScenarioService);

    Livewire::test(VectorLab::class)
        ->assertSet('title', 'Fallback Scenario')
        ->assertSet('audience', 'students');
});

test('ScenarioService getRandomFallbackScenario returns a valid scenario array', function () {
    $service = new ScenarioService;
    $scenario = $service->getRandomFallbackScenario();

    expect($scenario)->toHaveKeys(['title', 'audience', 'summary', 'content'])
        ->and($scenario['title'])->not->toBeEmpty()
        ->and($scenario['content'])->not->toBeEmpty();
});

test('ScenarioService falls back when OpenAI is not configured', function () {
    config(['openai.api_key' => null]);
    $scenario = (new ScenarioService)->generateRandomScenario();

    expect($scenario)->toHaveKeys(['title', 'audience', 'summary', 'content'])
        ->and($scenario['title'])->not->toBeEmpty()
        ->and($scenario['content'])->not->toBeEmpty();
});

test('ScenarioService reports exception and returns fallback when OpenAI throws during generation', function () {
    config(['openai.api_key' => 'sk-test-key-12345']);

    Exceptions::fake();

    OpenAI::fake([
        new RuntimeException('OpenAI API service unavailable'),
    ]);

    $scenario = (new ScenarioService)->generateRandomScenario();

    Exceptions::assertReported(function (RuntimeException $e): bool {
        return $e->getMessage() === 'OpenAI API service unavailable';
    });

    expect($scenario)->toHaveKeys(['title', 'audience', 'summary', 'content'])
        ->and($scenario['title'])->not->toBeEmpty()
        ->and($scenario['content'])->not->toBeEmpty();
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

test('dynamic cache checkbox reflects whether text is cached or requires live api call', function () {
    Cache::flush();

    Livewire::test(VectorLab::class)
        ->set('title', 'Brand New Uncached Title')
        ->set('content', 'Brand new uncached content.')
        ->assertSee('Live AI API Call Required')
        ->assertSee('New / Uncached Content')
        ->assertSeeHtml('checked disabled');

    $model = (string) config('ai.embedding.model', 'nomic-embed-text');
    $dimensions = (int) config('ai.embedding.dimensions', 512);
    $textToEmbed = "# Cached Title\n**Target Audience:** students\n## Content\nCached content.";
    $cleaned = trim(preg_replace('/\s+/', ' ', $textToEmbed) ?? $textToEmbed);
    $cacheKey = 'ai_embedding:'.hash('sha256', "{$model}:{$dimensions}:{$cleaned}");
    Cache::put($cacheKey, array_fill(0, 512, 0.01), 3600);

    Livewire::test(VectorLab::class)
        ->set('title', 'Cached Title')
        ->set('audience', 'students')
        ->set('summary', '')
        ->set('content', 'Cached content.')
        ->assertSee('Bypass Cache')
        ->assertSee('Cached Embedding Available')
        ->assertDontSeeHtml('checked disabled')
        ->assertSeeHtml('wire:model="forceLiveCall"');
});

test('generate embedding respects cached state and force live call flag', function () {
    Cache::flush();
    $sampleVector = array_fill(0, 512, 0.02);

    $mockService = Mockery::mock(EmbeddingService::class);
    // 1. Uncached generation: forceLive is true even if forceLiveCall is false
    $mockService->shouldReceive('generateWithTelemetry')
        ->once()
        ->with(Mockery::type('string'), true)
        ->andReturn([
            'embedding' => $sampleVector,
            'provider' => 'OpenAI API (Cloud)',
            'model' => 'text-embedding-3-small',
            'dimensions' => 512,
            'latency_ms' => 120.0,
            'is_cached' => false,
            'endpoint' => 'https://api.openai.com/v1',
            'character_count' => 100,
            'error' => null,
        ]);

    // 2. Cached generation with forceLiveCall = false passes false to service
    $mockService->shouldReceive('generateWithTelemetry')
        ->once()
        ->with(Mockery::type('string'), false)
        ->andReturn([
            'embedding' => $sampleVector,
            'provider' => 'OpenAI API (Cloud)',
            'model' => 'text-embedding-3-small',
            'dimensions' => 512,
            'latency_ms' => 0.5,
            'is_cached' => true,
            'endpoint' => 'https://api.openai.com/v1',
            'character_count' => 100,
            'error' => null,
        ]);

    $this->app->instance(EmbeddingService::class, $mockService);

    // Uncached call -> bypassCache is true
    Livewire::test(VectorLab::class)
        ->set('title', 'Uncached Article')
        ->set('audience', 'students')
        ->set('content', 'Brand new content')
        ->set('forceLiveCall', false)
        ->call('generateEmbedding');

    // Populate cache for a known text
    $model = (string) config('ai.embedding.model', 'nomic-embed-text');
    $dimensions = (int) config('ai.embedding.dimensions', 512);
    $textToEmbed = "# Cached Article\n**Target Audience:** students\n## Content\nCached content text";
    $cleaned = trim(preg_replace('/\s+/', ' ', $textToEmbed) ?? $textToEmbed);
    $cacheKey = 'ai_embedding:'.hash('sha256', "{$model}:{$dimensions}:{$cleaned}");
    Cache::put($cacheKey, $sampleVector, 3600);

    // Cached call with forceLiveCall = false -> bypassCache is false
    Livewire::test(VectorLab::class)
        ->set('title', 'Cached Article')
        ->set('audience', 'students')
        ->set('summary', '')
        ->set('content', 'Cached content text')
        ->set('forceLiveCall', false)
        ->call('generateEmbedding')
        ->assertSet('telemetry.is_cached', true);
});
