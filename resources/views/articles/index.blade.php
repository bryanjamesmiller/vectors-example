<x-layouts::app.header :title="__('Trade School Articles')">
    <div class="max-w-5xl mx-auto px-4 py-12 sm:px-6 lg:px-8 space-y-8">
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-6">
            <h1 class="text-3xl font-bold tracking-tight">Trade School Articles</h1>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Browse articles with in-database semantic vector search & recommendations powered by PostgreSQL & pgvector.
            </p>
        </div>

        {{-- Semantic Vector Search Bar --}}
        <x-semantic-search-bar
            :value="$search"
            :show-telemetry="true"
            :light-shadow="true"
            placeholder="Search articles semantically (e.g. 'welding safety', 'conduit sizing')..."
            :suggestions="[
                ['emoji' => '❄️', 'label' => 'HVAC Diagnostics', 'query' => 'commercial refrigeration superheat diagnostics'],
                ['emoji' => '🥽', 'label' => 'Workshop PPE', 'query' => 'personal protective equipment ppe guidelines'],
                ['emoji' => '🔒', 'label' => 'Lockout/Tagout', 'query' => 'high voltage electrical lockout tagout procedures'],
            ]"
        />

        {{-- Active Search Query Notification Banner --}}
        @if ($search !== '')
            <div class="p-3.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 flex items-center justify-between gap-3 text-xs text-blue-900 dark:text-blue-200">
                <div class="flex items-center gap-2">
                    <svg class="size-4 shrink-0 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>
                        Showing <strong>{{ $isVectorSearch ? 'AI vector similarity' : 'text keyword' }}</strong> search results for <strong>&ldquo;{{ $search }}&rdquo;</strong>{{ $isVectorSearch ? ' ranked by nearest semantic proximity.' : '.' }}
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
                    @if ($search !== '')
                        <p class="text-base font-medium text-zinc-700 dark:text-zinc-300">No articles matched your search query.</p>
                        <p class="text-xs text-zinc-500">Try adjusting your keywords or clearing the search to browse all published articles.</p>
                        <a href="{{ route('articles.index') }}" class="inline-block mt-3 px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                            Clear Search
                        </a>
                    @else
                        <p class="text-base font-medium text-zinc-700 dark:text-zinc-300">No published articles found.</p>
                        <p class="text-xs text-zinc-500">Check back soon for new trade school articles and guides.</p>
                    @endif
                </div>
            @endforelse
        </div>

        <div class="pt-4">
            {{ $articles->links() }}
        </div>
    </div>
</x-layouts::app.header>
