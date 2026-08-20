<main class="flex-grow py-10 sm:py-14 bg-zinc-50 dark:bg-zinc-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Page Header --}}
        <div class="space-y-2 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>SOLID Principles • Strategy Pattern • Swappable Payment Gateways</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-zinc-900 dark:text-white">
                Trade School Student Billing and Payment Portal
            </h1>
            <p class="text-zinc-600 dark:text-zinc-400 text-sm sm:text-base">
                Demonstrating swappable payment architecture in Laravel 12. Switch payment providers seamlessly at runtime without modifying business logic.
            </p>
        </div>

        {{-- Main Bill Statement Card --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
            
            {{-- Statement Header Banner --}}
            <div class="p-6 sm:p-8 border-b border-zinc-200 dark:border-zinc-800 bg-gradient-to-r from-zinc-900 via-zinc-900 to-indigo-950 text-white">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs uppercase tracking-widest text-indigo-300 font-bold">Apex Technical Institute</span>
                            <span class="text-zinc-500">•</span>
                            <span class="text-xs text-zinc-300">Office of the Bursar</span>
                        </div>
                        <h2 class="text-2xl font-bold mt-1 text-white">Tuition & Lab Fee Statement</h2>
                        <p class="text-xs text-zinc-300 mt-0.5">Invoice: <span class="font-mono text-indigo-200 font-semibold">{{ $bill['invoice_number'] }}</span></p>
                    </div>

                    <div class="flex sm:flex-col items-start sm:items-end justify-between sm:justify-center">
                        @if ($isPaid)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span>PAID IN FULL</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                <span class="size-2 rounded-full bg-amber-400 animate-ping"></span>
                                <span>PAYMENT DUE</span>
                            </span>
                        @endif
                        <span class="text-xs text-zinc-400 mt-1">Due: {{ $bill['due_date'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Student Information Strip --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-6 bg-zinc-50/70 dark:bg-zinc-800/40 border-b border-zinc-200 dark:border-zinc-800 text-sm">
                <div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Student Name</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $bill['name'] }}</span>
                    <span class="text-xs text-zinc-500 block">ID: {{ $bill['student_id'] }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Vocational Program</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $bill['program'] }}</span>
                    <span class="text-xs text-zinc-500 block">{{ $bill['term'] }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider block">Student Contact</span>
                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $bill['email'] }}</span>
                    <span class="text-xs text-zinc-500 block">Status: Active Apprentice</span>
                </div>
            </div>

            {{-- Line Items Table --}}
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 mb-3">
                        Itemized Statement Schedule
                    </h3>
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-800 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden text-sm">
                        @foreach ($bill['items'] as $item)
                            <div class="flex items-center justify-between p-4 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition">
                                <div class="space-y-0.5">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $item['description'] }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item['category'] }}</div>
                                </div>
                                <div class="font-mono font-medium text-zinc-900 dark:text-zinc-100">
                                    ${{ number_format($item['amount_in_cents'] / 100, 2) }}
                                </div>
                            </div>
                        @endforeach

                        {{-- Total Due Row --}}
                        <div class="flex items-center justify-between p-4 bg-indigo-50/50 dark:bg-indigo-950/20 font-bold">
                            <div class="text-base text-zinc-900 dark:text-white">Total Tuition Balance Due</div>
                            <div class="text-xl font-mono text-indigo-600 dark:text-indigo-400">
                                ${{ number_format($bill['amount_in_cents'] / 100, 2) }} <span class="text-xs font-normal text-zinc-500">USD</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Processing Form / Confirmation State --}}
                @if ($isPaid && $paymentReceipt)
                    {{-- Paid Confirmation Box --}}
                    <div class="p-6 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="size-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </div>
                            <div class="flex-1 space-y-1">
                                <h4 class="text-lg font-bold text-emerald-900 dark:text-emerald-200">
                                    Payment Confirmed and Settled
                                </h4>
                                <p class="text-sm text-emerald-800 dark:text-emerald-300">
                                    {{ $paymentReceipt['message'] }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-xs border-t border-emerald-200 dark:border-emerald-800/80">
                            <div>
                                <span class="text-emerald-700 dark:text-emerald-400 font-medium block">Transaction ID</span>
                                <span class="font-mono font-bold text-emerald-900 dark:text-emerald-200">{{ $paymentReceipt['transaction_id'] }}</span>
                            </div>
                            <div>
                                <span class="text-emerald-700 dark:text-emerald-400 font-medium block">Gateway Provider</span>
                                <span class="font-semibold text-emerald-900 dark:text-emerald-200">{{ $paymentReceipt['provider_label'] }}</span>
                            </div>
                            <div>
                                <span class="text-emerald-700 dark:text-emerald-400 font-medium block">Timestamp</span>
                                <span class="text-emerald-900 dark:text-emerald-200">{{ $paymentReceipt['processed_at'] }}</span>
                            </div>
                        </div>

                        <div class="pt-2 flex flex-col sm:flex-row gap-3">
                            <button
                                type="button"
                                wire:click="resetPayment"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-medium text-xs text-emerald-900 dark:text-emerald-100 bg-emerald-200/70 dark:bg-emerald-900/60 hover:bg-emerald-300 dark:hover:bg-emerald-800 transition cursor-pointer"
                            >
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                <span>Test Another Payment Provider (Reset Bill)</span>
                            </button>
                        </div>
                    </div>
                @else
                    {{-- Payment Method Selection Form --}}
                    <form wire:submit="processPayment" class="space-y-6">
                        <div class="space-y-3">
                            <label class="text-sm font-bold uppercase tracking-wider text-zinc-700 dark:text-zinc-300 block">
                                Select Payment Method
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($providers as $provider)
                                    <label
                                        class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition {{ $selectedProvider === $provider->value ? 'border-indigo-600 bg-indigo-50/40 dark:border-indigo-500 dark:bg-indigo-950/30 ring-2 ring-indigo-600 dark:ring-indigo-500' : 'border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-300 dark:hover:border-zinc-700' }}"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <input
                                                    type="radio"
                                                    wire:model.live="selectedProvider"
                                                    value="{{ $provider->value }}"
                                                    class="size-4 text-indigo-600 focus:ring-indigo-500 border-zinc-300"
                                                />
                                                <span class="font-bold text-sm text-zinc-900 dark:text-white">
                                                    {{ $provider->label() }}
                                                </span>
                                            </div>

                                            @if ($provider->value === 'stripe')
                                                <span class="px-2 py-0.5 text-2xs font-semibold rounded bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                                                    Card / Apple Pay
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 text-2xs font-semibold rounded bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300">
                                                    Wallet / Pay in 4
                                                </span>
                                            @endif
                                        </div>

                                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 pl-7 leading-relaxed">
                                            {{ $provider->description() }}
                                        </p>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedProvider')
                                <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                                <svg class="size-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                                <span>Wrapped in Database Transaction • Encrypted Mock Gateway</span>
                            </div>

                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-500 shadow-md hover:shadow-lg transition cursor-pointer text-sm disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="processPayment">
                                    Authorize & Pay ${{ number_format($bill['amount_in_cents'] / 100, 2) }}
                                </span>
                                <span wire:loading wire:target="processPayment" class="inline-flex items-center gap-2">
                                    <svg class="animate-spin size-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Processing Transaction...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- Educational SOLID Architecture Card --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8 space-y-6 shadow-xs">
            <div class="space-y-1">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Software Design Rationale</span>
                <h3 class="text-xl font-bold text-zinc-900 dark:text-white">
                    How SOLID Principles & The Strategy Pattern Power This Subsystem
                </h3>
                <p class="text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                    Payment processing is one of the most classic real-world applications of OOP design principles. Below is how each principle is structured in this application's code.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm">
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                    <div class="font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                        <span class="size-5 rounded-md bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center font-extrabold text-2xs">S</span>
                        <span>Single Responsibility (SRP)</span>
                    </div>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        <code class="text-xs font-mono bg-zinc-200 dark:bg-zinc-800 px-1 py-0.5 rounded">PaymentProcessor</code> coordinates database transactions and audit logging. Gateway classes only handle provider communication.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                    <div class="font-bold text-violet-600 dark:text-violet-400 flex items-center gap-1.5">
                        <span class="size-5 rounded-md bg-violet-100 dark:bg-violet-900/60 flex items-center justify-center font-extrabold text-2xs">O</span>
                        <span>Open / Closed Principle (OCP)</span>
                    </div>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Adding Square or Apple Pay 3 months later only requires adding a new Gateway class and registering it. Zero existing gateway or UI code is modified.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                    <div class="font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                        <span class="size-5 rounded-md bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center font-extrabold text-2xs">L</span>
                        <span>Liskov Substitution (LSP)</span>
                    </div>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Every gateway implements <code class="text-xs font-mono bg-zinc-200 dark:bg-zinc-800 px-1 py-0.5 rounded">PaymentGatewayInterface</code> and returns a standard <code class="text-xs font-mono bg-zinc-200 dark:bg-zinc-800 px-1 py-0.5 rounded">PaymentResponse</code> DTO without throwing unhandled vendor exceptions.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-800 space-y-1.5">
                    <div class="font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                        <span class="size-5 rounded-md bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center font-extrabold text-2xs">I & D</span>
                        <span>Interface Segregation & Dependency Inversion</span>
                    </div>
                    <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        High-level components depend on lean contracts (<code class="text-xs font-mono bg-zinc-200 dark:bg-zinc-800 px-1 py-0.5 rounded">PaymentGatewayInterface</code>) and factories, never concrete SDK clients.
                    </p>
                </div>
            </div>
        </div>

    </div>
</main>
