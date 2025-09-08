<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="pb-4" level="1">{{ __('Dish Details') }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    @php
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Storage;

        // --- Money formatting (hide .00) ---
        $money = fn($v) => fmod((float) $v, 1) == 0 ? number_format($v, 0) : number_format($v, 2);

        // --- Price math ---
        $basePrice = (float) ($dish->price ?? 0);
        $hasDiscount = $dish->discount_type && ($dish->discount ?? 0) > 0;
        $discounted = $hasDiscount
            ? ($dish->discount_type === 'percent'
                ? max(0, $basePrice - $basePrice * (($dish->discount ?? 0) / 100))
                : max(0, $basePrice - (float) $dish->discount))
            : $basePrice;

        // --- Tags: accept array or CSV of names ---
        $tags = $dish->tags;
        if (is_string($tags)) {
            $tags = collect(explode(',', $tags))->map(fn($t) => trim($t))->filter();
        } else {
            $tags = collect($tags ?? [])
                ->map(fn($t) => is_string($t) ? trim($t) : $t)
                ->filter();
        }

        // --- Media URLs ---
        $placeholder = asset('images/placeholder-dish.png'); // add a file in public/images/
        $mainThumb = $dish->thumbnail ? Storage::url($dish->thumbnail) : $placeholder;
        $gallery = is_array($dish->gallery) ? $dish->gallery : (array) ($dish->gallery ?? []);

        // --- Time display (12-hour) ---
        $fromDisplay = $dish->available_from
            ? Carbon::createFromFormat('H:i:s', $dish->available_from)->format('g:i A')
            : null;
        $tillDisplay = $dish->available_till
            ? Carbon::createFromFormat('H:i:s', $dish->available_till)->format('g:i A')
            : null;

        // --- Availability (robust for TIME columns, incl. overnight) ---
        $now24 = Carbon::now()->format('H:i:s');
        $from24 = $dish->available_from
            ? Carbon::createFromFormat('H:i:s', $dish->available_from)->format('H:i:s')
            : null;
        $till24 = $dish->available_till
            ? Carbon::createFromFormat('H:i:s', $dish->available_till)->format('H:i:s')
            : null;

        $withinWindow = true;
        if ($from24 && $till24) {
            if ($from24 <= $till24) {
                // Same-day window, e.g., 10:00 -> 18:00
                $withinWindow = $now24 >= $from24 && $now24 <= $till24;
            } else {
                // Overnight window, e.g., 22:00 -> 03:00
                $withinWindow = $now24 >= $from24 || $now24 <= $till24;
            }
        } elseif ($from24) {
            $withinWindow = $now24 >= $from24;
        } elseif ($till24) {
            $withinWindow = $now24 <= $till24;
        }

        $stockOk = ($dish->track_stock ?? 'No') === 'No' || ($dish->daily_stock ?? 0) > 0;

        $isAvailable = $dish->is_available ?? $dish->visibility === 'Yes' && $withinWindow && $stockOk;
    @endphp

    <div class="px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- LEFT: Media -->
            <div class="space-y-4">
                <!-- Main image -->
                <div class="w-full aspect-square bg-neutral-100 dark:bg-neutral-800 rounded-2xl overflow-hidden">
                    <img id="mainPreview" src="{{ $mainThumb }}" alt="{{ $dish->title }}" loading="lazy"
                        class="w-full h-full object-cover object-center">
                </div>

                <!-- Gallery -->
                @if (!empty($gallery))
                    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-3">
                        @foreach ($gallery as $img)
                            @php $imgUrl = $img ? Storage::url($img) : $placeholder; @endphp
                            <button type="button"
                                class="border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden focus:outline-none focus:ring-2 focus:ring-rose-500"
                                onclick="document.getElementById('mainPreview').src='{{ $imgUrl }}'">
                                <img src="{{ $imgUrl }}" alt="thumb" loading="lazy"
                                    class="w-full h-20 object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- RIGHT: Details -->
            <div class="space-y-6">

                <!-- Title -->
                <h1 class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">
                    {{ $dish->title }}
                </h1>

                <!-- Category · Cuisine -->
                <div class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ $dish->category?->name ?? '—' }}
                    @if ($dish->cuisine?->name)
                        · {{ $dish->cuisine->name }}
                    @endif
                </div>

                <!-- Price -->
                <div class="flex items-center gap-3">
                    @if ($hasDiscount)
                        <div class="text-2xl font-semibold text-rose-500">৳{{ $money($discounted) }}</div>
                        <div class="line-through text-neutral-400">৳{{ $money($basePrice) }}</div>
                        <span
                            class="px-2 py-0.5 text-xs rounded bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200">
                            -{{ $dish->discount_type === 'percent' ? $money($dish->discount) . '%' : '৳' . $money($dish->discount) }}
                        </span>
                    @else
                        <div class="text-2xl font-semibold text-emerald-500">৳{{ $money($basePrice) }}</div>
                    @endif

                    <flux:separator vertical />

                    <div>
                        @php
                            $basePrice = (float) ($dish->price ?? 0);

                            // Apply discount
                            if ($dish->discount_type && $dish->discount > 0) {
                                if ($dish->discount_type === 'percent') {
                                    $afterDiscount = max(0, $basePrice - $basePrice * ($dish->discount / 100));
                                } else {
                                    $afterDiscount = max(0, $basePrice - (float) $dish->discount);
                                }
                            } else {
                                $afterDiscount = $basePrice;
                            }

                            // Apply VAT (assume $dish->vat = percent, e.g. 15)
                            $vatPercent = (float) ($dish->vat ?? 0);
                            $finalPrice = $afterDiscount + $afterDiscount * ($vatPercent / 100);

                            // Format function
                            $money = fn($v) => fmod($v, 1) == 0 ? number_format($v, 0) : number_format($v, 2);
                        @endphp

                        <!-- Show price -->
                        <div class="text-xl font-semibold text-emerald-600">
                            ৳{{ $money($finalPrice) }} <span
                                class="px-2 py-0.5 text-xs rounded bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200">
                                -{{ $dish->discount_type === 'percent' ? $money($dish->discount) . '%' : '৳' . $money($dish->discount) }}
                            </span>
                        </div>

                        @if ($dish->vat)
                            <div class="text-xs text-neutral-500">
                                (includes {{ $dish->vat }}% VAT & Discount)
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Availability + Time window + Visibility -->
                <div class="flex flex-wrap items-center gap-2">
                    @if ($isAvailable)
                        <span
                            class="px-3 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Available</span>
                    @else
                        <span
                            class="px-3 py-1 text-xs rounded-full bg-neutral-200 text-neutral-700 dark:bg-neutral-700 dark:text-neutral-300">Unavailable</span>
                    @endif>

                    @if ($fromDisplay || $tillDisplay)
                        <span class="text-xs text-neutral-500 dark:text-neutral-400">
                            {{ $fromDisplay ?: '—' }} – {{ $tillDisplay ?: '—' }}
                        </span>
                    @endif

                    <span class="text-xs">
                        <span
                            class="px-2 py-0.5 rounded-full font-medium
                        {{ $dish->visibility === 'Yes'
                            ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                            : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300' }}">
                            Visibility: {{ $dish->visibility ?? '—' }}
                        </span>
                    </span>
                </div>

                <!-- Info grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="text-sm">
                        <div class="text-neutral-500 dark:text-neutral-400">SKU</div>
                        <div class="font-medium text-neutral-800 dark:text-neutral-100">
                            {{ $dish->sku ?: '—' }}
                        </div>
                    </div>
                    <div class="text-sm">
                        <div class="text-neutral-500 dark:text-neutral-400">VAT</div>
                        <div class="font-medium text-neutral-800 dark:text-neutral-100">
                            {{ isset($dish->vat) ? $dish->vat . '%' : '—' }}
                        </div>
                    </div>
                    <div class="text-sm">
                        <div class="text-neutral-500 dark:text-neutral-400">Track Stock</div>
                        <div class="font-medium text-neutral-800 dark:text-neutral-100">
                            {{ $dish->track_stock ?? '—' }}
                        </div>
                    </div>
                    <div class="text-sm">
                        <div class="text-neutral-500 dark:text-neutral-400">Daily Stock</div>
                        <div class="font-medium text-neutral-800 dark:text-neutral-100">
                            {{ $dish->track_stock === 'Yes' ? $dish->daily_stock ?? 0 : '—' }}
                        </div>
                    </div>
                </div>

                <!-- Tags -->
                @if ($tags->count())
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <span class="text-sm text-neutral-500 dark:text-neutral-400">Tags:</span>
                        @foreach ($tags as $tag)
                            <span
                                class="text-xs bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300 px-2 py-0.5 rounded-full font-medium">
                                {{ is_array($tag) ? $tag['name'] ?? json_encode($tag) : $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Buns -->
                @if ($dish->buns && $dish->buns->count())
                    <div class="p-4 rounded-2xl border border-neutral-200 dark:border-neutral-700">
                        <h3 class="font-semibold text-neutral-800 dark:text-neutral-100">Buns</h3>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($dish->buns as $bun)
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-sm">
                                    <span>{{ $bun->name }}</span>
                                    @if (!is_null($bun->price ?? null))
                                        <span
                                            class="text-neutral-500 dark:text-neutral-400">৳{{ $money($bun->price) }}</span>
                                    @endif

                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Crusts -->
                @if ($dish->crusts && $dish->crusts->count())
                    <div class="p-4 rounded-2xl border border-neutral-200 dark:border-neutral-700">
                        <h3 class="font-semibold text-neutral-800 dark:text-neutral-100">Crusts</h3>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($dish->crusts as $crust)
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-sm">
                                    <span>{{ $crust->name }}</span>
                                    <span class="text-neutral-500 dark:text-neutral-400">
                                        {{ isset($crust->price) && $crust->price > 0 ? '৳' . $money($crust->price) : 'Free' }}
                                    </span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add-ons -->
                @if ($dish->addOns && $dish->addOns->count())
                    <div class="p-4 rounded-2xl border border-neutral-200 dark:border-neutral-700">
                        <h3 class="font-semibold text-neutral-800 dark:text-neutral-100">Add-ons</h3>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($dish->addOns as $addon)
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 text-sm">
                                    <span>{{ $addon->name }}</span>
                                    @if (isset($addon->price))
                                        <span
                                            class="text-neutral-500 dark:text-neutral-400">৳{{ $money($addon->price) }}</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div> <!-- /RIGHT -->
        </div> <!-- /grid -->

        <div class="space-y-6 mt-6">
            <!-- Short description -->
            <div class="p-4 rounded-2xl border border-neutral-200 dark:border-neutral-700">
                <p class="font-semibold text-neutral-800 dark:text-neutral-100 mb-1">Short Description</p>
                <p class="text-neutral-600 dark:text-neutral-300">{{ $dish->short_description ?? '—' }}</p>
            </div>

            <!-- Description -->
            <div class="p-4 rounded-2xl border border-neutral-200 dark:border-neutral-700">
                <p class="font-semibold text-neutral-800 dark:text-neutral-100 mb-2">Description</p>
                <div class="prose dark:prose-invert max-w-none text-neutral-700 dark:text-neutral-200">
                    {!! $dish->description ?? '—' !!}
                </div>
            </div>

            <!-- SEO / Meta (collapsible) -->
            <div x-data="{ openSeo: false }" class="border border-neutral-200 dark:border-neutral-700 rounded-2xl">
                <button type="button" @click="openSeo = !openSeo"
                    class="w-full flex items-center justify-between px-4 py-3 text-left">
                    <span class="font-medium text-neutral-800 dark:text-neutral-100">SEO Metadata</span>
                    <span class="text-sm text-neutral-500" x-text="openSeo ? 'Hide' : 'Show'"></span>
                </button>
                <div x-show="openSeo" x-cloak class="px-4 pb-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-neutral-500 dark:text-neutral-400">Meta Title</div>
                            <div class="font-medium text-neutral-800 dark:text-neutral-100">
                                {{ $dish->meta_title ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-neutral-500 dark:text-neutral-400">Meta Keywords</div>
                            <div class="font-medium text-neutral-800 dark:text-neutral-100">
                                {{ $dish->meta_keyword ?: '—' }}</div>
                        </div>
                        <div class="sm:col-span-2">
                            <div class="text-neutral-500 dark:text-neutral-400">Meta Description</div>
                            <div class="font-medium text-neutral-800 dark:text-neutral-100">
                                {{ $dish->meta_description ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /container -->




</div>
