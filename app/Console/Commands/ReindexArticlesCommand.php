<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\GenerateArticleEmbeddingJob;
use App\Models\Article;
use Illuminate\Console\Command;

class ReindexArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:re-embed {--sync : Run embedding synchronously instead of background queuing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate OpenAI vector embeddings for all articles in the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $articles = Article::all();
        $count = $articles->count();

        $this->info("Found {$count} articles to process.");

        if ($count === 0) {
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($articles as $article) {
            if ($this->option('sync')) {
                GenerateArticleEmbeddingJob::dispatchSync($article);
            } else {
                GenerateArticleEmbeddingJob::dispatch($article);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All article embedding jobs have been processed/dispatched.');

        return self::SUCCESS;
    }
}
