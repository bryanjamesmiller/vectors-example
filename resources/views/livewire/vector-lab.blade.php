<div class="max-w-6xl mx-auto px-4 py-8 sm:px-6 lg:px-8 space-y-8">
    {{-- Header --}}
    <div class="border-b border-zinc-200 dark:border-zinc-800 pb-6">
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

    {{-- Preset Scenarios & AI Randomizer --}}
    <div class="space-y-2.5">
        {{-- Row 1: Presets --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Preset articles:</span>
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

        {{-- Row 2: AI Scenario Generator --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Or generate article with AI:</span>
            <button
                wire:click="randomizeScenario"
                type="button"
                wire:loading.attr="disabled"
                wire:target="randomizeScenario"
                class="px-3.5 py-1.5 text-xs font-semibold rounded-full border border-indigo-300 dark:border-indigo-700 bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-xs cursor-pointer inline-flex items-center gap-1.5"
            >
                <span wire:loading.remove wire:target="randomizeScenario" class="inline-flex items-center gap-1">
                    🎲 <span>Generate article with OpenAI API</span>
                </span>
                <span wire:loading.inline-flex wire:target="randomizeScenario" class="items-center gap-1" style="display: none;">
                    <svg class="animate-spin size-3 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Generating...</span>
                </span>
            </button>
        </div>
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
                            wire:model.blur="title"
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
                            wire:model.live="audience"
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
                            wire:model.blur="summary"
                            rows="2"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                        ></textarea>
                    </div>

                    <div>
                        <label for="content" class="block text-xs font-medium uppercase text-zinc-600 dark:text-zinc-400">Markdown Content</label>
                        <textarea
                            id="content"
                            wire:model.blur="content"
                            rows="5"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2 text-sm font-mono text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:outline-hidden"
                            required
                        ></textarea>
                    </div>

                    <div class="pt-2 flex flex-col gap-3">
                        @if ($this->isCached)
                            <label class="flex items-center gap-2 text-xs text-zinc-700 dark:text-zinc-300 cursor-pointer">
                                <input wire:model="forceLiveCall" type="checkbox" class="rounded border-zinc-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500">
                                <span>Bypass Cache <span class="text-zinc-500 dark:text-zinc-400 font-normal">(Force Live API Call — Cached Embedding Available)</span></span>
                            </label>
                        @else
                            <label class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 opacity-90 cursor-not-allowed" title="This content is not in cache yet, so a live AI embedding call is required.">
                                <input type="checkbox" checked disabled class="rounded border-zinc-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-not-allowed opacity-60">
                                <span>Live AI API Call Required <span class="text-zinc-400 dark:text-zinc-500 font-normal">(New / Uncached Content)</span></span>
                            </label>
                        @endif

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="generateEmbedding"
                            class="w-full relative inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 transition shadow-xs cursor-pointer"
                        >
                            <span wire:loading.remove wire:target="generateEmbedding" class="inline-flex items-center gap-1.5">
                                🚀 Calculate Vector &amp; Save Article
                            </span>
                            <span wire:loading.inline-flex wire:target="generateEmbedding" class="items-center justify-center gap-2" style="display: none;">
                                <svg class="animate-spin size-4 shrink-0 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Generating Vector &amp; Saving...</span>
                            </span>
                        </button>
                    </div>

                    <div class="p-3.5 rounded-xl bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 text-xs text-blue-900 dark:text-blue-200 leading-relaxed space-y-1">
                        <div class="font-bold flex items-center gap-1.5 text-blue-700 dark:text-blue-300">
                            <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            <span>Automatic In-Database Publishing</span>
                        </div>
                        <p>
                            Calculating a vector generates the 512-dimension embedding and immediately creates and saves this article in the PostgreSQL database. It will appear at the top of the <a href="{{ route('articles.index') }}" target="_blank" class="underline font-semibold hover:text-blue-600">Articles</a> catalog and immediately participate in semantic recommendations using its vector!
                        </p>
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
                                @click="navigator.clipboard.writeText(fullVector).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
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

                        {{-- In-Database Status Banner --}}
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            @if ($isDuplicateTitle)
                                <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
                                    <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span>Article already exists in PostgreSQL database!</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-medium">
                                    <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span>Article & 512d vector saved to PostgreSQL database!</span>
                                </div>
                            @endif
                            @if ($publishedArticleSlug)
                                <a
                                    href="{{ route('articles.show', $publishedArticleSlug) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 font-semibold hover:bg-emerald-100 dark:hover:bg-emerald-900 transition shrink-0"
                                >
                                    <span>View on Articles Page</span>
                                    <span>&rarr;</span>
                                </a>
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
