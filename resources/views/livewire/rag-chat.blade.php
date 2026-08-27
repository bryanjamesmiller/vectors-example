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
        const observer = new MutationObserver(() => scrollToBottom());
        observer.observe($refs.chatContainer, { childList: true, subtree: true, characterData: true });
        scrollToBottom();
    "
    class="max-w-5xl mx-auto px-4 py-6 sm:px-6 lg:px-8 space-y-6 flex flex-col h-[calc(100vh-5rem)]"
>
    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4 gap-4 shrink-0">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <div class="size-8 rounded-xl bg-gradient-to-tr from-indigo-500 via-blue-500 to-emerald-400 flex items-center justify-center text-white shadow-sm text-sm font-bold">
                    ✨
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                    Lumion AI
                </h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800/60">
                    <span class="size-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                    RAG Knowledge Assistant
                </span>
            </div>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Grounds answers strictly in trade school curriculum records via PostgreSQL <code class="font-mono bg-zinc-100 dark:bg-zinc-800 px-1 py-0.5 rounded text-[11px]">pgvector</code> similarity search and OpenAI synthesis.
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
                    <span>Clear Chat</span>
                </button>
            @endif
            <a
                href="{{ route('vector-lab') }}"
                class="px-3 py-1.5 text-xs font-medium rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50/70 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition cursor-pointer flex items-center gap-1.5 shadow-xs"
            >
                <span>🔬 Vector Lab</span>
            </a>
        </div>
    </div>

    {{-- Chat Messages Scroll Area --}}
    <div
        x-ref="chatContainer"
        class="flex-1 overflow-y-auto space-y-6 pr-2 -mr-2 scroll-smooth"
    >
        @if (empty($messages))
            {{-- Empty Welcome State --}}
            <div class="h-full flex flex-col items-center justify-center text-center max-w-xl mx-auto py-8 space-y-6">
                <div class="size-16 rounded-2xl bg-gradient-to-tr from-indigo-500 via-blue-500 to-emerald-400 flex items-center justify-center text-white text-3xl shadow-lg shadow-indigo-500/20 animate-pulse">
                    ✨
                </div>

                <div class="space-y-2">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                        Welcome to Lumion AI
                    </h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        I am your official trade school academic assistant. Ask me questions about our welding, electrical, HVAC, and civil technology curriculums, safety certifications, or grant opportunities.
                    </p>
                    <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                        🛡️ Strictly grounded: I only provide answers verified against our internal database articles.
                    </p>
                </div>

                {{-- Starter Prompt Chips --}}
                <div class="w-full space-y-2.5 pt-2">
                    <div class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                        Suggested questions to test RAG retrieval:
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
            {{-- Message History Stream --}}
            @foreach ($messages as $idx => $msg)
                @if ($msg['role'] === 'user')
                    {{-- User Message Bubble --}}
                    <div class="flex justify-end">
                        <div class="max-w-xl rounded-2xl rounded-tr-sm bg-blue-600 text-white px-4 py-3 text-sm shadow-xs leading-relaxed space-y-1">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-blue-200 text-right">
                                You
                            </div>
                            <div class="whitespace-pre-wrap">{{ $msg['content'] }}</div>
                        </div>
                    </div>
                @else
                    {{-- Assistant Message Bubble with RAG Internals --}}
                    <div class="flex items-start gap-3 max-w-3xl">
                        <div class="size-8 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white shrink-0 text-xs font-bold shadow-xs">
                            ✨
                        </div>

                        <div class="flex-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl rounded-tl-sm p-4 sm:p-5 text-sm shadow-xs space-y-3">
                            <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-zinc-900 dark:text-zinc-100">Lumion AI</span>
                                    @if ($msg['rag_details']['grounded'] ?? false)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                            ✓ Grounded in KB
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                            ⚠ Ungrounded / Refused
                                        </span>
                                    @endif
                                </div>

                                @if (!empty($msg['rag_details']['latency_ms']))
                                    <span class="text-[11px] font-mono text-zinc-400">
                                        {{ $msg['rag_details']['latency_ms'] }} ms retrieval
                                    </span>
                                @endif
                            </div>

                            {{-- Markdown / Text content --}}
                            <div class="text-zinc-800 dark:text-zinc-200 whitespace-pre-wrap leading-relaxed prose dark:prose-invert prose-sm max-w-none">
                                {{ $msg['content'] }}
                            </div>

                            {{-- Collapsible RAG Internals Panel --}}
                            @if (!empty($msg['rag_details']))
                                <details class="group border-t border-zinc-100 dark:border-zinc-800/80 pt-3 text-xs">
                                    <summary class="cursor-pointer font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 flex items-center justify-between select-none py-1">
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="size-4 group-open:rotate-90 transition-transform text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                            </svg>
                                            <span>📊 View RAG Details &amp; Retrieval Internals</span>
                                        </span>
                                        <span class="text-[11px] font-mono font-normal text-zinc-400">
                                            {{ count($msg['rag_details']['retrieved_articles'] ?? []) }} sources retrieved
                                        </span>
                                    </summary>

                                    <div class="mt-3 space-y-4 pl-4 border-l-2 border-indigo-200 dark:border-indigo-800/60 pt-1">
                                        {{-- Telemetry summary badges --}}
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                            <div class="p-2.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                                                <div class="text-[10px] uppercase font-semibold text-zinc-400">Model</div>
                                                <div class="font-mono font-bold text-zinc-800 dark:text-zinc-200 text-xs truncate">
                                                    {{ $msg['rag_details']['model'] ?? 'gpt-4o-mini' }}
                                                </div>
                                            </div>
                                            <div class="p-2.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                                                <div class="text-[10px] uppercase font-semibold text-zinc-400">Similarity Threshold</div>
                                                <div class="font-mono font-bold text-zinc-800 dark:text-zinc-200 text-xs">
                                                    &ge; {{ ($msg['rag_details']['min_threshold'] ?? 0.60) * 100 }}%
                                                </div>
                                            </div>
                                            <div class="p-2.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                                                <div class="text-[10px] uppercase font-semibold text-zinc-400">Sources Injected</div>
                                                <div class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-xs">
                                                    {{ count($msg['rag_details']['retrieved_articles'] ?? []) }} Articles
                                                </div>
                                            </div>
                                            <div class="p-2.5 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                                                <div class="text-[10px] uppercase font-semibold text-zinc-400">Retrieval Latency</div>
                                                <div class="font-mono font-bold text-emerald-600 dark:text-emerald-400 text-xs">
                                                    {{ $msg['rag_details']['latency_ms'] ?? 0 }} ms
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Retrieved Articles List --}}
                                        @if (!empty($msg['rag_details']['retrieved_articles']))
                                            <div class="space-y-2">
                                                <div class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                                    Injected Knowledge Base Sources (pgvector &lt;=&gt; Cosine Distance)
                                                </div>
                                                <div class="space-y-2">
                                                    @foreach ($msg['rag_details']['retrieved_articles'] as $srcIdx => $source)
                                                        <div class="p-3 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 flex items-start justify-between gap-3">
                                                            <div class="space-y-1">
                                                                <div class="flex items-center gap-2 flex-wrap">
                                                                    <span class="font-mono font-bold text-[11px] text-zinc-400">#{{ $srcIdx + 1 }}</span>
                                                                    <a
                                                                        href="{{ route('articles.show', $source['slug']) }}"
                                                                        target="_blank"
                                                                        class="font-semibold text-zinc-900 dark:text-zinc-100 hover:text-blue-600 dark:hover:text-blue-400 underline decoration-zinc-300 hover:decoration-blue-500 transition text-xs"
                                                                    >
                                                                        {{ $source['title'] }}
                                                                    </a>
                                                                    <span class="px-1.5 py-0.5 rounded text-[10px] bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                                                                        {{ ucfirst(str_replace('_', ' ', $source['audience'])) }}
                                                                    </span>
                                                                </div>
                                                                @if (!empty($source['summary']))
                                                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 line-clamp-1">
                                                                        {{ $source['summary'] }}
                                                                    </p>
                                                                @endif
                                                                <div class="text-[10px] font-mono text-zinc-400">
                                                                    Cosine Distance: <span class="text-zinc-700 dark:text-zinc-300 font-bold">{{ $source['distance'] }}</span>
                                                                </div>
                                                            </div>

                                                            <div class="shrink-0 text-end">
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                                    {{ $source['match_percentage'] }}% Match
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 text-[11px] text-amber-800 dark:text-amber-200">
                                                No database articles met the {{ ($msg['rag_details']['min_threshold'] ?? 0.60) * 100 }}% semantic similarity threshold. The assistant refused to answer to prevent hallucination.
                                            </div>
                                        @endif

                                        {{-- Full Assembled System Prompt Inspection --}}
                                        @if (!empty($msg['rag_details']['system_prompt']))
                                            <details class="text-[11px]">
                                                <summary class="font-semibold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 cursor-pointer select-none">
                                                    ▶ View Assembled System Prompt &amp; Injected Context
                                                </summary>
                                                <pre class="mt-2 p-3 rounded-xl bg-zinc-950 text-zinc-300 font-mono text-[10px] max-h-56 overflow-y-auto border border-zinc-800 whitespace-pre-wrap leading-relaxed select-all">{{ $msg['rag_details']['system_prompt'] }}</pre>
                                            </details>
                                        @endif
                                    </div>
                                </details>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Live Token Streaming Bubble (visible while streaming) --}}
            <div wire:loading.inline-flex wire:target="sendMessage,loadSuggestion" class="items-start gap-3 max-w-3xl w-full" style="display: none;">
                <div class="size-8 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white shrink-0 text-xs font-bold shadow-xs animate-bounce">
                    ✨
                </div>

                <div class="flex-1 bg-white dark:bg-zinc-900 border border-indigo-200 dark:border-indigo-800/60 rounded-2xl rounded-tl-sm p-4 text-sm shadow-xs space-y-2">
                    <div class="flex items-center gap-2 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                        <span class="size-2 rounded-full bg-indigo-500 animate-ping"></span>
                        <span>Retrieving from pgvector &amp; synthesizing answer...</span>
                    </div>
                    <div class="text-zinc-800 dark:text-zinc-200 whitespace-pre-wrap leading-relaxed text-sm">
                        <span wire:stream="assistant-response"></span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Bottom Input Form --}}
    <div class="shrink-0 pt-2 border-t border-zinc-200 dark:border-zinc-800">
        <form wire:submit="sendMessage" class="flex gap-2 items-center">
            <div class="relative flex-1">
                <input
                    wire:model="input"
                    type="text"
                    maxlength="500"
                    placeholder="Ask a question about trade school programs, safety protocols, or admissions..."
                    aria-label="{{ __('Ask Lumion AI a question') }}"
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
                    <span>Send</span>
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>
                </span>
                <span wire:loading.inline-flex wire:target="sendMessage,loadSuggestion" class="items-center gap-1.5" style="display: none;">
                    <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Thinking...</span>
                </span>
            </button>
        </form>

        <div class="mt-2 flex items-center justify-between text-[11px] text-zinc-400">
            <span>Powered by <code class="font-mono font-semibold text-zinc-500">pgvector</code> &amp; OpenAI <code class="font-mono font-semibold text-zinc-500">gpt-4o-mini</code></span>
            <span>IP Rate Limited (20 msgs/min)</span>
        </div>
    </div>
</div>
