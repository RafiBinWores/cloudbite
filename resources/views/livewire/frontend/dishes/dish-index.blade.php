<div
    x-data="{ filtersOpen: false }"
    x-init="$watch('filtersOpen', value => {
        if (value) {
            document.body.classList.add('overflow-hidden')
        } else {
            document.body.classList.remove('overflow-hidden')
        }
    })"
    class="font-sans"
>
    <!-- Container -->
    <div class="max-w-7xl px-4 mx-auto py-10 lg:py-16">

        <div class="grid grid-cols-12 gap-6">
            <!-- Sidebar (desktop) -->
            <aside class="hidden lg:block col-span-3 border border-gray-300 rounded-xl p-4 font-oswald">
                <p class="font-medium text-2xl pb-4 border-b border-gray-300">
                    Filters
                </p>

                <!-- Search -->
                <div class="mt-4">
                    <p class="mb-1 text-lg">Search</p>
                    <label class="input rounded-lg border border-gray-300 flex items-center gap-2 px-3 h-11">
                        <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                                stroke="currentColor">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </g>
                        </svg>
                        <input type="search" placeholder="Search"
                            class="flex-1 outline-none border-none focus:ring-0"
                            wire:model.live.debounce.300ms="search" />
                    </label>
                </div>

                <!-- Categories -->
                <div class="space-y-4">
                    <p class="text-lg mt-4">Categories</p>
                    <div x-data="{ showAll: false }">
                        <div class="ps-1 space-y-3">
                            @foreach ($categoryOptions as $cat)
                                <label
                                    x-show="
                                        showAll
                                        || {{ $loop->index }} < 5
                                        || (
                                            Array.isArray($wire.get('categories'))
                                            && $wire.get('categories').includes('{{ $cat['slug'] }}')
                                        )
                                    "
                                    x-cloak
                                    class="font-jost text-sm flex items-center font-medium gap-2"
                                >
                                    <input type="checkbox"
                                        class="focus:ring-red-500 text-red-500 size-4 border-2 border-gray-300 rounded"
                                        value="{{ $cat['slug'] }}" wire:model.live="categories" />
                                    {{ $cat['name'] }}
                                </label>
                            @endforeach
                        </div>

                        @if (count($categoryOptions) > 5)
                            <button type="button" @click="showAll = !showAll"
                                class="mt-3 text-sm font-medium text-customRed-100 hover:underline cursor-pointer"
                                x-text="showAll ? 'Show less' : 'View all ({{ count($categoryOptions) - 5 }})'"></button>
                        @endif
                    </div>
                </div>

                {{-- Cuisines --}}
                <div x-data="{ showAllCui: false }" class="mt-6">
                    <p class="text-lg mt-4 mb-4">Cuisines</p>
                    <div class="ps-1 space-y-3">
                        @foreach ($cuisineOptions as $cui)
                            <label
                                x-show="
                                    showAllCui
                                    || {{ $loop->index }} < 5
                                    || (
                                        Array.isArray($wire.get('cuisines'))
                                        && $wire.get('cuisines').includes('{{ $cui['slug'] }}')
                                    )
                                "
                                x-cloak
                                class="font-jost text-sm flex items-center font-medium gap-2"
                            >
                                <input type="checkbox"
                                    class="focus:ring-red-500 text-red-500 size-4 border-2 border-gray-300 rounded"
                                    value="{{ $cui['slug'] }}" wire:model.live="cuisines" />
                                {{ $cui['name'] }}
                            </label>
                        @endforeach
                    </div>

                    @if (count($cuisineOptions) > 5)
                        <button type="button" @click="showAllCui = !showAllCui"
                            class="mt-3 text-sm font-medium text-customRed-100 hover:underline cursor-pointer"
                            x-text="showAllCui ? 'Show less' : 'View all ({{ count($cuisineOptions) - 5 }})'"></button>
                    @endif
                </div>

            </aside>

            <!-- Mobile Filters Drawer -->
          
            <div class="lg:hidden fixed inset-0 z-40" x-cloak x-show="filtersOpen" x-transition.opacity>
                <!-- Overlay -->
                <div class="absolute inset-0 bg-black/40" @click="filtersOpen=false"></div>

                <!-- Panel -->
                <aside
                    class="absolute left-0 top-0 h-full w-[85%] max-w-sm bg-white shadow-xl p-4 font-oswald
                           transform transition-transform duration-300 overflow-y-auto pb-10"
                    :class="filtersOpen ? 'translate-x-0' : '-translate-x-full'"
                >
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                        <p class="font-medium text-2xl">Filters</p>
                        <button @click="filtersOpen=false"
                            class="w-10 h-10 grid place-items-center rounded-md border border-gray-300 cursor-pointer hover:bg-gray-100 duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Same content as desktop sidebar -->
                    <div class="mt-4">
                        <p class="mb-1 text-lg">Search</p>
                        <label class="input rounded-lg border border-gray-300 flex items-center gap-2 px-3 h-11">
                            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                                    stroke="currentColor">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.3-4.3"></path>
                                </g>
                            </svg>
                            <input type="search" placeholder="Search"
                                class="flex-1 outline-none border-none focus:ring-0"
                                wire:model.live.debounce.300ms="search" />
                        </label>
                    </div>

                    <!-- Categories -->
                    <div class="space-y-4">
                        <p class="text-lg mt-4">Categories</p>
                        <div x-data="{ showAll: false }">
                            <div class="ps-1 space-y-3">
                                @foreach ($categoryOptions as $cat)
                                    <label
                                        x-show="
                                            showAll
                                            || {{ $loop->index }} < 5
                                            || (
                                                Array.isArray($wire.get('categories'))
                                                && $wire.get('categories').includes('{{ $cat['slug'] }}')
                                            )
                                        "
                                        x-cloak
                                        class="font-jost text-sm flex items-center font-medium gap-2"
                                    >
                                        <input type="checkbox"
                                            class="focus:ring-red-500 text-red-500 size-4 border-2 border-gray-300 rounded"
                                            value="{{ $cat['slug'] }}" wire:model.live="categories" />
                                        {{ $cat['name'] }}
                                    </label>
                                @endforeach
                            </div>

                            @if (count($categoryOptions) > 5)
                                <button type="button" @click="showAll = !showAll"
                                    class="mt-3 text-sm font-medium text-customRed-100 hover:underline cursor-pointer"
                                    x-text="showAll ? 'Show less' : 'View all ({{ count($categoryOptions) - 5 }})'"></button>
                            @endif
                        </div>
                    </div>

                    {{-- Cuisines --}}
                    <div x-data="{ showAllCui: false }" class="mt-6">
                        <p class="text-lg mt-4 mb-4">Cuisines</p>
                        <div class="ps-1 space-y-3">
                            @foreach ($cuisineOptions as $cui)
                                <label
                                    x-show="
                                        showAllCui
                                        || {{ $loop->index }} < 5
                                        || (
                                            Array.isArray($wire.get('cuisines'))
                                            && $wire.get('cuisines').includes('{{ $cui['slug'] }}')
                                        )
                                    "
                                    x-cloak
                                    class="font-jost text-sm flex items-center font-medium gap-2"
                                >
                                    <input type="checkbox"
                                        class="focus:ring-red-500 text-red-500 size-4 border-2 border-gray-300 rounded"
                                        value="{{ $cui['slug'] }}" wire:model.live="cuisines" />
                                    {{ $cui['name'] }}
                                </label>
                            @endforeach
                        </div>

                        @if (count($cuisineOptions) > 5)
                            <button type="button" @click="showAllCui = !showAllCui"
                                class="mt-3 text-sm font-medium text-customRed-100 hover:underline cursor-pointer"
                                x-text="showAllCui ? 'Show less' : 'View all ({{ count($cuisineOptions) - 5 }})'"></button>
                        @endif
                    </div>
                </aside>
            </div>
            <!-- /Mobile Filters Drawer -->

            <!-- Main content -->
            <section class="col-span-12 lg:col-span-9 lg:ps-2">
                <div class="flex flex-wrap items-center justify-between gap-3 font-jost">
                    <button
                        class="lg:hidden inline-flex items-center gap-2 px-4 py-1.5 rounded border border-gray-300 bg-white cursor-pointer hover:bg-slate-50 duration-300"
                        @click="filtersOpen = true">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5h18M6 12h12M10 19h4" />
                        </svg>
                        Filters
                    </button>

                    <p class="text-base sm:text-lg hidden lg:block">
                        @if ($dishes->total())
                            Showing {{ $dishes->firstItem() }}–{{ $dishes->lastItem() }} of {{ $dishes->total() }}
                            results
                        @else
                            No results found
                        @endif
                    </p>

                    <div class="flex items-center gap-2">
                        <label class="text-base sm:text-lg">Sort By:</label>
                        <select
                            class="border px-2 border-gray-300 p-1.5 rounded focus:border-red-500 focus:ring-red-500"
                            wire:model.live="sort">
                            <option value="all">All</option>
                            <option value="name">Name</option>
                            <option value="popularity">Popularity</option>
                            <option value="price_asc">Price (low to high)</option>
                            <option value="price_desc">Price (high to low)</option>
                        </select>
                    </div>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                    @forelse ($dishes as $dish)
                        @php
                            // ---------- VARIATION PRICE HELPERS (EXTRA-BASED) ----------
                            $variations = collect($dish->variations ?? []);

                            // Collect all option extras (treat option['price'] as EXTRA amount)
                            $varExtras = $variations
                                ->flatMap(function ($g) {
                                    return collect($g['options'] ?? [])->pluck('price');
                                })
                                ->filter(fn($p) => is_numeric($p))
                                ->map(fn($p) => (float) $p);

                            $hasVariations = $varExtras->count() > 0;

                            $minExtra = $hasVariations ? $varExtras->min() : 0;
                            $maxExtra = $hasVariations ? $varExtras->max() : 0;

                            // ---------- BASE PRICE ----------
                            $baseOriginal = (float) ($dish->price ?? 0);

                            // Apply dish discount on BASE (not on extras)
                            $applyDiscountOnBase = function ($price) use ($dish) {
                                $price = (float) $price;

                                if ($dish->discount && $dish->discount_type) {
                                    if ($dish->discount_type === 'percent') {
                                        $price = max(0, $price - $price * ((float) $dish->discount / 100));
                                    } elseif ($dish->discount_type === 'amount') {
                                        $price = max(0, $price - (float) $dish->discount);
                                    }
                                }

                                return round($price, 2);
                            };

                            $baseDiscounted = $applyDiscountOnBase($baseOriginal);

                            // ---------- FINAL "FROM" PRICES ----------
                            // From price = discounted base + minimum extra
                            $fromPriceDiscounted = $baseDiscounted + $minExtra;

                            // Old compare = original base + minimum extra
                            $fromPriceOriginal = $baseOriginal + $minExtra;

                            // For non-variation dishes
                            $normalPrice = $baseDiscounted;
                            $normalOld = $baseOriginal;

                            // Formatting helper
                            $money = fn($v) => fmod((float) $v, 1) == 0 ? number_format($v, 0) : number_format($v, 2);
                        @endphp

                        <div class="card bg-base-100 shadow-sm rounded-xl">
                            <figure class="relative">
                                <img src="{{ asset($dish->thumbnail) }}" alt="{{ $dish->title }}"
                                    class="w-full h-48 object-cover rounded-t-xl" />

                                {{-- Discount Badge --}}
                                @if ($dish->discount && $dish->discount_type)
                                    <span
                                        class="absolute top-2 left-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-customRed-100/80 text-white z-10">
                                        @php
                                            $discountValue =
                                                fmod($dish->discount, 1) === 0.0
                                                    ? intval($dish->discount)
                                                    : number_format($dish->discount, 2, '.', '');
                                        @endphp

                                        @if ($dish->discount_type === 'percent')
                                            {{ $discountValue }} <span class="ps-1 font-jost">&#x25; OFF</span>
                                        @elseif($dish->discount_type === 'amount')
                                            {{ $discountValue }}
                                            <span class="font-normal font-oswald ps-1">&#2547;</span>
                                            <span class="ps-1">OFF</span>
                                        @endif
                                    </span>
                                @endif
                            </figure>

                            <div class="card-body p-3">
                                <h2 class="card-title font-medium font-oswald line-clamp-1 text-slate-900">
                                    {{ $dish->title }}
                                </h2>

                                <p class="font-jost line-clamp-1">{{ $dish->short_description }}</p>

                                <div class="flex items-center justify-between mt-2">
                                    <div class="font-oswald text-customRed-100 flex items-center gap-2">
                                        @if ($hasVariations)
                                            {{-- Show "From" price using base + min extra --}}
                                            <p class="font-medium text-lg">
                                                <span class="font-bold">&#2547;</span>
                                                <span>From</span> {{ $money($fromPriceDiscounted) }}
                                            </p>

                                            {{-- Old price compare if discount present --}}
                                            @if ($dish->discount && $dish->discount_type && $fromPriceDiscounted < $fromPriceOriginal)
                                                <p class="line-through text-gray-500">
                                                    <span class="font-bold">&#2547;</span>
                                                    {{ $money($fromPriceOriginal) }}
                                                </p>
                                            @endif
                                        @else
                                            {{-- Normal dish price --}}
                                            <p class="font-medium text-lg">
                                                <span class="font-bold">&#2547;</span>
                                                {{ $money($normalPrice) }}
                                            </p>

                                            @if ($normalPrice < $normalOld)
                                                <p class="font-medium line-through text-gray-500">
                                                    <span class="font-bold">&#2547;</span>
                                                    {{ $money($normalOld) }}
                                                </p>
                                            @endif
                                        @endif
                                    </div>

                                    <button
                                        wire:click="$dispatch('open-add-to-cart', { dishId: {{ $dish->id }} })"
                                        class="inline-block relative isolate rounded px-5 py-2 mt-1 overflow-hidden cursor-pointer bg-customRed-100 font-medium text-white group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/60">
                                        <span
                                            class="pointer-events-none absolute w-64 h-0 rotate-45 -translate-x-20 bg-slate-900 top-1/2 transition-all duration-300 ease-out group-hover:h-64 group-hover:-translate-y-32"></span>
                                        <span
                                            class="relative z-10 transition-colors font-medium font-oswald duration-300 group-hover:text-white">
                                            Add to Cart
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <div
                                class="rounded-xl border border-dashed p-8 text-center text-slate-600 flex flex-col items-center gap-4 font-medium">
                                <i class="fa-regular fa-box-open text-8xl mb-4 text-neutral-500"></i>
                                Nothing found. Try another search or remove some filters.
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $dishes->onEachSide(1)->links() }}
                </div>
            </section>
        </div>
    </div>

    {{-- cart modal --}}
    <livewire:frontend.cart.add-to-cart-modal />
</div>
