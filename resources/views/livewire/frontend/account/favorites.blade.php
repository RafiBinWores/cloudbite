<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Your Favorites</h1>
            <p class="text-sm opacity-70">Saved dishes you love ❤️</p>
        </div>

        <div class="flex items-center gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search favorites…"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-customRed-100 focus:border-customRed-100 block p-2.5 w-56" />

            <select wire:model.live="perPage" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-customRed-100 focus:border-customRed-100 block py-2.5">
                <option value="12">12 / page</option>
                <option value="24">24 / page</option>
                <option value="48">48 / page</option>
            </select>
        </div>
    </div>

    @if ($favorites->isEmpty())
        <div class="rounded-xl border p-10 text-center">
            <div class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-500/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-red-500" viewBox="0 0 24 24"
                    fill="currentColor" stroke="currentColor" stroke-width="2">
                    <path
                        d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
                </svg>
            </div>
            <h2 class="text-lg font-medium">No favorites yet</h2>
            <p class="opacity-70">Tap the heart on any dish to save it here.</p>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($favorites as $dish)
                <div class="swiper-slide">
                    <div class="card bg-base-100 shadow-sm rounded-xl">
                        <figure class="relative">
                            <img src="{{ asset($dish->thumbnail) }}" alt="{{ $dish->title }}"
                                class="w-full h-48 object-cover rounded-t-xl" />
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
                                        {{ $discountValue }} <span class="font-normal font-oswald ps-1">&#2547;
                                        </span> <span class="ps-1">OFF</span>
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
                                    <p class="font-medium text-lg">
                                        <span class="font-bold">&#2547;</span> {{ $dish->price_with_discount }}
                                    </p>
                                    @if ($dish->price_with_discount < $dish->display_price)
                                        <p class="font-medium line-through text-gray-500">
                                            <span class="font-bold">&#2547;</span> {{ $dish->display_price }}
                                        </p>
                                    @endif
                                </div>

                                <button wire:click="$dispatch('open-add-to-cart', { dishId: {{ $dish->id }} })"
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
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $favorites->links() }}
        </div>
    @endif

    {{-- cart modal --}}
    <livewire:frontend.cart.add-to-cart-modal />
</div>
