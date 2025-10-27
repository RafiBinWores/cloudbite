<div class="max-w-7xl mx-auto px-4 sm:px-6 mb-[50px]">
    <div class="mt-4 bg-customRed-100/15 px-8 py-5 rounded-lg border border-customRed-100/40">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('assets/images/icons/profile-placeholder.jpg') }}" alt="Profile placeholder"
                        class="rounded-full w-24 shadow">
                    <div class="md:border-e md:pe-10 md:border-e-customRed-200">
                        <p class="text-xl font-medium text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-gray-600 text-sm font-medium">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="border-e pe-10 ps-4 border-e-customRed-200 hidden md:block">
                    <p class="text-slate-900"> {{ str_pad($totalOrders, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-gray-600 text-sm font-medium">Total Orders</p>
                </div>
                <div class="hidden ps-4 md:block">
                    <p class="text-slate-900"> {{ str_pad($favoriteCount, 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-gray-600 text-sm font-medium">Favorites</p>
                </div>
            </div>

            {{-- Delete account --}}
            <div>

            </div>
        </div>
    </div>

    {{-- Account options --}}
    <div class="grid grid-cols-3 lg:grid-cols-8 gap-4 mt-6">
        {{-- Profile --}}
        <a href=""
            class="py-5 bg-gray-100/80 rounded-xl h-[130px] flex items-center justify-center flex-col group border border-transparent duration-150 hover:border-customRed-100/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-slate-800 group-hover:text-customRed-100"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-user-round-icon lucide-user-round">
                <circle cx="12" cy="8" r="5" />
                <path d="M20 21a8 8 0 0 0-16 0" />
            </svg>
            <p class="text-slate-900 mt-1.5 group-hover:text-customRed-100">Profile</p>
        </a>

        {{-- Orders --}}
        <a href="{{ route('account.orders') }}" wire:navigate
            class="py-5 bg-gray-100/80 rounded-xl h-[130px] flex items-center justify-center flex-col group border border-transparent duration-150 hover:border-customRed-100/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-slate-800 group-hover:text-customRed-100"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-file-box-icon lucide-file-box">
                <path d="M14.5 22H18a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4" />
                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                <path
                    d="M3 13.1a2 2 0 0 0-1 1.76v3.24a2 2 0 0 0 .97 1.78L6 21.7a2 2 0 0 0 2.03.01L11 19.9a2 2 0 0 0 1-1.76V14.9a2 2 0 0 0-.97-1.78L8 11.3a2 2 0 0 0-2.03-.01Z" />
                <path d="M7 17v5" />
                <path d="M11.7 14.2 7 17l-4.7-2.8" />
            </svg>
            <p class="text-slate-900 mt-1.5 group-hover:text-customRed-100">Orders</p>
        </a>

        {{-- Favorites --}}
        <a href="{{ route('account.favorites') }}" wire:navigate
            class="py-5 bg-gray-100/80 rounded-xl h-[130px] flex items-center justify-center flex-col group border border-transparent duration-150 hover:border-customRed-100/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-slate-800 group-hover:text-customRed-100"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-heart-icon lucide-heart">
                <path
                    d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5" />
            </svg>
            <p class="text-slate-900 mt-1.5 group-hover:text-customRed-100">Favorites</p>
        </a>

        {{-- Notifications --}}
        <a href=""
            class="py-5 bg-gray-100/80 rounded-xl h-[130px] flex items-center justify-center flex-col group border border-transparent duration-150 hover:border-customRed-100/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-slate-800 group-hover:text-customRed-100"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-bell-ring-icon lucide-bell-ring">
                <path d="M10.268 21a2 2 0 0 0 3.464 0" />
                <path d="M22 8c0-2.3-.8-4.3-2-6" />
                <path
                    d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326" />
                <path d="M4 2C2.8 3.7 2 5.7 2 8" />
            </svg>
            <p class="text-slate-900 mt-1.5 group-hover:text-customRed-100">Notifications</p>
        </a>

        {{-- Address --}}
        <a href="{{ route('account.address') }}" wire:navigate
            class="py-5 bg-gray-100/80 rounded-xl h-[130px] flex items-center justify-center flex-col group border border-transparent duration-150 hover:border-customRed-100/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-slate-800 group-hover:text-customRed-100"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-map-pin-house-icon lucide-map-pin-house">
                <path
                    d="M15 22a1 1 0 0 1-1-1v-4a1 1 0 0 1 .445-.832l3-2a1 1 0 0 1 1.11 0l3 2A1 1 0 0 1 22 17v4a1 1 0 0 1-1 1z" />
                <path d="M18 10a8 8 0 0 0-16 0c0 4.993 5.539 10.193 7.399 11.799a1 1 0 0 0 .601.2" />
                <path d="M18 22v-3" />
                <circle cx="10" cy="10" r="3" />
            </svg>
            <p class="text-slate-900 mt-1.5 group-hover:text-customRed-100">Address</p>
        </a>

        {{-- Return policy --}}
        <a href=""
            class="py-5 bg-gray-100/80 rounded-xl h-[130px] flex items-center justify-center flex-col group border border-transparent duration-150 hover:border-customRed-100/60">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-12 text-slate-800 group-hover:text-customRed-100">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.25 9.75h4.875a2.625 2.625 0 0 1 0 5.25H12M8.25 9.75 10.5 7.5M8.25 9.75 10.5 12m9-7.243V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" />
            </svg>

            <p class="text-slate-900 mt-1.5 group-hover:text-customRed-100">Return policy</p>
        </a>

        {{-- Refund policy --}}
        <a href=""
            class="py-5 bg-gray-100/80 rounded-xl h-[130px] flex items-center justify-center flex-col group border border-transparent duration-150 hover:border-customRed-100/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-slate-800 group-hover:text-customRed-100"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-banknote-arrow-down-icon lucide-banknote-arrow-down">
                <path d="M12 18H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5" />
                <path d="m16 19 3 3 3-3" />
                <path d="M18 12h.01" />
                <path d="M19 16v6" />
                <path d="M6 12h.01" />
                <circle cx="12" cy="12" r="2" />
            </svg>
            <p class="text-slate-900 mt-1.5 group-hover:text-customRed-100">Refund policy</p>
        </a>

        {{-- Cancellation policy --}}
        <a href=""
            class="py-5 bg-gray-100/80 rounded-xl h-[130px] flex items-center justify-center flex-col group border border-transparent duration-150 hover:border-customRed-100/60 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-slate-800 group-hover:text-customRed-100"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-file-x2-icon lucide-file-x-2">
                <path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4" />
                <path d="M14 2v4a2 2 0 0 0 2 2h4" />
                <path d="m8 12.5-5 5" />
                <path d="m3 12.5 5 5" />
            </svg>
            <p class="text-slate-900 mt-1.5 group-hover:text-customRed-100">Cancellation policy</p>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button type="submit"
                class="py-5 cursor-pointer bg-gray-100/80 rounded-xl h-[130px] flex items-center justify-center flex-col group border border-transparent duration-150 hover:border-customRed-100/60 text-center w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-slate-800 group-hover:text-customRed-100"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m16 17 5-5-5-5" />
                    <path d="M21 12H9" />
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                </svg>
                <p class="text-slate-900 mt-1.5 group-hover:text-customRed-100">Log Out</p>
            </button>
        </form>

    </div>
</div>
