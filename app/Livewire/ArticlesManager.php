<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\Audience;
use App\Jobs\GenerateArticleEmbeddingJob;
use App\Models\Article;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Manage Articles & Vector Embeddings')]
class ArticlesManager extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'audience')]
    public string $selectedAudience = '';

    #[Url(as: 'status')]
    public string $publishedFilter = '';

    public bool $showArticleModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingArticleId = null;

    public ?int $deletingArticleId = null;

    public string $title = '';

    public string $slug = '';

    public string $audience = 'students';

    public string $summary = '';

    public string $content = '';

    public bool $is_published = true;

    /**
     * Reset pagination when search filter changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when audience filter changes.
     */
    public function updatedSelectedAudience(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when published status filter changes.
     */
    public function updatedPublishedFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Open the modal to create a new article.
     */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showArticleModal = true;
    }

    /**
     * Open the modal to edit an existing article.
     */
    public function openEditModal(int $id): void
    {
        $article = Article::findOrFail($id);

        $this->editingArticleId = $article->id;
        $this->title = $article->title;
        $this->slug = $article->slug;
        $this->audience = $article->audience->value;
        $this->summary = $article->summary ?? '';
        $this->content = $article->content;
        $this->is_published = $article->is_published;

        $this->showArticleModal = true;
    }

    /**
     * Save the article (create or update).
     */
    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'audience' => ['required', Rule::enum(Audience::class)],
            'summary' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'is_published' => ['boolean'],
        ]);

        $slug = $this->generateUniqueSlug($validated['title'], $this->editingArticleId);

        if ($this->editingArticleId) {
            $article = Article::findOrFail($this->editingArticleId);
            $article->update([
                'title' => $validated['title'],
                'slug' => $slug,
                'audience' => Audience::from($validated['audience']),
                'summary' => ! empty($validated['summary']) ? $validated['summary'] : null,
                'content' => $validated['content'],
                'is_published' => $validated['is_published'],
            ]);

            Flux::toast(
                text: __('Article updated! OpenAI vector embedding job dispatched.'),
                variant: 'success'
            );
        } else {
            Article::create([
                'title' => $validated['title'],
                'slug' => $slug,
                'audience' => Audience::from($validated['audience']),
                'summary' => ! empty($validated['summary']) ? $validated['summary'] : null,
                'content' => $validated['content'],
                'is_published' => $validated['is_published'],
            ]);

            Flux::toast(
                text: __('Article created! OpenAI vector embedding job dispatched.'),
                variant: 'success'
            );
        }

        $this->showArticleModal = false;
        $this->resetForm();
    }

    /**
     * Open confirmation modal for article deletion.
     */
    public function confirmDelete(int $id): void
    {
        $this->deletingArticleId = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Delete the confirmed article.
     */
    public function deleteArticle(): void
    {
        if ($this->deletingArticleId) {
            $article = Article::find($this->deletingArticleId);
            if ($article) {
                $article->delete();
                Flux::toast(text: __('Article removed from database.'), variant: 'success');
            }
        }

        $this->showDeleteModal = false;
        $this->deletingArticleId = null;
    }

    /**
     * Manually trigger OpenAI vector embedding generation for a specific article.
     */
    public function triggerReEmbedding(int $id): void
    {
        $article = Article::findOrFail($id);
        GenerateArticleEmbeddingJob::dispatch($article);

        Flux::toast(
            text: __("Queued OpenAI embedding generation for ':title'.", ['title' => $article->title]),
            variant: 'info'
        );
    }

    /**
     * Get summary metrics for the articles and vector index.
     *
     * @return array{total: int, published: int, vectorized: int, pending: int}
     */
    #[Computed]
    public function metrics(): array
    {
        $total = Article::count();
        $published = Article::where('is_published', true)->count();
        $vectorized = Article::whereNotNull('embedding')->count();
        $pending = Article::whereNull('embedding')->count();

        return [
            'total' => $total,
            'published' => $published,
            'vectorized' => $vectorized,
            'pending' => $pending,
        ];
    }

    /**
     * Query articles for pagination.
     *
     * @return LengthAwarePaginator<int, Article>
     */
    public function articles(): LengthAwarePaginator
    {
        return Article::query()
            ->when($this->search !== '', function (Builder $query): void {
                $term = '%'.$this->search.'%';
                $query->where(function (Builder $sub) use ($term): void {
                    $sub->where('title', 'like', $term)
                        ->orWhere('summary', 'like', $term)
                        ->orWhere('content', 'like', $term);
                });
            })
            ->when($this->selectedAudience !== '', function (Builder $query): void {
                $query->where('audience', $this->selectedAudience);
            })
            ->when($this->publishedFilter !== '', function (Builder $query): void {
                $query->where('is_published', $this->publishedFilter === '1');
            })
            ->latest('updated_at')
            ->paginate(10);
    }

    /**
     * Generate a unique URL slug for the article.
     */
    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (Article::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Reset form state.
     */
    protected function resetForm(): void
    {
        $this->editingArticleId = null;
        $this->title = '';
        $this->slug = '';
        $this->audience = Audience::Students->value;
        $this->summary = '';
        $this->content = '';
        $this->is_published = true;
        $this->resetValidation();
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.articles-manager', [
            'articles' => $this->articles(),
            'audiences' => Audience::cases(),
            'metrics' => $this->metrics(),
        ]);
    }
}
