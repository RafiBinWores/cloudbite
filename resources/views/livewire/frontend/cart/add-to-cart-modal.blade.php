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
                    <div class="bg-customRed-100/15 shadow mt-2 md:p-5 rounded-lg @error('crust_id') border border-customRed-100 @enderror">
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
                    <div class="bg-customRed-100/15 shadow mt-2 md:p-5 rounded-lg  @error('bun_id') border border-customRed-100 @enderror">
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

              <p
                class="bg-white text-gray-500 font-jost px-3 py-1 rounded-full text-xs"
              >
                Optional
              </p>
            </div>

            <div class="font-jost text-gray-600 space-y-3 pe-2">

                @foreach ($dish->addOns as $a)
                                @php $inputId = 'addon_'.$a->id; @endphp
              <div class="flex items-center justify-between">
                <label for="{{ $inputId }}" class="label text-gray-600 font-jost">
                  <input id="{{ $inputId }}" value="{{ $a->id }}" wire:model.live="addon_ids"
                    type="checkbox"
                    class="checkbox checkbox-error checkbox-sm"
                  />
                  Extra Cheese
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
