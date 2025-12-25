<div>
    <!-- Breadcrumb -->
    <div
        class="bg-[url(/assets/images/breadcrumb-bg.jpg)] py-20 md:py-32 bg-no-repeat bg-cover bg-center text-center text-white grid place-items-center font-oswald">
        <h4 class="text-4xl md:text-6xl font-medium">Address</h4>
        <div class="breadcrumbs text-sm mt-3 font-medium">
            <nav class="flex justify-between">
                <ol
                    class="inline-flex items-center mb-3 space-x-3 text-sm text-white [&_.active-breadcrumb]:text-white sm:mb-0">
                    <li class="flex items-center h-full">
                        <a href="/" class="py-1 hover:text-white flex items-center gap-1"><svg class="w-4 h-4"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                                <path
                                    d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                            </svg>
                            Home
                        </a>
                    </li>
                    <span class="mx-2 text-white">/</span>
                    <li><a href="{{ route('account') }}"
                            class="inline-flex items-center py-1 font-normal hover:text-white focus:outline-none">Account</a>
                    </li>
                    <span class="mx-2 text-white">/</span>
                    <li><a
                            class="inline-flex items-center py-1 font-normal rounded cursor-default active-breadcrumb focus:outline-none">Address</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <div class="rounded-2xl border border-slate-200/70 bg-white/70 shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl sm:text-2xl font-semibold text-slate-800">My Address</h2>

                <a href="{{ route('address.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-customRed-100 hover:bg-customRed-200 text-white px-4 py-2 text-sm font-medium transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    Add Address
                </a>
            </div>

            @error('address')
                <div class="mb-3 rounded-lg border border-amber-200 bg-amber-50 text-amber-900 px-3 py-2 text-sm">
                    {{ $message }}</div>
            @enderror

            @if (($addresses ?? collect())->isEmpty())
                <div class="rounded-xl border border-slate-200 bg-slate-50 text-slate-700 p-4">
                    You haven’t added any address yet.
                    <a class="text-red-600 underline underline-offset-2 ml-1"
                        href="{{ route('address.create') }}">Create
                        one</a>.
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @foreach ($addresses as $a)
                        <div class="rounded-2xl border border-slate-200/70 overflow-hidden shadow-sm">
                            {{-- header strip --}}
                            <div class="flex items-center justify-between bg-red-50 px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-slate-800 font-medium">{{ ucfirst($a->label ?? 'Address') }}</span>
                                    {{-- @if (in_array(strtolower((string) $a->label), ['home', 'workplace', 'office', 'others']))
                                    <span class="text-[11px] px-1.5 py-0.5 rounded border border-slate-200 bg-white/70 text-slate-700">
                                        {{ ucfirst($a->label) }}
                                    </span>
                                @endif --}}
                                </div>

                                <div class="flex items-center gap-3">
                                    {{-- Edit --}}
                                    <a href="{{ route('address.create', $a->label) }}" wire:navigate
                                        class="text-slate-800 hover:text-slate-900 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-map-pin-pen-icon lucide-map-pin-pen">
                                            <path d="M17.97 9.304A8 8 0 0 0 2 10c0 4.69 4.887 9.562 7.022 11.468" />
                                            <path
                                                d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                                            <circle cx="10" cy="10" r="3" />
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <button type="button" wire:click="deleteAddress({{ $a->id }})"
                                        onclick="if(!confirm('Delete this address?')) return false;"
                                        class="text-red-500 hover:text-red-600 transition cursor-pointer"
                                        title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="lucide lucide-trash-icon lucide-trash">
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                            <path d="M3 6h18" />
                                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                        </svg>
                                    </button>

                                </div>
                            </div>

                            {{-- body --}}
                            <div class="p-5 space-y-4 text-[15px]">
                                <div class="grid grid-cols-5 gap-3">
                                    <div class="col-span-1 text-slate-600">Name</div>
                                    <div class="col-span-4 text-slate-900">{{ $a->contact_name ?: '—' }}</div>
                                </div>

                                <div class="grid grid-cols-5 gap-3">
                                    <div class="col-span-1 text-slate-600">Phone</div>
                                    <div class="col-span-4 text-slate-900">{{ $a->contact_phone ?: '—' }}</div>
                                </div>

                                <div class="grid grid-cols-5 gap-3">
                                    <div class="col-span-1 text-slate-600">Address</div>
                                    <div class="col-span-4 text-slate-900 truncate" title="{{ $a->address }}">
                                        {{ $a->address }}
                                        @if ($a->city)
                                            , {{ $a->city }}
                                        @endif
                                        @if ($a->postal_code)
                                            , {{ $a->postal_code }}
                                        @endif
                                        @if ($a->country)
                                            , {{ $a->country }}
                                        @endif
                                    </div>
                                </div>

                                @if (!empty($a->note))
                                    <div class="grid grid-cols-5 gap-3">
                                        <div class="col-span-1 text-slate-600">Note</div>
                                        <div class="col-span-4 text-slate-900">{{ $a->note }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
