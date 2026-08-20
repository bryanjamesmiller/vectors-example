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
                    Empowering trade schools with high-speed in-database vector recommendations and transparent real-time AI embedding telemetry.
                </p>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a
                        href="{{ route('articles.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md hover:shadow-lg transition text-base"
                    >
                        <span>Explore Articles</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a
                        href="#architecture"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-zinc-700 dark:text-zinc-200 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-base"
                    >
                        <span>Architecture Specs</span>
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
