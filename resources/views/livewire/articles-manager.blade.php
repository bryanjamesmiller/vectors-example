<div class="flex flex-col gap-6 w-full">
    {{-- Header & AI Intelligence Banner --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                <span>Articles & Vector Index</span>
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                    <span class="size-1.5 rounded-full bg-blue-600 dark:bg-blue-400 animate-pulse"></span>
                    pgvector 512d
                </span>
            </h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Manage trade school articles, inspect in-database vector embeddings, and trigger real-time AI vectorization.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('articles.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-750 transition shadow-xs">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
                <span>View Public Articles</span>
            </a>

            <button wire:click="openCreateModal" type="button" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 transition shadow-xs">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>New Article</span>
            </button>
        </div>
    </div>

    {{-- Winsome OpenAI Vector Architecture Callout --}}
    <div class="relative overflow-hidden rounded-xl border border-blue-200 dark:border-blue-800/60 bg-gradient-to-br from-blue-50/80 via-white to-indigo-50/50 dark:from-blue-950/30 dark:via-zinc-900 dark:to-indigo-950/20 p-5 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="p-2.5 rounded-xl bg-blue-600 text-white shadow-xs shrink-0 mt-0.5">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span>Real-Time AI Vector Embeddings</span>
                        <span class="text-xs font-normal text-blue-700 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/50 px-2 py-0.5 rounded font-mono">{{ config('ai.embedding.model') }}</span>
                    </h2>
                    <p class="mt-1 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">
                        When you create or update an article, an asynchronous observer queues a background job that converts the article title, audience, and Markdown content into a <strong>512-dimension vector</strong>. PostgreSQL stores it in a native <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded font-mono">vector(512)</code> column indexed with <strong>HNSW cosine distance (<code class="text-xs font-mono">&lt;=&gt;</code>)</strong>, powering instant semantic recommendations without third-party vector databases.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Metrics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Articles</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $metrics['total'] }}</span>
                <span class="text-xs text-zinc-500">In Database</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Vectorized</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $metrics['vectorized'] }}</span>
                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">512d Embeddings</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Published</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $metrics['published'] }}</span>
                <span class="text-xs text-zinc-500">Live in Articles</span>
            </div>
        </div>

        <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Pending Vectors</span>
            <div class="mt-1 flex items-baseline justify-between">
                <span class="text-2xl font-bold {{ $metrics['pending'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-400' }}">
                    {{ $metrics['pending'] }}
                </span>
                <span class="text-xs text-zinc-500">Unindexed</span>
            </div>
        </div>
    </div>

    {{-- Filter & Search Toolbar --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-3 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-zinc-400">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <input
                wire:model.live.debounce.300ms="search"
                type="search"
                aria-label="Search articles by title or keyword"
                placeholder="Search articles by title or keyword..."
                class="w-full ps-9 pe-3 py-1.5 text-sm rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-hidden focus:ring-2 focus:ring-blue-500"
            />
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <select
                wire:model.live="selectedAudience"
                aria-label="Filter by target audience"
                class="text-sm rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 px-3 py-1.5 focus:outline-hidden focus:ring-2 focus:ring-blue-500"
            >
                <option value="">All Audiences</option>
                @foreach ($audiences as $aud)
                    <option value="{{ $aud->value }}">{{ ucfirst(str_replace('_', ' ', $aud->value)) }}</option>
                @endforeach
            </select>

            <select
                wire:model.live="publishedFilter"
                aria-label="Filter by publication status"
                class="text-sm rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 px-3 py-1.5 focus:outline-hidden focus:ring-2 focus:ring-blue-500"
            >
                <option value="">All Statuses</option>
                <option value="1">Published</option>
                <option value="0">Draft</option>
            </select>
        </div>
    </div>

    {{-- Articles Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-sm text-zinc-600 dark:text-zinc-400">
                <thead class="bg-zinc-50 dark:bg-zinc-800/60 text-xs font-semibold uppercase text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-start">Article</th>
                        <th scope="col" class="px-4 py-3 text-start">Audience</th>
                        <th scope="col" class="px-4 py-3 text-start">Embedding Status</th>
                        <th scope="col" class="px-4 py-3 text-start">Status</th>
                        <th scope="col" class="px-4 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($articles as $article)
                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition">
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100 line-clamp-1">
                                    {{ $article->title }}
                                </div>
                                @if ($article->summary)
                                    <div class="text-xs text-zinc-500 line-clamp-1 mt-0.5">
                                        {{ $article->summary }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ ucfirst(str_replace('_', ' ', $article->audience->value)) }}
                                </span>
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if ($article->embedding)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                        <svg class="size-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                        </svg>
                                        <span>512d Vector Indexed</span>
                                    </span>
                                @else
                                    <button
                                        wire:click="triggerReEmbedding({{ $article->id }})"
                                        type="button"
                                        title="Click to generate OpenAI vector embedding"
                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50 hover:bg-amber-100 transition cursor-pointer"
                                    >
                                        <span class="size-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                        <span>Generate 512d Vector</span>
                                    </button>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if ($article->is_published)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                        Draft
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-end whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    @if ($article->is_published)
                                        <a
                                            href="{{ route('articles.show', $article) }}"
                                            target="_blank"
                                            title="View Live in Articles"
                                            class="p-1.5 rounded-md text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                                        >
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </a>
                                    @else
                                        <span
                                            title="Draft (Publish to view publicly)"
                                            class="p-1.5 rounded-md text-zinc-300 dark:text-zinc-700 cursor-not-allowed opacity-60"
                                        >
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </span>
                                    @endif

                                    <button
                                        wire:click="openEditModal({{ $article->id }})"
                                        type="button"
                                        title="Edit Article"
                                        class="p-1.5 rounded-md text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                                    >
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>

                                    <button
                                        wire:click="confirmDelete({{ $article->id }})"
                                        type="button"
                                        title="Delete Article"
                                        class="p-1.5 rounded-md text-zinc-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                                    >
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-zinc-500">
                                No articles match the search or filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($articles->hasPages())
            <div class="px-4 py-3 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

    {{-- Create / Edit Article Modal --}}
    @if ($showArticleModal)
        <div
            wire:keydown.escape="$set('showArticleModal', false)"
            tabindex="0"
            role="dialog"
            aria-modal="true"
            aria-labelledby="article-modal-title"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/60 backdrop-blur-xs focus:outline-hidden"
        >
            <div class="relative w-full max-w-2xl rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 id="article-modal-title" class="text-lg font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span>{{ $editingArticleId ? 'Edit Article' : 'Create New Article' }}</span>
                    </h3>
                    <button wire:click="$set('showArticleModal', false)" type="button" aria-label="Close modal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Explanatory Notice inside Modal --}}
                <div class="my-4 p-3.5 rounded-xl border border-blue-200 dark:border-blue-900/60 bg-blue-50/70 dark:bg-blue-950/30 text-xs text-blue-900 dark:text-blue-300 flex items-start gap-2.5">
                    <svg class="size-4 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                    </svg>
                    <div>
                        <strong class="font-semibold">Automatic AI Vector Generation:</strong> Saving will automatically dispatch a background job to convert this content into a 512-dimension vector embedding using <code class="bg-blue-100 dark:bg-blue-900/60 px-1 py-0.5 rounded text-xs font-mono">{{ config('ai.embedding.model') }}</code>.
                    </div>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Title</label>
                        <input
                            id="title"
                            wire:model="title"
                            type="text"
                            placeholder="e.g. Electrical Safety Protocols for Industrial Labs"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                            required
                        />
                        @error('title') <span class="text-xs text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="audience" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Target Audience</label>
                            <select
                                id="audience"
                                wire:model="audience"
                                class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                                required
                            >
                                @foreach ($audiences as $aud)
                                    <option value="{{ $aud->value }}">{{ ucfirst(str_replace('_', ' ', $aud->value)) }}</option>
                                @endforeach
                            </select>
                            @error('audience') <span class="text-xs text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input
                                    wire:model="is_published"
                                    type="checkbox"
                                    class="sr-only peer"
                                />
                                <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-hidden peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer dark:bg-zinc-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-zinc-600 peer-checked:bg-blue-600"></div>
                                <span class="ms-3 text-sm font-medium text-zinc-900 dark:text-zinc-300">Published to Articles</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="summary" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Brief Summary</label>
                        <textarea
                            id="summary"
                            wire:model="summary"
                            rows="2"
                            placeholder="A concise 1-2 sentence overview for the article card and vector weighting..."
                            class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                        ></textarea>
                        @error('summary') <span class="text-xs text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Markdown Content</label>
                        <textarea
                            id="content"
                            wire:model="content"
                            rows="7"
                            placeholder="Detailed technical instructions, safety standards, and practical trade guidance..."
                            class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-sm font-mono text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                            required
                        ></textarea>
                        @error('content') <span class="text-xs text-red-600 dark:text-red-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <button
                            wire:click="$set('showArticleModal', false)"
                            type="button"
                            class="px-4 py-2 text-sm font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-750 transition"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 active:bg-blue-800 transition shadow-xs"
                        >
                            <span>{{ $editingArticleId ? 'Save & Vectorize' : 'Create & Vectorize' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div
            wire:keydown.escape="$set('showDeleteModal', false)"
            tabindex="0"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-modal-title"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/60 backdrop-blur-xs focus:outline-hidden"
        >
            <div class="relative w-full max-w-md rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-red-100 text-red-600 dark:bg-red-950/50 dark:text-red-400">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                    <div>
                        <h4 id="delete-modal-title" class="text-base font-bold text-zinc-900 dark:text-zinc-100">Delete Article</h4>
                        <p class="mt-1 text-xs text-zinc-500">
                            Are you sure you want to permanently remove this article and its vector embedding from PostgreSQL?
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button
                        wire:click="$set('showDeleteModal', false)"
                        type="button"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-750 transition"
                    >
                        Cancel
                    </button>

                    <button
                        wire:click="deleteArticle"
                        type="button"
                        class="px-4 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 active:bg-red-800 transition shadow-xs"
                    >
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
