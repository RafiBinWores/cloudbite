<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1"><img class="w-8"
                src="{{ asset('assets/images/icons/online-shop.png') }}" alt="Bun Icon">{{ __('Bisiness Setup') }}
        </flux:heading>
        <flux:separator variant="subtle" />
    </div>

    {{-- tabs --}}
    <x-tab wire:model="activeTab" class="">
        <x-slot name="items">
            <x-tab-item id="business" label="Business Settings" />
            <x-tab-item id="delivery" label="Delivery Fee Setup" />
            <x-tab-item id="qr_code" label="QR Code" />
        </x-slot>

        <x-tab-content id="business">
            <section
                class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Company Information</h3>

                <form wire:submit="companyInformationSubmit" class="space-y-6">
                    <div class="grid lg:grid-cols-3 gap-4">

                        <div class="form-group">
                            <flux:input wire:model="company_name" label="Company Name"
                                placeholder="e.g. Homies Company" />
                        </div>

                        <div class="form-group">
                            <flux:input wire:model="phone" label="Phone" placeholder="e.g. +8801XXXXXXXXX" />
                        </div>

                        <div class="form-group">
                            <flux:input type="email" wire:model="email" label="Email"
                                placeholder="e.g. cloudbite@example.com" />
                        </div>

                        <div class="form-group">
                            <flux:textarea wire:model="address" label="Address" placeholder="Write your address." />
                        </div>

                        {{-- Logo --}}
                        <div class="form-group">
                            <flux:input type="file" wire:model="logo_upload" label="Logo" accept="image/*" />

                            @if ($logo_upload)
                                <div class="mt-2">
                                    <p class="text-xs text-slate-500 mb-1">New Logo Preview (3:1)</p>
                                    <img src="{{ $logo_upload->temporaryUrl() }}"
                                        class="w-full aspect-[3/1] object-contain rounded-lg border" alt="Logo preview">
                                </div>
                            @elseif ($logo)
                                <div class="mt-2">
                                    <p class="text-xs text-slate-500 mb-1">Current Logo</p>
                                    <img src="{{ asset('storage/' . $logo) }}"
                                        class="w-full aspect-[3/1] object-contain rounded-lg border" alt="Current logo">
                                </div>
                            @else
                                <div
                                    class="mt-2 w-full aspect-[3/1] grid place-items-center border rounded-lg text-slate-400 text-sm">
                                    3:1 Logo placeholder
                                </div>
                            @endif
                        </div>

                        {{-- Favicon --}}
                        <div class="form-group">
                            <flux:input type="file" wire:model="favicon_upload" label="Favicon" accept="image/*" />

                            @if ($favicon_upload)
                                <div class="mt-2">
                                    <p class="text-xs text-slate-500 mb-1">New Favicon Preview (1:1)</p>
                                    <img src="{{ $favicon_upload->temporaryUrl() }}"
                                        class="w-24 h-24 object-contain rounded-md border" alt="Favicon preview">
                                </div>
                            @elseif ($favicon)
                                <div class="mt-2">
                                    <p class="text-xs text-slate-500 mb-1">Current Favicon</p>
                                    <img src="{{ asset('storage/' . $favicon) }}"
                                        class="w-24 h-24 object-contain rounded-md border" alt="Current favicon">
                                </div>
                            @else
                                <div
                                    class="mt-2 w-24 h-24 grid place-items-center border rounded-md text-slate-400 text-xs">
                                    1:1</div>
                            @endif
                        </div>
                    </div>

                    <flux:button type="submit" variant="primary" class="cursor-pointer">
                        {{ $info ? 'Update Information' : 'Save Information' }}
                    </flux:button>
                </form>

            </section>
        </x-tab-content>

        {{-- Tab 2 --}}
        <x-tab-content id="delivery">
            <section
                class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Delivery Charge</h3>

                <form wire:submit="save" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <flux:input type="number" step="0.01" min="0" wire:model.live="base_fee"
                                label="Base Delivery Fee (৳)" placeholder="e.g. 60.00" />
                        </div>

                        <div class="form-group">
                            <flux:field variant="inline">
                                <flux:label>Enable Free Delivery</flux:label>

                                <flux:switch wire:model.live="free_delivery"
                                    description="When enabled, shipping is free and the minimum is locked to 0." />

                                <flux:error name="free_delivery" />
                            </flux:field>
            
                        </div>
                    </div>

                    {{-- Use native input to guarantee disabled binding works in all cases --}}
                        <div class="form-group">
                            <flux:input type="number" step="0.01" min="0" wire:model.live.debounce.500ms="free_minimum"
                                label="Free Delivery Minimum Order (৳)" placeholder="e.g. 500.00" :disabled="$free_delivery" />
                            @if ($free_delivery)
                                <p class="text-sm text-gray-400 mt-1">Free Delivery is ON — minimum is fixed at 0.
                                    Toggle off to edit.</p>
                            @else
                                <p class="text-sm text-gray-400 mt-1">Set a threshold: orders ≥ this amount get free
                                    delivery.</p>
                            @endif
                        </div>

                    <flux:button type="submit" variant="primary">Save Delivery Settings</flux:button>
                </form>
            </section>
        </x-tab-content>

        {{-- QR Code --}}
        <x-tab-content id="qr_code"> Tab 3 </x-tab-content>
    </x-tab>
</div>
