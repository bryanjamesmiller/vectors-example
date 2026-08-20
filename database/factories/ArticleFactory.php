<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Audience;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->randomNumber(4),
            'audience' => fake()->randomElement(Audience::cases()),
            'summary' => fake()->paragraph(2),
            'content' => fake()->paragraphs(4, true),
            'is_published' => true,
            'embedding' => array_map(fn () => fake()->randomFloat(6, -0.1, 0.1), range(1, 512)),
        ];
    }

    /**
     * Indicate that the article is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }
}
