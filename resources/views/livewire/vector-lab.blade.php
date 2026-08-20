<div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8 space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-6">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Interactive AI Vector Lab
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                    <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Live Telemetry
                </span>
            </div>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 max-w-3xl leading-relaxed">
                Test live AI embedding generation in real time, inspect raw 512-dimension vector representations, and observe native PostgreSQL <code class="text-xs font-mono bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded">pgvector</code> cosine distance (<code class="text-xs font-mono">&lt;=&gt;</code>) similarity queries.
            </p>
        </div>

        <div class="flex items-center gap-3">
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
            <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-zinc-900 text-white hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white transition shadow-xs">
                &larr; View Articles
            </a>
        </div>
    </div>

    {{-- Preset Scenarios --}}
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider me-1">Try 1-Click Presets:</span>
        <button
            wire:click="loadPreset('welding')"
            type="button"
            class="px-3 py-1 text-xs font-medium rounded-full border border-blue-200 dark:border-blue-800 bg-blue-50/70 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 hover:bg-blue-100 transition cursor-pointer"
        >
            🤿 Underwater Welding
        </button>
        <button
            wire:click="loadPreset('electrical')"
            type="button"
            class="px-3 py-1 text-xs font-medium rounded-full border border-amber-200 dark:border-amber-800 bg-amber-50/70 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 hover:bg-amber-100 transition cursor-pointer"
        >
            ⚡ Journeyman Electrical Exam
        </button>
        <button
            wire:click="loadPreset('financial_aid')"
            type="button"
            class="px-3 py-1 text-xs font-medium rounded-full border border-emerald-200 dark:border-emerald-800 bg-emerald-50/70 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 transition cursor-pointer"
        >
            ☀️ Solar Apprenticeship Grants
        </button>
        <button
            wire:click="loadPreset('safety')"
            type="button"
            class="px-3 py-1 text-xs font-medium rounded-full border border-purple-200 dark:border-purple-800 bg-purple-50/70 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 hover:bg-purple-100 transition cursor-pointer"
        >
            🥽 Eye-Wash & Workshop Safety
        </button>
    </div>

    {{-- Main Two-Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left Column: Input Form --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 shadow-xs space-y-4">
                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                    <span>1. Input Article Content</span>
                </h2>

                <form wire:submit="generateEmbedding" class="space-y-4">
                    <div>
                        <label for="title" class="block text-xs font-medium uppercase text-zinc-600 dark:text-zinc-400">Title</label>
                        <input
                            id="title"
                            wire:model="title"
                            type="text"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                            required
                        />
                        @error('title') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="audience" class="block text-xs font-medium uppercase text-zinc-600 dark:text-zinc-400">Target Audience</label>
                        <select
                            id="audience"
                            wire:model="audience"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                        >
                            @foreach ($audiences as $aud)
                                <option value="{{ $aud->value }}">{{ ucfirst(str_replace('_', ' ', $aud->value)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="summary" class="block text-xs font-medium uppercase text-zinc-600 dark:text-zinc-400">Brief Summary</label>
                        <textarea
                            id="summary"
                            wire:model="summary"
                            rows="2"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                        ></textarea>
                    </div>

                    <div>
                        <label for="content" class="block text-xs font-medium uppercase text-zinc-600 dark:text-zinc-400">Markdown Content</label>
                        <textarea
                            id="content"
                            wire:model="content"
                            rows="5"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-sm font-mono text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                            required
                        ></textarea>
                    </div>

                    <div class="pt-2 flex flex-col gap-3">
                        <label class="flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-400 cursor-pointer">
                            <input wire:model="forceLiveCall" type="checkbox" class="rounded border-zinc-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500">
                            <span>Bypass Cache (Force Live API Call)</span>
                        </label>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition shadow-xs cursor-pointer"
                        >
                            <span wire:loading.remove>🚀 Calculate Vector</span>
                            <span wire:loading class="inline-flex items-center gap-1.5">
                                <svg class="animate-spin size-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Calling AI API...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column: Live Telemetry & Vector Proximity Output --}}
        <div class="lg:col-span-7 space-y-6">
            @if ($telemetry)
                {{-- Live Telemetry Stats Bar --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3 gap-2">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2 flex-wrap">
                            <span>2. Live AI Telemetry</span>
                            @if ($telemetry['is_cached'])
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                                    ⚡ Cached ({{ $telemetry['latency_ms'] }} ms)
                                </span>
                            @else
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300">
                                    🌐 Live Network API Call
                                </span>
                            @endif
                        </h3>

                        <div class="flex items-center gap-3 text-xs font-mono text-zinc-500">
                            <span class="truncate max-w-[200px]" title="{{ $telemetry['endpoint'] }}">{{ $telemetry['endpoint'] }}</span>
                            <span>•</span>
                            <span>{{ $telemetry['latency_ms'] }} ms roundtrip</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60">
                            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">AI Engine</span>
                            <div class="mt-1 font-bold text-sm text-zinc-900 dark:text-zinc-100 truncate" title="{{ $telemetry['provider'] }}">
                                {{ $telemetry['provider'] }}
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60">
                            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Model</span>
                            <div class="mt-1 font-bold text-sm text-blue-600 dark:text-blue-400 truncate" title="{{ $telemetry['model'] }}">
                                {{ $telemetry['model'] }}
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60">
                            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Dimensions</span>
                            <div class="mt-1 font-bold text-sm text-emerald-600 dark:text-emerald-400">
                                {{ $telemetry['dimensions'] }} Float32
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60">
                            <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">Latency</span>
                            <div class="mt-1 font-bold text-sm text-indigo-600 dark:text-indigo-400">
                                {{ $telemetry['latency_ms'] }} ms
                            </div>
                        </div>
                    </div>

                    @if ($telemetry['error'])
                        <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-xs text-red-700 dark:text-red-300">
                            <strong>API Error:</strong> {{ $telemetry['error'] }}
                        </div>
                    @endif
                </div>

                {{-- Raw Vector Floats Matrix --}}
                @if (!empty($generatedVector))
                    <div
                        x-data="{ copied: false, fullVector: @js(json_encode($generatedVector)) }"
                        class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 shadow-xs space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <span>3. Raw 512-Dimension Vector Output</span>
                            </h3>
                            <button
                                type="button"
                                @click="navigator.clipboard.writeText(fullVector); copied = true; setTimeout(() => copied = false, 2000)"
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 transition cursor-pointer"
                            >
                                <span x-show="!copied">📋 Copy Full 512d JSON</span>
                                <span x-show="copied" class="text-emerald-600 dark:text-emerald-400 font-semibold" style="display: none;">✓ Copied to Clipboard!</span>
                            </button>
                        </div>

                        <div class="p-3 rounded-xl bg-zinc-950 text-emerald-400 font-mono text-xs overflow-x-auto border border-zinc-800 leading-relaxed">
                            [
                            @foreach (array_slice($generatedVector, 0, 12) as $val)
                                <span class="text-emerald-300">{{ number_format($val, 6) }}</span>,
                            @endforeach
                            <span class="text-zinc-500">... +500 more dimensions</span>
                            ]
                        </div>

                        <details class="text-xs group">
                            <summary class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer select-none">
                                ▶ View complete 512-dimension float array
                            </summary>
                            <div class="mt-2 p-3 rounded-xl bg-zinc-950 text-emerald-300 font-mono text-[11px] max-h-48 overflow-y-auto border border-zinc-800 leading-relaxed select-all">
                                [{{ implode(', ', array_map(fn($v) => number_format($v, 6, '.', ''), $generatedVector)) }}]
                            </div>
                        </details>
                    </div>

                    {{-- In-Database Cosine Similarity Matches from PostgreSQL --}}
                    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                            <div>
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                    <span>4. PostgreSQL In-Database Cosine Proximity Matches</span>
                                    <span class="text-xs font-mono bg-blue-100 dark:bg-blue-950 text-blue-800 dark:text-blue-300 px-2 py-0.5 rounded">&lt;=&gt;</span>
                                </h3>
                                <p class="text-xs text-zinc-500 mt-0.5">
                                    Top 3 semantically closest articles calculated instantly in PostgreSQL via pgvector HNSW index.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @forelse ($nearestMatches as $idx => $match)
                                <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/40 flex items-start justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-zinc-400 font-mono">#{{ $idx + 1 }}</span>
                                            <span class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ $match['title'] }}
                                            </span>
                                            <span class="text-xs px-2 py-0.5 rounded bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                                                {{ ucfirst(str_replace('_', ' ', $match['audience'])) }}
                                            </span>
                                        </div>
                                        @if ($match['summary'])
                                            <p class="text-xs text-zinc-500 line-clamp-1">
                                                {{ $match['summary'] }}
                                            </p>
                                        @endif
                                        <div class="text-xs font-mono text-zinc-400 pt-1">
                                            Cosine Distance: <span class="text-zinc-700 dark:text-zinc-300 font-bold">{{ $match['distance'] }}</span>
                                        </div>
                                    </div>

                                    <div class="shrink-0 text-end">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            {{ $match['match_percentage'] }}% Match
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-xs text-zinc-500">
                                    No database articles found to compare.
                                </div>
                            @endforelse
                        </div>

                        {{-- Action: Publish to Knowledge Base --}}
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                            @if ($isPublished)
                                <div class="flex items-center gap-2 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span>Published live to database!</span>
                                    <a href="{{ route('articles.show', $publishedArticleSlug) }}" target="_blank" class="underline font-bold">
                                        View Live Article &rarr;
                                    </a>
                                </div>
                            @else
                                <span class="text-xs text-zinc-500">Want to add this tested article to the live database?</span>
                                <button
                                    wire:click="publishArticle"
                                    type="button"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-xs cursor-pointer"
                                >
                                    💾 Publish to Live Articles
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                {{-- Empty Placeholder state --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-800 p-12 text-center space-y-3">
                    <div class="inline-flex p-3 rounded-full bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400">
                        <svg class="size-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Ready to Vectorize</h3>
                    <p class="text-xs text-zinc-500 max-w-sm mx-auto">
                        Pick a 1-click scenario above or type custom article content, then click <strong>"Calculate Vector"</strong> to observe live API telemetry and in-database similarity matches.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
