<x-layouts::app.header :title="$article->title">
    <div class="max-w-4xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-10">
        {{-- Navigation Breadcrumb --}}
        <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4 text-sm">
            <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                &larr; Back to all articles
            </a>
            <span class="text-xs text-zinc-500 font-mono">
                PostgreSQL pgvector
            </span>
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
</x-layouts::app.header>
