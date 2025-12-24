<div x-data="{ open: @entangle('open').live }" @keydown.escape.window="open = false"
    x-effect="document.body.classList.toggle('overflow-hidden', open)">

    <div wire:teleport="body">
        <div x-cloak x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center p-4" role="dialog"
            aria-modal="true">

            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/50 backdrop-blur-[1px]"
                style="animation: backdrop-fade-in var(--backdrop-in-dur) var(--modal-ease-out) both" @click="open=false"
                x-transition:leave="!none" x-transition:leave.start="!none" x-transition:leave.end="!none"
                x-on:leave="($el.style.animation = `backdrop-fade-out var(--backdrop-out-dur) var(--modal-ease-in) both`)">
            </div>

            <!-- Panel -->
            <div class="relative w-full max-w-3xl rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 overflow-hidden
               transform-gpu will-change-transform will-change-opacity"
                style="animation: modal-smooth-in var(--modal-in-dur) var(--modal-spring) both"
                x-transition:leave="!none" x-transition:leave.start="!none" x-transition:leave.end="!none"
                x-on:leave="($el.style.animation = `modal-smooth-out var(--modal-out-dur) var(--modal-ease-in) both`)">
                {{-- Header --}}
                <div class="p-5 flex gap-4 items-start border-b border-slate-200">
                    <img src="{{ asset($dish?->thumbnail ?? 'https://placehold.co/200x150') }}"
                        alt="{{ $dish?->title }}" class="w-20 md:w-36  md:h-28 rounded-xl object-cover" />

                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="flex items-center gap-3">
                                    <h3 class="text-base md:text-xl font-medium md:font-semibold text-slate-900">
                                        {{ $dish?->title }}</h3>

                                    {{-- Favorites --}}
                                    <button type="button" wire:click="toggleFavorite" wire:loading.attr="disabled"
                                        aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                                        title="{{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}"
                                        class="md:inline-flex items-center justify-center rounded-full p-2 transition cursor-pointer hidden
                                 {{ $isFavorited ? 'bg-red-500/10' : 'bg-slate-200/70 hover:bg-slate-300/70' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="size-3 md:size-5 {{ $isFavorited ? 'text-red-500 fill-red-500' : 'text-slate-700' }}"
                                            viewBox="0 0 24 24" fill="{{ $isFavorited ? 'currentColor' : 'none' }}"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path
                                                d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
                                        </svg>
                                        <span
                                            class="sr-only">{{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}</span>
                                    </button>
                                </div>

                                <p class="mt-1 text-sm md:text-lg text-slate-600 font-jost leading-relaxed">
                                    {{ $dish?->short_description }}
                                </p>

                                {{-- ✅ Base price: main first, then discount --}}
                                <div class="mt-3 flex items-center gap-2 font-oswald">
                                    @if ($this->has_base_discount)
                                        <span class="text-xs md:text-base text-slate-500 line-through">
                                            {{ number_format($this->base_item_original_price, 2) }} ৳
                                        </span>
                                        <span class="text-sm md:text-lg font-bold text-slate-900">
                                            {{ number_format($this->base_item_price, 2) }} ৳
                                        </span>
                                    @else
                                        <span class="text-sm md:text-lg font-bold text-slate-900">
                                            {{ number_format($this->base_item_original_price, 2) }} ৳
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Close -->
                    <button type="button"
                        class="absolute right-3 top-3 inline-flex size-7 md:size-9 cursor-pointer items-center justify-center rounded-full hover:bg-slate-300/70 text-slate-700 bg-slate-200/70 text-xs md:text-lg"
                        @click="open = false" aria-label="Close">✕</button>

                    {{-- Favorites --}}
                    <button type="button" wire:click="toggleFavorite" wire:loading.attr="disabled"
                        aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                        title="{{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}"
                        class="inline-flex absolute right-3 top-12 items-center justify-center rounded-full p-2 transition cursor-pointer md:hidden
                                 {{ $isFavorited ? 'bg-red-500/10' : 'bg-slate-200/70 hover:bg-slate-300/70' }}">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="size-3 md:size-5 {{ $isFavorited ? 'text-red-500 fill-red-500' : 'text-slate-700' }}"
                            viewBox="0 0 24 24" fill="{{ $isFavorited ? 'currentColor' : 'none' }}"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
                        </svg>
                        <span class="sr-only">{{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}</span>
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-5 py-3 space-y-3 overflow-y-auto max-h-[50vh]">

                    {{-- ================= VARIATIONS ================= --}}
                    @if ($dish && !empty($dish->variations))
                        @php $variations = collect($dish->variations); @endphp

                        <div class="space-y-3">
                            @foreach ($variations as $gIndex => $group)
                                @php
                                    $gName = $group['name'] ?? 'Variation';
                                    $options = $group['options'] ?? [];
                                @endphp

                                @if (!empty($options))
                                    <div
                                        class="bg-customRed-100/10 shadow p-4 md:p-5 rounded-lg
                              @error('variation_selection.' . $gIndex) border border-customRed-100 @enderror">
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <h4 class="font-oswald font-medium text-lg">{{ $gName }}</h4>
                                                <p class="text-xs opacity-60">Please select one</p>
                                            </div>
                                            <p
                                                class="bg-customRed-100 text-white font-jost px-3 py-1 rounded-full text-xs">
                                                Required</p>
                                        </div>

                                        <div class="font-jost text-gray-700 space-y-2 pe-2">
                                            @foreach ($options as $oIndex => $opt)
                                                @php
                                                    $label = $opt['label'] ?? ($opt['name'] ?? 'Option');
                                                    $price = (float) ($opt['price'] ?? 0);
                                                @endphp

                                                <label class="flex items-center justify-between gap-3">
                                                    <span class="flex items-center gap-3">
                                                        <input type="radio" name="variation_{{ $gIndex }}"
                                                            value="{{ $oIndex }}"
                                                            wire:model.live="variation_selection.{{ $gIndex }}"
                                                            class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500" />
                                                        <span>{{ $label }}</span>
                                                    </span>

                                                    <span class="text-sm">+ {{ number_format($price, 2) }} ৳</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        @error('variation_selection.' . $gIndex)
                                            <p class="text-xs text-customRed-100 mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    {{-- ================= /VARIATIONS ================= --}}

                    {{-- Crust select --}}
                    @if ($dish && $dish->crusts->count())
                        <div
                            class="bg-customRed-100/15 shadow p-4 md:p-5 rounded-lg @error('crust_id') border border-customRed-100 @enderror">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-oswald font-medium text-lg">Crust</h4>
                                    <p class="text-xs opacity-60">Please select one</p>
                                </div>
                                <p class="bg-customRed-100 text-white font-jost px-3 py-1 rounded-full text-xs">Required
                                </p>
                            </div>

                            @php
                                $crusts = $dish->crusts;
                                $first = $crusts->take(3);
                                $rest = $crusts->skip(3);
                            @endphp

                            <div class="font-jost text-gray-700 space-y-2 pe-2">
                                @foreach ($first as $c)
                                    <label class="flex items-center justify-between gap-3">
                                        <span class="flex items-center gap-3">
                                            <input type="radio" name="crust" value="{{ $c->id }}"
                                                wire:model.live="crust_id"
                                                class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500" />
                                            <span>{{ $c->name }}</span>
                                        </span>
                                        <span class="text-sm">+ {{ number_format($c->price ?? 0, 2) }} ৳</span>
                                    </label>
                                @endforeach

                                @if ($rest->count() > 0)
                                    <div x-data="{ showMore: false }">
                                        <div x-show="showMore" x-collapse>
                                            @foreach ($rest as $c)
                                                <label class="flex items-center justify-between gap-3">
                                                    <span class="flex items-center gap-3">
                                                        <input type="radio" name="crust"
                                                            value="{{ $c->id }}" wire:model.live="crust_id"
                                                            class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500" />
                                                        <span>{{ $c->name }}</span>
                                                    </span>
                                                    <span class="text-sm">+ {{ number_format($c->price ?? 0, 2) }}
                                                        ৳</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        <button type="button" x-on:click="showMore = !showMore"
                                            class="mt-0.5 cursor-pointer text-customRed-100 font-jost font-medium text-sm">
                                            <span x-text="showMore ? 'View Less' : 'View More'"></span>
                                            <span> ({{ $rest->count() }})</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Bun select --}}
                    @if ($dish && $dish->buns->count())
                        <div
                            class="bg-customRed-100/15 shadow p-4 md:p-5 rounded-lg @error('bun_id') border border-customRed-100 @enderror">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-oswald font-medium text-lg">Bun</h4>
                                    <p class="text-xs opacity-60">Please select one</p>
                                </div>
                                <p class="bg-customRed-100 text-white font-jost px-3 py-1 rounded-full text-xs">
                                    Required</p>
                            </div>

                            @php
                                $buns = $dish->buns;
                                $first = $buns->take(3);
                                $rest = $buns->skip(3);
                            @endphp

                            <div class="font-jost text-gray-700 space-y-2 pe-2">
                                @foreach ($first as $b)
                                    <label class="flex items-center justify-between gap-3">
                                        <span class="flex items-center gap-3">
                                            <input type="radio" name="bun" value="{{ $b->id }}"
                                                wire:model.live="bun_id"
                                                class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500" />
                                            <span>{{ $b->name }}</span>
                                        </span>
                                        <span class="text-sm">+ {{ number_format($b->price ?? 0, 2) }} ৳</span>
                                    </label>
                                @endforeach

                                @if ($rest->count() > 0)
                                    <div x-data="{ showMore: false }">
                                        <div x-show="showMore" x-collapse>
                                            @foreach ($rest as $b)
                                                <label class="flex items-center justify-between gap-3">
                                                    <span class="flex items-center gap-3">
                                                        <input type="radio" name="bun"
                                                            value="{{ $b->id }}" wire:model.live="bun_id"
                                                            class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500" />
                                                        <span>{{ $b->name }}</span>
                                                    </span>
                                                    <span class="text-sm">+ {{ number_format($b->price ?? 0, 2) }}
                                                        ৳</span>
                                                </label>
                                            @endforeach
                                        </div>

                                        <button type="button" x-on:click="showMore = !showMore"
                                            class="mt-0.5 cursor-pointer text-customRed-100 font-jost font-medium text-sm">
                                            <span x-text="showMore ? 'View Less' : 'View More'"></span>
                                            <span> ({{ $rest->count() }})</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Add-ons --}}
                    @if ($dish && $dish->addOns->count())
                        <div class="bg-customRed-100/15 p-4 md:p-5 rounded-md shadow">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-oswald font-medium text-lg">Add Ons</h4>
                                <p class="bg-white text-gray-500 font-jost px-3 py-1 rounded-full text-xs">Optional</p>
                            </div>

                            <div class="font-jost text-gray-700 space-y-3 pe-2">
                                @foreach ($dish->addOns as $a)
                                    @php
                                        $inputId = 'addon_' . $a->id;
                                        $selected = in_array($a->id, $addon_ids ?? []);
                                        $addonQty = $addon_qty[$a->id] ?? 1;
                                    @endphp

                                    <label for="{{ $inputId }}"
                                        class="flex items-center justify-between gap-3 cursor-pointer">
                                        {{-- Left: checkbox + name --}}
                                        <span class="flex items-center gap-3">
                                            <input id="{{ $inputId }}" value="{{ $a->id }}"
                                                wire:model.live="addon_ids" type="checkbox"
                                                class="h-4 w-4 rounded border-slate-300 text-red-500 focus:ring-red-500" />
                                            <span>{{ $a->name }}</span>
                                        </span>

                                        {{-- Right: price + qty beside price --}}
                                        <span class="flex items-center gap-3">
                                            <span class="text-sm">
                                                + {{ number_format($a->price ?? 0, 2) }} ৳
                                            </span>

                                            @if ($selected)
                                                <span class="flex items-center gap-1">
                                                    <button type="button"
                                                        class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-slate-300 text-slate-700 text-xs hover:bg-slate-100 cursor-pointer"
                                                        @click.stop.prevent="$wire.decrementAddon({{ $a->id }})">
                                                        –
                                                    </button>

                                                    <span class="w-6 text-center text-sm font-medium select-none">
                                                        {{ $addonQty }}
                                                    </span>

                                                    <button type="button"
                                                        class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-slate-300 text-slate-700 text-xs hover:bg-slate-100 cursor-pointer"
                                                        @click.stop.prevent="$wire.incrementAddon({{ $a->id }})">
                                                        +
                                                    </button>
                                                </span>
                                            @endif
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif



                </div>

                {{-- Total --}}
                <div class="px-5 pt-3 pb-2">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-slate-900">Total</span>
                        <span class="text-red-600 font-semibold">
                            {{ number_format($this->preview_total, 2) }}
                            <span class="!font-bold text-md font-oswald">&#2547;</span>
                        </span>
                    </div>
                    {{-- <p class="text-[11px] opacity-60 mt-1">
            {{ $qty }} × {{ number_format($this->unit_total, 2) }} ৳
          </p> --}}
                </div>

                {{-- Footer --}}
                <div class="p-5 border-t border-slate-200 flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <button type="button"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100 text-slate-700 cursor-pointer"
                            @click.prevent="$wire.decrementQty()">–</button>
                        <span class="w-8 text-center font-medium select-none">{{ $qty }}</span>
                        <button type="button"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100 text-slate-700 cursor-pointer"
                            @click.prevent="$wire.incrementQty()">+</button>
                    </div>

                    <button type="button"
                        class="inline-flex justify-center items-center gap-2 flex-1 h-12 rounded-xl bg-customRed-100 text-white font-medium shadow-sm hover:bg-customRed-100/90 active:scale-[.99] transition"
                        wire:click="addToCart">
                        Add to Cart
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-shopping-cart-icon lucide-shopping-cart">
                            <circle cx="8" cy="21" r="1" />
                            <circle cx="19" cy="21" r="1" />
                            <path
                                d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
