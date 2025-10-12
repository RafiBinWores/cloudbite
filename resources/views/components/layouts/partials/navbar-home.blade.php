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
            <a href="/" wire:navigate class="absolute left-1/2 -translate-x-1/2 lg:static lg:translate-x-0">
                <img src="{{ asset('assets/images/logos/logo.png') }}" alt="Logo" class="h-10" />
            </a>

            <!-- Desktop Menu -->
            <ul class="hidden lg:flex items-center gap-10 font-oswald text-lg font-medium text-slate-900">
                <li><a href="/" wire:navigate class="hover:text-customRed-200">Home</a></li>
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
                            <a href="/categories/pizza" class="block px-4 py-2 hover:bg-gray-100 rounded-t-xl">Pizza</a>
                        </li>
                        <li>
                            <a href="/categories/burger" class="block px-4 py-2 hover:bg-gray-100">Burger</a>
                        </li>
                        <li>
                            <a href="/categories/crab"
                                class="block px-4 py-2 hover:bg-gray-100 rounded-b-xl">Seafood</a>
                        </li>
                    </ul>
                </li>

                <li><a href="shop.html" class="hover:text-customRed-200">Shop</a></li>
                <li><a href="#" class="hover:text-customRed-200">Contact</a></li>
                <li><a href="#" class="hover:text-customRed-200">About</a></li>
            </ul>

            <div class="flex items-center gap-3 ml-auto lg:ml-0">
                <div class="drawer drawer-end z-[60]" x-data="cartDrawer()" x-cloak>
                    <!-- Toggle -->
                    {{-- <input id="cart-drawer" type="checkbox" class="drawer-toggle" /> --}}

                    <!-- Cart button -->
                    <a href="{{ route('cart.page') }}" wire:navigate class="drawer-content">
                        <label for="cart-drawer"
                            class="relative inline-grid place-items-center size-10 sm:size-12 rounded-full bg-slate-900 cursor-pointer">
                            <i class="fa-solid fa-cart-shopping text-white text-lg sm:text-xl"></i>
                            <span
                                class="absolute -top-1 -right-1 grid place-items-center rounded-full bg-red-600 text-white text-[10px] sm:text-xs font-bold w-4 h-4 sm:w-5 sm:h-5">
                                <span x-text="badgeCount"></span>
                            </span>
                        </label>
                    </a>

                    <!-- Drawer panel -->
                    {{-- <div class="drawer-side">
                        <!-- overlay closes drawer -->
                        <label for="cart-drawer" class="drawer-overlay" aria-label="Close cart"></label>

                        <div class="bg-base-100 text-base-content min-h-full w-full sm:w-96 shadow-2xl flex flex-col">
                            <!-- Header -->
                            <div class="flex items-center justify-between px-5 py-4 border-b">
                                <h2 class="font-oswald text-xl">Your Cart</h2>
                                <label for="cart-drawer" class="p-2 rounded hover:bg-slate-100 cursor-pointer"
                                    aria-label="Close cart">
                                    <i class="fa-solid fa-xmark text-xl"></i>
                                </label>
                            </div>

                            <!-- Items -->
                            <div class="flex-1 overflow-y-auto divide-y">
                                <template x-for="(item, i) in cart" :key="i">
                                    <div class="flex gap-3 p-4">
                                        <img :src="item.image" alt=""
                                            class="w-16 h-16 object-cover rounded">
                                        <div class="flex-1">
                                            <p class="font-medium" x-text="item.name"></p>
                                            <p class="text-sm text-slate-500" x-text="item.desc"></p>
                                            <div class="mt-2 flex items-center justify-between">
                                                <div class="inline-flex items-center border rounded">
                                                    <button type="button" @click="decrement(i)"
                                                        class="px-2 py-1 hover:bg-slate-100">-</button>
                                                    <span class="px-3" x-text="item.qty"></span>
                                                    <button type="button" @click="increment(i)"
                                                        class="px-2 py-1 hover:bg-slate-100">+</button>
                                                </div>
                                                <span class="font-medium">৳<span
                                                        x-text="item.qty * item.price"></span></span>
                                            </div>
                                        </div>
                                        <button type="button" @click="remove(i)"
                                            class="self-start p-2 rounded hover:bg-slate-100" aria-label="Remove item">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </div>
                                </template>

                                <!-- Empty state -->
                                <div x-show="cart.length === 0" class="p-6 text-center text-slate-500">Your cart is
                                    empty.</div>
                            </div>

                            <!-- Footer -->
                            <div class="border-t p-5 space-y-4">
                                <div class="flex items-center justify-between text-base">
                                    <span class="text-slate-600">Subtotal</span>
                                    <span class="font-semibold">৳<span x-text="subtotal()"></span></span>
                                </div>
                                <div class="flex gap-3">
                                    <a href="/cart"
                                        class="flex-1 inline-flex items-center justify-center h-11 rounded-md border border-slate-300 hover:bg-slate-50">
                                        View Cart
                                    </a>
                                    <a href="/checkout"
                                        class="flex-1 inline-flex items-center justify-center h-11 rounded-md bg-[#e80f3a] text-white hover:opacity-95">
                                        Checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>

                <!-- Account icon -->
                <a href="{{ route('account') }}" wire:navigate
                    class="grid border rounded-full border-slate-900 w-10 h-10 sm:w-12 sm:h-12 place-items-center hover:bg-slate-900 group duration-200 flex-none">
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
                            <a href="/categories/pizza" class="block py-2 hover:text-rose-600">Pizza</a>
                        </li>
                        <li>
                            <a href="/categories/burger" class="block py-2 hover:text-rose-600">Burger</a>
                        </li>
                        <li>
                            <a href="/categories/seafood" class="block py-2 hover:text-rose-600">Seafood</a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="#" class="block px-5 py-4 hover:bg-slate-100">Shop</a>
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
