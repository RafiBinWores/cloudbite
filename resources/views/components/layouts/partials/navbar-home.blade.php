    <!-- Header -->
    <header class="lg:max-w-7xl mx-auto lg:pt-1 fixed inset-x-0 lg:top-2 z-50">
        <nav id="siteNav"
            class="sticky top-0 z-40 h-20 px-4 sm:px-6 bg-white lg:h-24 lg:px-12 lg:bg-[linear-gradient(0deg,rgba(255,255,255,0.8)_0%,#ECF0F3_100%)] lg:rounded-2xl shadow-md flex items-center justify-between lg:opacity-95">
            <!-- Hamburger (sm & md) -->
            <button id="menuToggle" aria-label="Toggle menu"
                class="lg:hidden inline-flex items-center gap-2 p-2 rounded-lg">
                <i class="fa-regular fa-bars text-2xl"></i>
            </button>

            <!-- Logo (centered on sm & md) -->
            <a href="/" class="absolute left-1/2 -translate-x-1/2 lg:static lg:translate-x-0">
                <img src="{{ asset($businessSetting->logo_dark) }}" alt="Logo" class="h-14" />
            </a>

            <!-- Desktop Menu -->
            <ul class="hidden lg:flex items-center gap-10 font-oswald text-lg font-medium text-slate-900">
                <li><a href="/" class="hover:text-customRed-200">Home</a></li>
                <li class="relative group">
                    <a href="#" class="hover:text-customRed-200 flex items-center gap-1">
                        Categories
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    <!-- Dropdown -->
                    <ul
                        class="absolute left-0 mt-2 w-48 bg-white rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-200 z-50">
                        <li>
                            <a href="{{ route('fontDishes.index') }}" wire:navigate class="block px-4 py-2 hover:bg-gray-100 rounded-t-xl">All</a>
                        </li>
                        @foreach ($navbarCategories as $cat)
                            <li>
                            <a href="{{ route('fontDishes.index', ['categories' => [$cat->slug]]) }}" wire:navigate class="block px-4 py-2 hover:bg-gray-100 rounded-t-xl">{{ $cat->name }}</a>
                        </li>
                        @endforeach
                        
                    </ul>
                </li>

                <li><a href="{{ route('fontDishes.index') }}" class="hover:text-customRed-200">Dishes</a></li>
                <li><a href="#" class="hover:text-customRed-200">Contact</a></li>
                <li><a href="#" class="hover:text-customRed-200">About</a></li>
            </ul>

            <div class="flex items-center gap-3 ml-auto lg:ml-0">
                <div class="drawer drawer-end z-[60]" x-data="cartDrawer" x-cloak>
                    <!-- Cart button -->
                    <a href="{{ route('cart.page') }}" wire:navigate class="drawer-content">
                        <label for="cart-drawer"
                            class="relative inline-grid place-items-center size-9 sm:size-12 rounded-full bg-slate-900 cursor-pointer">
                            <i class="fa-solid fa-cart-shopping text-white text-lg sm:text-xl"></i>
                            <livewire:frontend.cart.cart-badge />
                        </label>
                    </a>
                </div>

                <livewire:frontend.partials.notification-bell />

                <!-- Account icon -->
                <a href="{{ route('account') }}" wire:navigate
                    class="grid border rounded-full border-slate-900 size-9 sm:w-12 sm:h-12 place-items-center hover:bg-slate-900 group duration-200 flex-none">
                    <i class="fa-regular fa-user group-hover:text-white"></i>
                </a>
            </div>
        </nav>

        <!-- Sidebar Menu (sm & md) -->
        <div id="mobileMenu"
            class="fixed top-0 left-0 w-64 h-screen bg-white shadow-lg transform -translate-x-full transition-transform duration-300 lg:hidden z-50"
            x-data="{ catOpen: false }">
            <!-- Header -->
            <div class="p-5 flex justify-between items-center border-b border-slate-200">
                <span class="font-bold text-lg">Menu</span>
                <button id="menuClose" class="text-2xl font-bold">&times;</button>
            </div>

            <!-- Menu Items -->
            <ul class="flex flex-col font-oswald text-lg font-medium text-slate-900 mt-4">
                <li>
                    <a href="/" wire:navigate class="block px-5 py-4 hover:bg-slate-100">Home</a>
                </li>

                <!-- Dropdown -->
                <li>
                    <button @click="catOpen = !catOpen"
                        class="w-full flex justify-between items-center hover:bg-slate-100">
                        <span class="px-5 py-4">Categories</span>
                        <svg class="w-4 h-4 transition-transform me-4" :class="catOpen ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Inline Dropdown -->
                    <ul x-show="catOpen" x-transition class="pl-8 pr-4 bg-slate-50 text-base text-slate-800">
                        <li>
                            <a href="{{ route('fontDishes.index') }}" wire:navigate class="block py-2 hover:text-rose-600">All</a>
                        </li>
                        @foreach ($navbarCategories as $cat)
                            <li>
                            <a href="{{ route('fontDishes.index', ['categories' => [$cat->slug]]) }}" wire:navigate class="block py-2 hover:text-rose-600">{{ $cat->name }}</a>
                        </li>
                        @endforeach
                    </ul>
                </li>

                <li>
                    <a href="{{ route('fontDishes.index') }}" class="block px-5 py-4 hover:bg-slate-100">Dishes</a>
                </li>
                <li>
                    <a href="#" class="block px-5 py-4 hover:bg-slate-100">Contact</a>
                </li>
                <li>
                    <a href="#" class="block px-5 py-4 hover:bg-slate-100">About</a>
                </li>
            </ul>
        </div>

        <!-- Overlay (for sidebar) -->
        <div id="menuOverlay" class="hidden fixed inset-0 bg-black/40 z-30 lg:hidden"></div>
    </header>
    <!-- Header end -->
