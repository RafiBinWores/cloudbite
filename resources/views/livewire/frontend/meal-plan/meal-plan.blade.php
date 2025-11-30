<div>
    @push('styles')
    @endpush

    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-10 pb-20">
        <div class="max-w-6xl mx-auto px-4 py-10 space-y-6">

            {{-- Plan toggle + header --}}
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-slate-900 font-oswald text-3xl md:text-4xl lg:text-5xl font-medium capitalize mb-1">
                        {{ $planType === 'weekly' ? 'Weekly Plan' : 'Monthly Plan' }}
                    </h1>
                    <p class="font-jost text-gray-600 text-lg">
                        Customize meals for the next {{ $planType === 'weekly' ? '7 days' : '4 weeks' }}.
                    </p>
                </div>

                <div class="inline-flex rounded-full border border-slate-200 bg-white overflow-hidden">
                    <button
                        type="button"
                        wire:click="$set('planType', 'weekly')"
                        class="px-4 py-2 text-sm font-medium font-jost cursor-pointer
                               {{ $planType === 'weekly'
                                    ? 'bg-customRed-100 text-white'
                                    : 'text-slate-700 hover:bg-slate-50' }}"
                    >
                        Weekly
                    </button>
                    <button
                        type="button"
                        wire:click="$set('planType', 'monthly')"
                        class="px-4 py-2 text-sm font-medium font-jost border-l border-slate-200 cursor-pointer
                               {{ $planType === 'monthly'
                                    ? 'bg-customRed-100 text-white'
                                    : 'text-slate-700 hover:bg-slate-50' }}"
                    >
                        Monthly
                    </button>
                </div>
            </div>

            {{-- Start Date + Week selector (monthly) --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-3">
                    <label class="text-neutral-700 font-jost text-lg">Start Date:</label>
                    <input
                        type="date"
                        wire:model="startDate"
                        class="px-3 py-2 rounded-xl border border-customRed-100/60 focus:outline-none focus:ring-2 focus:ring-customRed-100 bg-white"
                    />
                </div>

                @if ($planType === 'monthly')
                    <div class="flex items-center gap-2">
                        <span class="text-neutral-700 font-jost text-lg">Week:</span>
                        <div class="inline-flex rounded-full border border-slate-200 bg-white overflow-hidden">
                            @for ($w = 1; $w <= 4; $w++)
                                <button
                                    type="button"
                                    wire:click="$set('currentWeek', {{ $w }})"
                                    class="px-4 py-2 text-sm font-medium font-jost
                                           {{ $currentWeek === $w
                                                ? 'bg-slate-900 text-white'
                                                : 'text-slate-700 hover:bg-slate-50' }}"
                                >
                                    Week {{ $w }}
                                </button>
                            @endfor
                        </div>
                    </div>
                @endif
            </div>

            {{-- Meal preferences --}}
            <div class="bg-customRed-100/10 mt-4 px-4 pt-4 pb-6 md:pt-4 md:pb-6 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-oswald font-medium text-lg text-slate-900 flex items-center gap-2">
                        Meal Preference
                        <span class="text-customRed-100">*</span>
                    </h4>
                    <span class="bg-customRed-100 text-white font-jost px-2 py-1 rounded-full text-xs">
                        Choose one or more
                    </span>
                </div>

                <p class="font-jost text-sm text-gray-600 mb-4">
                    Select which meal slots you want to plan. You can pick Breakfast, Lunch, Tiffin, and/or Dinner; then choose meals per day.
                </p>

                <div class="font-jost text-gray-700 gap-3 md:gap-4 flex flex-wrap">
                    {{-- Breakfast --}}
                    <label
                        class="rounded-lg px-5 py-1.5 transition cursor-pointer flex items-center gap-2 border
                        {{ $mealPrefs['breakfast']
                            ? 'bg-customRed-100 text-white border-customRed-100'
                            : 'border-customRed-100 hover:bg-white/60' }}"
                    >
                        <input type="checkbox" class="hidden" wire:model.live="mealPrefs.breakfast" value="1" />
                        <span class="text-base">Breakfast</span>
                    </label>

                    {{-- Lunch --}}
                    <label
                        class="rounded-lg px-5 py-1.5 transition cursor-pointer flex items-center gap-2 border
                        {{ $mealPrefs['lunch']
                            ? 'bg-customRed-100 text-white border-customRed-100'
                            : 'border-customRed-100 hover:bg-white/60' }}"
                    >
                        <input type="checkbox" class="hidden" wire:model.live="mealPrefs.lunch" value="1" />
                        <span class="text-base">Lunch</span>
                    </label>

                    {{-- Tiffin --}}
                    <label
                        class="rounded-lg px-5 py-1.5 transition cursor-pointer flex items-center gap-2 border
                        {{ $mealPrefs['tiffin']
                            ? 'bg-customRed-100 text-white border-customRed-100'
                            : 'border-customRed-100 hover:bg-white/60' }}"
                    >
                        <input type="checkbox" class="hidden" wire:model.live="mealPrefs.tiffin" value="1" />
                        <span class="text-base">Tiffin</span>
                    </label>

                    {{-- Dinner --}}
                    <label
                        class="rounded-lg px-5 py-1.5 transition cursor-pointer flex items-center gap-2 border
                        {{ $mealPrefs['dinner']
                            ? 'bg-customRed-100 text-white border-customRed-100'
                            : 'border-customRed-100 hover:bg-white/60' }}"
                    >
                        <input type="checkbox" class="hidden" wire:model.live="mealPrefs.dinner" value="1" />
                        <span class="text-base">Dinner</span>
                    </label>
                </div>
            </div>

            {{-- Main Grid --}}
            <div class="grid grid-cols-12 gap-6 mt-6">
                {{-- Left: days & slots --}}
                <div class="col-span-12 lg:col-span-8">
                    <div class="rounded-2xl overflow-hidden bg-customRed-100/10 shadow">
                        <div class="flex items-center justify-between px-5 py-4">
                            <p class="font-oswald text-lg">Select your meals</p>

                            @if ($planType === 'monthly')
                                <button
                                    type="button"
                                    wire:click="copyWeekOneToAll"
                                    class="text-xs md:text-sm inline-flex items-center gap-1 px-3 py-1.5 rounded-full border border-slate-300 text-slate-700 hover:bg-white bg-slate-50 cursor-pointer font-medium"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-copy-icon lucide-copy"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg> Copy Week 1 to Weeks 2–4
                                </button>
                            @endif
                        </div>

                        <div class="divide-y divide-slate-200">
                            @php
                                $selectedSlots = $this->selectedSlots;
                                $slotLabel = [
                                    'breakfast' => 'Breakfast',
                                    'lunch'     => 'Lunch',
                                    'tiffin'    => 'Tiffin',
                                    'dinner'    => 'Dinner',
                                ];
                            @endphp

                            @if (empty($selectedSlots))
                                <div class="px-5 py-6 text-slate-500">
                                    <div class="rounded-lg border border-dashed border-slate-300 bg-white p-6 text-center">
                                        <p class="font-medium">No meal preferences selected</p>
                                        <p class="text-sm mt-1">
                                            Turn on Breakfast, Lunch, Tiffin, or Dinner above to start adding dishes.
                                        </p>
                                    </div>
                                </div>
                            @else
                                @foreach ($this->visibleDays as $weekDay)
                                    @php
                                        $dayIndex = $weekDay['index'];
                                        $day      = $weekDay['day'];

                                        $colsClass =
                                            count($selectedSlots) === 4 ? 'lg:grid-cols-4 md:grid-cols-2 grid-cols-1' :
                                            (count($selectedSlots) === 3 ? 'lg:grid-cols-3 md:grid-cols-2 grid-cols-1' :
                                            (count($selectedSlots) === 2 ? 'md:grid-cols-2 grid-cols-1' : 'grid-cols-1'));
                                    @endphp

                                    <div class="px-5 py-4">
                                        {{-- Date on top --}}
                                        <div class="mb-3">
                                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                                Day
                                            </p>
                                            <p class="text-sm md:text-base font-semibold text-slate-800">
                                                {{ $day['name'] }}
                                            </p>
                                        </div>

                                        {{-- Slots grid --}}
                                        <div class="grid {{ $colsClass }} gap-3">
                                            @foreach ($selectedSlots as $slot)
                                                @php
                                                    $items = $day['slots'][$slot]['items'] ?? [];
                                                @endphp

                                                <div
                                                    class="w-full border border-customRed-100/60 rounded-lg p-3 bg-white cursor-pointer group"
                                                    wire:click="openSlot({{ $dayIndex }}, '{{ $slot }}')"
                                                >
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span class="text-sm font-medium text-slate-700">
                                                            {{ $slotLabel[$slot] ?? $slot }}
                                                        </span>
                                                        <span class="text-xs text-slate-500">
                                                            {{ count($items) }} selected
                                                        </span>
                                                    </div>

                                                    <div class="space-y-2">
                                                        @forelse ($items as $itemIndex => $item)
                                                            @php
                                                                $dish = $dishes->firstWhere('id', $item['dish_id']);
                                                                $qty  = max(1, (int) ($item['qty'] ?? 1));
                                                                $img  = $dish && $dish->thumbnail
                                                                    ? \Illuminate\Support\Facades\Storage::url($dish->thumbnail)
                                                                    : null;

                                                                $unit  = $dish ? $this->calculateItemUnitPrice($dish, $item) : 0;
                                                                $price = $unit * $qty;

                                                                $metaParts = [];

                                                                if ($dish) {
                                                                    // VARIANT
                                                                    $variantKey = $item['variant_key'] ?? null;
                                                                    if ($variantKey !== null && $variantKey !== '') {
                                                                        $vLabel = null;
                                                                        $gName  = 'Variant';

                                                                        $varData = $dish->variations ?? [];
                                                                        $variantOptionsLookup = [];

                                                                        if (is_array($varData)) {
                                                                            if (isset($varData['variants']) && is_array($varData['variants'])) {
                                                                                $variantOptionsLookup = $varData['variants'];
                                                                            } elseif (isset($varData['options']) && is_array($varData['options'])) {
                                                                                $variantOptionsLookup = $varData['options'];
                                                                            } else {
                                                                                foreach ($varData as $vGroup) {
                                                                                    if (!empty($vGroup['options']) && is_array($vGroup['options'])) {
                                                                                        $variantOptionsLookup = array_merge($variantOptionsLookup, $vGroup['options']);
                                                                                        if (isset($vGroup['name'])) {
                                                                                            $gName = $vGroup['name'];
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }

                                                                        foreach ($variantOptionsLookup as $opt) {
                                                                            $okey = $opt['key'] ?? ($opt['id'] ?? null);
                                                                            if ((string) $okey === (string) $variantKey) {
                                                                                $vLabel = $opt['label'] ?? ($opt['name'] ?? $variantKey);
                                                                                break;
                                                                            }
                                                                        }

                                                                        if ($vLabel) {
                                                                            $metaParts[] = $gName . ': ' . $vLabel;
                                                                        }
                                                                    }

                                                                    // CRUST
                                                                    $crustKey = $item['crust_key'] ?? null;
                                                                    if ($crustKey) {
                                                                        $c = $dish->crusts->firstWhere('id', $crustKey);
                                                                        if ($c) {
                                                                            $metaParts[] = 'Crust: ' . $c->name;
                                                                        }
                                                                    }

                                                                    // BUN
                                                                    $bunKey = $item['bun_key'] ?? null;
                                                                    if ($bunKey) {
                                                                        $b = $dish->buns->firstWhere('id', $bunKey);
                                                                        if ($b) {
                                                                            $metaParts[] = 'Bun: ' . $b->name;
                                                                        }
                                                                    }

                                                                    // ADDONS
                                                                    $addonKeys = $item['addon_keys'] ?? [];
                                                                    $addonNames = [];
                                                                    if (!empty($addonKeys)) {
                                                                        $addonKeysInt = array_map('intval', (array) $addonKeys);
                                                                        foreach ($dish->addOns as $a) {
                                                                            if (in_array((int) $a->id, $addonKeysInt, true)) {
                                                                                $addonNames[] = $a->name;
                                                                            }
                                                                        }
                                                                    }
                                                                    if (!empty($addonNames)) {
                                                                        $metaParts[] = 'Add-ons: ' . implode(', ', $addonNames);
                                                                    }
                                                                }
                                                            @endphp

                                                            <div class="flex items-center justify-between bg-white border border-slate-200 rounded-lg p-2 overflow-hidden">
                                                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                                                    <div class="w-10 h-10 rounded-md overflow-hidden bg-slate-100 shrink-0">
                                                                        @if ($img)
                                                                            <img src="{{ $img }}" class="w-full h-full object-cover" alt="">
                                                                        @endif
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <div class="text-sm font-medium truncate flex items-center gap-1">
                                                                            {{ $dish->title ?? 'Dish' }}
                                                                            @if ($qty > 1)
                                                                                <span class="inline-flex items-center justify-center text-[10px] px-2 h-5 rounded-full bg-slate-800 text-white">
                                                                                    x{{ $qty }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="text-[11px] text-slate-500">
                                                                            ৳{{ number_format($price, 0) }}
                                                                        </div>

                                                                        @if (!empty($metaParts))
                                                                            <div class="mt-0.5 text-[10px] text-slate-500 line-clamp-2">
                                                                                {{ implode(' • ', $metaParts) }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="flex items-center gap-2 shrink-0">
                                                                    <button
                                                                        type="button"
                                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-600 text-white text-xs hover:bg-red-700 transition"
                                                                        wire:click.stop="removeItem({{ $dayIndex }}, '{{ $slot }}', {{ $itemIndex }})"
                                                                    >
                                                                        🗑
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="text-sm text-slate-500">
                                                                No {{ $slotLabel[$slot] ?? $slot }} items yet.
                                                            </div>
                                                        @endforelse
                                                    </div>

                                                    <div class="mt-2 text-[11px] text-slate-400 group-hover:text-slate-600">
                                                        Click to select meals
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: summary --}}
                <aside class="col-span-12 lg:col-span-4">
                    <div class="rounded-2xl p-6 bg-customRed-100/10 shadow space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500">Plan</p>
                                <h3 class="font-oswald text-xl">
                                    {{ $planType === 'weekly' ? 'Weekly Plan' : 'Monthly Plan' }}
                                </h3>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-slate-500">Price</p>
                                @if ($planType === 'weekly')
                                    <p class="font-semibold">
                                        <span class="font-oswald">৳</span> {{ number_format($this->weeklyTotal, 0) }} / week
                                    </p>
                                @else
                                    <p class="font-semibold">
                                        <span class="font-oswald">৳</span> {{ number_format($this->monthlyTotal, 0) }} / month
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="border-t border-slate-200 pt-4">
                            <p class="text-sm text-slate-500 mb-2">Selected meals</p>
                            <ul class="text-sm space-y-2 pb-2 max-h-64 overflow-y-auto">
                                @php
                                    $slots = $selectedSlots;
                                    $slotLabel = [
                                        'breakfast' => 'Breakfast',
                                        'lunch'     => 'Lunch',
                                        'tiffin'    => 'Tiffin',
                                        'dinner'    => 'Dinner',
                                    ];
                                    $hasAny = false;
                                @endphp

                                @foreach ($days as $dayIndex => $day)
                                    @foreach ($slots as $slot)
                                        @foreach ($day['slots'][$slot]['items'] ?? [] as $item)
                                            @php
                                                $dish  = $dishes->firstWhere('id', $item['dish_id']);
                                                $qty   = max(1, (int) ($item['qty'] ?? 1));
                                                $unit  = $dish ? $this->calculateItemUnitPrice($dish, $item) : 0;
                                                $price = $unit * $qty;
                                                $hasAny = true;

                                                $metaPartsSummary = [];

                                                if ($dish) {
                                                    // VARIANT
                                                    $variantKey = $item['variant_key'] ?? null;
                                                    if ($variantKey !== null && $variantKey !== '') {
                                                        $vLabel = null;
                                                        $gName  = 'Variant';

                                                        $varData = $dish->variations ?? [];
                                                        $variantOptionsLookup = [];

                                                        if (is_array($varData)) {
                                                            if (isset($varData['variants']) && is_array($varData['variants'])) {
                                                                $variantOptionsLookup = $varData['variants'];
                                                            } elseif (isset($varData['options']) && is_array($varData['options'])) {
                                                                $variantOptionsLookup = $varData['options'];
                                                            } else {
                                                                foreach ($varData as $vGroup) {
                                                                    if (!empty($vGroup['options']) && is_array($vGroup['options'])) {
                                                                        $variantOptionsLookup = array_merge($variantOptionsLookup, $vGroup['options']);
                                                                        if (isset($vGroup['name'])) {
                                                                            $gName = $vGroup['name'];
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }

                                                        foreach ($variantOptionsLookup as $opt) {
                                                            $okey = $opt['key'] ?? ($opt['id'] ?? null);
                                                            if ((string) $okey === (string) $variantKey) {
                                                                $vLabel = $opt['label'] ?? ($opt['name'] ?? $variantKey);
                                                                break;
                                                            }
                                                        }

                                                        if ($vLabel) {
                                                            $metaPartsSummary[] = $gName . ': ' . $vLabel;
                                                        }
                                                    }

                                                    // CRUST
                                                    $crustKey = $item['crust_key'] ?? null;
                                                    if ($crustKey) {
                                                        $c = $dish->crusts->firstWhere('id', $crustKey);
                                                        if ($c) {
                                                            $metaPartsSummary[] = 'Crust: ' . $c->name;
                                                        }
                                                    }

                                                    // BUN
                                                    $bunKey = $item['bun_key'] ?? null;
                                                    if ($bunKey) {
                                                        $b = $dish->buns->firstWhere('id', $bunKey);
                                                        if ($b) {
                                                            $metaPartsSummary[] = 'Bun: ' . $b->name;
                                                        }
                                                    }

                                                    // ADDONS
                                                    $addonKeys = $item['addon_keys'] ?? [];
                                                    $addonNames = [];
                                                    if (!empty($addonKeys)) {
                                                        $addonKeysInt = array_map('intval', (array) $addonKeys);
                                                        foreach ($dish->addOns as $a) {
                                                            if (in_array((int) $a->id, $addonKeysInt, true)) {
                                                                $addonNames[] = $a->name;
                                                            }
                                                        }
                                                    }
                                                    if (!empty($addonNames)) {
                                                        $metaPartsSummary[] = 'Add-ons: ' . implode(', ', $addonNames);
                                                    }
                                                }
                                            @endphp
                                            <li class="flex flex-col gap-0.5">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-slate-700">
                                                        {{ $day['name'] }} ({{ $slotLabel[$slot] ?? $slot }})
                                                    </span>
                                                    —
                                                    <span class="font-medium">
                                                        {{ $dish->title ?? 'Dish' }} x{{ $qty }}
                                                    </span>
                                                    <span class="ml-auto">৳{{ number_format($price, 0) }}</span>
                                                </div>
                                                @if (!empty($metaPartsSummary))
                                                    <div class="text-[11px] text-slate-500">
                                                        {{ implode(' • ', $metaPartsSummary) }}
                                                    </div>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endforeach
                                @endforeach

                                @unless($hasAny)
                                    <li class="text-slate-500 text-sm">No meals selected yet.</li>
                                @endunless
                            </ul>
                        </div>

                        <div class="border-t border-slate-200 pt-4">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-lg">Total</p>
                                <p class="font-bold text-lg">
                                    @if ($planType === 'weekly')
                                        <span class="font-oswald">৳</span> {{ number_format($this->weeklyTotal, 0) }}
                                    @else
                                        <span class="font-oswald">৳</span> {{ number_format($this->monthlyTotal, 0) }}
                                    @endif
                                </p>
                            </div>

                            @if ($planType === 'monthly')
                                <p class="text-xs text-slate-500 mt-1">
                                    (Approx. 4 weeks × <span class="font-oswald">৳</span>{{ number_format($this->weeklyTotal, 0) }} / week)
                                </p>
                            @endif
                        </div>

                        <button
                            type="button"
                            class="group relative inline-flex w-full items-center justify-center rounded-md px-8 md:px-10 py-3 overflow-hidden bg-customRed-100 font-oswald text-white no-underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/60 cursor-pointer"
                        >
                            <span class="pointer-events-none absolute inset-0 bg-slate-900 transform origin-center scale-0 rotate-45 transition-transform duration-500 ease-out group-hover:scale-1125"></span>
                            <span class="relative z-10 transition-colors duration-300 group-hover:text-white">
                                CONFIRM BOOKING
                            </span>
                        </button>

                        <button
                            type="button"
                            wire:click="resetPlan"
                            class="relative w-full rounded-md px-8 md:px-10 py-3 overflow-hidden cursor-pointer bg-slate-900 font-oswald text-white group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/60"
                        >
                            <span class="absolute inset-0 bg-white transform origin-center scale-0 rotate-45 transition-transform duration-500 ease-out group-hover:scale-1150"></span>
                            <span class="relative z-10 transition-colors duration-300 group-hover:text-black">
                                RESET PLAN
                            </span>
                        </button>
                    </div>
                </aside>
            </div>

            {{-- Slot Modal --}}
            <div
                x-data="{ open: @entangle('showModal') }"
                x-cloak
                x-show="open"
                class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 px-3"
            >
                <div
                    class="bg-white w-full max-w-5xl rounded-2xl shadow-xl overflow-hidden flex flex-col max-h-[90vh]"
                >
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                        <h3 class="font-oswald text-lg md:text-xl">
                            @php
                                $slotLabelFull = [
                                    'breakfast' => 'Breakfast',
                                    'lunch'     => 'Lunch',
                                    'tiffin'    => 'Tiffin',
                                    'dinner'    => 'Dinner',
                                ];
                            @endphp

                            @if (!is_null($tempSelection['day']) && !is_null($tempSelection['slot']))
                                {{ $days[$tempSelection['day']]['name'] }}
                                ({{ $slotLabelFull[$tempSelection['slot']] ?? $tempSelection['slot'] }})
                                — Select dishes
                            @else
                                Select dishes
                            @endif
                        </h3>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-slate-300 text-slate-700 hover:bg-slate-100"
                            @click="$wire.closeModal()"
                        >
                            ✕
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 md:p-5 overflow-y-auto flex-1">
                        <p class="text-sm text-slate-500 mb-3">
                            Tap a dish to configure variant, crust, bun, add-ons and quantity for this slot.
                        </p>

                        {{-- Category filter --}}
                        @php
                            $categories = $this->dishCategories;
                        @endphp

                        @if ($categories->isNotEmpty())
                            <div class="flex items-center gap-2 mb-4 overflow-x-auto">
                                <button
                                    type="button"
                                    wire:click="setActiveDishCategory(null)"
                                    class="whitespace-nowrap px-3 py-1.5 rounded-full border text-xs md:text-sm
                                        {{ is_null($activeDishCategoryId)
                                            ? 'bg-customRed-100 text-white border-customRed-100'
                                            : 'border-slate-300 text-slate-700 hover:bg-slate-100' }}"
                                >
                                    All
                                </button>

                                @foreach ($categories as $category)
                                    <button
                                        type="button"
                                        wire:click="setActiveDishCategory({{ $category->id }})"
                                        class="whitespace-nowrap px-3 py-1.5 rounded-full border text-xs md:text-sm
                                            {{ $activeDishCategoryId === $category->id
                                                ? 'bg-customRed-100 text-white border-customRed-100'
                                                : 'border-slate-300 text-slate-700 hover:bg-slate-100' }}"

                                    >
                                        {{ $category->name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        {{-- Dish grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach ($dishes as $dish)
                                @php
                                    $dishCategoryId = optional($dish->category)->id;
                                    if (!is_null($activeDishCategoryId) && $dishCategoryId !== $activeDishCategoryId) {
                                        continue;
                                    }

                                    $dishId = $dish->id;

                                    $temp = $tempSelection['dishes'][$dishId] ?? [
                                        'selected'    => false,
                                        'qty'         => 1,
                                        'variant_key' => null,
                                        'crust_key'   => null,
                                        'bun_key'     => null,
                                        'addon_keys'  => [],
                                    ];

                                    $img = $dish->thumbnail
                                        ? \Illuminate\Support\Facades\Storage::url($dish->thumbnail)
                                        : null;

                                    $rawVars              = $dish->variations ?? [];
                                    $variantOptionsLookup = [];

                                    if (is_array($rawVars)) {
                                        if (isset($rawVars['variants']) && is_array($rawVars['variants'])) {
                                            $variantOptionsLookup = $rawVars['variants'];
                                        } elseif (isset($rawVars['options']) && is_array($rawVars['options'])) {
                                            $variantOptionsLookup = $rawVars['options'];
                                        } elseif (isset($rawVars[0]['options']) && is_array($rawVars[0]['options'])) {
                                            $variantOptionsLookup = $rawVars[0]['options'];
                                        } else {
                                            foreach ($rawVars as $g) {
                                                if (!empty($g['options']) && is_array($g['options'])) {
                                                    $variantOptionsLookup = array_merge($variantOptionsLookup, $g['options']);
                                                }
                                            }
                                        }
                                    }

                                    // Build selected meta for card preview
                                    $metaPartsCard = [];

                                    $variantKeyCard = $temp['variant_key'] ?? null;
                                    if ($variantKeyCard !== null && $variantKeyCard !== '') {
                                        $vLabel = null;
                                        $gName  = 'Variant';

                                        foreach ($variantOptionsLookup as $opt) {
                                            $okey = $opt['key'] ?? ($opt['id'] ?? null);
                                            if ((string) $okey === (string) $variantKeyCard) {
                                                $vLabel = $opt['label'] ?? ($opt['name'] ?? $variantKeyCard);
                                                break;
                                            }
                                        }

                                        if ($vLabel) {
                                            // we don't know group name here (single flat group) so fallback
                                            $metaPartsCard[] = $gName . ': ' . $vLabel;
                                        }
                                    }

                                    $crustKeyCard = $temp['crust_key'] ?? null;
                                    if ($crustKeyCard) {
                                        $c = $dish->crusts->firstWhere('id', $crustKeyCard);
                                        if ($c) {
                                            $metaPartsCard[] = 'Crust: ' . $c->name;
                                        }
                                    }

                                    $bunKeyCard = $temp['bun_key'] ?? null;
                                    if ($bunKeyCard) {
                                        $b = $dish->buns->firstWhere('id', $bunKeyCard);
                                        if ($b) {
                                            $metaPartsCard[] = 'Bun: ' . $b->name;
                                        }
                                    }

                                    $addonKeysCard = $temp['addon_keys'] ?? [];
                                    $addonNamesCard = [];
                                    if (!empty($addonKeysCard)) {
                                        $addonKeysInt = array_map('intval', (array) $addonKeysCard);
                                        foreach ($dish->addOns as $a) {
                                            if (in_array((int) $a->id, $addonKeysInt, true)) {
                                                $addonNamesCard[] = $a->name;
                                            }
                                        }
                                    }
                                    if (!empty($addonNamesCard)) {
                                        $metaPartsCard[] = 'Add-ons: ' . implode(', ', $addonNamesCard);
                                    }

                                    $unitForPreview = $this->calculateItemUnitPrice($dish, $temp);
                                @endphp

                                <div
                                    class="relative bg-white border rounded-xl shadow-sm hover:shadow-md transition text-left overflow-hidden cursor-pointer"
                                    @class([
                                        'border-customRed-100 ring-2 ring-customRed-100' => !empty($temp['selected']),
                                    ])
                                    wire:click="openDishConfig({{ $dishId }})"
                                >
                                    {{-- Remove button top-left when selected --}}
                                    @if (!empty($temp['selected']))
                                        <button
                                            type="button"
                                            class="absolute top-1 left-1 z-10 inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/90 text-[10px] text-slate-700 shadow hover:bg-white"
                                            wire:click.stop="deselectDish({{ $dishId }})"
                                            title="Remove from this slot"
                                        >
                                            ✕
                                        </button>
                                    @endif

                                    <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                                        @if ($img)
                                            <img src="{{ $img }}" alt="{{ $dish->title }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>

                                    <div class="p-2 space-y-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <h4 class="text-sm font-medium leading-tight truncate">
                                                {{ $dish->title }}
                                            </h4>

                                            @if (!empty($temp['selected']))
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-customRed-100 text-white">
                                                        Added
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <p class="text-xs text-slate-500 truncate">
                                            {{ $dish->short_description }}
                                        </p>

                                        @if (!empty($metaPartsCard))
                                            <div class="text-[10px] text-slate-500 line-clamp-2">
                                                {{ implode(' • ', $metaPartsCard) }}
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-between mt-1" wire:click.stop>
                                            <span class="inline-flex items-center text-[11px] px-2 py-0.5 rounded-full border border-slate-300 text-slate-700">
                                                ৳{{ number_format($unitForPreview, 0) }}
                                            </span>

                                            <div class="flex items-center gap-1">
                                                <div class="inline-flex items-center border border-slate-300 rounded-md overflow-hidden">
                                                    <button
                                                        type="button"
                                                        class="px-2 h-7 text-xs hover:bg-slate-100"
                                                        wire:click="decrementDishQty({{ $dishId }})"
                                                    >
                                                        -
                                                    </button>
                                                    <input
                                                        type="number"
                                                        min="1"
                                                        wire:model.live="tempSelection.dishes.{{ $dishId }}.qty"
                                                        class="w-12 h-7 text-xs text-center border-0 focus:ring-0 focus:outline-none text-slate-800 bg-white"
                                                    >
                                                    <button
                                                        type="button"
                                                        class="px-2 h-7 text-xs hover:bg-slate-100"
                                                        wire:click="incrementDishQty({{ $dishId }})"
                                                    >
                                                        +
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-1 text-[11px] text-customRed-100 font-medium">
                                            Tap to customize
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($dishes->isEmpty())
                            <p class="text-sm text-slate-500 mt-4">
                                No dishes available. Please add dishes first.
                            </p>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="px-5 pb-5 pt-3 flex items-center justify-between gap-3 border-t border-slate-200">
                        @php
                            $countSelected = 0;
                            foreach (($tempSelection['dishes'] ?? []) as $d) {
                                if (!empty($d['selected'])) {
                                    $countSelected++;
                                }
                            }
                        @endphp

                        <span class="text-sm text-slate-500">
                            {{ $countSelected }} selected
                        </span>

                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 text-sm"
                                @click="$wire.closeModal()"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                wire:click="confirmSlotSelection"
                                class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-customRed-100 text-white text-sm disabled:opacity-60 disabled:cursor-not-allowed"
                                @if ($countSelected === 0) disabled @endif
                            >
                                Add Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dish Customization Modal (teleported) --}}
            <div wire:teleport="body">
                <div
                    x-data="{ open: @entangle('dishConfigOpen').live }"
                    x-cloak
                    x-show="open"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                    role="dialog"
                    aria-modal="true"
                >
                    {{-- Backdrop --}}
                    <div
                        class="absolute inset-0 bg-black/50 backdrop-blur-[1px]"
                        @click="open = false; $wire.closeDishConfig()"
                    ></div>

                    {{-- Panel --}}
                    <div
                        class="relative w-full max-w-3xl rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 overflow-hidden
                               transform-gpu will-change-transform will-change-opacity"
                    >
                        @php
                            $configDish   = $configDishId ? $dishes->firstWhere('id', $configDishId) : null;
                            $configTemp   = $configDishId ? ($tempSelection['dishes'][$configDishId] ?? null) : null;
                        @endphp

                        @if ($configDish && $configTemp)
                            @php
                                $rawVars       = $configDish->variations ?? [];
                                $variantGroups = [];

                                if (is_array($rawVars)) {
                                    if (isset($rawVars['variants']) && is_array($rawVars['variants'])) {
                                        $variantGroups[] = [
                                            'name'    => 'Variant',
                                            'options' => $rawVars['variants'],
                                        ];
                                    } elseif (isset($rawVars['options']) && is_array($rawVars['options'])) {
                                        $variantGroups[] = [
                                            'name'    => 'Variant',
                                            'options' => $rawVars['options'],
                                        ];
                                    } else {
                                        foreach ($rawVars as $g) {
                                            if (!empty($g['options']) && is_array($g['options'])) {
                                                $variantGroups[] = $g;
                                            }
                                        }
                                    }
                                }

                                $crusts = $configDish->crusts ?? collect();
                                $buns   = $configDish->buns   ?? collect();
                                $addons = $configDish->addOns ?? collect();

                                $configImg     = $configDish->thumbnail
                                    ? \Illuminate\Support\Facades\Storage::url($configDish->thumbnail)
                                    : null;

                                $unitTotal     = $this->calculateItemUnitPrice($configDish, $configTemp);
                                $modalQty      = max(1, (int) ($configTemp['qty'] ?? 1));
                                $previewTotal  = $unitTotal * $modalQty;
                            @endphp

                            {{-- Header --}}
                            <div class="p-5 flex gap-4 items-start border-b border-slate-200">
                                <img
                                    src="{{ $configImg ?? 'https://placehold.co/200x150' }}"
                                    alt="{{ $configDish->title }}"
                                    class="w-20 md:w-36 md:h-28 rounded-xl object-cover"
                                />

                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-base md:text-xl font-medium md:font-semibold text-slate-900">
                                                {{ $configDish->title }}
                                            </h3>
                                            <p class="mt-1 text-sm md:text-lg text-slate-600 font-jost leading-relaxed">
                                                {{ $configDish->short_description }}
                                            </p>

                                            <div class="mt-3 flex items-center gap-2 font-oswald">
                                                <span class="text-sm md:text-lg font-bold text-slate-900">
                                                    ৳{{ number_format($unitTotal, 2) }} / unit
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Close --}}
                                <button
                                    type="button"
                                    class="absolute right-3 top-3 inline-flex h-9 w-9 cursor-pointer items-center justify-center rounded-full hover:bg-slate-100 text-slate-700"
                                    @click="open = false; $wire.closeDishConfig()"
                                    aria-label="Close"
                                >
                                    ✕
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="px-5 py-3 space-y-3 overflow-y-auto max-h-[50vh]">
                                {{-- VARIATIONS (REQUIRED if exists) --}}
                                @if (!empty($variantGroups))
                                    @php
                                        $variantError = $errors->has('config.variant');
                                    @endphp

                                    <div class="space-y-3">
                                        @foreach ($variantGroups as $gIndex => $group)
                                            @php
                                                $gName   = $group['name'] ?? 'Variation';
                                                $options = $group['options'] ?? [];
                                            @endphp

                                            @if (!empty($options))
                                                <div
                                                    class="bg-customRed-100/10 shadow p-4 md:p-5 rounded-lg
                                                           {{ $variantError ? 'border border-red-500' : '' }}"
                                                >
                                                    <div class="flex items-center justify-between mb-4">
                                                        <div>
                                                            <h4 class="font-oswald font-medium text-lg">{{ $gName }}</h4>
                                                            <p class="text-xs opacity-60">Please select one (required)</p>
                                                        </div>
                                                        <p class="bg-customRed-100 text-white font-jost px-3 py-1 rounded-full text-xs">
                                                            Required
                                                        </p>
                                                    </div>

                                                    <div class="font-jost text-gray-700 space-y-2 pe-2">
                                                        @foreach ($options as $oIndex => $opt)
                                                            @php
                                                                $label = $opt['label'] ?? ($opt['name'] ?? 'Option');
                                                                $price = (float) ($opt['price'] ?? 0);
                                                                $optKey = $opt['key'] ?? ($opt['id'] ?? $oIndex);
                                                            @endphp

                                                            <label class="flex items-center justify-between gap-3">
                                                                <span class="flex items-center gap-3">
                                                                    <input
                                                                        type="radio"
                                                                        name="variation_{{ $gIndex }}"
                                                                        value="{{ $optKey }}"
                                                                        wire:model.live="tempSelection.dishes.{{ $configDishId }}.variant_key"
                                                                        class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500"
                                                                    />
                                                                    <span>{{ $label }}</span>
                                                                </span>

                                                                @if ($price > 0)
                                                                    <span class="text-sm">+ {{ number_format($price, 2) }} ৳</span>
                                                                @endif
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Crust select (REQUIRED if exists) --}}
                                @if ($crusts->count())
                                    @php
                                        $crustError = $errors->has('config.crust');
                                    @endphp

                                    <div
                                        class="bg-customRed-100/15 shadow p-4 md:p-5 rounded-lg
                                               {{ $crustError ? 'border border-red-500' : '' }}"
                                    >
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <h4 class="font-oswald font-medium text-lg">Crust</h4>
                                                <p class="text-xs opacity-60">Please select one (required)</p>
                                            </div>
                                            <p class="bg-customRed-100 text-white font-jost px-3 py-1 rounded-full text-xs">Required</p>
                                        </div>

                                        <div class="font-jost text-gray-700 space-y-2 pe-2">
                                            @foreach ($crusts as $c)
                                                <label class="flex items-center justify-between gap-3">
                                                    <span class="flex items-center gap-3">
                                                        <input
                                                            type="radio"
                                                            name="crust"
                                                            value="{{ $c->id }}"
                                                            wire:model.live="tempSelection.dishes.{{ $configDishId }}.crust_key"
                                                            class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500"
                                                        />
                                                        <span>{{ $c->name }}</span>
                                                    </span>
                                                    <span class="text-sm">+ {{ number_format($c->price ?? 0, 2) }} ৳</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Bun select (REQUIRED if exists) --}}
                                @if ($buns->count())
                                    @php
                                        $bunError = $errors->has('config.bun');
                                    @endphp

                                    <div
                                        class="bg-customRed-100/15 shadow p-4 md:p-5 rounded-lg
                                               {{ $bunError ? 'border border-red-500' : '' }}"
                                    >
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <h4 class="font-oswald font-medium text-lg">Bun</h4>
                                                <p class="text-xs opacity-60">Please select one (required)</p>
                                            </div>
                                            <p class="bg-customRed-100 text-white font-jost px-3 py-1 rounded-full text-xs">
                                                Required
                                            </p>
                                        </div>

                                        <div class="font-jost text-gray-700 space-y-2 pe-2">
                                            @foreach ($buns as $b)
                                                <label class="flex items-center justify-between gap-3">
                                                    <span class="flex items-center gap-3">
                                                        <input
                                                            type="radio"
                                                            name="bun"
                                                            value="{{ $b->id }}"
                                                            wire:model.live="tempSelection.dishes.{{ $configDishId }}.bun_key"
                                                            class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500"
                                                        />
                                                        <span>{{ $b->name }}</span>
                                                    </span>
                                                    <span class="text-sm">+ {{ number_format($b->price ?? 0, 2) }} ৳</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Add-ons (optional) --}}
                                @if ($addons->count())
                                    <div class="bg-customRed-100/15 p-4 md:p-5 rounded-md shadow">
                                        <div class="flex items-center justify-between mb-4">
                                            <h4 class="font-oswald font-medium text-lg">Add Ons</h4>
                                            <p class="bg-white text-gray-500 font-jost px-3 py-1 rounded-full text-xs">Optional</p>
                                        </div>

                                        <div class="font-jost text-gray-700 space-y-3 pe-2">
                                            @foreach ($addons as $a)
                                                @php
                                                    $inputId     = 'addon_' . $a->id;
                                                    $selectedIds = $configTemp['addon_keys'] ?? [];
                                                    $selectedIds = array_map('intval', $selectedIds);
                                                    $selected    = in_array((int) $a->id, $selectedIds, true);
                                                @endphp

                                                <label
                                                    for="{{ $inputId }}"
                                                    class="flex items-center justify-between gap-3 cursor-pointer"
                                                >
                                                    <span class="flex items-center gap-3">
                                                        <input
                                                            id="{{ $inputId }}"
                                                            value="{{ $a->id }}"
                                                            wire:model.live="tempSelection.dishes.{{ $configDishId }}.addon_keys"
                                                            type="checkbox"
                                                            class="h-4 w-4 rounded border-slate-300 text-red-500 focus:ring-red-500"
                                                        />
                                                        <span>{{ $a->name }}</span>
                                                    </span>

                                                    <span class="flex items-center gap-3">
                                                        <span class="text-sm">
                                                            + {{ number_format($a->price ?? 0, 2) }} ৳
                                                        </span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Total + Qty --}}
                            <div class="px-5 pt-3 pb-2">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-slate-900">Total</span>
                                    <span class="text-red-600 font-semibold">
                                        {{ number_format($previewTotal, 2) }}
                                        <span class="!font-bold text-md font-oswald">&#2547;</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="p-5 border-t border-slate-200 flex items-center gap-4">
                                <div class="flex items-center gap-3">
                                    <button
                                        type="button"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100 text-slate-700 cursor-pointer"
                                        wire:click.prevent="decrementDishQty({{ $configDishId }})"
                                    >
                                        –
                                    </button>
                                    <span class="w-8 text-center font-medium select-none">{{ $modalQty }}</span>
                                    <button
                                        type="button"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100 text-slate-700 cursor-pointer"
                                        wire:click.prevent="incrementDishQty({{ $configDishId }})"
                                    >
                                        +
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    wire:click="applyDishConfig({{ $configDishId }})"
                                    class="inline-flex justify-center items-center gap-2 flex-1 h-12 rounded-xl bg-customRed-100 text-white font-medium shadow-sm hover:bg-customRed-100/90 active:scale-[.99] transition cursor-pointer"
                                >
                                    Save & Continue
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
