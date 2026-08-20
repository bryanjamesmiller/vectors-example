<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Audience;
use Carbon\CarbonImmutable;
use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\Distance;
use Pgvector\Laravel\HasNeighbors;
use Pgvector\Laravel\Vector;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property Audience $audience
 * @property string|null $summary
 * @property string $content
 * @property bool $is_published
 * @property Vector|null $embedding
 * @property float|null $neighbor_distance
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory, HasNeighbors;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'audience',
        'summary',
        'content',
        'is_published',
        'embedding',
    ];

    /**
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'audience' => Audience::class,
            'is_published' => 'boolean',
            'embedding' => Vector::class,
        ];
    }

    /**
     * Retrieve the most semantically related articles based on vector proximity.
     *
     * @return Collection<int, Article>
     */
    public function relatedArticles(int $limit = 3): Collection
    {
        if (is_null($this->embedding)) {
            return new Collection;
        }

        /** @var Collection<int, Article> $results */
        $results = $this->nearestNeighbors('embedding', Distance::Cosine)
            ->where('is_published', true)
            ->take($limit)
            ->get();

        return $results;
    }

    /**
     * Calculate a readable match percentage (0% to 100%) from the cosine neighbor distance.
     */
    public function getMatchPercentage(): int
    {
        if (is_null($this->neighbor_distance)) {
            return 100;
        }

        // Cosine distance = 1 - cosine_similarity (ranges from 0 to 2)
        $similarity = 1.0 - $this->neighbor_distance;

        return (int) max(0, min(100, round($similarity * 100)));
    }
}
