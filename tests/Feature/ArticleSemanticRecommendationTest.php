<?php

declare(strict_types=1);

use App\Enums\Audience;
use App\Models\Article;
use Database\Seeders\ArticleSeeder;

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

    // Related articles must be in the same Financial Aid & Grants cluster
    $titles = $related->pluck('title')->all();
    expect($titles)->toContain('Veteran Financial Assistance & GI Bill Tuition Coverage')
        ->and($titles)->toContain('Emergency Student Aid & Housing Relief Grants');

    // Verify high similarity percentage
    expect($related->first()->getMatchPercentage())->toBeGreaterThan(80);
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
    $response = $this->get(route('articles.index'));

    $response->assertOk()
        ->assertSee('Trade School Knowledge Base')
        ->assertSee('Applying for Trade Tool Grants &amp; Fee Waivers', false);
});

test('article show page renders article and related recommendations', function () {
    $article = Article::where('slug', 'applying-for-trade-tool-grants-fee-waivers')->firstOrFail();

    $response = $this->get(route('articles.show', $article));

    $response->assertOk()
        ->assertSee($article->title)
        ->assertSee('Related Articles')
        ->assertSee('Auto-matched via pgvector')
        ->assertSee('Veteran Financial Assistance &amp; GI Bill Tuition Coverage', false);
});
