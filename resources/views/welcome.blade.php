<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trade School AI & Vector Architecture - {{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased flex flex-col justify-between selection:bg-indigo-500 selection:text-white">

    <!-- Header Navigation -->
    <header class="w-full border-b border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-lg bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center text-white shadow-sm group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-base tracking-tight block leading-tight">Trade School AI</span>
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono">pgvector • RAG • Multi-Tenant</span>
                    </div>
                </a>
            </div>

            <nav class="flex items-center gap-3 sm:gap-4">
                <a
                    href="{{ route('vector-lab') }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-xs"
                >
                    <span>🔬 Try Live Vector Lab</span>
                </a>

                <a
                    href="{{ route('articles.index') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-sm font-medium rounded-lg text-zinc-700 dark:text-zinc-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition"
                >
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span>Articles</span>
                </a>

                @if (Route::has('login'))
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-1.5 text-sm font-medium rounded-lg bg-zinc-900 text-white dark:bg-zinc-100 dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-white transition shadow-sm"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center px-3.5 py-1.5 text-sm font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 hover:border-zinc-400 dark:hover:border-zinc-600 transition"
                        >
                            Log in
                        </a>
                    @endauth
                @endif
            </nav>
        </div>
    </header>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative overflow-hidden py-16 sm:py-24 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-b from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                    <span>PostgreSQL 16 + pgvector • Local AI & OpenAI • Multi-Tenant RAG</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-zinc-900 dark:text-white max-w-4xl mx-auto leading-tight sm:leading-none">
                    Intelligent Operating Architecture for <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-500">Modern Trade Schools</span>
                </h1>

                <p class="text-lg sm:text-xl text-zinc-600 dark:text-zinc-300 max-w-2xl mx-auto leading-relaxed">
                    Empowering vocational institutions with high-speed in-database vector recommendations, multi-tenant AI assistants, and grounded RAG document querying.
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
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Featured Articles</span>
                    <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        Great Trade School Articles — Relevant for All Trade Schools!
                    </h2>
                    <p class="text-zinc-600 dark:text-zinc-400 text-base leading-relaxed">
                        A curated library of 18 generic trade guides spanning Welding, Electrical, HVAC, Lab Safety, Apprenticeships, and Financial Aid. Every guide leverages PostgreSQL cosine distance (<code class="text-xs font-mono bg-zinc-100 dark:bg-zinc-800 px-1.5 py-0.5 rounded">&lt;=&gt;</code>) to automatically recommend the top related articles in <strong class="text-zinc-900 dark:text-white">&lt;2ms</strong> without external LLM runtime latency.
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
                            Open any trade guide to see live semantic similarity percentage scores (e.g. <em>81% Semantic Match</em>) and toggle between "All Audiences" and "Same Audience" hybrid filtering.
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
                        Engineered for High Performance, Security & Zero Lock-In
                    </h2>
                    <p class="text-zinc-600 dark:text-zinc-400 text-base">
                        A modern Laravel 12 application running on PHP 8.5, uniting relational data integrity with AI vector capabilities.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Pillar 1: PostgreSQL & pgvector -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">PostgreSQL 16 & pgvector Engine</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Vector embeddings live directly alongside traditional relational tables. Approximate Nearest Neighbor (ANN) search is powered by <strong>HNSW Cosine Distance indexes</strong> (<code class="text-xs font-mono">vector_cosine_ops</code>), eliminating the overhead of dedicated vector databases.
                        </p>
                    </div>

                    <!-- Pillar 2: Dual AI Engine -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                        <div class="w-12 h-12 rounded-xl bg-violet-50 dark:bg-violet-950 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">Dual-Engine AI (Ollama & OpenAI)</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Develop 100% free and offline on your machine using <strong>local Ollama</strong> with <code class="text-xs font-mono">nomic-embed-text</code> (512 dimensions), or seamlessly switch to cloud <strong>OpenAI</strong> (<code class="text-xs font-mono">text-embedding-3-small</code>) in production simply by toggling environment variables.
                        </p>
                    </div>

                    <!-- Pillar 3: SHA-256 Caching & JSON Fixtures -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">Content-Hash Caching & Fixtures</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Embeddings are cached using <strong>SHA-256 text fingerprinting</strong> so repeated documents resolve in <strong class="text-zinc-900 dark:text-white">0.1ms</strong> for $0.00. Pre-calculated vector fixtures in JSON allow <code class="text-xs font-mono">migrate:fresh --seed</code> to run in 10ms with zero network dependencies.
                        </p>
                    </div>

                    <!-- Pillar 4: Multi-Tenant Filament Backend -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4 shadow-sm hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">Filament 3 Multi-Tenant Assistant</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            School administrators query student enrollments, fee schedules, and compliance policies via the <strong>Lumion AI Assistant</strong>. Queries are strictly scoped to the active tenant with deep links to Filament resource records.
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
            <div class="flex items-center gap-4">
                <a href="{{ route('articles.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">Articles</a>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <span class="font-mono">PHP 8.5</span>
                <span class="text-zinc-300 dark:text-zinc-700">•</span>
                <span class="font-mono">Pest 5</span>
            </div>
        </div>
    </footer>
</body>
</html>
