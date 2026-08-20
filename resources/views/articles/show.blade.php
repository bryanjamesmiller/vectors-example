<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $article->title }} - Trade School Articles</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased">
    <div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-10">
        {{-- Navigation Header --}}
        <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4 text-sm gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('articles.index') }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    &larr; Back to all articles
                </a>
                <a href="{{ route('vector-lab') }}" class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 hover:bg-blue-100 transition">
                    🔬 Vector Lab
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a
                    href="https://github.com/bryanjamesmiller/vectors-example"
                    target="_blank"
                    class="hidden sm:inline-flex items-center gap-1 text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition"
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
                    class="hidden sm:inline-flex items-center gap-1 text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition"
                    title="Documentation"
                >
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Docs</span>
                </a>
                <span class="text-xs text-zinc-500 font-mono">
                    PostgreSQL pgvector
                </span>
            </div>
        </div>

        {{-- Main Article Content --}}
        <article class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-8 sm:p-10 shadow-sm space-y-6">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 dark:bg-indigo-950 text-indigo-800 dark:text-indigo-300">
                        Audience: {{ ucfirst(str_replace('_', ' ', $article->audience->value)) }}
                    </span>
                    <span class="text-xs text-zinc-400">
                        Published {{ $article->created_at?->diffForHumans() }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight">
                    {{ $article->title }}
                </h1>
                @if ($article->summary)
                    <p class="text-base text-zinc-600 dark:text-zinc-400 font-medium italic border-l-4 border-indigo-500 pl-4 py-1">
                        {{ $article->summary }}
                    </p>
                @endif
            </div>

            <div class="prose dark:prose-invert max-w-none text-sm sm:text-base leading-relaxed text-zinc-800 dark:text-zinc-200 whitespace-pre-wrap pt-4 border-t border-zinc-100 dark:border-zinc-800">
                {{ $article->content }}
            </div>
        </article>

        {{-- Related Articles Section (Vector Proximity via pgvector) --}}
        <section class="space-y-4 pt-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <span>Related Articles</span>
                        <span class="px-2 py-0.5 text-[11px] font-mono bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 rounded-full">
                            Auto-matched via pgvector
                        </span>
                    </h2>
                    <p class="text-xs text-zinc-500 mt-0.5">
                        Selected dynamically based on cosine similarity of their 512-dimension vector embeddings.
                    </p>
                </div>

                {{-- Hybrid Filter Pills --}}
                <div class="flex items-center gap-1.5 p-1 bg-zinc-200/70 dark:bg-zinc-800 rounded-lg text-xs self-start sm:self-auto">
                    <a
                        href="{{ route('articles.show', $article) }}"
                        class="px-2.5 py-1 rounded-md font-medium transition {{ ! $filterByAudience ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900' }}"
                    >
                        All Audiences
                    </a>
                    <a
                        href="{{ route('articles.show', [$article, 'filter_audience' => 1]) }}"
                        class="px-2.5 py-1 rounded-md font-medium transition {{ $filterByAudience ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900' }}"
                    >
                        Same Audience ({{ ucfirst($article->audience->value) }})
                    </a>
                </div>
            </div>

            @if ($relatedArticles->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($relatedArticles as $related)
                        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-5 flex flex-col justify-between shadow-xs hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                        {{ ucfirst(str_replace('_', ' ', $related->audience->value)) }}
                                    </span>
                                    <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">
                                        {{ $related->getMatchPercentage() }}% match
                                    </span>
                                </div>
                                <h3 class="text-sm font-semibold leading-snug line-clamp-2">
                                    <a href="{{ route('articles.show', $related) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ $related->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-zinc-500 line-clamp-2">
                                    {{ $related->summary ?? Str::limit($related->content, 90) }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                <a href="{{ route('articles.show', $related) }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Read article &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 bg-zinc-100 dark:bg-zinc-900 rounded-xl text-center text-xs text-zinc-500">
                    No related articles found for this audience filter.
                </div>
            @endif
        </section>
    </div>
</body>
</html>
