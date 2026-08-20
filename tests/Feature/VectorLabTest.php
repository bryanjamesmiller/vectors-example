<?php

declare(strict_types=1);

use App\Enums\Audience;
use App\Livewire\VectorLab;
use App\Models\Article;
use App\Services\Ai\EmbeddingService;
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

test('embedding can be generated with live telemetry and postgres cosine nearest neighbors', function () {
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
        ->assertCount('nearestMatches', 1);
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
