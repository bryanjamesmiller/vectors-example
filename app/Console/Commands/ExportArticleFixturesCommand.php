<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportArticleFixturesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:export-fixtures {--path= : Custom file path for the exported JSON fixtures}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all database articles and their vector embeddings to a JSON fixture file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $customPath = $this->option('path');
        $targetPath = is_string($customPath) && $customPath !== ''
            ? $customPath
            : database_path('seeders/data/articles.json');

        $articles = Article::query()->orderBy('id')->get();

        if ($articles->isEmpty()) {
            $this->warn('No articles found in database to export.');

            return self::SUCCESS;
        }

        $data = $articles->map(function (Article $article): array {
            return [
                'title' => $article->title,
                'audience' => $article->audience->value,
                'summary' => $article->summary,
                'content' => $article->content,
                'is_published' => $article->is_published,
                'embedding' => $article->embedding?->toArray(),
            ];
        })->all();

        $directory = dirname($targetPath);
        File::ensureDirectoryExists($directory);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            $this->error('Failed to encode article fixtures to JSON.');

            return self::FAILURE;
        }

        File::put($targetPath, $json);

        $this->info("Successfully exported {$articles->count()} articles with embeddings to:");
        $this->line("  <comment>{$targetPath}</comment>");

        return self::SUCCESS;
    }
}
