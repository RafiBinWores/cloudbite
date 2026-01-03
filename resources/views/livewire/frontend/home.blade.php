<div>
    @push('meta')
        <title>{{ $seoTitle ?? 'CloudBite – Fresh Meals & Meal Plans Delivered in Dhaka' }}</title>

        <meta name="description"
            content="{{ $seoDesc ?? 'Order fresh, hygienic meals and flexible weekly or monthly meal plans. Fast delivery across Dhaka with customizable menus.' }}">
        <meta name="robots" content="index, follow">
        <meta name="keywords"
            content="fresh meals, meal plans, food delivery, Dhaka, healthy eating, customizable menu, weekly meal plan, monthly meal plan, hygienic food, chef-crafted meals, cloud kitchen, food subscription, diet-friendly meals">

        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $seoTitle ?? 'CloudBite – Fresh Meals & Meal Plans Delivered in Dhaka' }}">
        <meta property="og:description"
            content="{{ $seoDesc ?? 'Order fresh, hygienic meals and flexible weekly or monthly meal plans. Fast delivery across Dhaka with customizable menus.' }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="CloudBite">
        <meta property="og:image" content="{{ $seoImage ?? asset('assets/images/seo/default-og.jpg') }}">
        <meta property="og:image:alt" content="{{ $seoTitle ?? 'CloudBite Cloud Kitchen' }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $seoTitle ?? 'CloudBite – Fresh Meals & Meal Plans Delivered in Dhaka' }}">
        <meta name="twitter:description"
            content="{{ $seoDesc ?? 'Order fresh, hygienic meals and flexible weekly or monthly meal plans. Fast delivery across Dhaka with customizable menus.' }}">
        <meta name="twitter:image" content="{{ $seoImage ?? asset('assets/images/seo/default-og.jpg') }}">
    @endpush


    @push('styles')
        <style>
            .discountBannerPagination {
                display: flex;
                gap: 8px;
            }

            .discountBannerPagination .swiper-pagination-bullet {
                width: 28px;
                height: 4px;
                border-radius: 9999px;
                background: rgba(239, 68, 68, 0.35);
                opacity: 1;
                transition: all 0.3s ease;
            }

            .discountBannerPagination .swiper-pagination-bullet-active {
                width: 42px;
                background: #ef4444;
            }
        </style>
    @endpush


    <!-- Hero section start -->
    <section class="overflow-hidden">
        <div class="relative w-screen h-screen bg-cover bg-center overflow-hidden"
            style="background-image: url('{{ asset('assets/images/banner-bg.jpg') }}')">
            <!-- bg left image -->
            <img src="{{ asset('assets/images/banner-left-bg.png') }}" alt="Bg image"
                class="absolute -left-36 top-[30%] animate__animated animate__fadeInLeft hidden lg:block" />

            <!-- bg right image -->
            <img src="{{ asset('assets/images/banner-right-bg.png') }}" alt="Bg Image"
                class="absolute -right-24 top-[65%] animate__animated animate__fadeInRight hidden lg:block" />
            <div class="swiper myHeroSwiper relative top-24">
                <div class="swiper-wrapper">
                    @forelse($heroDishes as $dish)
                        @php
                            $heroSrc = $dish->hero_image
                                ? \Illuminate\Support\Facades\Storage::url($dish->hero_image)
                                : ($dish->thumbnail
                                    ? \Illuminate\Support\Facades\Storage::url($dish->thumbnail)
                                    : '/assets/images/pizza.png');

                            $discountSrc = $dish->hero_discount_image
                                ? \Illuminate\Support\Facades\Storage::url($dish->hero_discount_image)
                                : '/assets/images/discount.png';
                        @endphp

                        <div class="swiper-slide px-4">
                            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 place-items-center gap-6 h-[90vh]">
                                <!-- Left: Text -->
                                <div class="relative z-10 text-center lg:text-left">
                                    <!-- Price pill -->
                                    <div
                                        class="inline-flex items-center gap-2 px-5 py-2 rounded-full border-[0.5px] border-white/60 mb-6 text-white animate__animated animate__fadeInLeft">
                                        <span class="uppercase tracking-wide text-sm md:text-base font-jost">
                                            Purchase today, just
                                        </span>
                                        <span class="font-bold">
                                            ৳{{ $dish->price_with_discount ?? $dish->display_price }}
                                        </span>
                                    </div>

                                    <h2
                                        class="font-medium font-oswald leading-[0.95] uppercase tracking-tight text-4xl text-white sm:text-5xl md:text-6xl lg:text-[80px] lg:leading-24 animate__animated animate__fadeInDown">
                                        {{ $dish->title }}
                                    </h2>

                                    <p
                                        class="mt-5 max-w-2xl text-white/90 text-xl md:text-lg lg:text-xl font-jost leading-9 animate__animated animate__fadeInUp">
                                        {{ $dish->short_description }}
                                    </p>

                                    <button wire:click="$dispatch('open-add-to-cart', { dishId: {{ $dish->id }} })"
                                        class="mt-8 inline-flex items-center justify-center px-7 py-4 rounded-lg bg-customRed-100 cursor-pointer hover:bg-customRed-200 transition text-white font-semibold uppercase tracking-wide shadow-lg animate__animated animate__fadeInUp">
                                        Order Now
                                    </button>
                                </div>

                                <div>
                                    <!-- Right: Image -->
                                    <div
                                        class="relative z-10 flex justify-center lg:justify-end animate__animated animate__fadeInDown">
                                        <!-- Discount bubble -->
                                        <img src="{{ $discountSrc }}" alt="Discount bubble"
                                            class="absolute top-0 md:top-2 lg:top-8 right-2 md:right-20 lg:right-2 lg:-left-20 size-24 md:size-36 lg:size-40 z-50" />

                                        <!-- Main food image -->
                                        <img src="{{ $heroSrc }}" alt="{{ $dish->title }}"
                                            class="relative z-10 w-[90%] md:w-[60%] lg:w-[500px] drop-shadow-2xl" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        {{-- Original static slides as fallback if no hero dishes --}}
                        <div class="swiper-slide px-4">
                            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 place-items-center gap-6 h-[90vh]">
                                <!-- Left: Text -->
                                <div class="relative z-10 text-center lg:text-left">
                                    <!-- Price pill -->
                                    <div
                                        class="inline-flex items-center gap-2 px-5 py-2 rounded-full border-[0.5px] border-white/60 mb-6 text-white animate__animated animate__fadeInLeft">
                                        <span class="uppercase tracking-wide text-sm md:text-base font-jost">Purchase
                                            today,
                                            just</span>
                                        <span class="font-bold">$58</span>
                                    </div>

                                    <h2
                                        class="font-medium font-oswald leading-[0.95] uppercase tracking-tight text-4xl text-white sm:text-5xl md:text-6xl lg:text-[80px] lg:leading-24 animate__animated animate__fadeInDown">
                                        French Break<br />Cheesy Pizza
                                    </h2>

                                    <p
                                        class="mt-5 max-w-2xl text-white/90 text-xl md:text-lg lg:text-xl font-jost leading-9 animate__animated animate__fadeInUp">
                                        Plan upon yet way get cold spot its week. Almost do am or
                                        limits hearts. Resolve parties but why she shewing know.
                                    </p>

                                    <a href="#order"
                                        class="mt-8 inline-flex items-center justify-center px-7 py-4 rounded-lg bg-customRed-100 hover:bg-customRed-200 transition text-white font-semibold uppercase tracking-wide shadow-lg animate__animated animate__fadeInUp">
                                        Order Now
                                    </a>
                                </div>

                                <div>
                                    <!-- Right: Image -->
                                    <div
                                        class="relative z-10 flex justify-center lg:justify-end animate__animated animate__fadeInDown">
                                        <!-- Discount bubble -->
                                        <img src="./assets/images/discount.png" alt="Discount bubble"
                                            class="absolute top-0 md:top-2 lg:top-8 right-2 md:right-20 lg:right-2 lg:-left-20 size-24 md:size-36 lg:size-40 z-50" />

                                        <!-- Main food image -->
                                        <img src="/assets/images/pizza.png" alt="Cheesy Pizza"
                                            class="relative z-10 w-[90%] md:w-[60%] lg:w-[500px] drop-shadow-2xl" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide px-4">
                            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 place-items-center gap-6 h-[90vh]">
                                <!-- Left: Text -->
                                <div class="relative z-10 text-center lg:text-left">
                                    <!-- Price pill -->
                                    <div
                                        class="inline-flex items-center gap-2 px-5 py-2 rounded-full border-[0.5px] border-white/60 mb-6 text-white animate__animated animate__fadeInLeft">
                                        <span class="uppercase tracking-wide text-sm md:text-base font-jost">Purchase
                                            today,
                                            just</span>
                                        <span class="font-bold">$58</span>
                                    </div>

                                    <h2
                                        class="font-medium font-oswald leading-[0.95] uppercase tracking-tight text-4xl text-white sm:text-5xl md:text-6xl lg:text-[80px] lg:leading-24 animate__animated animate__fadeInDown">
                                        French Break<br />Cheesy Pizza
                                    </h2>

                                    <p
                                        class="mt-5 max-w-2xl text-white/90 text-xl md:text-lg lg:text-xl font-jost leading-9 animate__animated animate__fadeInUp">
                                        Plan upon yet way get cold spot its week. Almost do am or
                                        limits hearts. Resolve parties but why she shewing know.
                                    </p>

                                    <a href="#order"
                                        class="mt-8 inline-flex items-center justify-center px-7 py-4 rounded-lg bg-customRed-100 hover:bg-customRed-200 transition text-white font-semibold uppercase tracking-wide shadow-lg animate__animated animate__fadeInUp">
                                        Order Now
                                    </a>
                                </div>

                                <div>
                                    <!-- Right: Image -->
                                    <div
                                        class="relative z-10 flex justify-center lg:justify-end animate__animated animate__fadeInDown">
                                        <!-- Discount bubble -->
                                        <img src="./assets/images/discount.png" alt="Discount bubble"
                                            class="absolute top-0 md:top-2 lg:top-8 right-2 md:right-20 lg:right-2 lg:-left-20 size-24 md:size-36 lg:size-40 z-50" />

                                        <!-- Main food image -->
                                        <img src="/assets/images/burger.png" alt="Cheesy Pizza"
                                            class="relative z-10 w-[90%] md:w-[60%] lg:w-[500px] drop-shadow-2xl" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Circular nav buttons -->
                <div class="hidden lg:block">
                    <div class="swiper-button-prev hidden !left-6 sm:!left-10 !right-auto"></div>
                    <div class="swiper-button-next hidden !right-6 sm:!right-10 !left-auto"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero section end -->

    <!-- feature categories -->
    <section class="relative w-full py-16 lg:py-28 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center font-oswald animate__animated animate__fadeInDown">
                <div class="flex items-center justify-center gap-3">
                    <img src="{{ asset('assets/images/icons/arrow.png') }}" alt="Arrow icon"
                        class="h-2.5 rotate-180" />
                    <p class="uppercase text-base md:text-xl text-customRed-100 font-medium">Best deal</p>
                    <img src="{{ asset('assets/images/icons/arrow.png') }}" alt="Arrow icon" class="h-2.5" />
                </div>
                <h3 class="capitalize font-medium text-4xl md:text-5xl lg:text-6xl">Our Popular Dishes</h3>
            </div>

            <div class="swiper categoriesSwiper mt-10">
                <div class="swiper-wrapper">
                    @foreach ($navbarCategories as $cat)
                        <div class="swiper-slide">
                            <a href="{{ route('fontDishes.index', ['categories' => [$cat->slug]]) }}" wire:navigate
                                class="group p-5 md:p-[30px] bg-[linear-gradient(180deg,rgba(255,255,255,0.8)_0%,#ECF0F3_100%)] 
                           border-gray-200 border rounded-md overflow-hidden block">

                                <!-- Product Image -->
                                <img src="{{ asset($cat->image) }}" alt="{{ $cat->name }}"
                                    class="mx-auto size-[100px] md:size-[150px] object-cover rounded" />

                                <!-- Product Info -->
                                <div class="text-center mt-6 font-oswald space-y-5">
                                    <p class="text-xl font-medium">{{ $cat->name }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

    </section>

    <!-- Feature dish section start -->
    @php
        $menuCategories = $menuDishes->groupBy('category_id');
    @endphp

    <section class="w-full bg-center bg-cover bg-no-repeat relative overflow-hidden"
        style="background-image: url('{{ asset('assets/images/feature-dish-bg.jpg') }}')">
        <!-- Left white split: hide on mobile, show from md+ -->
        <div class="pointer-events-none hidden lg:block absolute inset-y-0 left-0 w-[37%] bg-white z-30"></div>

        <div
            class="max-w-7xl mx-auto relative z-40 grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-10 px-4 sm:px-6 py-12 sm:py-16 lg:py-28">
            <!-- Left column -->
            <div class="w-full">
                <div
                    class="flex items-center justify-center lg:justify-normal gap-4 sm:gap-6 md:gap-8 mb-8 sm:mb-10 animate__animated animate__fadeInLeft">
                    <div
                        class="size-20 sm:size-24 md:size-[120px] border-customRed-100 rounded-full border-2 grid place-items-center shrink-0">
                        <!-- Dish icon -->
                        <img src="{{ asset('assets/images/icons/serving-dish.png') }}" alt="Serving Dish Icon"
                            class="size-10 sm:size-12 md:size-[60px]" />
                    </div>
                    <div class="font-oswald">
                        <p
                            class="text-4xl sm:text-6xl md:text-[80px] font-bold text-customRed-100 leading-none md:leading-20 mb-2 md:mb-4">
                            500+
                        </p>
                        <p class="text-lg sm:text-xl md:text-2xl uppercase">
                            menu and dishes
                        </p>
                    </div>
                </div>

                <!-- Dish image -->
                <img src="{{ asset('assets/images/feature-dish.jpg') }}" alt="Feature Dish"
                    class="w-full lg:max-w-[500px] h-auto lg:h-[470px] object-cover animate__animated animate__fadeInDown" />
            </div>

            <!-- Right column (Menus) -->
            <div class="w-full">
                <div class="flex items-center gap-3 mb-2 animate__animated animate__fadeInDown">
                    <p class="uppercase text-customRed-100 font-medium font-oswald text-base sm:text-lg md:text-xl">
                        Food Items
                    </p>
                    <img src="{{ asset('assets/images/icons/arrow.png') }}" alt="Arrow icon" class="h-3" />
                </div>

                <h3
                    class="text-3xl sm:text-4xl md:text-5xl lg:text-[55px] font-oswald font-medium lg:mb-16 animate__animated animate__fadeInDown">
                    Starters & Main Dishes
                </h3>

                <!-- Tabs -->
                <div id="menuTabs"
                    class="flex flex-wrap items-center gap-2 sm:gap-3 font-oswald font-medium mt-6 sm:mt-8 mb-4 sm:mb-5 animate__animated animate__fadeInRight">

                    @forelse ($menuCategories as $categoryId => $items)
                        @php
                            $catName = optional($items->first()->category)->name ?? 'Menu';
                            $tabKey = 'cat-' . $categoryId;
                        @endphp

                        <a class="{{ $loop->first ? 'bg-customRed-100 text-white' : 'bg-white' }} px-6 py-4 rounded-xl"
                            href="#" data-tab="{{ $tabKey }}">
                            {{ $catName }}
                        </a>
                    @empty
                        <a class="bg-customRed-100 text-white px-6 py-4 rounded-xl" href="#"
                            data-tab="cat-empty">
                            Menu
                        </a>
                    @endforelse
                </div>

                <!-- PANES -->
                <div id="menuPanes" class="space-y-6 animate__animated animate__fadeInRight">
                    @forelse ($menuCategories as $categoryId => $items)
                        @php $paneKey = 'cat-' . $categoryId; @endphp

                        <div data-pane="{{ $paneKey }}" class="space-y-6 {{ $loop->first ? '' : 'hidden' }}">
                            @foreach ($items->take(4) as $dish)
                                <article class="space-y-2">
                                    <div class="flex flex-wrap items-baseline gap-3 font-oswald">
                                        <button
                                            wire:click="$dispatch('open-add-to-cart', { dishId: {{ $dish->id }} })"
                                            class="text-lg sm:text-xl md:text-[22px] tracking-wide font-medium hover:text-customRed-100 cursor-pointer">
                                            {{ $dish->title }}
                                        </button>

                                        <div class="min-w-0 flex-1 border-t-2 mx-4 border-dashed border-gray-400">
                                        </div>

                                        <div class="flex items-center gap-4 sm:gap-5 whitespace-nowrap">
                                            <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">
                                                &#2547;{{ number_format((float) $dish->price, 0) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 font-jost">
                                        <p class="text-slate-600">{{ $dish->short_description }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @empty
                        <div data-pane="cat-empty" class="space-y-6">
                            <p class="text-slate-600 font-jost">No menu items available right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Weekly & Monthly package -->
    <section class="bg-slate-900 w-full py-16 lg:py-28 relative overflow-hidden">
        <!-- decorated leaf -->
        <img src="./assets/images/leaf.png" alt=""
            class="pointer-events-none absolute top-[170px] left-[100px]" />
        <div class="max-w-7xl mx-auto font-oswald px-4 sm:px-6 relative">
            <div class="mb-8 animate__animated animate__fadeInDown">
                <div class="flex items-center gap-4">
                    <h3
                        class="text-white text-3xl md:text-5xl lg:text-6xl font-medium leading-tight md:leading-[64px] lg:leading-[72px] capitalize">
                        Choose your plan
                    </h3>
                    <img src="./assets/images/icons/arrow.png" alt="Arrow icon" class="h-3 md:h-5 mt-2 md:mt-3" />
                </div>
                <p class="font-jost text-white">
                    Weekly for flexibility. Monthly for best value.
                </p>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 gap-6 md:gap-8 lg:grid-cols-5 lg:grid-rows-2 lg:gap-8 lg:h-[720px]">
                <!-- Big left image (becomes a single equal-height card on sm/md) -->
                <div
                    class="h-[450px] md:h-[350px] lg:h-full col-span-1 row-span-1 lg:col-span-2 lg:row-span-2 overflow-hidden bg-[url(/assets/images/meal-package-3.jpg)] bg-cover bg-center rounded-xl w-full px-[24px] py-[36px] md:px-[30px] md:py-[44px] lg:p-[50px] relative text-center md:text-left animate__animated animate__fadeInLeft">
                    <p
                        class="inline-block px-3 py-1 rounded-full text-[10px] md:text-xs font-jost bg-white/20 text-white mb-2">
                        Fresh • On time • Everyday
                    </p>
                    <h3
                        class="text-white text-3xl md:text-5xl lg:text-6xl font-medium leading-tight md:leading-[64px] lg:leading-[72px] uppercase">
                        Your Lunch & Dinner
                    </h3>
                    <p class="inline-block pt-4 md:pt-5 font-jost text-white md:max-w-[70%] lg:max-w-none">
                        Pick a plan that fits your routine. Customize meals for each day
                        and enjoy chef-crafted dishes delivered hot.
                    </p>

                    <!-- Menu image -->
                    <img src="./assets/images/dish-1.png" alt="Lunch Dish"
                        class="absolute left-1/2 -translate-x-1/2 bottom-6 size-[220px] md:left-auto md:translate-x-0 md:-right-[90px] md:-bottom-16 md:size-[300px] lg:-right-20 lg:bottom-16 lg:size-[350px] pointer-events-none" />
                </div>

                <!-- Top-right -->
                <div
                    class="h-[450px] md:h-[350px] lg:h-full col-span-1 row-span-1 lg:col-span-3 lg:col-start-3 bg-[url(/assets/images/meal-package-1.jpg)] bg-cover bg-center rounded-xl w-full px-[24px] py-[36px] md:px-[30px] md:py-[44px] lg:p-[50px] relative overflow-hidden text-center md:text-left animate__animated animate__fadeInDown">
                    <div>
                        <h3
                            class="text-white text-3xl md:text-5xl lg:text-6xl font-medium leading-tight md:leading-[64px] lg:leading-[72px] capitalize mb-2">
                            Weekly <br class="hidden md:block" />
                            Plan
                        </h3>
                        <p class="text-white font-jost">
                            Weekly lunch, dinner or both meals, skip/swap any day.
                        </p>
                    </div>

                    <!-- Button -->
                    <a href="{{ route('meal.plans', ['plan' => 'weekly']) }}" wire:navigate wire:navigate
                        class="inline-block relative isolate rounded-full px-8 md:px-10 py-2.5 md:py-3 m-1 overflow-hidden cursor-pointer bg-red-500 font-medium text-white group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/60 mt-4 md:mt-5">
                        <span
                            class="pointer-events-none absolute w-64 h-0 rotate-45 -translate-x-20 bg-white top-1/2 transition-all duration-300 ease-out group-hover:h-64 group-hover:-translate-y-32"></span>
                        <span
                            class="relative z-10 transition-colors font-medium font-oswald duration-300 group-hover:text-black">
                            Book Now
                        </span>
                    </a>

                    <img src="./assets/images/dish-4.png" alt="Dish"
                        class="absolute left-1/2 -translate-x-1/2 bottom-6 size-[220px] md:left-auto md:translate-x-0 md:-right-[90px] md:-bottom-16 md:size-[300px] lg:-right-24 lg:-bottom-24 lg:size-[350px] pointer-events-none" />
                </div>

                <!-- Bottom-right -->
                <div
                    class="h-[450px] md:h-[350px] lg:h-full col-span-1 row-span-1 lg:col-span-3 lg:col-start-3 lg:row-start-2 bg-[url(/assets/images/meal-package-2.jpg)] bg-cover bg-center rounded-xl w-full px-[24px] py-[36px] md:px-[30px] md:py-[44px] lg:p-[50px] relative overflow-hidden text-center md:text-left animate__animated animate__fadeInUp">
                    <div>
                        <h3
                            class="text-white text-3xl md:text-5xl lg:text-6xl font-medium leading-tight md:leading-[64px] lg:leading-[72px] capitalize mb-2">
                            Monthly <br class="hidden md:block" />
                            Plan
                        </h3>
                        <p class="text-white font-jost">
                            Monthly lunch, dinner or both meals, skip/swap any day.
                        </p>
                    </div>

                    <!-- Button -->
                    <a href="{{ route('meal.plans', ['plan' => 'monthly']) }}" wire:navigate
                        class="inline-block relative isolate rounded-full px-8 md:px-10 py-2.5 md:py-3 m-1 overflow-hidden cursor-pointer bg-slate-900 font-medium text-white group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/60 mt-4 md:mt-5">
                        <span
                            class="pointer-events-none absolute w-64 h-0 rotate-45 -translate-x-20 bg-white top-1/2 transition-all duration-300 ease-out group-hover:h-64 group-hover:-translate-y-32"></span>
                        <span
                            class="relative z-10 transition-colors font-medium font-oswald duration-300 group-hover:text-black">
                            Book Now
                        </span>
                    </a>

                    <img src="./assets/images/dish-2.png" alt="Dish"
                        class="absolute left-1/2 -translate-x-1/2 bottom-6 size-[220px] md:left-auto md:translate-x-0 md:-right-[90px] md:-bottom-16 md:size-[300px] lg:-right-20 lg:-bottom-20 lg:size-[350px] pointer-events-none" />
                </div>
            </div>
        </div>

        <img src="./assets/images/border-bottom.png" alt="brush"
            class="absolute -bottom-0.5 left-0 bg-center object-cover bg-no-repeat z-10">
    </section>

    <!-- All dishes -->
    <section class="relative w-full py-16 lg:py-28 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center font-oswald animate__animated animate__fadeInDown">
                <div class="flex items-center justify-center gap-3">
                    <img src="{{ asset('assets/images/icons/arrow.png') }}" alt="Arrow icon"
                        class="h-2.5 rotate-180" />
                    <p class="uppercase text-base md:text-xl text-customRed-100 font-medium">Best deal</p>
                    <img src="{{ asset('assets/images/icons/arrow.png') }}" alt="Arrow icon" class="h-2.5" />
                </div>
                <h3 class="capitalize font-medium text-4xl md:text-5xl lg:text-6xl">Our Popular Dishes</h3>
            </div>

            {{-- Dishes Slider --}}
            <div class="swiper dishesSwiper mt-10">
                <div class="swiper-wrapper pb-5">
                    @foreach ($dishes as $dish)
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

                        <div class="swiper-slide">
                            <div class="card bg-base-100 shadow-sm rounded-md md:rounded-xl">
                                <figure class="relative">
                                    <img src="{{ asset($dish->thumbnail) }}" alt="{{ $dish->title }}"
                                        class="w-full h-36 md:h-48 object-cover rounded-t-md md:rounded-t-xl" />

                                    {{-- Discount Badge --}}
                                    @if ($dish->discount && $dish->discount_type)
                                        <span
                                            class="absolute top-2 left-2 inline-flex items-center px-2 py-0.5 md:py-1 rounded-full text-[10px] md:text-xs font-semibold bg-customRed-100/80 text-white z-10">
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
                                    <h2
                                        class="card-title text-sm md:text-base md:font-medium font-oswald line-clamp-1 text-slate-950">
                                        {{ $dish->title }}
                                    </h2>

                                    <p
                                        class="font-jost line-clamp-1 text-xs md:text-sm font-medium md:font-normal pt-2">
                                        {{ $dish->short_description }}</p>

                                    <div class="flex items-center justify-between mt-2 gap-2 md:items-center">
                                        <div class="font-oswald text-customRed-100">
                                            @if ($hasVariations)
                                                <div class="leading-tight md:leading-normal flex items-center gap-1">
                                                    <p class="font-medium text-xs md:text-lg">
                                                        <span class="font-bold">&#2547;</span>
                                                        <span class="md:text-base font-semibold">From</span>
                                                        {{ $money($fromPriceDiscounted) }}
                                                    </p>

                                                    @if ($dish->discount && $dish->discount_type && $fromPriceDiscounted < $fromPriceOriginal)
                                                        <p
                                                            class="font-medium line-through text-gray-500 text-[10px] md:text-base">
                                                            <span class="font-bold">&#2547;</span>
                                                            {{ $money($fromPriceOriginal) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="leading-tight md:leading-normal flex items-center gap-1">
                                                    <p class="font-medium text-xs md:text-lg">
                                                        <span class="font-bold">&#2547;</span>
                                                        {{ $money($normalPrice) }}
                                                    </p>

                                                    @if ($normalPrice < $normalOld)
                                                        <p
                                                            class="font-medium line-through text-gray-500 text-[10px] md:text-base">
                                                            <span class="font-bold">&#2547;</span>
                                                            {{ $money($normalOld) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <button
                                            wire:click="$dispatch('open-add-to-cart', { dishId: {{ $dish->id }} })"
                                            class="group relative isolate shrink-0 overflow-hidden cursor-pointer bg-customRed-100 text-white font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/60 w-8 h-8 rounded-full grid place-items-center md:w-auto md:h-auto md:rounded md:px-5 md:py-2 md:mt-1 md:inline-flex md:items-center md:justify-center"
                                            aria-label="Add to cart" title="Add to cart">
                                            {{-- ✅ Hover sweep (md+) --}}
                                            <span
                                                class="pointer-events-none absolute hidden md:block w-64 h-0 rotate-45 -translate-x-20 bg-slate-900 top-1/2 transition-all duration-300 ease-out group-hover:h-64 group-hover:-translate-y-32">
                                            </span>

                                            {{-- Mobile icon --}}
                                            <span class="relative z-10 md:hidden">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="8" cy="21" r="1" />
                                                    <circle cx="19" cy="21" r="1" />
                                                    <path
                                                        d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
                                                </svg>
                                            </span>

                                            {{-- Desktop text --}}
                                            <span
                                                class="relative z-10 hidden md:inline transition-colors font-medium font-oswald duration-300 group-hover:text-white whitespace-nowrap">
                                                Add to Cart
                                            </span>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach


                </div>

                <!-- Circular nav buttons -->
                <div class="hidden lg:block">
                    <div class="swiper-button-prev hidden !left-6 sm:!left-10 !right-auto"></div>
                    <div class="swiper-button-next hidden !right-6 sm:!right-10 !left-auto"></div>
                </div>
            </div>
    </section>

    <!-- Offer promotion section -->
    {{-- for promotion slider --}}
    @if ($sliderBanners->count())
        <section class="relative w-full bg-no-repeat bg-center bg-cover"
            style="background-image: url('{{ asset('assets/images/feature-dish-bg.jpg') }}')">

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-14">

                <!-- Section Heading -->
                <div class="mb-6 md:mb-8 text-center md:text-left">
                    <div class="inline-flex items-center gap-2 rounded-full bg-red-500/10 px-4 py-1.5 mb-3">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        <span class="text-xs font-semibold uppercase tracking-widest text-red-500">
                            Special Promotions
                        </span>
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-oswald text-neutral-900 dark:text-white">
                        Today’s Best Deals
                    </h2>

                    <p
                        class="mt-2 max-w-2xl mx-auto md:mx-0 text-sm sm:text-base text-neutral-600 dark:text-neutral-300 font-jost">
                        Limited-time offers crafted specially for you. Don’t miss out on our hottest discounts.
                    </p>
                </div>

                <!-- Banner Slider -->
                <div class="relative">

                    <div class="swiper discountBannerSwiper">
                        <div class="swiper-wrapper">

                            @foreach ($sliderBanners as $banner)
                                <div class="swiper-slide">

                                    @if ($banner->item_type === 'category')
                                        {{-- CATEGORY → Redirect --}}
                                        <a href="{{ route('fontDishes.index', ['categories' => [$banner->category->slug]]) }}"
                                            class="block relative w-full min-h-[240px] sm:min-h-[300px] md:aspect-[3/1] md:min-h-0 overflow-hidden rounded-2xl group">

                                            <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}"
                                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                                            <div
                                                class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent">
                                            </div>

                                            <div class="absolute inset-0 flex items-end sm:items-center">
                                                <div class="p-4 sm:p-6 md:p-10 text-white max-w-lg">
                                                    <span
                                                        class="inline-block mb-2 rounded-full bg-red-500 px-3 py-1 text-xs font-semibold uppercase tracking-wider">
                                                        Limited Offer
                                                    </span>

                                                    <h3
                                                        class="text-2xl sm:text-3xl md:text-4xl font-oswald leading-tight capitalize">
                                                        {{ $banner->title }}
                                                    </h3>

                                                    @if ($banner->description)
                                                        <p class="mt-2 text-sm sm:text-base text-white/90 font-jost">
                                                            {{ $banner->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    @elseif($banner->item_type === 'dish')
                                        {{-- DISH → Open Add To Cart --}}
                                        <div wire:click="$dispatch('open-add-to-cart', { dishId: {{ $banner->item_id }} })"
                                            class="relative w-full cursor-pointer min-h-[240px] sm:min-h-[300px] md:aspect-[3/1] md:min-h-0 overflow-hidden rounded-2xl group">

                                            <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}"
                                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                                            <div
                                                class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent">
                                            </div>

                                            <div class="absolute inset-0 flex items-end sm:items-center">
                                                <div class="p-4 sm:p-6 md:p-10 text-white max-w-lg">
                                                    <span
                                                        class="inline-block mb-2 rounded-full bg-red-500 px-3 py-1 text-xs font-semibold uppercase tracking-wider">
                                                        Limited Offer
                                                    </span>

                                                    <h3
                                                        class="text-2xl sm:text-3xl md:text-4xl font-oswald leading-tight capitalize">
                                                        {{ $banner->title }}
                                                    </h3>

                                                    @if ($banner->description)
                                                        <p class="mt-2 text-sm sm:text-base text-white/90 font-jost">
                                                            {{ $banner->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            @endforeach


                        </div>
                    </div>

                    <!-- Navigation -->
                    <button type="button"
                        class="discountBannerPrev absolute -left-3 sm:-left-6 top-1/2 -translate-y-1/2 z-20
                       h-11 w-11 rounded-full bg-white text-neutral-900
                       grid place-items-center shadow-lg hover:bg-red-500 hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-chevron-left-icon lucide-chevron-left">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                    </button>

                    <button type="button"
                        class="discountBannerNext absolute -right-3 sm:-right-6 top-1/2 -translate-y-1/2 z-20
                       h-11 w-11 rounded-full bg-white text-neutral-900
                       grid place-items-center shadow-lg hover:bg-red-500 hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-chevron-right-icon lucide-chevron-right">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </button>

                    <!-- Pagination -->
                    <div class="discountBannerPagination mt-4 flex justify-center"></div>

                </div>
            </div>
        </section>
    @endif

    <!-- for single banner -->
    @if (!empty($singleBanner))

        @if ($singleBanner->item_type === 'category' && $singleBanner->category)
            {{-- CATEGORY → Redirect using slug --}}
            <a href="{{ route('fontDishes.index', ['categories' => [$singleBanner->category->slug]]) }}"
                class="block">
                <section class="relative w-full bg-no-repeat bg-center object-cover"
                    style="background-image: url('{{ asset('assets/images/discount-bg.jpg') }}')">

                    <!-- Brush -->
                    {{-- <img src="{{ asset('assets/images/border-bottom.png') }}" alt="Bottom border" class="rotate-180 absolute top-0"> --}}

                    <div
                        class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-24 cursor-pointer">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

                            <!-- LEFT: Text -->
                            <div class="text-white text-center lg:text-left">
                                <!-- Badge -->
                                <div
                                    class="inline-flex items-center gap-2 rounded-md border border-white/50 px-4 py-2 mb-6 font-oswald animate__animated animate__fadeInDown">
                                    <span class="tracking-widest text-[22px]">LIMITED OFFER</span>
                                </div>

                                <!-- Title -->
                                <h1
                                    class="text-7xl md:text-7xl lg:text-[110px] font-extrabold bg-clip-text text-transparent bg-[url(/assets/images/pattern.png)] bg-cover bg-center uppercase font-oswald lg:leading-[110px] lg:w-[500px] animate__animated animate__fadeInDown">
                                    {{ $singleBanner->title }}
                                </h1>

                                <!-- Paragraph -->
                                <p
                                    class="font-jost text-white text-lg leading-7 max-w-xl mx-auto mt-6 animate__animated animate__fadeInDown">
                                    {{ $singleBanner->description }}
                                </p>

                                <!-- Countdown -->
                                <div id="deal-countdown"
                                    data-deadline="{{ \Carbon\Carbon::parse($singleBanner->end_at)->toIso8601String() }}"
                                    class="mt-10 flex flex-wrap justify-center lg:justify-start gap-1 font-oswald animate__animated animate__fadeInDown">

                                    <!-- Days -->
                                    <div
                                        class="w-[85px] md:w-[100px] h-[90px] rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 grid place-items-center">
                                        <div class="text-center">
                                            <div class="text-xl md:text-2xl font-bold" data-days>05</div>
                                            <div class="mt-1 text-sm md:tracking-[0.15em] uppercase">Days</div>
                                        </div>
                                    </div>

                                    <!-- Hours -->
                                    <div
                                        class="w-[85px] md:w-[100px] h-[90px] rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 grid place-items-center">
                                        <div class="text-center">
                                            <div class="text-xl md:text-2xl font-bold" data-hours>12</div>
                                            <div class="mt-1 text-sm md:tracking-[0.15em] uppercase">Hours</div>
                                        </div>
                                    </div>

                                    <!-- Minutes -->
                                    <div
                                        class="w-[85px] md:w-[100px] h-[90px] rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 grid place-items-center">
                                        <div class="text-center">
                                            <div class="text-xl md:text-2xl font-bold" data-minutes>30</div>
                                            <div class="mt-1 text-sm md:tracking-[0.15em] uppercase">Minutes</div>
                                        </div>
                                    </div>

                                    <!-- Seconds -->
                                    <div
                                        class="w-[85px] md:w-[100px] h-[90px] rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 grid place-items-center">
                                        <div class="text-center">
                                            <div class="text-xl md:text-2xl font-bold" data-seconds>40</div>
                                            <div class="mt-1 text-sm md:tracking-[0.15em] uppercase">Seconds</div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- RIGHT: Burger visual -->
                            <div class="relative">
                                <!-- burger -->
                                <img src="{{ asset($singleBanner->image) }}" alt="{{ $singleBanner->title }}"
                                    class="relative w-full max-w-[550px] mx-auto drop-shadow-[0_30px_40px_rgba(0,0,0,0.45)] z-10 animate__animated animate__fadeInUp" />
                            </div>

                        </div>
                    </div>
                </section>
            </a>
        @elseif($singleBanner->item_type === 'dish')
            {{-- DISH → Open add-to-cart modal --}}
            <section class="relative w-full bg-no-repeat bg-center object-cover"
                style="background-image: url('{{ asset('assets/images/discount-bg.jpg') }}')"
                wire:click="$dispatch('open-add-to-cart', { dishId: {{ $singleBanner->item_id }} })">

                <!-- Brush -->
                {{-- <img src="{{ asset('assets/images/border-bottom.png') }}" alt="Bottom border" class="rotate-180 absolute top-0"> --}}

                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-24 cursor-pointer">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 items-center">

                        <!-- LEFT: Text -->
                        <div class="text-white text-center lg:text-left">
                            <!-- Badge -->
                            <div
                                class="inline-flex items-center gap-2 rounded-md border border-white/50 px-4 py-2 mb-6 font-oswald animate__animated animate__fadeInDown">
                                <span class="tracking-widest text-[22px]">LIMITED OFFER</span>
                            </div>

                            <!-- Title -->
                            <h1
                                class="text-7xl md:text-7xl lg:text-[110px] font-extrabold bg-clip-text text-transparent bg-[url(/assets/images/pattern.png)] bg-cover bg-center uppercase font-oswald lg:leading-[110px] lg:w-[500px] animate__animated animate__fadeInDown">
                                {{ $singleBanner->title }}
                            </h1>

                            <!-- Paragraph -->
                            <p
                                class="font-jost text-white text-lg leading-7 max-w-xl mx-auto mt-6 animate__animated animate__fadeInDown">
                                {{ $singleBanner->description }}
                            </p>

                            <!-- Countdown -->
                            <div id="deal-countdown"
                                data-deadline="{{ \Carbon\Carbon::parse($singleBanner->end_at)->toIso8601String() }}"
                                class="mt-10 flex flex-wrap justify-center lg:justify-start gap-1 font-oswald animate__animated animate__fadeInDown">

                                <!-- Days -->
                                <div
                                    class="w-[85px] md:w-[100px] h-[90px] rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 grid place-items-center">
                                    <div class="text-center">
                                        <div class="text-xl md:text-2xl font-bold" data-days>05</div>
                                        <div class="mt-1 text-sm md:tracking-[0.15em] uppercase">Days</div>
                                    </div>
                                </div>

                                <!-- Hours -->
                                <div
                                    class="w-[85px] md:w-[100px] h-[90px] rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 grid place-items-center">
                                    <div class="text-center">
                                        <div class="text-xl md:text-2xl font-bold" data-hours>12</div>
                                        <div class="mt-1 text-sm md:tracking-[0.15em] uppercase">Hours</div>
                                    </div>
                                </div>

                                <!-- Minutes -->
                                <div
                                    class="w-[85px] md:w-[100px] h-[90px] rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 grid place-items-center">
                                    <div class="text-center">
                                        <div class="text-xl md:text-2xl font-bold" data-minutes>30</div>
                                        <div class="mt-1 text-sm md:tracking-[0.15em] uppercase">Minutes</div>
                                    </div>
                                </div>

                                <!-- Seconds -->
                                <div
                                    class="w-[85px] md:w-[100px] h-[90px] rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 grid place-items-center">
                                    <div class="text-center">
                                        <div class="text-xl md:text-2xl font-bold" data-seconds>40</div>
                                        <div class="mt-1 text-sm md:tracking-[0.15em] uppercase">Seconds</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- RIGHT: Burger visual -->
                        <div class="relative">
                            <!-- burger -->
                            <img src="{{ asset($singleBanner->image) }}" alt="{{ $singleBanner->title }}"
                                class="relative w-full max-w-[550px] mx-auto drop-shadow-[0_30px_40px_rgba(0,0,0,0.45)] z-10 animate__animated animate__fadeInUp" />
                        </div>

                    </div>
                </div>
            </section>
        @endif

    @endif

    {{-- cart modal --}}
    <livewire:frontend.cart.add-to-cart-modal />

    {{-- testimonial --}}
    <section class="relative bg-[url('/assets/images/testimonial-bg.jpg')] bg-center bg-cover overflow-hidden w-full">
        <!-- brush top -->
        <img src="/assets/images/border-bottom.png" alt=""
            class="rotate-180 absolute top-0 left-0 w-full pointer-events-none select-none">

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-28">
            <div class="swiper myHeroSwiper">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide">
                        <div class="mx-auto grid place-items-center pb-28 gap-6 font-oswald">
                            <!-- stars -->
                            <div class="flex gap-2 justify-center">
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <i class="fa-solid fa-star text-amber-500"></i>
                            </div>

                            <h3 class="text-3xl md:text-4xl capitalize font-medium">Best Chicken fry</h3>

                            <p
                                class="text-center font-jost text-lg sm:text-xl lg:text-2xl text-gray-500 leading-8 sm:leading-9 max-w-3xl">
                                “Thanks to your web agency team for their professional work. The website they created
                                for my business
                                exceeded my expectations, and my clients have given positive feedback about its design
                                and user-friendliness.”
                            </p>

                            <!-- avatars -->
                            <div class="mt-8 sm:mt-10 relative h-24 sm:h-36">
                                <img src="{{ asset('assets/images/feature-dish.jpg') }}" alt="Burger"
                                    class="size-28 md:size-[190px] left-1/2 -translate-x-1/2 top-0 object-cover bg-center border-4 border-white rotate-10">
                                <img src="./assets/images/user.jpg" alt="Burger"
                                    class="size-28 md:size-[190px] md:left-[30%] left-[20%] absolute top-10 md:top-14  object-cover bg-center border-4 border-white -rotate-2">
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="mx-auto grid place-items-center pb-28 gap-6 font-oswald">
                            <!-- stars -->
                            <div class="flex gap-2 justify-center">
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <i class="fa-solid fa-star text-amber-500"></i>
                            </div>

                            <h3 class="text-3xl md:text-4xl capitalize font-medium">Best pizza fry</h3>

                            <p
                                class="text-center font-jost text-lg sm:text-xl lg:text-2xl text-gray-500 leading-8 sm:leading-9 max-w-3xl">
                                “Thanks to your web agency team for their professional work. The website they created
                                for my business
                                exceeded my expectations, and my clients have given positive feedback about its design
                                and user-friendliness.”
                            </p>

                            <!-- avatars -->
                            <div class="mt-8 sm:mt-10 relative h-24 sm:h-36">
                                <img src="./assets/images/feature-dish.jpg" alt="Burger"
                                    class="size-28 md:size-[190px] left-1/2 -translate-x-1/2 top-0 object-cover bg-center border-4 border-white rotate-10">
                                <img src="./assets/images/user.jpg" alt="Burger"
                                    class="size-28 md:size-[190px] md:left-[30%] left-[20%] absolute top-10 md:top-14  object-cover bg-center border-4 border-white -rotate-2">
                            </div>
                        </div>
                    </div>

                    <!-- Duplicate slides as needed ... -->
                </div>

            </div>
        </div>
    </section>
</div>


@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Categories slider
            new Swiper(".categoriesSwiper", {
                slidesPerView: 4,
                spaceBetween: 20,
                loop: false,
                preventClicks: false,
                preventClicksPropagation: false,
                touchStartPreventDefault: false,
                // autoplay: {
                //     delay: 2500,
                //     disableOnInteraction: false
                // },
                pagination: {
                    el: ".categoriesSwiper .swiper-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".categoriesSwiper .swiper-button-next",
                    prevEl: ".categoriesSwiper .swiper-button-prev",
                },
                breakpoints: {
                    320: {
                        slidesPerView: 2
                    },
                    640: {
                        slidesPerView: 3
                    },
                    1024: {
                        slidesPerView: 4
                    },
                },
            });

            // Dishes slider
            new Swiper(".dishesSwiper", {
                slidesPerView: 3,
                spaceBetween: 20,
                loop: false,
                // autoplay: {
                //     delay: 3000,
                //     disableOnInteraction: false
                // },
                pagination: {
                    el: ".dishesSwiper .swiper-pagination",
                    clickable: true
                },
                navigation: {
                    nextEl: ".dishesSwiper .swiper-button-next",
                    prevEl: ".dishesSwiper .swiper-button-prev",
                },
                breakpoints: {
                    320: {
                        slidesPerView: 2
                    },
                    640: {
                        slidesPerView: 3
                    },
                    1024: {
                        slidesPerView: 4
                    },
                },
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabsWrap = document.getElementById('menuTabs');
            const panesWrap = document.getElementById('menuPanes');
            if (!tabsWrap || !panesWrap) return;

            const tabs = Array.from(tabsWrap.querySelectorAll('[data-tab]'));
            const panes = Array.from(panesWrap.querySelectorAll('[data-pane]'));

            const setActive = (key) => {
                tabs.forEach(t => {
                    const isActive = t.getAttribute('data-tab') === key;
                    t.classList.toggle('bg-customRed-100', isActive);
                    t.classList.toggle('text-white', isActive);
                    t.classList.toggle('bg-white', !isActive);
                });

                panes.forEach(p => {
                    const isActive = p.getAttribute('data-pane') === key;
                    p.classList.toggle('hidden', !isActive);
                });
            };

            // Default: first tab active
            if (tabs.length) setActive(tabs[0].getAttribute('data-tab'));

            tabs.forEach(tab => {
                tab.addEventListener('click', (e) => {
                    e.preventDefault();
                    setActive(tab.getAttribute('data-tab'));
                });
            });
        });
    </script>

    <script>
        (function() {
            const root = document.getElementById('deal-countdown');
            if (!root) return;

            // Prefer ISO datetime via data-deadline; fallback to +3 days from now
            const deadlineAttr = root.getAttribute('data-deadline');
            const target = deadlineAttr ? new Date(deadlineAttr) : new Date(Date.now() + 3 * 24 * 60 * 60 * 1000);

            // Elements
            const el = {
                d: root.querySelector('[data-days]'),
                h: root.querySelector('[data-hours]'),
                m: root.querySelector('[data-minutes]'),
                s: root.querySelector('[data-seconds]'),
            };

            const pad2 = (n) => String(n).padStart(2, '0');

            function render(diffMs) {
                if (diffMs <= 0) {
                    el.d.textContent = '00';
                    el.h.textContent = '00';
                    el.m.textContent = '00';
                    el.s.textContent = '00';
                    return false; // finished
                }
                const totalSec = Math.floor(diffMs / 1000);
                const days = Math.floor(totalSec / 86400);
                const hours = Math.floor((totalSec % 86400) / 3600);
                const minutes = Math.floor((totalSec % 3600) / 60);
                const seconds = totalSec % 60;

                el.d.textContent = pad2(days);
                el.h.textContent = pad2(hours);
                el.m.textContent = pad2(minutes);
                el.s.textContent = pad2(seconds);
                return true; // keep going
            }

            function tick() {
                const now = Date.now();
                const alive = render(target - now);
                if (alive) {
                    // Run roughly every 200ms for smoothness without heavy CPU
                    setTimeout(tick, 200);
                }
            }

            tick();
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new Swiper('.discountBannerSwiper', {
                loop: true,
                autoplay: {
                    delay: 4500,
                    disableOnInteraction: false,
                },
                speed: 700,
                navigation: {
                    nextEl: '.discountBannerNext',
                    prevEl: '.discountBannerPrev',
                },
                pagination: {
                    el: '.discountBannerPagination',
                    clickable: true,
                },
            });
        });
    </script>
@endpush
