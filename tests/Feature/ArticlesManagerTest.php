<?php

declare(strict_types=1);

use App\Enums\Audience;
use App\Jobs\GenerateArticleEmbeddingJob;
use App\Livewire\ArticlesManager;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

test('guests are redirected to login when visiting the dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can see the articles manager and metrics', function () {
    $user = User::factory()->create();

    $article = Article::create([
        'title' => 'Welding Lab Safety Standards',
        'slug' => 'welding-lab-safety-standards',
        'audience' => Audience::Students,
        'summary' => 'Essential welding lab safety tips.',
        'content' => 'Full markdown content for welding safety.',
        'is_published' => true,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Articles & Vector Index')
        ->assertSee('Real-Time OpenAI Vector Embeddings')
        ->assertSee('Welding Lab Safety Standards');
});

test('an article can be created via the articles manager and queues vector embedding generation', function () {
    Queue::fake([GenerateArticleEmbeddingJob::class]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ArticlesManager::class)
        ->call('openCreateModal')
        ->assertSet('showArticleModal', true)
        ->set('title', 'High Voltage Conduit Bending Guide')
        ->set('audience', Audience::Students->value)
        ->set('summary', 'Practical conduit bending techniques.')
        ->set('content', 'Detailed guide for 90-degree stub ups.')
        ->set('is_published', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showArticleModal', false);

    $this->assertDatabaseHas('articles', [
        'title' => 'High Voltage Conduit Bending Guide',
        'slug' => 'high-voltage-conduit-bending-guide',
        'audience' => Audience::Students->value,
    ]);

    Queue::assertPushed(GenerateArticleEmbeddingJob::class);
});

test('an existing article can be updated via the articles manager', function () {
    Queue::fake([GenerateArticleEmbeddingJob::class]);

    $user = User::factory()->create();

    $article = Article::create([
        'title' => 'Original HVAC Troubleshooting Article',
        'slug' => 'original-hvac-troubleshooting-article',
        'audience' => Audience::Recruits,
        'summary' => 'Old summary.',
        'content' => 'Old content.',
        'is_published' => true,
    ]);

    Livewire::actingAs($user)
        ->test(ArticlesManager::class)
        ->call('openEditModal', $article->id)
        ->assertSet('showArticleModal', true)
        ->assertSet('title', 'Original HVAC Troubleshooting Article')
        ->set('title', 'Updated HVAC Troubleshooting Article')
        ->set('summary', 'New updated summary.')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showArticleModal', false);

    $this->assertDatabaseHas('articles', [
        'id' => $article->id,
        'title' => 'Updated HVAC Troubleshooting Article',
        'slug' => 'updated-hvac-troubleshooting-article',
    ]);

    Queue::assertPushed(GenerateArticleEmbeddingJob::class);
});

test('an article can be deleted via the articles manager', function () {
    $user = User::factory()->create();

    $article = Article::create([
        'title' => 'Article to be Removed',
        'slug' => 'article-to-be-removed',
        'audience' => Audience::Alumni,
        'summary' => 'Summary to delete.',
        'content' => 'Content to delete.',
        'is_published' => true,
    ]);

    Livewire::actingAs($user)
        ->test(ArticlesManager::class)
        ->call('confirmDelete', $article->id)
        ->assertSet('showDeleteModal', true)
        ->call('deleteArticle')
        ->assertSet('showDeleteModal', false);

    $this->assertDatabaseMissing('articles', [
        'id' => $article->id,
    ]);
});

test('manual vector embedding generation can be triggered for an article', function () {
    Queue::fake([GenerateArticleEmbeddingJob::class]);

    $user = User::factory()->create();

    $article = Article::create([
        'title' => 'Manual Vector Test Article',
        'slug' => 'manual-vector-test-article',
        'audience' => Audience::Teachers,
        'summary' => 'Summary.',
        'content' => 'Content.',
        'is_published' => true,
    ]);

    Livewire::actingAs($user)
        ->test(ArticlesManager::class)
        ->call('triggerReEmbedding', $article->id);

    Queue::assertPushed(GenerateArticleEmbeddingJob::class, function ($job) use ($article) {
        return $job->article->id === $article->id;
    });
});

test('articles can be searched and filtered by audience', function () {
    $user = User::factory()->create();

    Article::create([
        'title' => 'Electrical Theory for Electricians',
        'slug' => 'electrical-theory-for-electricians',
        'audience' => Audience::Students,
        'content' => 'Content about ohms law.',
        'is_published' => true,
    ]);

    Article::create([
        'title' => 'Plumbing Hydraulics for Teachers',
        'slug' => 'plumbing-hydraulics-for-teachers',
        'audience' => Audience::Teachers,
        'content' => 'Content about water pressure.',
        'is_published' => true,
    ]);

    Livewire::actingAs($user)
        ->test(ArticlesManager::class)
        ->set('search', 'Electricians')
        ->assertSee('Electrical Theory for Electricians')
        ->assertDontSee('Plumbing Hydraulics for Teachers')
        ->set('search', '')
        ->set('selectedAudience', Audience::Teachers->value)
        ->assertSee('Plumbing Hydraulics for Teachers')
        ->assertDontSee('Electrical Theory for Electricians');
});
