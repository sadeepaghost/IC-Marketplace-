{{--
    Livewire component view: resources/views/livewire/ic-part-search.blade.php
    Requires: Tailwind CSS v3, Alpine.js v3
--}}
<div
    class="w-full"
    x-data="{
        showFilters: false,
        activeResult: null,
    }"
>

    {{-- ═══════════════════════════════════════════════════════════════════════
         SEARCH BAR
    ═══════════════════════════════════════════════════════════════════════ --}}
    <div class="relative">
        <div class="flex items-center gap-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm px-4 py-3 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition-all">

            {{-- Search Icon --}}
            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>

            {{-- Input --}}
            <input
                type="text"
                wire:model.live.debounce.300ms="query"
                placeholder="Search by part number, e.g. LM741, STM32F103…"
                autocomplete="off"
                spellcheck="false"
                class="flex-1 bg-transparent text-slate-900 dark:text-white placeholder-slate-400 text-base outline-none font-mono tracking-wide"
            />

            {{-- Loading Spinner --}}
            <div wire:loading wire:target="updatedQuery">
                <svg class="w-5 h-5 text-blue-500 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
            </div>

            {{-- Clear Button --}}
            @if($query)
            <button
                wire:click="$set('query', '')"
                class="text-slate-400 hover:text-slate-600 transition-colors shrink-0"
                aria-label="Clear search"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            @endif
        </div>

        {{-- Keyboard hint --}}
        <p class="mt-1.5 text-xs text-slate-400 pl-1">
            Search across {{ number_format(\App\Models\Product::count()) }} components from 500+ manufacturers
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
         SEARCH RESULTS
    ═══════════════════════════════════════════════════════════════════════ --}}
    @if($mode === 'search' && count($results) > 0)

        {{-- Result count --}}
        <div class="flex items-center justify-between mt-6 mb-4" wire:loading.class="opacity-50" wire:target="updatedQuery">
            <p class="text-sm text-slate-500">
                Showing <strong class="text-slate-800 dark:text-slate-200">{{ count($results) }}</strong> result(s) for
                <span class="font-mono font-semibold text-blue-600">"{{ $lastSearchedQuery }}"</span>
            </p>
        </div>

        {{-- Results Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4" wire:loading.class="opacity-40" wire:target="updatedQuery">
            @foreach($results as $product)
                @include('livewire.partials.product-card', ['product' => $product])
            @endforeach
        </div>

    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════
         SOURCING / OUT-OF-STOCK MODE
    ═══════════════════════════════════════════════════════════════════════ --}}
    @if($mode === 'sourcing' && $lastSearchedQuery && !$requestSubmitted)
    <div class="mt-6 space-y-6" wire:loading.class="opacity-40" wire:target="updatedQuery">

        {{-- ── Out-of-Stock Alert Banner ─────────────────────────── --}}
        <div class="rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-950/30 dark:border-amber-800 p-5">
            <div class="flex gap-4">
                <div class="shrink-0 w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-full flex items-center justify-center">
                    {{-- Radar/AI icon --}}
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.347A3.999 3.999 0 0114 21H10a3.999 3.999 0 01-2.83-1.172l-.346-.347z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-amber-900 dark:text-amber-200 text-base">
                        <span class="font-mono font-black text-amber-700 dark:text-amber-400">
                            {{ strtoupper($lastSearchedQuery) }}
                        </span>
                        — Not In Current Inventory
                    </h3>
                    <p class="mt-1 text-sm text-amber-800 dark:text-amber-300 leading-relaxed">
                        Our <strong>procurement AI is now searching</strong> global supplier networks for this component.
                        Submit your contact details below and we will email you a
                        <strong>competitive quote within 24 hours</strong>.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── AI Suggestions ────────────────────────────────────── --}}
        @if(count($aiSuggestions) > 0)
        <div class="rounded-xl border border-blue-200 bg-blue-50 dark:bg-blue-950/30 dark:border-blue-800 p-5">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 7H7v6h6V7z"/><path fill-rule="evenodd" clip-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h1a2 2 0 012 2v1H3V5a2 2 0 012-2h1V2a1 1 0 011-1zM3 9h14v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                </svg>
                <span class="text-sm font-semibold text-blue-800 dark:text-blue-300">
                    AI-Suggested Alternatives for
                    <span class="font-mono">{{ strtoupper($lastSearchedQuery) }}</span>
                </span>
            </div>
            <div class="space-y-2">
                @foreach($aiSuggestions as $suggestion)
                <div class="flex items-start gap-3 bg-white dark:bg-slate-800 rounded-lg p-3 border border-blue-100 dark:border-slate-700">
                    <button
                        wire:click="searchSuggestion('{{ $suggestion['part_number'] }}')"
                        class="shrink-0 font-mono font-bold text-sm text-blue-700 dark:text-blue-400 hover:underline leading-tight"
                    >
                        {{ $suggestion['part_number'] }}
                    </button>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-600 dark:text-slate-300">{{ $suggestion['manufacturer'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $suggestion['reason'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Sourcing Request Form ─────────────────────────────── --}}
        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <h3 class="font-semibold text-slate-800 dark:text-slate-200 text-base flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Request a Quote — <span class="font-mono text-green-700 dark:text-green-400">{{ strtoupper($lastSearchedQuery) }}</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Free · No obligation · Secure</p>
            </div>

            <div class="p-5 space-y-4">
                @if($errors->any())
                <div class="rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 p-4">
                    <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex gap-1.5 items-start">
                                <span class="mt-0.5">•</span> {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model.defer="customerName"
                            placeholder="John Smith"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            wire:model.defer="customerEmail"
                            placeholder="john@company.com"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Company</label>
                        <input
                            type="text"
                            wire:model.defer="companyName"
                            placeholder="Acme Electronics Ltd."
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                            Quantity Required <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            wire:model.defer="quantityRequired"
                            min="1"
                            placeholder="100"
                            class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                        Notes / Specifications
                    </label>
                    <textarea
                        wire:model.defer="customerNotes"
                        rows="3"
                        placeholder="Any specific requirements? (temperature range, package type, compliance certifications, etc.)"
                        class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                    ></textarea>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <button
                        wire:click="backToSearch"
                        class="text-sm text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors"
                    >
                        ← Back to search
                    </button>
                    <button
                        wire:click="submitSourcingRequest"
                        wire:loading.attr="disabled"
                        wire:target="submitSourcingRequest"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold text-sm px-5 py-2.5 rounded-lg transition-colors"
                    >
                        <span wire:loading.remove wire:target="submitSourcingRequest">
                            Submit Sourcing Request
                        </span>
                        <span wire:loading wire:target="submitSourcingRequest">
                            Submitting…
                        </span>
                        <svg wire:loading.remove wire:target="submitSourcingRequest" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════
         SUCCESS STATE
    ═══════════════════════════════════════════════════════════════════════ --}}
    @if($requestSubmitted)
    <div class="mt-6 rounded-xl bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800 p-6 text-center"
         x-data x-init="$el.scrollIntoView({ behavior: 'smooth' })">
        <div class="w-14 h-14 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-green-800 dark:text-green-200">Sourcing Request Submitted!</h3>
        <p class="mt-1 text-sm text-green-700 dark:text-green-300">
            Reference: <span class="font-mono font-bold">{{ $submittedRefNumber }}</span>
        </p>
        <p class="mt-3 text-sm text-green-700 dark:text-green-400 max-w-sm mx-auto">
            Our procurement AI is now searching global supplier networks.
            Expect a competitive quote to your inbox within <strong>24 hours</strong>.
        </p>
        <button
            wire:click="backToSearch"
            class="mt-5 text-sm font-semibold text-green-700 dark:text-green-400 hover:underline"
        >
            Search for another component →
        </button>
    </div>
    @endif

    {{-- Empty state (typed something but no results yet mode not triggered) --}}
    @if($mode === 'search' && strlen($query) >= 2 && count($results) === 0 && !$lastSearchedQuery)
    <div class="mt-8 text-center text-slate-400 text-sm">
        <svg class="w-10 h-10 mx-auto mb-2 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
        </svg>
        Searching…
    </div>
    @endif

</div>
