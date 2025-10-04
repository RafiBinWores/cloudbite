<div>
    <input type="checkbox" class="modal-toggle" {{ $open ? 'checked' : '' }} />
    <div class="modal">
        <div class="modal-box max-w-3xl p-0 rounded-2xl overflow-hidden">

            {{-- Header --}}
            <div class="p-5 flex gap-4 items-start border-b">
                <img src="{{ asset($dish?->thumbnail ?? 'https://placehold.co/200x150') }}" alt="{{ $dish?->title }}"
                    class="w-36 h-28 rounded-xl object-cover" />
                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-semibold">{{ $dish?->title }}</h3>
                                <button class="btn btn-ghost btn-circle">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-70" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.312-2.733C5.099 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </button>
                            </div>
                            <p>{{ $dish?->short_description }}</p>
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
            <div class="px-5 py-4 space-y-5 overflow-y-scroll max-h-[50vh]">

                {{-- Crust (single-select, required if exists) --}}
                @if ($dish && $dish->crusts->count())
                    <section class="rounded-xl border">
                        <header class="flex items-center justify-between px-4 py-3 border-b">
                            <div>
                                <h4 class="font-medium">Crust</h4>
                                <p class="text-xs opacity-60">Please select 1 option</p>
                            </div>
                            <span
                                class="badge badge-error badge-soft text-red-500 bg-red-50 border-red-200">Required</span>
                        </header>

                        @php
                            $crusts = $dish->crusts;
                            $first = $crusts->take(3);
                            $rest = $crusts->skip(3);
                        @endphp

                        <div class="p-2">
                            <ul class="divide-y">
                                @foreach ($first as $c)
                                    <li class="flex items-center justify-between px-2 py-2">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="radio" class="radio radio-sm" name="crust"
                                                value="{{ $c->id }}" wire:model.live="crust_id" />
                                            <span>{{ $c->name }}</span>
                                        </label>
                                        <span class="text-sm opacity-80">
                                            {{ number_format($c->price ?? 0, 2) }} <span
                                                class="font-oswald">&#2547;</span>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            @if ($rest->count())
                                <details class="px-2 py-2">
                                    <summary class="text-sm text-red-500 cursor-pointer flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                        View {{ $rest->count() }} more
                                    </summary>
                                    <ul class="mt-2 divide-y">
                                        @foreach ($rest as $c)
                                            <li class="flex items-center justify-between px-2 py-2">
                                                <label class="flex items-center gap-3 cursor-pointer">
                                                    <input type="radio" class="radio radio-sm" name="crust"
                                                        value="{{ $c->id }}" wire:model.live="crust_id" />
                                                    <span>{{ $c->name }}</span>
                                                </label>
                                                <span class="text-sm opacity-80">
                                                    {{ number_format($c->price ?? 0, 2) }} <span
                                                        class="font-oswald">&#2547;</span>
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif

                            @error('crust_id')
                                <div class="px-2 pb-2 text-xs text-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </section>
                @endif

                {{-- Bun (single-select, optional; free) --}}
                {{-- Bun (single-select, required if exists) --}}
                @if ($dish && $dish->buns->count())
                    <section class="rounded-xl border">
                        <header class="flex items-center justify-between px-4 py-3 border-b">
                            <div>
                                <h4 class="font-medium">Bun</h4>
                                <p class="text-xs opacity-60">Please select 1 option</p>
                            </div>
                            <span
                                class="badge badge-error badge-soft text-red-500 bg-red-50 border-red-200">Required</span>
                        </header>

                        <div class="p-2">
                            <ul class="divide-y">
                                @foreach ($dish->buns as $b)
                                    <li class="flex items-center justify-between px-2 py-2">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="radio" class="radio radio-sm" name="bun"
                                                value="{{ $b->id }}" wire:model.live="bun_id" />
                                            <span>{{ $b->name }}</span>
                                        </label>
                                        <span class="text-sm opacity-80">Free</span>
                                    </li>
                                @endforeach
                            </ul>

                            {{-- Bun validation message --}}
                            @error('bun_id')
                                <div class="px-2 pb-2 text-xs text-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </section>
                @endif


                {{-- Add-ons (multiple & optional) --}}
                @if ($dish && $dish->addOns->count())
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
                                        {{ number_format($a->price ?? 0, 2) }} <span class="font-oswald">&#2547;</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
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
