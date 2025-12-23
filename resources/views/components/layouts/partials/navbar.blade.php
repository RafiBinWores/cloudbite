    <!-- Header -->
    <header class="shadow-sm">
        <nav id="siteNav"
            class="sticky top-0 z-40 h-20 px-4 sm:px-6 lg:h-24 lg:px-12 max-w-7xl mx-auto flex items-center justify-between">
            <!-- Hamburger (sm & md) -->
            <button id="menuToggle" aria-label="Toggle menu"
                class="lg:hidden inline-flex items-center gap-2 p-2 rounded-lg">
                <i class="fa-regular fa-bars text-2xl"></i>
            </button>

            <!-- Logo (centered on sm & md) -->
            <a href="/" class="absolute left-1/2 -translate-x-1/2 lg:static lg:translate-x-0">
                <img src="{{ asset($businessSetting->logo_dark) }}" alt="Logo" class="h-10 md:h-14" />
            </a>

            <!-- Desktop Menu -->
            <ul class="hidden lg:flex items-center gap-10 font-oswald text-lg font-medium text-slate-900">
                <li><a href="/" class="hover:opacity-80">Home</a></li>
                <li class="relative group">
                    <a href="#" class="hover:opacity-80 flex items-center gap-1">
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
                            <a href="{{ route('fontDishes.index') }}" wire:navigate
                                class="block px-4 py-2 hover:bg-gray-100 rounded-t-xl">All</a>
                        </li>
                        @foreach ($navbarCategories as $cat)
                            <li>
                                <a href="{{ route('fontDishes.index', ['categories' => [$cat->slug]]) }}" wire:navigate
                                    class="block px-4 py-2 hover:bg-gray-100 rounded-t-xl">{{ $cat->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>

                <li><a href="{{ route('fontDishes.index') }}" class="hover:opacity-80">Dishes</a></li>
                <li><a href="#" class="hover:opacity-80">Contact</a></li>
                <li><a href="#" class="hover:opacity-80">About</a></li>
            </ul>

            <div class="flex items-center gap-3 ml-auto lg:ml-0">
                <a href="{{ route('cart.page') }}" wire:navigate
                    class="relative inline-grid place-items-center size-10 sm:size-12 rounded-full bg-slate-900 cursor-pointer">
                    <i class="fa-solid fa-cart-shopping text-white text-lg sm:text-xl"></i>
                    <livewire:frontend.cart.cart-badge />
                </a>

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
                            <a href="/" wire:navigate class="block py-2 hover:text-rose-600">All</a>
                        </li>
                        @foreach ($navbarCategories as $cat)
                            <li>
                            <a href="{{ route('fontDishes.index', ['categories' => [$cat->slug]]) }}" wire:navigate class="block py-2 hover:text-rose-600">{{ $cat->name }}</a>
                        </li>
                        @endforeach
                    </ul>
                </li>

                <li>
                    <a href="#" class="block px-5 py-4 hover:bg-slate-100">Dishes</a>
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












    {{-- <!-- Header -->
<header class="shadow-sm">
  <nav id="siteNav"
       class="sticky top-0 z-40 h-20 px-4 sm:px-6 lg:h-24 lg:px-12 max-w-7xl mx-auto flex items-center justify-between bg-white">
    <!-- Hamburger (sm & md) -->
    <button id="menuToggle" aria-label="Toggle menu"
            class="lg:hidden inline-flex items-center gap-2 p-2 rounded-lg">
      <i class="fa-regular fa-bars text-2xl"></i>
    </button>

    <!-- Logo (centered on sm & md) -->
    <a href="/" class="absolute left-1/2 -translate-x-1/2 lg:static lg:translate-x-0">
      <img src="{{ asset($businessSetting->logo_dark) }}" alt="Logo" class="h-10 md:h-12" />
    </a>

    <!-- Desktop Search -->
    <form
      action="{{ route('fontDishes.index') }}"
      method="GET"
      class="hidden lg:flex items-center gap-2 mx-6 flex-1 max-w-xl"
      role="search"
      aria-label="Site search"
    >
      <div class="relative w-full">
        <input
          id="desktopNavSearch"
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Search dishes, categories..."
          class="w-full h-11 rounded-xl border border-slate-300 px-10 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
        >
        <!-- icon left -->
        <i class="fa-regular fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
        <!-- clear right -->
        @if(request('search'))
          <a href="{{ route('fontDishes.index') }}"
             class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
             aria-label="Clear search">
            <i class="fa-regular fa-xmark"></i>
          </a>
        @endif
      </div>
      <button
        type="submit"
        class="inline-flex items-center gap-2 h-11 px-4 rounded-xl bg-slate-900 text-white text-sm hover:opacity-90"
        aria-label="Search"
      >
        <i class="fa-regular fa-magnifying-glass"></i>
        <span class="hidden xl:inline">Search</span>
      </button>
      <span
        class="ml-2 hidden 2xl:inline-flex items-center justify-center h-7 px-2 rounded-md border text-xs text-slate-500 border-slate-200"
        title="Press / to focus search"
      >/</span>
    </form>

    <!-- Desktop Menu -->
    <ul class="hidden lg:flex items-center gap-10 font-oswald text-lg font-medium text-slate-900">
      <li><a href="/" class="hover:opacity-80">Home</a></li>

      <li class="relative group">
        <a href="#" class="hover:opacity-80 flex items-center gap-1">
          Categories
          <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </a>
        <!-- Dropdown -->
        <ul class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-200 z-50 py-2">
          <li>
            <a href="{{ route('fontDishes.index') }}" class="block px-4 py-2 hover:bg-gray-100 rounded-t-xl">All</a>
          </li>
          @foreach ($navbarCategories as $cat)
            <li>
              <a href="{{ route('fontDishes.index', ['categories' => [$cat->slug]]) }}" class="block px-4 py-2 hover:bg-gray-100">{{ $cat->name }}</a>
            </li>
          @endforeach
        </ul>
      </li>

      <li><a href="{{ route('fontDishes.index') }}" class="hover:opacity-80">Dishes</a></li>
      <li><a href="#" class="hover:opacity-80">Contact</a></li>
      <li><a href="#" class="hover:opacity-80">About</a></li>
    </ul>

    <!-- Right actions -->
    <div class="flex items-center gap-3 ml-auto lg:ml-0">
      <!-- Mobile Search Trigger -->
      <button
        class="lg:hidden inline-grid place-items-center size-10 rounded-full border border-slate-300"
        aria-label="Open search"
        id="mobileSearchOpen"
      >
        <i class="fa-regular fa-magnifying-glass"></i>
      </button>

      <a href="{{ route('cart.page') }}" wire:navigate
         class="relative inline-grid place-items-center size-10 sm:size-12 rounded-full bg-slate-900 cursor-pointer">
        <i class="fa-solid fa-cart-shopping text-white text-lg sm:text-xl"></i>
        <span class="absolute -top-1 -right-1 grid place-items-center rounded-full bg-red-600 text-white text-[10px] sm:text-xs font-bold w-4 h-4 sm:w-5 sm:h-5">
          <span x-text="badgeCount"></span>
        </span>
      </a>

      <!-- Account icon -->
      <a href="{{ route('account') }}" wire:navigate
         class="grid border rounded-full border-slate-900 w-10 h-10 sm:w-12 sm:h-12 place-items-center hover:bg-slate-900 group duration-200 flex-none">
        <i class="fa-regular fa-user group-hover:text-white"></i>
      </a>
    </div>
  </nav>

  <!-- Mobile Search Bar (slide-down) -->
  <div id="mobileSearchBar"
       class="lg:hidden fixed top-0 left-0 right-0 -translate-y-full transition-transform duration-300 z-50">
    <div class="bg-white border-b border-slate-200 px-4 py-3 flex items-center gap-2">
      <form action="{{ route('fontDishes.index') }}" method="GET" class="flex-1" role="search" aria-label="Mobile search">
        <div class="relative">
          <input
            id="mobileSearchInput"
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search dishes..."
            class="w-full h-11 rounded-lg border border-slate-300 pl-10 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
          >
          <i class="fa-regular fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
          @if(request('search'))
            <a href="{{ route('fontDishes.index') }}"
               class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
               aria-label="Clear search">
              <i class="fa-regular fa-xmark"></i>
            </a>
          @endif
        </div>
      </form>
      <button
        class="shrink-0 inline-grid place-items-center size-10 rounded-lg border border-slate-300"
        aria-label="Close search"
        id="mobileSearchClose"
      >
        <i class="fa-regular fa-chevron-up"></i>
      </button>
    </div>
  </div>

  <!-- Sidebar Menu (sm & md) -->
  <div id="mobileMenu"
       class="fixed top-0 left-0 w-64 h-screen bg-white shadow-lg transform -translate-x-full transition-transform duration-300 lg:hidden z-50"
       x-data="{ catOpen: false }">
    <!-- Header -->
    <div class="p-5 flex justify-between items-center border-b border-slate-200">
      <span class="font-bold text-lg">Menu</span>
      <button id="menuClose" class="text-2xl font-bold" aria-label="Close menu">&times;</button>
    </div>

    <!-- Inline Search inside menu (optional small) -->
    <div class="p-4 border-b border-slate-100">
      <form action="{{ route('fontDishes.index') }}" method="GET" role="search" aria-label="Drawer search">
        <input
          type="search"
          name="search"
          value="{{ request('search') }}"
          placeholder="Search..."
          class="w-full h-10 rounded-lg border border-slate-300 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-slate-300"
        >
      </form>
    </div>

    <!-- Menu Items -->
    <ul class="flex flex-col font-oswald text-lg font-medium text-slate-900 mt-2">
      <li>
        <a href="/" class="block px-5 py-4 hover:bg-slate-100">Home</a>
      </li>

      <!-- Dropdown -->
      <li>
        <button @click="catOpen = !catOpen" class="w-full flex justify-between items-center hover:bg-slate-100">
          <span class="px-5 py-4">Categories</span>
          <svg class="w-4 h-4 transition-transform me-4" :class="catOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <ul x-show="catOpen" x-transition class="pl-8 pr-4 bg-slate-50 text-base text-slate-800">
          <li>
            <a href="{{ route('fontDishes.index') }}" class="block py-2 hover:text-rose-600">All</a>
          </li>
          @foreach ($navbarCategories as $cat)
            <li>
              <a href="{{ route('fontDishes.index', ['categories' => [$cat->slug]]) }}" class="block py-2 hover:text-rose-600">{{ $cat->name }}</a>
            </li>
          @endforeach
        </ul>
      </li>

      <li><a href="{{ route('fontDishes.index') }}" class="block px-5 py-4 hover:bg-slate-100">Dishes</a></li>
      <li><a href="#" class="block px-5 py-4 hover:bg-slate-100">Contact</a></li>
      <li><a href="#" class="block px-5 py-4 hover:bg-slate-100">About</a></li>
    </ul>
  </div>

  <!-- Overlay (for sidebar) -->
  <div id="menuOverlay" class="hidden fixed inset-0 bg-black/40 z-30 lg:hidden"></div>
</header>

@push('scripts')
<script>
  (function () {
    const menu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('menuOverlay');
    const openBtn = document.getElementById('menuToggle');
    const closeBtn = document.getElementById('menuClose');

    const openMenu = () => {
      menu.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    };
    const closeMenu = () => {
      menu.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    };

    if (openBtn) openBtn.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);
    document.addEventListener('keydown', (e) => {
      // ESC closes menu or mobile search
      if (e.key === 'Escape') {
        closeMenu();
        closeMobileSearch();
      }
      // "/" focuses desktop search when not typing in an input/textarea
      if (e.key === '/' && !e.metaKey && !e.ctrlKey && !e.altKey && !e.shiftKey) {
        const active = document.activeElement;
        if (!active || (active.tagName !== 'INPUT' && active.tagName !== 'TEXTAREA' && active.getAttribute('contenteditable') !== 'true')) {
          e.preventDefault();
          const input = document.getElementById('desktopNavSearch');
          if (input) input.focus();
        }
      }
    });

    // Mobile search slide-down
    const mobileSearchBar = document.getElementById('mobileSearchBar');
    const mobileSearchOpen = document.getElementById('mobileSearchOpen');
    const mobileSearchClose = document.getElementById('mobileSearchClose');
    const mobileSearchInput = document.getElementById('mobileSearchInput');

    const openMobileSearch = () => {
      if (!mobileSearchBar) return;
      mobileSearchBar.classList.remove('-translate-y-full');
      // slight delay to allow transition before focusing
      setTimeout(() => mobileSearchInput && mobileSearchInput.focus(), 120);
    };
    const closeMobileSearch = () => {
      if (!mobileSearchBar) return;
      mobileSearchBar.classList.add('-translate-y-full');
    };

    if (mobileSearchOpen) mobileSearchOpen.addEventListener('click', openMobileSearch);
    if (mobileSearchClose) mobileSearchClose.addEventListener('click', closeMobileSearch);

    // ensure overlay is hidden on load for SSR/Livewire navigations
    closeMenu();
  })();
</script>
@endpush --}}
