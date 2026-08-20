<x-layouts::app.header :title="__('Trade School Articles')">
    <div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Trade School Articles</h1>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    Browse articles with in-database semantic vector search & recommendations powered by PostgreSQL & pgvector.
                </p>
            </div>
            <a href="{{ route('vector-lab') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 hover:bg-blue-100 transition shrink-0">
                <span>🚀 Open Vector Lab</span>
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        {{-- Semantic Vector Search Bar --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-5 shadow-xs space-y-3">
            <form method="GET" action="{{ route('articles.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-zinc-400">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search semantically (e.g., 'hyperbaric pressure testing', 'electrical code exam', 'FAFSA grant aid')..."
                        class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 pl-10 pr-4 py-2.5 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer shrink-0 shadow-xs"
                    >
                        <span>🧠 Semantic Search</span>
                    </button>
                    @if ($search !== '')
                        <a
                            href="{{ route('articles.index') }}"
                            class="px-4 py-2.5 text-sm font-medium rounded-xl border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-center shrink-0"
                        >
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-zinc-500 dark:text-zinc-400 pt-1">
                <div class="flex items-center gap-1.5">
                    <span class="inline-block size-2 rounded-full bg-emerald-500"></span>
                    <span>In-database 512d pgvector similarity query (<code>&lt;=&gt;</code> cosine distance)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span>Try:</span>
                    <a href="{{ route('articles.index', ['q' => 'underwater welding safety']) }}" class="underline hover:text-blue-600">underwater welding</a> &bull;
                    <a href="{{ route('articles.index', ['q' => 'solar clean energy grants']) }}" class="underline hover:text-blue-600">solar grants</a> &bull;
                    <a href="{{ route('articles.index', ['q' => 'workshop chemical eye wash']) }}" class="underline hover:text-blue-600">eye wash safety</a>
                </div>
            </div>
        </div>

        {{-- Active Search Query Notification Banner --}}
        @if ($search !== '')
            <div class="p-3.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 flex items-center justify-between gap-3 text-xs text-blue-900 dark:text-blue-200">
                <div class="flex items-center gap-2">
                    <span class="text-base">🔍</span>
                    <span>
                        Showing <strong>{{ $isVectorSearch ? 'AI vector similarity' : 'text keyword' }}</strong> search results for <strong>&ldquo;{{ $search }}&rdquo;</strong> ranked by nearest semantic proximity.
                    </span>
                </div>
                <a href="{{ route('articles.index') }}" class="underline font-semibold hover:text-blue-600 shrink-0">
                    Reset search
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($articles as $article)
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 p-6 flex flex-col justify-between shadow-xs hover:shadow transition-shadow duration-200">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-950 text-indigo-800 dark:text-indigo-300">
                                {{ ucfirst(str_replace('_', ' ', $article->audience->value)) }}
                            </span>
                            @if ($isVectorSearch && ! is_null($article->neighbor_distance))
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 font-mono">
                                    {{ $article->getMatchPercentage() }}% Match
                                </span>
                            @else
                                <span class="text-xs text-zinc-400 font-mono">512d Vector</span>
                            @endif
                        </div>
                        <h2 class="text-base font-semibold leading-snug">
                            <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-3">
                            {{ $article->summary ?? Str::limit($article->content, 120) }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center text-xs">
                        <a href="{{ route('articles.show', $article) }}" class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            Read article &rarr;
                        </a>
                        @if ($isVectorSearch && ! is_null($article->neighbor_distance))
                            <span class="text-zinc-400 text-[11px] font-mono">
                                d: {{ round((float) $article->neighbor_distance, 3) }}
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-zinc-500 space-y-2">
                    <p class="text-base font-medium text-zinc-700 dark:text-zinc-300">No articles matched your search query.</p>
                    <p class="text-xs text-zinc-500">Try adjusting your keywords or clearing the search to browse all published articles.</p>
                    <a href="{{ route('articles.index') }}" class="inline-block mt-3 px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                        Clear Search
                    </a>
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $articles->links() }}
        </div>
    </div>
</x-layouts::app.header>
