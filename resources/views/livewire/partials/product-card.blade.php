{{--
    Partial: resources/views/livewire/partials/product-card.blade.php
    $product = array from Product::select([...])->get()->toArray()
--}}
@php
    $isInStock   = $product['stock_status'] === 'in_stock';
    $isLowStock  = $product['stock_status'] === 'low_stock';
    $isOutOfStock = in_array($product['stock_status'], ['out_of_stock', 'discontinued']);

    $statusLabel = match($product['stock_status']) {
        'in_stock'     => 'In Stock',
        'low_stock'    => 'Low Stock',
        'out_of_stock' => 'Procurement Required',
        'discontinued' => 'Discontinued',
        default        => 'Unknown',
    };

    $statusClasses = match($product['stock_status']) {
        'in_stock'     => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700',
        'low_stock'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700',
        'out_of_stock' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700',
        'discontinued' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 border-red-200 dark:border-red-800',
        default        => 'bg-slate-100 text-slate-600',
    };

    $dotColor = match($product['stock_status']) {
        'in_stock'  => 'bg-emerald-500',
        'low_stock' => 'bg-yellow-500',
        default     => 'bg-slate-400',
    };
@endphp

<div class="group relative rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="px-4 pt-4 pb-3 flex items-start justify-between gap-3">
        <div class="min-w-0">
            {{-- Part Number --}}
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono font-black text-lg text-slate-900 dark:text-white tracking-tight leading-none">
                    {{ $product['part_number'] }}
                </span>
                @if($product['rohs_compliant'])
                <span class="inline-flex items-center text-[10px] font-bold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800 px-1.5 py-0.5 rounded">
                    RoHS
                </span>
                @endif
            </div>
            {{-- Manufacturer --}}
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-medium">
                {{ $product['manufacturer'] }}
            </p>
        </div>

        {{-- Stock Status Badge --}}
        <div class="shrink-0">
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full border {{ $statusClasses }}">
                @if($isInStock || $isLowStock)
                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} {{ $isInStock ? 'animate-pulse' : '' }}"></span>
                @endif
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    {{-- ── Description ─────────────────────────────────────────────────── --}}
    <div class="px-4 pb-3">
        <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2 leading-relaxed">
            {{ $product['description'] }}
        </p>
    </div>

    {{-- ── Technical Specs Grid ─────────────────────────────────────────── --}}
    <div class="mx-4 mb-3 rounded-lg bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/50 overflow-hidden">
        <div class="grid grid-cols-2 divide-x divide-y divide-slate-100 dark:divide-slate-700/50">

            @if($product['package_type'])
            <div class="px-3 py-2">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">Package</p>
                <p class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $product['package_type'] }}</p>
            </div>
            @endif

            @if($product['supply_voltage_min'] || $product['supply_voltage_max'])
            <div class="px-3 py-2">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">Supply Voltage</p>
                <p class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                    {{ $product['supply_voltage_min'] }}
                    @if($product['supply_voltage_min'] && $product['supply_voltage_max']) – @endif
                    {{ $product['supply_voltage_max'] }}
                </p>
            </div>
            @endif

            @if($product['operating_temp_min'] || $product['operating_temp_max'])
            <div class="px-3 py-2">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">Operating Temp</p>
                <p class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                    {{ $product['operating_temp_min'] }}
                    @if($product['operating_temp_min'] && $product['operating_temp_max']) to @endif
                    {{ $product['operating_temp_max'] }}
                </p>
            </div>
            @endif

            @if($product['lead_time'])
            <div class="px-3 py-2">
                <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">Lead Time</p>
                <p class="text-xs font-mono font-bold {{ $isInStock ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-200' }} mt-0.5">
                    {{ $isInStock ? 'Ready to Ship' : $product['lead_time'] }}
                </p>
            </div>
            @endif

        </div>
    </div>

    {{-- ── Footer: Pricing + Actions ────────────────────────────────────── --}}
    <div class="px-4 pb-4 flex items-center justify-between gap-3">

        {{-- Pricing --}}
        <div>
            @if($product['unit_price_usd'])
                <p class="text-xl font-black text-slate-900 dark:text-white leading-none">
                    ${{ number_format($product['unit_price_usd'], 4) }}
                </p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500">per unit (1–{{ $product['quantity_available'] ?? '∞' }} pcs)</p>
            @else
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 italic">Price on request</p>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2">
            @if($product['datasheet_url'])
            <a
                href="{{ $product['datasheet_url'] }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800 rounded-lg px-2.5 py-1.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
            >
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/>
                </svg>
                Datasheet
            </a>
            @endif

            @if($isInStock || $isLowStock)
            <button class="inline-flex items-center gap-1.5 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-3 py-1.5 transition-colors">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Add to RFQ
            </button>
            @else
            <button
                wire:click="$set('mode', 'sourcing')"
                class="inline-flex items-center gap-1.5 text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white rounded-lg px-3 py-1.5 transition-colors"
            >
                Request Quote
            </button>
            @endif
        </div>
    </div>

    {{-- ── Hover: Stock count ────────────────────────────────────────────── --}}
    @if($product['quantity_available'] > 0)
    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
    @endif

</div>
