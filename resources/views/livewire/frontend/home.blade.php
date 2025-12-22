<div>
    <!-- Hero section start -->
    <section class="overflow-hidden">
        <div
            class="relative w-screen h-screen bg-[url(/assets/images/banner-bg.jpg)] bg-cover bg-center overflow-hidden">
            <!-- bg left image -->
            <img src="./assets/images/banner-left-bg.png" alt="Bg image"
                class="absolute -left-36 top-[30%] animate__animated animate__fadeInLeft hidden lg:block" />

            <!-- bg right image -->
            <img src="./assets/images/banner-right-bg.png" alt="Bg Image"
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

                                        <button
                                            wire:click="$dispatch('open-add-to-cart', { dishId: {{ $dish->id }} })"
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
                                            <span
                                                class="uppercase tracking-wide text-sm md:text-base font-jost">Purchase
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
                                            <span
                                                class="uppercase tracking-wide text-sm md:text-base font-jost">Purchase
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
                    <img src="./assets/images/icons/arrow.png" alt="Arrow icon" class="h-3 rotate-180" />
                    <p class="uppercase text-lg md:text-xl text-customRed-100 font-medium">Best deal</p>
                    <img src="./assets/images/icons/arrow.png" alt="Arrow icon" class="h-3" />
                </div>
                <h3 class="capitalize font-medium text-4xl md:text-5xl lg:text-6xl">Our Popular category</h3>
            </div>

            <div class="swiper categoriesSwiper mt-10">
                <div class="swiper-wrapper">
                    @foreach ($navbarCategories as $cat)
                        <div class="swiper-slide">
                            <a href="{{ route('fontDishes.index', ['categories' => [$cat->slug]]) }}" wire:navigate
                                class="group p-[30px] bg-[linear-gradient(180deg,rgba(255,255,255,0.8)_0%,#ECF0F3_100%)] 
                           border-gray-200 border rounded-md overflow-hidden block">

                                <!-- Product Image -->
                                <img src="{{ asset($cat->image) }}" alt="{{ $cat->name }}"
                                    class="mx-auto size-[150px] object-cover rounded" />

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
    <section
        class="bg-[url(/assets/images/feature-dish-bg.jpg)] w-full bg-center bg-cover bg-no-repeat relative overflow-hidden">
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
                        <img src="./assets/images/icons/serving-dish.png" alt="Serving Dish Icon"
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
                <img src="./assets/images/feature-dish.jpg" alt="Feature Dish"
                    class="w-full lg:max-w-[500px] h-auto lg:h-[470px] object-cover animate__animated animate__fadeInDown" />
            </div>

            <!-- Right column (Menus) -->
            <div class="w-full">
                <div class="flex items-center gap-3 mb-2 animate__animated animate__fadeInDown">
                    <p
                        class="uppercase text-customRed-100 font-medium font-oswald text-base mb-2 sm:text-lg md:text-xl">
                        Food Items
                    </p>
                    <img src="./assets/images/icons/arrow.png" alt="Arrow icon" class="h-3" />
                </div>

                <h3
                    class="text-3xl sm:text-4xl md:text-5xl lg:text-[55px] font-oswald font-medium lg:mb-16 animate__animated animate__fadeInDown">
                    Starters & Main Dishes
                </h3>

                <!-- Tabs -->
                <div id="menuTabs"
                    class="flex flex-wrap items-center gap-2 sm:gap-3 font-oswald font-medium mt-6 sm:mt-8 mb-4 sm:mb-5 animate__animated animate__fadeInRight">
                    <a class="bg-customRed-100 text-white px-6 py-4 rounded-xl" href="#" data-tab="main">Main
                        Dishes</a>
                    <a class="bg-white px-6 py-4 rounded-xl" href="#" data-tab="sea">Sea Food</a>
                    <a class="bg-white px-6 py-4 rounded-xl" href="#" data-tab="burger">Burger</a>
                    <a class="bg-white px-6 py-4 rounded-xl" href="#" data-tab="pizza">Pizza</a>
                </div>

                <!-- price for half and full plate -->
                <div
                    class="flex items-center justify-end gap-6 sm:gap-10 font-jost font-bold mb-4 animate__animated animate__fadeInRight">
                    <p class="border-2 text-slate-500 px-3 py-1 border-slate-500">
                        Half
                    </p>
                    <p class="border-2 text-slate-500 px-3 py-1 border-slate-500">
                        Full
                    </p>
                </div>

                <!-- PANES -->
                <div id="menuPanes" class="space-y-6 animate__animated animate__fadeInRight">
                    <!-- Main Dishes -->
                    <div data-pane="main" class="space-y-6">
                        <!-- Item -->
                        <article class="space-y-2">
                            <div class="flex flex-wrap items-baseline gap-3 font-oswald">
                                <a href=""
                                    class="text-lg sm:text-xl md:text-[22px] tracking-wide font-medium hover:text-customRed-100">
                                    Chicken Alfredo
                                </a>
                                <div class="min-w-0 flex-1 border-t-2 mx-4 border-dashed border-gray-400"></div>
                                <div class="flex items-center gap-4 sm:gap-5 whitespace-nowrap">
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;220</span>
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;330</span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 font-jost">
                                <p class="text-slate-600">Ricotta / Goat Cheese / Beetroot</p>
                                <p class="text-slate-700">Extra Free Juice.</p>
                            </div>
                        </article>

                        <!-- Item -->
                        <article class="space-y-2">
                            <div class="flex flex-wrap items-baseline gap-3 font-oswald">
                                <a href=""
                                    class="text-lg sm:text-xl md:text-[22px] tracking-wide font-medium hover:text-customRed-100">
                                    Turkey Alfredo
                                </a>
                                <div class="min-w-0 flex-1 border-t-2 mx-4 border-dashed border-gray-400"></div>
                                <div class="flex items-center gap-4 sm:gap-5 whitespace-nowrap">
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;220</span>
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;330</span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 font-jost">
                                <p class="text-slate-600">Ricotta / Goat Cheese / Beetroot</p>
                                <p class="text-slate-700">Extra Free Juice.</p>
                            </div>
                        </article>

                        <!-- Item -->
                        <article class="space-y-2">
                            <div class="flex flex-wrap items-baseline gap-3 font-oswald">
                                <a href=""
                                    class="text-lg sm:text-xl md:text-[22px] tracking-wide font-medium hover:text-customRed-100">
                                    Mutton Alfredo
                                </a>
                                <div class="min-w-0 flex-1 border-t-2 mx-4 border-dashed border-gray-400"></div>
                                <div class="flex items-center gap-4 sm:gap-5 whitespace-nowrap">
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;220</span>
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;330</span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 font-jost">
                                <p class="text-slate-600">Ricotta / Goat Cheese / Beetroot</p>
                                <p class="text-slate-700">Extra Free Juice.</p>
                            </div>
                        </article>

                        <!-- Item -->
                        <article class="space-y-2">
                            <div class="flex flex-wrap items-baseline gap-3 font-oswald">
                                <a href=""
                                    class="text-lg sm:text-xl md:text-[22px] tracking-wide font-medium hover:text-customRed-100">
                                    Beef Alfredo
                                </a>
                                <div class="min-w-0 flex-1 border-t-2 mx-4 border-dashed border-gray-400"></div>
                                <div class="flex items-center gap-4 sm:gap-5 whitespace-nowrap">
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;220</span>
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;330</span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 font-jost">
                                <p class="text-slate-600">Ricotta / Goat Cheese / Beetroot</p>
                                <p class="text-slate-700">Extra Free Juice.</p>
                            </div>
                        </article>
                    </div>

                    <!-- Sea Food -->
                    <div data-pane="sea" class="space-y-6 hidden">
                        <article class="space-y-2">
                            <div class="flex flex-wrap items-baseline gap-3 font-oswald">
                                <a href=""
                                    class="text-lg sm:text-xl md:text-[22px] tracking-wide font-medium hover:text-customRed-100">
                                    Fish & Chips
                                </a>
                                <div class="min-w-0 flex-1 border-t-2 mx-4 border-dashed border-gray-400"></div>
                                <div class="flex items-center gap-4 sm:gap-5 whitespace-nowrap">
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;360</span>
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;550</span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 font-jost">
                                <p class="text-slate-600">
                                    Atlantic / Chips / Salad / Tartare
                                </p>
                                <p class="text-slate-700">Extra Free Juice.</p>
                            </div>
                        </article>
                    </div>

                    <!-- Burger -->
                    <div data-pane="burger" class="space-y-6 hidden">
                        <article class="space-y-2">
                            <div class="flex flex-wrap items-baseline gap-3 font-oswald">
                                <a href=""
                                    class="text-lg sm:text-xl md:text-[22px] tracking-wide font-medium hover:text-customRed-100">
                                    Classic Beef Burger
                                </a>
                                <div class="min-w-0 flex-1 border-t-2 mx-4 border-dashed border-gray-400"></div>
                                <div class="flex items-center gap-4 sm:gap-5 whitespace-nowrap">
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;250</span>
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;380</span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 font-jost">
                                <p class="text-slate-600">
                                    Cheddar / Lettuce / Tomato / Sauce
                                </p>
                                <p class="text-slate-700">Extra Free Juice.</p>
                            </div>
                        </article>
                    </div>

                    <!-- Pizza -->
                    <div data-pane="pizza" class="space-y-6 hidden">
                        <article class="space-y-2">
                            <div class="flex flex-wrap items-baseline gap-3 font-oswald">
                                <a href=""
                                    class="text-lg sm:text-xl md:text-[22px] tracking-wide font-medium hover:text-customRed-100">
                                    Margherita Pizza
                                </a>
                                <div class="min-w-0 flex-1 border-t-2 mx-4 border-dashed border-gray-400"></div>
                                <div class="flex items-center gap-4 sm:gap-5 whitespace-nowrap">
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;420</span>
                                    <span class="text-customRed-100 text-2xl sm:text-3xl font-bold">&#2547;620</span>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 font-jost">
                                <p class="text-slate-600">Tomato / Mozzarella / Basil</p>
                                <p class="text-slate-700">Extra Free Juice.</p>
                            </div>
                        </article>
                    </div>
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

        <img src="./assets/images/border-bttom.png" alt="brush"
            class="absolute -bottom-0.5 left-0 bg-center object-cover bg-no-repeat z-10">
    </section>

    <!-- All dishes -->
    <section class="relative w-full py-16 lg:py-28 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center font-oswald animate__animated animate__fadeInDown">
                <div class="flex items-center justify-center gap-3">
                    <img src="./assets/images/icons/arrow.png" alt="Arrow icon" class="h-3 rotate-180" />
                    <p class="uppercase text-lg md:text-xl text-customRed-100 font-medium">Best deal</p>
                    <img src="./assets/images/icons/arrow.png" alt="Arrow icon" class="h-3" />
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

                                    {{-- Small badge if variations exist --}}
                                    @if ($hasVariations)
                                        <span
                                            class="absolute top-2 right-2 inline-flex items-center px-2 py-1 rounded-full text-[10px] font-semibold bg-black/60 text-white z-10">
                                            Multiple Options
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
                                                    From {{ $money($fromPriceDiscounted) }}
                                                </p>

                                                {{-- Old price compare if discount present --}}
                                                @if ($dish->discount && $dish->discount_type && $fromPriceDiscounted < $fromPriceOriginal)
                                                    <p class="font-medium line-through text-gray-500">
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


    {{-- cart modal --}}
    <livewire:frontend.cart.add-to-cart-modal />


    <!-- Offer promotion section -->
    <section class="relative bg-[url(/assets/images/discount-bg.jpg)] w-full bg-no-repeat bg-center object-cover">

        <!-- Brush -->
        <img src="./assets/images/border-bttom.png" alt="" class="rotate-180 absolute top-0">

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 lg:py-24">
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
                        Delicious Burger
                    </h1>

                    <!-- Paragraph -->
                    <p
                        class="font-jost text-white text-lg leading-7 max-w-xl mx-auto mt-6 animate__animated animate__fadeInDown">
                        It is a long established fact that a reader will be distracted lorem the
                        readable content of a page when looking
                    </p>

                    <!-- Countdown -->
                    <div id="deal-countdown" data-deadline="2025-08-31T23:59:59+06:00"
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
                    <!-- splash/offer badge (optional) -->
                    <div
                        class="absolute right-0 lg:-top-2 md:top-5 md:right-10 lg:right-0 block z-40 animate__animated animate__fadeInRight">
                        <img src="/assets/images/discount-50.png" alt="Save 50%"
                            class="w-32 md:w-48 h-auto select-none pointer-events-none">
                    </div>
                    <!-- burger -->
                    <img src="/assets/images/discount-burger.png" alt="Delicious Burger"
                        class="relative w-full max-w-[680px] mx-auto drop-shadow-[0_30px_40px_rgba(0,0,0,0.45)] z-10 animate__animated animate__fadeInUp" />
                </div>

            </div>
        </div>
    </section>


    {{-- testimonial --}}
    <section class="relative bg-[url('/assets/images/testimonial-bg.jpg')] bg-center bg-cover overflow-hidden w-full">
        <!-- brush top -->
        <img src="/assets/images/border-bttom.png" alt=""
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
                                <img src="./assets/images/feature-dish.jpg" alt="Burger"
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
@endpush
