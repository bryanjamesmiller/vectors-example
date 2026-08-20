<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trade School Articles - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased">
    <div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Trade School Articles</h1>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Browse articles with in-database vector recommendations powered by PostgreSQL & pgvector.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('vector-lab') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-xs">
                    <span>🔬 Try Live Vector Lab</span>
                </a>
                <a
                    href="https://github.com/bryanjamesmiller/vectors-example"
                    target="_blank"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                    title="GitHub Repository"
                >
                    <svg class="size-4" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                    </svg>
                    <span>Repo</span>
                </a>
                <a
                    href="https://github.com/bryanjamesmiller/vectors-example/blob/main/README.md"
                    target="_blank"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                    title="Documentation"
                >
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Docs</span>
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white transition shadow-xs">
                    &larr; Home
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($articles as $article)
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col justify-between shadow-sm hover:shadow transition">
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
</body>
</html>
