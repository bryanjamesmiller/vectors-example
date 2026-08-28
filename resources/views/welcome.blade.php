<x-layouts::app.header :title="__('Trade School AI & Vector Architecture')">
    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative overflow-hidden py-16 sm:py-24 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-b from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <span>PostgreSQL 16 + pgvector • Local Ollama AI & Production OpenAI • Real-Time Telemetry</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-zinc-900 dark:text-white max-w-4xl mx-auto leading-tight sm:leading-none">
                    Intelligent Operating Architecture for <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-500">Modern Trade Schools</span>
                </h1>

                <p class="text-lg sm:text-xl text-zinc-600 dark:text-zinc-300 max-w-2xl mx-auto leading-relaxed">
                    Empowering trade schools with high-speed in-database vector recommendations, natural language semantic search, and transparent real-time AI embedding telemetry.
                </p>

                {{-- Hero Semantic Vector Search Bar --}}
                <div class="pt-2 max-w-2xl mx-auto w-full">
                    <x-semantic-search-bar
                        :suggestions="[
                            ['emoji' => '🤿', 'label' => 'Underwater Welding', 'query' => 'underwater welding safety'],
                            ['emoji' => '⚡', 'label' => 'Journeyman Electrical Exam', 'query' => 'electrical master exam tactics'],
                            ['emoji' => '☀️', 'label' => 'Solar Apprenticeship Grants', 'query' => 'solar photovoltaic apprenticeship grants'],
                        ]"
                        :centered="true"
                    />
                </div>

                {{-- Action Links --}}
                <div class="pt-4 flex items-center justify-center text-sm">
                    <a
                        href="#architecture"
                        class="text-zinc-600 dark:text-zinc-400 font-medium hover:text-zinc-900 dark:hover:text-zinc-200 hover:underline inline-flex items-center gap-1.5"
                    >
                        <span>Architecture Specs &darr;</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Featured Showcase: Articles & Vector Recommendations -->
        <section class="py-16 sm:py-20 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center space-y-3 max-w-3xl mx-auto">
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Articles for All Trade Schools with Semantic Vector Recommendations
                    </h2>
                    <p class="text-zinc-600 dark:text-zinc-400 text-base leading-relaxed">
                        A curated library of 18 trade articles spanning Welding, Electrical, HVAC, Lab Safety, Apprenticeships, and Financial Aid. Every article leverages PostgreSQL cosine distance (<code class="text-xs font-mono bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded">&lt;=&gt;</code>) to automatically recommend the top related articles in <strong class="text-zinc-900 dark:text-white">&lt;2ms</strong> without external LLM runtime latency.
                    </p>
                </div>

                <!-- Feature Metric Pills -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-800 text-center space-y-1">
                        <div class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">512d</div>
                        <div class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Vector Embeddings</div>
                    </div>
                    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-800 text-center space-y-1">
                        <div class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">&lt; 2ms</div>
                        <div class="text-xs font-medium text-zinc-600 dark:text-zinc-400">HNSW Cosine Proximity</div>
                    </div>
                    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-800 text-center space-y-1">
                        <div class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">100% Free</div>
                        <div class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Offline Local Ollama</div>
                    </div>
                    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-800 text-center space-y-1">
                        <div class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">6 Audiences</div>
                        <div class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Hybrid SQL Filtering</div>
                    </div>
                </div>

                <!-- Showcase Call-to-Action Card -->
                <div class="p-8 rounded-2xl bg-gradient-to-r from-indigo-900 to-zinc-900 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-lg">
                    <div class="space-y-2 text-center md:text-left">
                        <h3 class="text-xl font-bold">Try the Vector Recommendation Demo</h3>
                        <p class="text-indigo-200 text-sm max-w-xl">
                            Open any article to see live semantic similarity percentage scores (e.g. <em>81% Semantic Match</em>) and toggle between "All Audiences" and "Same Audience" hybrid filtering.
                        </p>
                    </div>
                    <a
                        href="{{ route('articles.index') }}"
                        class="px-6 py-3 rounded-xl bg-white text-indigo-900 font-semibold hover:bg-indigo-50 transition shadow whitespace-nowrap text-sm"
                    >
                        Browse All 18 Articles &rarr;
                    </a>
                </div>
            </div>
        </section>

        <!-- Featured Showcase: RAG Evaluation Arena -->
        <section class="py-16 sm:py-20 bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center space-y-3 max-w-3xl mx-auto">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Live AI Benchmark</span>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        RAG Evaluation Arena: Side-by-Side Dual-Stream Benchmark
                    </h2>
                    <p class="text-zinc-600 dark:text-zinc-400 text-base leading-relaxed">
                        Experience the direct contrast between in-database vector knowledge grounding and unassisted LLM parametric memory. For every user query, the arena dispatches two parallel pipelines against the exact same underlying model (<code class="text-xs font-mono bg-zinc-200 dark:bg-zinc-800 px-1.5 py-0.5 rounded">{{ config('ai.chat.model', 'gpt-4o-mini') }}</code>) with real-time token streaming.
                    </p>
                </div>

                <!-- Side-by-Side Comparison Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left: Grounded RAG -->
                    <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800/60 bg-white dark:bg-zinc-900 shadow-sm p-6 space-y-4 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-emerald-500 to-teal-400"></div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="size-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">🟢</span>
                                <h3 class="font-bold text-base text-zinc-900 dark:text-zinc-100">Grounded RAG Pipeline</h3>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                In-Database Grounded
                            </span>
                        </div>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Queries PostgreSQL 16 <code class="text-xs font-mono">pgvector</code> with HNSW cosine distance (<code class="text-xs font-mono">&lt;=&gt;</code>) to retrieve authoritative trade school records, curriculum documents, and safety standards.
                        </p>
                        <ul class="space-y-2 text-xs text-zinc-600 dark:text-zinc-300">
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span><strong>Strict Grounding Cutoff:</strong> Enforces a 75% similarity threshold, refusing out-of-domain queries to guarantee zero hallucination.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span><strong>Inline Citations:</strong> Attributes facts directly to verified sources with inspectable cosine distances and match percentages.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span><strong>Live Dual Stream:</strong> Incremental token streaming powered by Livewire 4 <code class="text-xs font-mono">wire:stream</code>.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Right: Raw LLM Baseline -->
                    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm p-6 space-y-4 relative overflow-hidden">
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-zinc-400 to-zinc-500"></div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="size-6 rounded-lg bg-zinc-500 text-white flex items-center justify-center text-xs font-bold">⚪</span>
                                <h3 class="font-bold text-base text-zinc-900 dark:text-zinc-100">Raw LLM Baseline</h3>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                Parametric Memory Only
                            </span>
                        </div>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Queries the exact same model with zero database context, relying solely on general training weights to demonstrate typical enterprise LLM failure modes.
                        </p>
                        <ul class="space-y-2 text-xs text-zinc-600 dark:text-zinc-300">
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">!</span>
                                <span><strong>Zero Database Context:</strong> Cannot verify campus-specific rules, tuition fee schedules, or lab requirements.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-amber-500 font-bold">!</span>
                                <span><strong>Hallucination Drift:</strong> Frequently guesses plausible-sounding but unverified curriculum specifications.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-zinc-400 font-bold">•</span>
                                <span><strong>Context Isolation:</strong> Maintains partitioned conversation history so RAG knowledge never contaminates baseline turns.</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Showcase Call-to-Action Card -->
                <div class="p-8 rounded-2xl bg-gradient-to-r from-indigo-950 via-zinc-900 to-emerald-950 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-lg border border-indigo-900/50">
                    <div class="space-y-2 text-center md:text-left">
                        <div class="flex items-center gap-2 justify-center md:justify-start">
                            <span class="text-lg">⚔️</span>
                            <h3 class="text-xl font-bold">Launch the Side-by-Side RAG Arena</h3>
                        </div>
                        <p class="text-indigo-200 text-sm max-w-xl">
                            Try asking questions like <em>"What are hyperbaric welding chamber protocols?"</em> or <em>"How do I diagnose HVAC superheat?"</em> to watch both streams generate simultaneously.
                        </p>
                    </div>
                    <a
                        href="{{ route('rag') }}"
                        class="px-6 py-3 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-400 transition shadow whitespace-nowrap text-sm flex items-center gap-2"
                    >
                        <span>Open RAG Arena</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Featured Showcase: Tuition Payments & SOLID Architecture -->
        <section class="py-16 sm:py-20 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center space-y-3 max-w-3xl mx-auto">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Enterprise Design Patterns</span>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Swappable Tuition Payments with SOLID Strategy Architecture
                    </h2>
                    <p class="text-zinc-600 dark:text-zinc-400 text-base leading-relaxed">
                        An enterprise-grade, enum-driven Strategy Pattern implementation demonstrating real-world Open-Closed (OCP) and Dependency Inversion (DIP) principles. Trade schools can seamlessly swap or add payment processors without modifying existing business logic.
                    </p>
                </div>

                <!-- 4 SOLID Architecture Pillars Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-800 space-y-2.5">
                        <div class="size-10 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm">
                            🔌
                        </div>
                        <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100">Strategy Pattern</h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Every provider implements <code class="text-[11px] font-mono">PaymentGatewayInterface</code> with strict contract adherence (<code class="text-[11px] font-mono">charge()</code>, <code class="text-[11px] font-mono">calculateFee()</code>).
                        </p>
                    </div>

                    <div class="p-5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-800 space-y-2.5">
                        <div class="size-10 rounded-lg bg-violet-50 dark:bg-violet-950 text-violet-600 dark:text-violet-400 flex items-center justify-center font-bold text-sm">
                            🏭
                        </div>
                        <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100">Enum-Driven Factory</h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            <code class="text-[11px] font-mono">PaymentGatewayFactory</code> dynamically resolves concrete drivers from typed <code class="text-[11px] font-mono">PaymentGatewayType</code> enums without brittle conditionals.
                        </p>
                    </div>

                    <div class="p-5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-800 space-y-2.5">
                        <div class="size-10 rounded-lg bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                            🔒
                        </div>
                        <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100">Open-Closed (OCP)</h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Add a new processor (Square, Apple Pay) by creating a single class. Zero changes required in checkout controllers or existing gateways.
                        </p>
                    </div>

                    <div class="p-5 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-800 space-y-2.5">
                        <div class="size-10 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-sm">
                            🛡️
                        </div>
                        <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100">Atomic Transactions</h3>
                        <p class="text-xs text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Database transactional boundaries guarantee that balance updates, ledger records, and audit events never fall out of sync.
                        </p>
                    </div>
                </div>

                <!-- Showcase Call-to-Action Card -->
                <div class="p-8 rounded-2xl bg-gradient-to-r from-zinc-900 to-indigo-950 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-lg border border-zinc-800">
                    <div class="space-y-2 text-center md:text-left">
                        <div class="flex items-center gap-2 justify-center md:justify-start">
                            <span class="text-lg">💳</span>
                            <h3 class="text-xl font-bold">Try the Interactive Tuition Billing Demo</h3>
                        </div>
                        <p class="text-zinc-300 text-sm max-w-xl">
                            Test live gateway swapping between Stripe, Authorize.Net, Cash App, Zelle, and Manual Check with automatic fee calculations and student ledger tracking.
                        </p>
                    </div>
                    <a
                        href="{{ route('payments') }}"
                        class="px-6 py-3 rounded-xl bg-white text-zinc-900 font-semibold hover:bg-zinc-100 transition shadow whitespace-nowrap text-sm flex items-center gap-2"
                    >
                        <span>Explore Tuition Bill & Payments</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- Brochure / Architecture Grid -->
        <section id="architecture" class="py-16 sm:py-24 bg-zinc-50 dark:bg-zinc-950">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="text-center space-y-3 max-w-3xl mx-auto">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">System Architecture & Tech Stack</span>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        How the In-Database AI & Vector Architecture Works
                    </h2>
                    <p class="text-zinc-600 dark:text-zinc-400 text-base">
                        A modern Laravel 12 application running on PHP 8.5, combining PostgreSQL pgvector embeddings with flexible AI provider integration.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Pillar 1: PostgreSQL & pgvector -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors duration-150">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">PostgreSQL 16 & pgvector Engine</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Vector embeddings live directly alongside relational tables. Cosine distance (<code class="text-xs font-mono">&lt;=&gt;</code>) search runs natively inside PostgreSQL, eliminating the cost and latency of external vector databases like Pinecone.
                        </p>
                    </div>

                    <!-- Pillar 2: Pluggable AI Engine -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors duration-150">
                        <div class="w-12 h-12 rounded-xl bg-violet-50 dark:bg-violet-950 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">Pluggable AI Engine (Ollama, OpenAI & More)</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Develop 100% free and offline on your machine using <strong>local Ollama</strong> (<code class="text-xs font-mono">nomic-embed-text</code>), and this app seamlessly switches to cloud <strong>OpenAI</strong> (<code class="text-xs font-mono">text-embedding-3-small</code>) in production. Because <code class="text-xs font-mono">EmbeddingService</code> connects to standard OpenAI-compatible endpoints, swapping to other providers (like Mistral or LocalAI) is 100% config-driven via <code class="text-xs font-mono">.env</code>.
                        </p>
                    </div>

                    <!-- Pillar 3: SHA-256 Caching -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors duration-150">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">SHA-256 Content-Hash Caching</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Embeddings are cached using <strong>SHA-256 text fingerprinting</strong> so repeated text lookups resolve in <strong class="text-zinc-900 dark:text-white">&lt;1ms</strong> for $0.00, preventing duplicate API requests and unnecessary cloud billing.
                        </p>
                    </div>

                    <!-- Pillar 4: Interactive Vector Lab & Management -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition-colors duration-150">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">Interactive Vector Lab & Management</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Test live embedding generation, inspect raw 512-dimension vector representations, evaluate real-time PostgreSQL similarity rankings, and manage published articles with full CRUD control.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500 dark:text-zinc-400">
            <div>
                &copy; {{ date('Y') }} Trade School AI. Built with Laravel 12, PostgreSQL 16 & pgvector.
            </div>
            <div class="flex flex-wrap items-center justify-center sm:justify-end gap-x-4 gap-y-2">
                <a href="{{ route('articles.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">Articles</a>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <a href="{{ route('rag') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">RAG Arena</a>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <a href="{{ route('vector-lab') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">Vector Lab</a>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <a href="{{ route('payments') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">Tuition Bill</a>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <a href="https://github.com/bryanjamesmiller/vectors-example" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">Repository</a>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <a href="https://github.com/bryanjamesmiller/vectors-example/blob/main/README.md" target="_blank" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">Documentation</a>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <span class="font-mono">PHP 8.5</span>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <span class="font-mono">Pest 5</span>
            </div>
        </div>
    </footer>
</x-layouts::app.header>
