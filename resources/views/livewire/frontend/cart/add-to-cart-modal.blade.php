<div>
    <input type="checkbox" class="modal-toggle" {{ $open ? 'checked' : '' }} />
    <div class="modal ">
        <div class="modal-box max-w-3xl p-0 rounded-2xl overflow-hidden bg-white">

            {{-- Header --}}
            <div class="p-5 flex gap-4 items-start border-b">
                <img src="{{ asset($dish?->thumbnail ?? 'https://placehold.co/200x150') }}" alt="{{ $dish?->title }}"
                    class="w-36 h-28 rounded-xl object-cover" />
                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-semibold">{{ $dish?->title }}</h3>

                                {{-- Favorites --}}
                                <button wire:click="toggleFavorite" wire:loading.attr="disabled"
                                    aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                                    title="{{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}"
                                    class="inline-flex items-center justify-center rounded-full p-2 transition cursor-pointer
               {{ $isFavorited ? 'bg-red-500/10' : 'bg-slate-200/70 hover:bg-slate-300/70' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="size-5 {{ $isFavorited ? 'text-red-500 fill-red-500' : 'text-slate-700' }}"
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
                            <p class="text-base md:text-lg text-slate-600 font-jost leading-relaxed">
                                {{ $dish?->short_description }}</p>
                            <div class="mt-2">
                                <span class="text-lg font-semibold">
                                    {{ number_format($this->base_price, 2) }} <span
                                        class="!font-bold text-md font-oswald">&#2547;</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-5 py-3 space-y-5 overflow-y-scroll max-h-[50vh]">

                {{-- Crust select (if have any) --}}
                @if ($dish && $dish->crusts->count())
                    <div
                        class="bg-customRed-100/15 shadow mt-2 md:p-5 rounded-lg @error('crust_id') border border-customRed-100 @enderror">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-oswald font-medium text-lg">Crust</h4>
                                <p class="text-xs opacity-60">Please select one</p>
                            </div>

                            <p class="bg-customRed-100 font-jost px-3 py-1 rounded-full text-xs text-white">
                                Required
                            </p>
                        </div>

                        @php
                            $crusts = $dish->crusts;
                            $first = $crusts->take(3);
                            $rest = $crusts->skip(3);
                        @endphp

                        <div class="font-jost text-gray-600 space-y-3 pe-2">
                            @foreach ($first as $c)
                                <div class="flex items-center justify-between">
                                    <label class="label text-gray-600 font-jost">
                                        <input type="radio" class="radio radio-error radio-sm" name="crust"
                                            value="{{ $c->id }}" wire:model.live="crust_id" />
                                        {{ $c->name }}
                                    </label>
                                    <p>Tk {{ number_format($c->price ?? 0, 2) }}</p>
                                </div>
                            @endforeach

                            {{-- Additional crust options (hidden by default) --}}
                            @if ($rest->count() > 0)
                                <div x-data="{ showMore: false }">
                                    <div x-show="showMore" x-collapse>
                                        @foreach ($rest as $c)
                                            <div class="flex items-center justify-between">
                                                <label class="label text-gray-600 font-jost">
                                                    <input type="radio" class="radio radio-error radio-sm"
                                                        name="crust" value="{{ $c->id }}"
                                                        wire:model.live="crust_id" />
                                                    {{ $c->name }}
                                                </label>
                                                <p>Tk {{ number_format($c->price ?? 0, 2) }}</p>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- View More / View Less button --}}
                                    <button type="button" x-on:click="showMore = !showMore"
                                        class="mt-0.5 cursor-pointer text-customRed-100 font-jost font-medium text-sm transition-colors">
                                        <span x-text="showMore ? 'View Less' : 'View More'"></span>
                                        <span> ({{ $rest->count() }})</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Bun select (if have any) --}}
                @if ($dish && $dish->buns->count())
                    <div
                        class="bg-customRed-100/15 shadow mt-2 md:p-5 rounded-lg  @error('bun_id') border border-customRed-100 @enderror">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-oswald font-medium text-lg">Bun</h4>
                                <p class="text-xs opacity-60">Please select one</p>
                            </div>

                            <p class="bg-customRed-100 font-jost px-3 py-1 rounded-full text-xs text-white">
                                Required
                            </p>
                        </div>

                        @php
                            $buns = $dish->buns;
                            $first = $buns->take(3);
                            $rest = $buns->skip(3);
                        @endphp

                        <div class="font-jost text-gray-600 space-y-3 pe-2">
                            @foreach ($first as $b)
                                <div class="flex items-center justify-between">
                                    <label class="label text-gray-600 font-jost">
                                        <input type="radio" class="radio radio-error radio-sm" name="bun"
                                            value="{{ $b->id }}" wire:model.live="bun_id" />
                                        {{ $b->name }}
                                    </label>
                                    <p>Tk {{ number_format($b->price ?? 0, 2) }}</p>
                                </div>
                            @endforeach

                            {{-- Additional bun options (hidden by default) --}}
                            @if ($rest->count() > 0)
                                <div x-data="{ showMore: false }">
                                    <div x-show="showMore" x-collapse>
                                        @foreach ($rest as $b)
                                            <div class="flex items-center justify-between">
                                                <label class="label text-gray-600 font-jost">
                                                    <input type="radio" class="radio radio-error radio-sm"
                                                        name="bun" value="{{ $b->id }}"
                                                        wire:model.live="bun_id" />
                                                    {{ $b->name }}
                                                </label>
                                                <p>Tk {{ number_format($b->price ?? 0, 2) }}</p>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- View More / View Less button --}}
                                    <button type="button" x-on:click="showMore = !showMore"
                                        class="mt-0.5 cursor-pointer text-customRed-100 font-jost font-medium text-sm transition-colors">
                                        <span x-text="showMore ? 'View Less' : 'View More'"></span>
                                        <span> ({{ $rest->count() }})</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Add ones -->
                @if ($dish && $dish->addOns->count())
                    <div class="bg-customRed-100/15 mt-5 md:p-6 rounded-md">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-oswald font-medium text-lg">Add Ons</h4>

                            <p class="bg-white text-gray-500 font-jost px-3 py-1 rounded-full text-xs">
                                Optional
                            </p>
                        </div>

                        <div class="font-jost text-gray-600 space-y-3 pe-2">

                            @foreach ($dish->addOns as $a)
                                @php $inputId = 'addon_'.$a->id; @endphp
                                <div class="flex items-center justify-between">
                                    <label for="{{ $inputId }}" class="label text-gray-600 font-jost">
                                        <input id="{{ $inputId }}" value="{{ $a->id }}"
                                            wire:model.live="addon_ids" type="checkbox"
                                            class="checkbox checkbox-error checkbox-sm" />
                                        {{ $a->name }}
                                    </label>
                                    <p>TK {{ number_format($a->price ?? 0, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Add-ons (multiple & optional) --}}
                {{-- @if ($dish && $dish->addOns->count())
                    <section class="rounded-xl border">
                        <header class="flex items-center justify-between px-4 py-3 border-b">
                            <h4 class="font-medium">Add-ons</h4>
                            <span class="badge badge-ghost">Optional</span>
                        </header>

                        <ul class="p-2 divide-y">
                            @foreach ($dish->addOns as $a)
                                @php $inputId = 'addon_'.$a->id; @endphp
                                <li class="flex items-center justify-between px-2 py-2">
                                    <label for="{{ $inputId }}" class="flex items-center gap-3 cursor-pointer">
                                        <input id="{{ $inputId }}" type="checkbox" class="checkbox checkbox-sm"
                                            value="{{ $a->id }}" wire:model.live="addon_ids" />
                                        <span>{{ $a->name }}</span>
                                    </label>
                                    <span class="text-sm opacity-80">
                                        {{ number_format($a->price ?? 0, 2) }} <span
                                            class="font-oswald">&#2547;</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif --}}
            </div>

            {{-- Price breakdown + Total --}}
            <div class="px-1 pt-1 space-y-1">
                {{-- <div class="flex items-center justify-between text-sm">
                        <span>Base</span>
                        <span>{{ number_format($this->base_price, 2) }} <span class="font-oswald">&#2547;</span></span>
                    </div>

                    @if ($this->crust_extra > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span>Crust</span>
                            <span>+{{ number_format($this->crust_extra, 2) }} <span class="font-oswald">&#2547;</span></span>
                        </div>
                    @endif

                    @if ($this->addons_extra > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span>Add-ons</span>
                            <span>+{{ number_format($this->addons_extra, 2) }} <span class="font-oswald">&#2547;</span></span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-sm font-medium border-t pt-1">
                        <span>Unit total</span>
                        <span>
                            {{ number_format($this->unit_total, 2) }} <span class="font-oswald">&#2547;</span>
                        </span>
                    </div> --}}

                <div class="flex items-center justify-between mt-1 px-5">
                    <span class="font-semibold">Total</span>
                    <span class="text-red-500 font-semibold">
                        {{ number_format($this->previewTotal, 2) }} <span
                            class="!font-bold text-md font-oswald">&#2547;</span>
                    </span>
                </div>
            </div>

            {{-- Footer: qty + CTA --}}
            <div class="p-5 bg-base-100 border-t flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <button class="btn btn-circle btn-ghost" @click.prevent="$wire.decrementQty()">–</button>
                    <span class="w-8 text-center font-medium">{{ $qty }}</span>
                    <button class="btn btn-circle btn-ghost" @click.prevent="$wire.incrementQty()">+</button>
                </div>

                <button class="btn bg-customRed-100 flex-1 h-12 text-white rounded-xl" wire:click="addToCart">
                    Add to Cart
                </button>
            </div>

            <button class="btn btn-circle btn-ghost absolute right-3 top-3"
                wire:click="$set('open', false)">✕</button>
        </div>

        <label class="modal-backdrop" wire:click="$set('open', false)">Close</label>
    </div>
</div>
