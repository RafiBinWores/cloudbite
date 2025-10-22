<div x-data="{ open: @entangle('open').live }"
     @keydown.escape.window="open = false"
     x-effect="document.body.classList.toggle('overflow-hidden', open)">

  <div wire:teleport="body">
    <div x-cloak x-show="open"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4"
         role="dialog" aria-modal="true">

      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-[1px]"
        style="animation: backdrop-fade-in var(--backdrop-in-dur) var(--modal-ease-out) both"
        @click="open=false"
        x-transition:leave="!none"
        x-transition:leave.start="!none"
        x-transition:leave.end="!none"
        x-on:leave="($el.style.animation = `backdrop-fade-out var(--backdrop-out-dur) var(--modal-ease-in) both`)"
      ></div>

      <!-- Panel -->
      <div
        class="relative w-full max-w-3xl rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 overflow-hidden
               transform-gpu will-change-transform will-change-opacity"
        style="animation: modal-smooth-in var(--modal-in-dur) var(--modal-spring) both"
        x-transition:leave="!none"
        x-transition:leave.start="!none"
        x-transition:leave.end="!none"
        x-on:leave="($el.style.animation = `modal-smooth-out var(--modal-out-dur) var(--modal-ease-in) both`)"
      >
        {{-- Header --}}
        <div class="p-5 flex gap-4 items-start border-b border-slate-200">
          <img src="{{ asset($dish?->thumbnail ?? 'https://placehold.co/200x150') }}"
               alt="{{ $dish?->title }}"
               class="w-36 h-28 rounded-xl object-cover" />

          <div class="flex-1">
            <div class="flex items-start justify-between">
              <div>
                <div class="flex items-center gap-3">
                  <h3 class="text-xl font-semibold text-slate-900">{{ $dish?->title }}</h3>

                  {{-- Favorites --}}
                  <button type="button"
                          wire:click="toggleFavorite"
                          wire:loading.attr="disabled"
                          aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                          title="{{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}"
                          class="inline-flex items-center justify-center rounded-full p-2 transition
                                 {{ $isFavorited ? 'bg-red-500/10' : 'bg-slate-200/70 hover:bg-slate-300/70' }}">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="size-5 {{ $isFavorited ? 'text-red-500 fill-red-500' : 'text-slate-700' }}"
                         viewBox="0 0 24 24"
                         fill="{{ $isFavorited ? 'currentColor' : 'none' }}"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
                    </svg>
                    <span class="sr-only">{{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}</span>
                  </button>
                </div>

                <p class="mt-1 text-base md:text-lg text-slate-600 font-jost leading-relaxed">
                  {{ $dish?->short_description }}
                </p>

                <div class="mt-2">
                  <span class="text-lg font-semibold text-slate-900">
                    {{ number_format($this->base_price, 2) }}
                    <span class="!font-bold text-md font-oswald">&#2547;</span>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Close -->
          <button type="button"
                  class="absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100 text-slate-700"
                  @click="open = false"
                  aria-label="Close">✕</button>
        </div>

        {{-- Body --}}
        <div class="px-5 py-3 space-y-5 overflow-y-auto max-h-[60vh]">
          {{-- Crust select --}}
          @if ($dish && $dish->crusts->count())
            <div class="bg-customRed-100/15 shadow mt-2 md:p-5 rounded-lg @error('crust_id') border border-customRed-100 @enderror">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h4 class="font-oswald font-medium text-lg">Crust</h4>
                  <p class="text-xs opacity-60">Please select one</p>
                </div>
                <p class="bg-customRed-100 text-white font-jost px-3 py-1 rounded-full text-xs">Required</p>
              </div>

              @php
                $crusts = $dish->crusts;
                $first = $crusts->take(3);
                $rest = $crusts->skip(3);
              @endphp

              <div class="font-jost text-gray-700 space-y-3 pe-2">
                @foreach ($first as $c)
                  <label class="flex items-center justify-between gap-3">
                    <span class="flex items-center gap-3">
                      <input type="radio" name="crust" value="{{ $c->id }}"
                             wire:model.live="crust_id"
                             class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500" />
                      <span>{{ $c->name }}</span>
                    </span>
                    <span class="text-sm">Tk {{ number_format($c->price ?? 0, 2) }}</span>
                  </label>
                @endforeach

                @if ($rest->count() > 0)
                  <div x-data="{ showMore: false }">
                    <div x-show="showMore" x-collapse>
                      @foreach ($rest as $c)
                        <label class="flex items-center justify-between gap-3">
                          <span class="flex items-center gap-3">
                            <input type="radio" name="crust" value="{{ $c->id }}"
                                   wire:model.live="crust_id"
                                   class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500" />
                            <span>{{ $c->name }}</span>
                          </span>
                          <span class="text-sm">Tk {{ number_format($c->price ?? 0, 2) }}</span>
                        </label>
                      @endforeach
                    </div>

                    <button type="button"
                            x-on:click="showMore = !showMore"
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
            <div class="bg-customRed-100/15 shadow mt-2 md:p-5 rounded-lg @error('bun_id') border border-customRed-100 @enderror">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h4 class="font-oswald font-medium text-lg">Bun</h4>
                  <p class="text-xs opacity-60">Please select one</p>
                </div>
                <p class="bg-customRed-100 text-white font-jost px-3 py-1 rounded-full text-xs">Required</p>
              </div>

              @php
                $buns = $dish->buns;
                $first = $buns->take(3);
                $rest = $buns->skip(3);
              @endphp

              <div class="font-jost text-gray-700 space-y-3 pe-2">
                @foreach ($first as $b)
                  <label class="flex items-center justify-between gap-3">
                    <span class="flex items-center gap-3">
                      <input type="radio" name="bun" value="{{ $b->id }}"
                             wire:model.live="bun_id"
                             class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500" />
                      <span>{{ $b->name }}</span>
                    </span>
                    <span class="text-sm">Tk {{ number_format($b->price ?? 0, 2) }}</span>
                  </label>
                @endforeach

                @if ($rest->count() > 0)
                  <div x-data="{ showMore: false }">
                    <div x-show="showMore" x-collapse>
                      @foreach ($rest as $b)
                        <label class="flex items-center justify-between gap-3">
                          <span class="flex items-center gap-3">
                            <input type="radio" name="bun" value="{{ $b->id }}"
                                   wire:model.live="bun_id"
                                   class="h-4 w-4 text-red-500 border-slate-300 focus:ring-red-500" />
                            <span>{{ $b->name }}</span>
                          </span>
                          <span class="text-sm">Tk {{ number_format($b->price ?? 0, 2) }}</span>
                        </label>
                      @endforeach
                    </div>

                    <button type="button"
                            x-on:click="showMore = !showMore"
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
            <div class="bg-customRed-100/15 mt-5 md:p-6 rounded-md">
              <div class="flex items-center justify-between mb-4">
                <h4 class="font-oswald font-medium text-lg">Add Ons</h4>
                <p class="bg-white text-gray-500 font-jost px-3 py-1 rounded-full text-xs">Optional</p>
              </div>

              <div class="font-jost text-gray-700 space-y-3 pe-2">
                @foreach ($dish->addOns as $a)
                  @php $inputId = 'addon_'.$a->id; @endphp
                  <label for="{{ $inputId }}" class="flex items-center justify-between gap-3 cursor-pointer">
                    <span class="flex items-center gap-3">
                      <input id="{{ $inputId }}" value="{{ $a->id }}"
                             wire:model.live="addon_ids" type="checkbox"
                             class="h-4 w-4 rounded border-slate-300 text-red-500 focus:ring-red-500" />
                      <span>{{ $a->name }}</span>
                    </span>
                    <span class="text-sm">TK {{ number_format($a->price ?? 0, 2) }}</span>
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
              {{ number_format($this->previewTotal, 2) }}
              <span class="!font-bold text-md font-oswald">&#2547;</span>
            </span>
          </div>
        </div>

        {{-- Footer --}}
        <div class="p-5 border-t border-slate-200 flex items-center gap-4">
          <div class="flex items-center gap-3">
            <button type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100 text-slate-700"
                    @click.prevent="$wire.decrementQty()">–</button>
            <span class="w-8 text-center font-medium select-none">{{ $qty }}</span>
            <button type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full hover:bg-slate-100 text-slate-700"
                    @click.prevent="$wire.incrementQty()">+</button>
          </div>

          <button type="button"
                  class="inline-flex justify-center items-center gap-2 flex-1 h-12 rounded-xl bg-customRed-100 text-white font-medium shadow-sm hover:bg-customRed-100/90 active:scale-[.99] transition"
                  wire:click="addToCart">
            Add to Cart
          </button>
        </div>

      </div>
    </div>
  </div>
</div>
