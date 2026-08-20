<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\Audience;
use App\Models\Article;
use App\Services\Ai\EmbeddingService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Pgvector\Laravel\Vector;

/**
 * @property-read bool $isCached
 */
#[Title('Interactive AI Vector Lab — Live Telemetry & Proximity Inspector')]
class VectorLab extends Component
{
    public string $title = 'Underwater Welding Safety & Pressure Chamber Protocols';

    public string $audience = 'students';

    public string $summary = 'Critical hyperbaric welding safety standards, decompression protocols, and gas manifold checks for offshore technicians.';

    public string $content = 'Hyperbaric and underwater welding requires strict adherence to ASME Section IX standards. Technicians must verify gas manifold pressure differentials before diving and monitor sealed electrode insulation. Never initiate an arc while oxygen saturation in the hyperbaric habitat exceeds safety thresholds.';

    public bool $forceLiveCall = true;

    /**
     * @var array{
     *     provider: string,
     *     model: string,
     *     dimensions: int,
     *     latency_ms: float,
     *     is_cached: bool,
     *     endpoint: string,
     *     character_count: int,
     *     error: ?string
     * }|null
     */
    public ?array $telemetry = null;

    /**
     * @var list<float>|null
     */
    public ?array $generatedVector = null;

    /**
     * @var array<int, array{id: int, title: string, audience: string, summary: ?string, slug: string, distance: float, match_percentage: int}>
     */
    public array $nearestMatches = [];

    public bool $isPublished = false;

    public ?string $publishedArticleSlug = null;

    public bool $isDuplicateTitle = false;

    /**
     * Presets available for 1-click testing.
     *
     * @var array<string, array{title: string, audience: string, summary: string, content: string}>
     */
    protected array $presets = [
        'welding' => [
            'title' => 'Underwater Welding Safety & Pressure Chamber Protocols',
            'audience' => 'students',
            'summary' => 'Critical hyperbaric welding safety standards, decompression protocols, and gas manifold checks for offshore technicians.',
            'content' => 'Hyperbaric and underwater welding requires strict adherence to ASME Section IX standards. Technicians must verify gas manifold pressure differentials before diving and monitor sealed electrode insulation. Never initiate an arc while oxygen saturation in the hyperbaric habitat exceeds safety thresholds.',
        ],
        'electrical' => [
            'title' => 'Journeyman Electrical Master Licensing Exam Tactics',
            'audience' => 'alumni',
            'summary' => 'Speed calculation tactics for branch circuit sizing, conduit fill tables, and National Electrical Code navigation.',
            'content' => 'Passing the journeyman electrical examination requires rapid mastery of NEC Chapter 9 tables. Practice timed conduit fill calculations and motor feeder overload protection formulas to maximize scoring on the practical code section.',
        ],
        'financial_aid' => [
            'title' => 'Solar Technician Apprenticeship Grants and Stipends',
            'audience' => 'recruits',
            'summary' => 'How prospective photovoltaic technicians can secure state clean energy grants and monthly tool allowances.',
            'content' => 'Clean energy vocational initiatives offer tuition assistance and tool stipends for solar photovoltaic installation recruits. Submit proof of enrollment and FAFSA documentation to receive full program coverage and safety equipment vouchers.',
        ],
        'safety' => [
            'title' => 'Workshop Eye-Wash Stations & Chemical Splash Response',
            'audience' => 'teachers',
            'summary' => 'Inspection protocols and emergency response procedures for acid, solvent, and coolant exposure in workshop labs.',
            'content' => 'All vocational workshop labs must test eye-wash stations and emergency showers weekly. In the event of chemical contact, flush the affected area for a minimum of 15 minutes and immediately report the incident to campus safety personnel.',
        ],
    ];

    /**
     * Load a pre-defined test scenario.
     */
    public function loadPreset(string $presetKey): void
    {
        if (isset($this->presets[$presetKey])) {
            $preset = $this->presets[$presetKey];
            $this->title = $preset['title'];
            $this->audience = $preset['audience'];
            $this->summary = $preset['summary'];
            $this->content = $preset['content'];
            $this->resetCalculations();
        }
    }

