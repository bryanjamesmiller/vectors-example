<div
    x-data="{
        scrollToBottom() {
            $nextTick(() => {
                const el = this.$refs.chatContainer;
                if (el) {
                    el.scrollTop = el.scrollHeight;
                }
            });
        }
    }"
    x-init="
        const observer = new MutationObserver(() => {
            const el = $refs.chatContainer;
            if (el) {
                const distanceFromBottom = el.scrollHeight - el.scrollTop - el.clientHeight;
                if (distanceFromBottom <= 50) {
                    scrollToBottom();
                }
            }
        });
        observer.observe($refs.chatContainer, { childList: true, subtree: true, characterData: true });
        scrollToBottom();
    "
    class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 space-y-5 flex flex-col h-[calc(100vh-5rem)]"
>
    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4 gap-4 shrink-0">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5 flex-wrap">
                <div class="size-8 rounded-xl bg-gradient-to-tr from-indigo-500 via-blue-500 to-emerald-400 flex items-center justify-center text-white shadow-sm text-sm font-bold">
                    ⚔️
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Trade School AI
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60">
                    <span class="size-1.5 rounded-full bg-indigo-500 motion-safe:animate-pulse"></span>
                    RAG Evaluation Arena
                </span>
                <span class="text-xs font-mono px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700">
                    Model: {{ config('ai.chat.model', 'gpt-4o-mini') }}
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Side-by-side benchmark: compare in-database <code class="font-mono bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded text-[11px]">pgvector</code> knowledge grounding against a raw LLM parametric memory baseline on the exact same question.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            @if (count($messages) > 0)
                <button
                    wire:click="clearChat"
                    type="button"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700/60 transition cursor-pointer flex items-center gap-1.5 shadow-xs"
                >
                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    <span>Clear Arena</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Chat Messages Scroll Area --}}
    <div
        x-ref="chatContainer"
        class="flex-1 overflow-y-auto space-y-6 pr-2 -mr-2 scroll-smooth"
    >
        @if (empty($messages))
            {{-- Empty Welcome State --}}
            <div class="h-full flex flex-col items-center justify-center text-center max-w-3xl mx-auto py-6 space-y-6">
                <div class="size-16 rounded-2xl bg-gradient-to-tr from-indigo-500 via-blue-500 to-emerald-400 flex items-center justify-center text-white text-3xl shadow-lg shadow-indigo-500/20 motion-safe:animate-pulse">
                    ⚔️
                </div>

                <div class="space-y-2">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                        RAG Evaluation Arena
                    </h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed max-w-xl mx-auto">
                        Ask any question once to compare how knowledge retrieval changes AI behavior in real time. Both pipelines run the exact same underlying model (<code class="font-mono text-xs">{{ config('ai.chat.model', 'gpt-4o-mini') }}</code>).
                    </p>
                </div>

                {{-- Two-Column Explanation Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full text-start">
                    <div class="p-4 rounded-xl border border-emerald-200 dark:border-emerald-800/60 bg-emerald-50/40 dark:bg-emerald-950/20 space-y-1.5">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-300">
                            <span class="size-2 rounded-full bg-emerald-500"></span>
                            <span>Left Column: With Grounded RAG</span>
                        </div>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Queries PostgreSQL via <code class="font-mono text-[11px]">pgvector</code> HNSW index, injects verified curriculum and safety policies into the prompt, cites sources, and refuses ungrounded questions.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 space-y-1.5">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-zinc-700 dark:text-zinc-300">
                            <span class="size-2 rounded-full bg-zinc-400"></span>
                            <span>Right Column: Without RAG (Raw LLM)</span>
                        </div>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Bypasses all database retrieval. Demonstrates how the same model behaves using only general pre-trained memory—often resulting in generic answers or hallucinations on specific policies.
                        </p>
                    </div>
                </div>

                {{-- Starter Prompt Chips --}}
                <div class="w-full space-y-2.5 pt-2">
                    <div class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                        Suggested questions to test side-by-side grounding:
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-start">
                        @foreach ($starterPrompts as $prompt)
                            <button
                                wire:click="loadSuggestion(@js($prompt))"
                                type="button"
                                class="p-3 text-xs font-medium rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-indigo-400 dark:hover:border-indigo-600 hover:bg-indigo-50/40 dark:hover:bg-indigo-950/30 text-zinc-800 dark:text-zinc-200 transition text-start flex items-start gap-2 shadow-xs group cursor-pointer"
                            >
                                <span class="text-indigo-500 group-hover:translate-x-0.5 transition-transform shrink-0">💬</span>
                                <span class="line-clamp-2 leading-relaxed">{{ $prompt }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            {{-- Message History Stream (Pairwise: User Query + Dual Column Response) --}}
            @for ($i = 0; $i < count($messages); $i += 2)
                @php
                    $userMsg = $messages[$i];
                    $assistantMsg = $messages[$i + 1] ?? null;
                @endphp

                <div class="space-y-3">
                    {{-- User Message Centered Banner --}}
                    <div class="flex justify-center">
                        <div class="inline-flex items-center gap-2 max-w-2xl px-4 py-2 rounded-full bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800 text-xs font-medium text-blue-950 dark:text-blue-200 shadow-xs">
                            <span class="size-2 rounded-full bg-blue-500 shrink-0"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500 shrink-0">Query</span>
                            <span class="font-semibold text-zinc-900 dark:text-zinc-100 text-xs">{{ $userMsg['content'] }}</span>
                        </div>
                    </div>

                    @if ($assistantMsg)
                        {{-- Side-by-Side Dual Column Response --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
                            {{-- LEFT COLUMN: Grounded RAG --}}
                            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800/60 bg-white dark:bg-zinc-900 shadow-xs p-4 sm:p-5 space-y-3 relative overflow-hidden">
                                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-400"></div>

                                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-2.5 flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="size-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-bold shadow-xs">
                                            ✓
                                        </div>
                                        <div>
                                            <span class="font-bold text-xs text-zinc-900 dark:text-zinc-100">Grounded RAG</span>
                                            <span class="text-[10px] text-zinc-400 block font-mono">pgvector &lt;=&gt; Cosine HNSW</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        @if ($assistantMsg['rag_details']['has_error'] ?? false)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300">
                                                ✕ Error
                                            </span>
                                        @elseif ($assistantMsg['rag_details']['grounded'] ?? false)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                ✓ Grounded in KB
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                                ⚠ Refused (Ungrounded)
                                            </span>
                                        @endif

                                        @if (!empty($assistantMsg['rag_details']['latency_ms']))
                                            <span class="text-[10px] font-mono text-zinc-400">
                                                {{ $assistantMsg['rag_details']['latency_ms'] }}ms
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- RAG Answer Content --}}
                                <div class="text-zinc-800 dark:text-zinc-200 whitespace-pre-wrap leading-relaxed prose dark:prose-invert prose-sm max-w-none text-xs sm:text-sm">
                                    {{ $assistantMsg['content'] }}
                                </div>

                                {{-- Collapsible RAG Internals & Sources Drawer --}}
                                @if (!empty($assistantMsg['rag_details']))
                                    <details class="group border-t border-zinc-100 dark:border-zinc-800/80 pt-2.5 text-xs">
                                        <summary class="cursor-pointer font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 flex items-center justify-between select-none py-1">
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="size-3.5 group-open:rotate-90 transition-transform text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                </svg>
                                                <span>📊 Injected Sources &amp; Telemetry</span>
                                            </span>
                                            <span class="text-[11px] font-mono font-normal text-zinc-400">
                                                {{ count($assistantMsg['rag_details']['retrieved_articles'] ?? []) }} sources
                                            </span>
                                        </summary>

                                        <div class="mt-2.5 space-y-3 pl-3 border-l-2 border-emerald-300 dark:border-emerald-700/60 pt-1">
                                            {{-- Telemetry summary badges --}}
                                            <div class="grid grid-cols-2 gap-1.5 text-[10px]">
                                                <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                                                    <div class="uppercase font-semibold text-zinc-400">Model</div>
                                                    <div class="font-mono font-bold text-zinc-800 dark:text-zinc-200 truncate">
                                                        {{ $assistantMsg['rag_details']['model'] ?? 'gpt-4o-mini' }}
                                                    </div>
                                                </div>
                                                <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                                                    <div class="uppercase font-semibold text-zinc-400">Similarity Cutoff</div>
                                                    <div class="font-mono font-bold text-zinc-800 dark:text-zinc-200">
                                                        &ge; {{ ($assistantMsg['rag_details']['min_threshold'] ?? 0.60) * 100 }}%
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Retrieved Articles List --}}
                                            @if (!empty($assistantMsg['rag_details']['retrieved_articles']))
                                                <div class="space-y-1.5">
                                                    @foreach ($assistantMsg['rag_details']['retrieved_articles'] as $srcIdx => $source)
                                                        <div class="p-2.5 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 flex items-start justify-between gap-2">
                                                            <div class="space-y-0.5">
                                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                                    <span class="font-mono font-bold text-[10px] text-zinc-400">#{{ $srcIdx + 1 }}</span>
                                                                    <a
                                                                        href="{{ route('articles.show', $source['slug']) }}"
                                                                        target="_blank"
                                                                        class="font-semibold text-zinc-900 dark:text-zinc-100 hover:text-emerald-600 dark:hover:text-emerald-400 underline decoration-zinc-300 hover:decoration-emerald-500 transition text-xs"
                                                                    >
                                                                        {{ $source['title'] }}
                                                                    </a>
                                                                    <span class="px-1 py-0.2 rounded text-[9px] bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                                                                        {{ ucfirst(str_replace('_', ' ', $source['audience'])) }}
                                                                    </span>
                                                                </div>
                                                                <div class="text-[10px] font-mono text-zinc-400">
                                                                    Cosine Distance: <span class="text-zinc-700 dark:text-zinc-300 font-bold">{{ $source['distance'] }}</span>
                                                                </div>
                                                            </div>

                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 shrink-0">
                                                                {{ $source['match_percentage'] }}% Match
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="p-2.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 text-[10px] text-amber-800 dark:text-amber-200">
                                                    No database articles met the similarity cutoff. Response strictly refused to prevent hallucination.
                                                </div>
                                            @endif

                                            {{-- Full Assembled System Prompt Inspection --}}
                                            @if (!empty($assistantMsg['rag_details']['system_prompt']))
                                                <details class="text-[10px]">
                                                    <summary class="font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 cursor-pointer select-none">
                                                        View Grounded System Prompt
                                                    </summary>
                                                    <pre class="mt-1.5 p-2 rounded-lg bg-zinc-950 text-zinc-300 font-mono text-[9px] max-h-40 overflow-y-auto border border-zinc-800 whitespace-pre-wrap leading-relaxed select-all">{{ $assistantMsg['rag_details']['system_prompt'] }}</pre>
                                                </details>
                                            @endif
                                        </div>
                                    </details>
                                @endif
                            </div>

                            {{-- RIGHT COLUMN: Raw LLM Baseline (Without RAG) --}}
                            <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs p-4 sm:p-5 space-y-3 relative overflow-hidden">
                                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-zinc-400 to-zinc-500"></div>

                                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-2.5 flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="size-6 rounded-lg bg-zinc-500 text-white flex items-center justify-center text-xs font-bold shadow-xs">
                                            ⚪
                                        </div>
                                        <div>
                                            <span class="font-bold text-xs text-zinc-900 dark:text-zinc-100">Raw LLM Baseline</span>
                                            <span class="text-[10px] text-zinc-400 block font-mono">Parametric Memory Only (No RAG)</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                            Zero DB Context
                                        </span>

                                        @if (!empty($assistantMsg['raw_details']['latency_ms']))
                                            <span class="text-[10px] font-mono text-zinc-400">
                                                {{ $assistantMsg['raw_details']['latency_ms'] }}ms
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Raw Answer Content --}}
                                <div class="text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap leading-relaxed prose dark:prose-invert prose-sm max-w-none text-xs sm:text-sm">
                                    {{ $assistantMsg['raw_details']['content'] ?? $assistantMsg['content'] }}
                                </div>

                                {{-- Baseline Notice & Explainer --}}
                                <div class="border-t border-zinc-100 dark:border-zinc-800/80 pt-2.5 space-y-2 text-xs">
                                    <div class="p-2 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/70 dark:border-zinc-800 flex items-start gap-2 text-[11px] text-zinc-500 dark:text-zinc-400">
                                        <span class="text-amber-500 shrink-0">ℹ️</span>
                                        <span>Notice: Without RAG grounding, the model cannot verify trade school policies, exact grant reimbursement numbers, or campus lab rules.</span>
                                    </div>

                                    @if (!empty($assistantMsg['raw_details']['system_prompt']))
                                        <details class="text-[10px]">
                                            <summary class="font-semibold text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 cursor-pointer select-none">
                                                View Baseline System Prompt
                                            </summary>
                                            <pre class="mt-1.5 p-2 rounded-lg bg-zinc-950 text-zinc-300 font-mono text-[9px] max-h-32 overflow-y-auto border border-zinc-800 whitespace-pre-wrap leading-relaxed select-all">{{ $assistantMsg['raw_details']['system_prompt'] }}</pre>
                                        </details>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endfor

            {{-- Live Token Streaming Bubble (Side-by-Side while streaming) --}}
            <div wire:loading.grid wire:target="sendMessage,loadSuggestion" class="grid-cols-1 lg:grid-cols-2 gap-4 items-start w-full" style="display: none;">
                {{-- Left Streaming Panel: Grounded RAG --}}
                <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800/60 bg-white dark:bg-zinc-900 p-4 sm:p-5 text-sm shadow-xs space-y-2">
                    <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                        <span class="size-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>[1/2] Grounded RAG Stream (pgvector)...</span>
                    </div>
                    <div class="text-zinc-800 dark:text-zinc-200 whitespace-pre-wrap leading-relaxed text-xs sm:text-sm">
                        <span wire:stream="rag-response"></span>
                    </div>
                </div>

                {{-- Right Streaming Panel: Raw LLM Baseline --}}
                <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-4 sm:p-5 text-sm shadow-xs space-y-2">
                    <div class="flex items-center gap-2 text-xs font-semibold text-zinc-500 dark:text-zinc-400">
                        <span class="size-2 rounded-full bg-zinc-400 animate-ping"></span>
                        <span>[2/2] Raw LLM Baseline Stream (Direct inference)...</span>
                    </div>
                    <div class="text-zinc-800 dark:text-zinc-200 whitespace-pre-wrap leading-relaxed text-xs sm:text-sm">
                        <span wire:stream="raw-response"></span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Bottom Universal Input Form --}}
    <div class="shrink-0 pt-2 border-t border-zinc-200 dark:border-zinc-800">
        <form wire:submit="sendMessage" class="flex gap-2 items-center">
            <div class="relative flex-1">
                <input
                    wire:model="input"
                    type="text"
                    maxlength="500"
                    placeholder="Ask a question to test side-by-side RAG grounding..."
                    aria-label="{{ __('Ask a question') }}"
                    class="w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden pr-16 shadow-xs disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="sendMessage,loadSuggestion"
                    required
                    autofocus
                />
                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-mono text-zinc-400 select-none">
                    <span x-text="$wire.input ? $wire.input.length : 0"></span>/500
                </div>
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="sendMessage,loadSuggestion"
                class="px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition shadow-xs disabled:opacity-50 cursor-pointer flex items-center justify-center gap-1.5 shrink-0"
            >
                <span wire:loading.remove wire:target="sendMessage,loadSuggestion" class="inline-flex items-center gap-1.5">
                    <span>Compare</span>
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                </span>
                <span wire:loading.inline-flex wire:target="sendMessage,loadSuggestion" class="items-center gap-1.5" style="display: none;">
                    <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Evaluating...</span>
                </span>
            </button>
        </form>

        <div class="mt-2 flex items-center justify-between text-[11px] text-zinc-400">
            <span>⚔️ <strong>Dual-Stream Arena:</strong> Grounded via PostgreSQL <code class="font-mono text-zinc-500">pgvector</code> &amp; {{ config('ai.chat.model', 'gpt-4o-mini') }}</span>
            <span>IP Rate Limited (20 queries/min)</span>
        </div>
    </div>
</div>
