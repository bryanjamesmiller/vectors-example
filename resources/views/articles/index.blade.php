<x-layouts::app.header :title="__('Trade School Articles')">
    <div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-6">
            <h1 class="text-3xl font-bold tracking-tight">Trade School Articles</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Browse articles with in-database vector recommendations powered by PostgreSQL & pgvector.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($articles as $article)
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col justify-between shadow-sm hover:shadow transition-shadow duration-200">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-950 text-indigo-800 dark:text-indigo-300">
                                {{ ucfirst(str_replace('_', ' ', $article->audience->value)) }}
                            </span>
                            <span class="text-xs text-zinc-400 font-mono">512d Vector</span>
                        </div>
                        <h2 class="text-base font-semibold leading-snug">
                            <a href="{{ route('articles.show', $article) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-3">
                            {{ $article->summary ?? Str::limit($article->content, 120) }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center text-xs">
                        <a href="{{ route('articles.show', $article) }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                            Read article &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-zinc-500">
                    <p>No articles found in the database. Run <code>php artisan db:seed</code> to populate sample articles.</p>
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $articles->links() }}
        </div>
    </div>
</x-layouts::app.header>
