@props([
    'action' => route('articles.index'),
    'value' => '',
    'placeholder' => 'Search trade school articles semantically...',
    'ariaLabel' => 'Search trade school articles semantically',
    'showClear' => true,
    'suggestions' => [],
    'showTelemetry' => false,
    'centered' => false,
    'lightShadow' => false,
])

<div {{ $attributes->merge(['class' => 'w-full space-y-3']) }}>
    <form
        method="GET"
        action="{{ $action }}"
        class="relative flex flex-col sm:flex-row gap-2.5 p-2 rounded-2xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-900/10 dark:ring-white/10"
        style="{{ $lightShadow ? 'box-shadow: 0 -3px 16px -2px rgba(0, 0, 0, 0.065), 0 9px 22px -3px rgba(0, 0, 0, 0.11);' : 'box-shadow: 0 -4px 20px -2px rgba(0, 0, 0, 0.09), 0 12px 28px -4px rgba(0, 0, 0, 0.15);' }}"
    >
        <div class="relative flex-1 flex items-center">
            <div class="pointer-events-none absolute left-3.5 text-zinc-400">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <input
                type="text"
                name="q"
                value="{{ $value }}"
                aria-label="{{ $ariaLabel }}"
                placeholder="{{ $placeholder }}"
                class="w-full rounded-xl border-0 bg-transparent pl-11 pr-4 py-3 text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:ring-0 focus:outline-hidden"
            />
        </div>
        <div class="flex items-center gap-2">
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md hover:shadow-lg transition text-sm cursor-pointer shrink-0"
            >
                <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <span>Semantic Search</span>
            </button>
            @if ($showClear && $value !== '')
                <a
                    href="{{ $action }}"
                    class="px-4 py-3 text-sm font-medium rounded-xl border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition text-center shrink-0"
                >
                    Clear
                </a>
            @endif
        </div>
    </form>

    @if ($showTelemetry || ! empty($suggestions))
        <div @class([
            'flex flex-wrap items-center gap-2.5 text-xs text-zinc-500 dark:text-zinc-400 pt-0.5',
            'justify-between' => $showTelemetry && ! empty($suggestions),
            'justify-center' => $centered || (! $showTelemetry && ! empty($suggestions)),
        ])>
            @if ($showTelemetry)
                <div class="flex items-center gap-1.5">
                    <span class="inline-block size-2 rounded-full bg-emerald-500"></span>
                    <span>In-database 512d pgvector similarity query (<code>&lt;=&gt;</code> cosine distance)</span>
                </div>
            @endif

            @if (! empty($suggestions))
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-medium text-zinc-600 dark:text-zinc-300">Try searching:</span>
                    @foreach ($suggestions as $suggestion)
                        <a
                            href="{{ route('articles.index', ['q' => $suggestion['query']]) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white dark:bg-zinc-800 hover:text-indigo-600 dark:hover:text-indigo-400 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 transition shadow-2xs"
                        >
                            @if (! empty($suggestion['emoji']))
                                <span>{{ $suggestion['emoji'] }}</span>
                            @endif
                            <span>{{ $suggestion['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