    /**
     * Reset calculated results.
     */
    public function resetCalculations(): void
    {
        $this->telemetry = null;
        $this->generatedVector = null;
        $this->nearestMatches = [];
        $this->isPublished = false;
        $this->publishedArticleSlug = null;
        $this->isDuplicateTitle = false;
    }

    /**
     * Determine whether an embedding is already cached for the current input.
     */
    #[Computed]
    public function isCached(): bool
    {
        $summaryText = $this->summary ? "**Summary:** {$this->summary}\n\n" : '';
        $textToEmbed = <<<MARKDOWN
        # {$this->title}
        **Target Audience:** {$this->audience}
        {$summaryText}## Content
        {$this->content}
        MARKDOWN;

        $model = (string) config('ai.embedding.model', 'nomic-embed-text');
        $dimensions = (int) config('ai.embedding.dimensions', 512);
        $cleaned = trim(preg_replace('/\s+/', ' ', $textToEmbed) ?? $textToEmbed);
        $cacheKey = 'ai_embedding:'.hash('sha256', "{$model}:{$dimensions}:{$cleaned}");

        return Cache::has($cacheKey);
    }

    /**
     * Generate embedding with live telemetry and execute in-database pgvector similarity query.
     */
    public function generateEmbedding(EmbeddingService $embeddingService): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'audience' => ['required', 'string'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
        ]);

        $summaryText = $this->summary ? "**Summary:** {$this->summary}\n\n" : '';
        $textToEmbed = <<<MARKDOWN
        # {$this->title}
        **Target Audience:** {$this->audience}
        {$summaryText}## Content
        {$this->content}
        MARKDOWN;

        $forceLive = ! $this->isCached() || $this->forceLiveCall;
        $isCached = ! $forceLive;

        if (! $isCached) {
            $key = 'vector-lab-live:'.(request()->ip() ?? 'unknown');
            if (RateLimiter::tooManyAttempts($key, 15)) {
                $seconds = RateLimiter::availableIn($key);
                Flux::toast(
                    text: __('Rate limit reached. Please wait :seconds seconds before requesting another live AI embedding.', ['seconds' => $seconds]),
                    variant: 'danger'
                );

                return;
            }

            RateLimiter::hit($key, 60);
        }

        $telemetryResult = $embeddingService->generateWithTelemetry($textToEmbed, $forceLive);

        $this->telemetry = [
            'provider' => $telemetryResult['provider'],
            'model' => $telemetryResult['model'],
            'dimensions' => $telemetryResult['dimensions'],
            'latency_ms' => $telemetryResult['latency_ms'],
            'is_cached' => $telemetryResult['is_cached'],
            'endpoint' => $telemetryResult['endpoint'],
            'character_count' => $telemetryResult['character_count'],
            'error' => $telemetryResult['error'],
        ];

        $this->generatedVector = $telemetryResult['embedding'];

        if (! empty($this->generatedVector)) {
            $this->calculateNearestNeighbors($this->generatedVector);

            $existingArticle = Article::where('title', $this->title)->first();

            if ($existingArticle) {
                Article::withoutEvents(function () use ($existingArticle): void {
                    $existingArticle->update([
                        'audience' => Audience::from($this->audience),
                        'summary' => ! empty($this->summary) ? $this->summary : null,
                        'content' => $this->content,
                        'is_published' => true,
                        'embedding' => new Vector($this->generatedVector),
                    ]);
                });

                $this->isPublished = true;
                $this->publishedArticleSlug = $existingArticle->slug;
                $this->isDuplicateTitle = true;

                if ($telemetryResult['is_cached']) {
                    Flux::toast(
                        text: __('⚡ 512d vector resolved from cache in :ms ms (Article already exists)', [
                            'ms' => $telemetryResult['latency_ms'],
                        ]),
                        variant: 'success'
                    );
                } else {
                    Flux::toast(
                        text: __('🌐 Live 512d vector generated in :ms ms via :provider (Article already exists)', [
                            'ms' => $telemetryResult['latency_ms'],
                            'provider' => $telemetryResult['provider'],
                        ]),
                        variant: 'success'
                    );
                }
            } else {
                $this->isDuplicateTitle = false;

                $baseSlug = Str::slug($this->title);
                $slug = $baseSlug;
                $counter = 1;

                while (Article::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $article = Article::withoutEvents(fn (): Article => Article::create([
                    'title' => $this->title,
                    'slug' => $slug,
                    'audience' => Audience::from($this->audience),
                    'summary' => ! empty($this->summary) ? $this->summary : null,
                    'content' => $this->content,
                    'is_published' => true,
                    'embedding' => new Vector($this->generatedVector),
                ]));

                $this->isPublished = true;
                $this->publishedArticleSlug = $article->slug;

                if ($telemetryResult['is_cached']) {
                    Flux::toast(
                        text: __('⚡ 512d vector resolved from cache in :ms ms and saved to Articles!', [
                            'ms' => $telemetryResult['latency_ms'],
                        ]),
                        variant: 'success'
                    );
                } else {
                    Flux::toast(
                        text: __('🌐 Live 512d vector generated in :ms ms via :provider and saved to Articles!', [
                            'ms' => $telemetryResult['latency_ms'],
                            'provider' => $telemetryResult['provider'],
                        ]),
                        variant: 'success'
                    );
                }
            }
        } else {
            Flux::toast(
                text: __('Vector generation failed: :error', ['error' => $telemetryResult['error'] ?? 'Unknown error']),
                variant: 'danger'
            );
        }
    }

    /**
     * Query PostgreSQL pgvector for the closest existing articles via cosine distance (<=>).
     *
     * @param  list<float>  $vector
     */
    protected function calculateNearestNeighbors(array $vector): void
    {
        $vectorString = '['.implode(',', $vector).']';

        /** @var Collection<int, Article> $articles */
        $articles = Article::query()
            ->where('is_published', true)
            ->whereNotNull('embedding')
            ->select('articles.*')
            ->selectRaw('embedding <=> ? as cosine_distance', [$vectorString])
            ->orderBy('cosine_distance')
            ->take(3)
            ->get();

        $this->nearestMatches = $articles->map(function (Article $article): array {
            $distance = (float) ($article->getAttribute('cosine_distance') ?? 1.0);
            $similarity = 1.0 - $distance;
            $percentage = (int) max(0, min(100, round($similarity * 100)));

            return [
                'id' => $article->id,
                'title' => $article->title,
                'audience' => $article->audience->value,
                'summary' => $article->summary,
                'slug' => $article->slug,
                'distance' => round($distance, 4),
                'match_percentage' => $percentage,
            ];
        })->all();
    }

    /**
     * Publish the tested article and its generated vector directly to PostgreSQL.
     */
    public function publishArticle(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'audience' => ['required', 'string'],
            'content' => ['required', 'string'],
        ]);

        if (empty($this->generatedVector)) {
            Flux::toast(text: __('Please generate a vector embedding first before publishing.'), variant: 'danger');

            return;
        }

        $baseSlug = Str::slug($this->title);
        $slug = $baseSlug;
        $counter = 1;

        while (Article::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $article = Article::create([
            'title' => $this->title,
            'slug' => $slug,
            'audience' => Audience::from($this->audience),
            'summary' => ! empty($this->summary) ? $this->summary : null,
            'content' => $this->content,
            'is_published' => true,
            'embedding' => new Vector($this->generatedVector),
        ]);

        $this->isPublished = true;
        $this->publishedArticleSlug = $article->slug;

        Flux::toast(
            text: __('Published to live Articles! You can now view it live.'),
            variant: 'success'
        );
    }

    /**
     * Render the Vector Lab view.
     */
    public function render(): View
    {
        return view('livewire.vector-lab', [
            'audiences' => Audience::cases(),
        ])->layout('layouts.app.header', ['title' => 'Interactive AI Vector Lab']);
    }
}
