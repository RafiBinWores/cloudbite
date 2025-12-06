@php
    use Illuminate\Support\Facades\Storage;
@endphp

{{-- Sub heading + Save --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
        <h3 class="text-lg md:text-xl font-semibold text-slate-900 dark:text-gray-100">
            {{ $pageTitle }}
        </h3>
    </div>

    <div class="flex items-center gap-3">
        {{-- Small logo preview --}}
        <div class="h-10 w-24 flex items-center justify-center border border-slate-200 bg-white rounded">
            @if ($logo)
                <img src="{{ $logo->temporaryUrl() }}" class="max-h-9 max-w-full object-contain" alt="Logo preview">
            @elseif ($logo_path)
                <img src="{{ Storage::disk('public')->url($logo_path) }}" class="max-h-9 max-w-full object-contain"
                    alt="Logo preview">
            @else
                <span class="text-[11px] text-slate-400">No logo</span>
            @endif
        </div>

        <flux:button
    wire:click="save"
    variant="primary"
    class="cursor-pointer !bg-rose-500 hover:!bg-rose-600 border-rose-500"
    wire:loading.attr="disabled"
    wire:target="save"
>
    <span wire:loading.remove wire:target="save">
        Save Template
    </span>

    <span wire:loading wire:target="save">
        Saving...
    </span>
</flux:button>

    </div>
</div>

{{-- MAIN: Preview + Editor SIDE BY SIDE --}}
<div class="flex flex-col lg:flex-row gap-6">

    {{-- LEFT: Email Preview --}}
    <section
        class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl overflow-hidden lg:w-[500px] flex-shrink-0">
        <div
            class="bg-slate-100 dark:bg-neutral-800 px-4 py-2 text-xs text-slate-500 dark:text-neutral-300 flex items-center justify-between">
            <span>Email Preview</span>
        </div>

        <div class="flex justify-center bg-slate-50 dark:bg-neutral-800 px-3 py-4">
            <div
                class="w-full max-w-[600px] bg-white dark:bg-neutral-700 border border-slate-200 dark:border-neutral-600 text-[13px] leading-relaxed font-sans">

                {{-- Header --}}
                <div
                    class="px-5 py-4 border-b border-slate-200 dark:border-neutral-600 flex items-center justify-between">
                    <div class=" gap-3">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" alt="Logo" class="h-8 w-auto">
                        @elseif ($logo_path)
                            <img src="{{ Storage::disk('public')->url($logo_path) }}" alt="Logo" class="h-8 w-auto">
                        @else
                            <div
                                class="h-8 w-8 rounded-full bg-rose-500 flex items-center justify-center text-white text-xs font-bold">
                                CB
                            </div>
                        @endif

                        @if ($main_title !== '')
                            <div class="text-sm font-semibold text-slate-900 dark:text-neutral-100">
                                {{ $main_title }}
                            </div>
                        @endif
                    </div>

                    <span class="text-xs font-medium text-slate-500 dark:text-neutral-300">
                        {{ $activeTab === 'dish_order' ? 'Order #' : 'Booking #' }}{{ $preview['order_code'] ?? '' }}
                    </span>
                </div>

                {{-- Info + Address --}}
                <div
                    class="px-5 pt-4 pb-5 grid grid-cols-1 md:grid-cols-2 gap-4 border-b border-slate-200 dark:border-neutral-600">
                    <div
                        class="bg-rose-50 dark:bg-rose-900/20 rounded-md border border-rose-100 dark:border-rose-700/60">
                        <div class="px-4 py-3 border-b border-rose-100 dark:border-rose-700/60 text-center">
                            @if ($header_title !== '')
                                <div
                                    class="text-xs font-medium text-rose-700 dark:text-rose-300 uppercase tracking-wide">
                                    {{ $header_title }}
                                </div>
                            @endif
                        </div>
                        <div class="px-4 py-3 space-y-1 text-xs text-slate-700 dark:text-neutral-200">
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-neutral-300">Date</span>
                                <span>{{ $preview['date'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-neutral-300">Time</span>
                                <span>{{ $preview['time'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-neutral-300">Payment</span>
                                <span>{{ $preview['payment_method'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-neutral-300">
                                    {{ $activeTab === 'dish_order' ? 'Order Type' : 'Plan Type' }}
                                </span>
                                <span>{{ $preview['order_type'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-md border border-slate-200 dark:border-neutral-600">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-neutral-600">
                            <div
                                class="text-xs font-medium text-slate-700 dark:text-neutral-100 uppercase tracking-wide">
                                Delivery Address
                            </div>
                        </div>
                        <div class="px-4 py-3 text-xs text-slate-700 dark:text-neutral-200 space-y-1">
                            <div class="font-semibold">
                                {{ $preview['customer_name'] }}
                            </div>
                            <div>{{ $preview['customer_phone'] }}</div>
                            <div class="text-slate-600 dark:text-neutral-300">{{ $preview['customer_address'] }}</div>
                            <div class="text-slate-500 dark:text-neutral-400">{{ $preview['customer_city'] }}</div>
                        </div>
                    </div>
                </div>

                {{-- Items / Plan lines --}}
                <div class="px-5 pt-4 pb-2">
                    <div class="text-xs font-semibold text-slate-800 dark:text-neutral-100 mb-2">
                        {{ $activeTab === 'dish_order' ? 'Order Summary' : 'Plan Summary' }}
                    </div>

                    <table class="w-full text-[12px]">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-neutral-600">
                                <th class="text-left py-1 text-slate-500 dark:text-neutral-300 font-medium">
                                    {{ $activeTab === 'dish_order' ? 'Product' : 'Plan Item' }}
                                </th>
                                <th class="text-right py-1 text-slate-500 dark:text-neutral-300 font-medium w-12">Qty
                                </th>
                                <th class="text-right py-1 text-slate-500 dark:text-neutral-300 font-medium w-20">Price
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($preview['items'] as $item)
                                <tr class="border-b border-slate-100 dark:border-neutral-700 align-top">
                                    <td class="py-1.5 pr-2">
                                        <div class="font-medium text-slate-800 dark:text-neutral-100">
                                            {{ $item['name'] }}</div>
                                        <div class="text-[11px] text-slate-500 dark:text-neutral-300">
                                            {{ $item['meta'] }}</div>
                                    </td>
                                    <td class="py-1.5 text-right align-top">{{ $item['qty'] }}</td>
                                    <td class="py-1.5 text-right align-top">
                                        {{ $this->currency($item['total']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3 space-y-1 text-[12px]">
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-neutral-300">Item Price</span>
                            <span>{{ $this->currency($preview['subtotal']) }}</span>
                        </div>
                        @if ($preview['discount'] > 0)
                            <div class="flex justify-between">
                                <span class="text-slate-500 dark:text-neutral-300">Discount</span>
                                <span class="text-rose-600 dark:text-rose-300">
                                    - {{ $this->currency($preview['discount']) }}
                                </span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-neutral-300">Delivery Charge</span>
                            <span>{{ $this->currency($preview['delivery_charge']) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-neutral-300">VAT / Tax</span>
                            <span>{{ $this->currency($preview['vat']) }}</span>
                        </div>
                        <div
                            class="flex justify-between font-semibold border-t border-slate-200 dark:border-neutral-600 pt-1.5 mt-1.5 text-slate-900 dark:text-neutral-100">
                            <span>Total</span>
                            <span>{{ $this->currency($preview['total']) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Body (only if not empty) --}}
                @if ($body !== '')
                    <div
                        class="px-5 pt-4 text-[13px] text-slate-700 dark:text-neutral-200 border-t border-slate-100 dark:border-neutral-700">
                        {!! $body !!}
                    </div>
                @endif

                {{-- Button (only if text is present) --}}
                @if ($button_text !== '')
                    <div class="px-5 pb-4 pt-3">
                        <a href="#"
                            class="inline-flex items-center justify-center rounded-full bg-rose-500 px-5 py-2 text-xs font-medium text-white hover:bg-rose-600">
                            {{ $button_text }}
                        </a>
                    </div>
                @endif

                {{-- Footer --}}
                <div
                    class="px-5 pb-3 text-[11px] text-slate-500 dark:text-neutral-300 border-t border-slate-200 dark:border-neutral-700">
                    @if ($footer_section !== '')
                        <p class="mt-3">
                            {{ $footer_section }}
                        </p>
                    @endif

                    <p class="mt-3">
                        Thanks &amp; Regards,<br>
                        <span class="font-semibold text-slate-700 dark:text-neutral-100">CloudBite</span>
                    </p>

                    {{-- POLICY LINKS --}}
                    <div class="mt-4 flex flex-wrap gap-x-3 gap-y-1">
                        @if ($show_privacy_policy)
                            <a href="#" class="underline hover:text-slate-700 dark:hover:text-neutral-100">Privacy
                                Policy</a>
                        @endif
                        @if ($show_refund_policy)
                            <a href="#" class="underline hover:text-slate-700 dark:hover:text-neutral-100">Refund
                                Policy</a>
                        @endif
                        @if ($show_cancellation_policy)
                            <a href="#"
                                class="underline hover:text-slate-700 dark:hover:text-neutral-100">Cancellation
                                Policy</a>
                        @endif
                        @if ($show_contact_us)
                            <a href="#" class="underline hover:text-slate-700 dark:hover:text-neutral-100">Contact
                                us</a>
                        @endif
                    </div>

                    {{-- SOCIAL LINKS (from company_info + checkboxes) --}}
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @php
                            $socialsRendered = [];

                            if ($show_facebook && !empty($companySocials['facebook'])) {
                                $socialsRendered[] = ['label' => 'Facebook', 'key' => 'facebook'];
                            }
                            if ($show_instagram && !empty($companySocials['instagram'])) {
                                $socialsRendered[] = ['label' => 'Instagram', 'key' => 'instagram'];
                            }
                            if ($show_twitter && !empty($companySocials['twitter'])) {
                                $socialsRendered[] = ['label' => 'Twitter', 'key' => 'twitter'];
                            }
                            if ($show_tiktok && !empty($companySocials['tiktok'])) {
                                $socialsRendered[] = ['label' => 'TikTok', 'key' => 'tiktok'];
                            }
                            if ($show_youtube && !empty($companySocials['youtube'])) {
                                $socialsRendered[] = ['label' => 'YouTube', 'key' => 'youtube'];
                            }
                            if ($show_whatsapp && !empty($companySocials['whatsapp'])) {
                                $socialsRendered[] = ['label' => 'WhatsApp', 'key' => 'whatsapp'];
                            }
                        @endphp

                        @if (count($socialsRendered))
                            <span>Social:</span>
                            @foreach ($socialsRendered as $item)
                                @php
                                    $url = $companySocials[$item['key']];
                                    $href = str_starts_with($url, 'http') ? $url : 'https://' . $url;
                                @endphp
                                <a href="{{ $href }}"
                                    class="underline hover:text-slate-700 dark:hover:text-neutral-100" target="_blank">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        @endif
                    </div>

                    @if ($copyright !== '')
                        <p class="mt-4 text-[10px] text-slate-400 dark:text-neutral-400">
                            {{ $copyright }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- RIGHT: Editor --}}
    <section
        class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5 space-y-6 flex-1">

        {{-- Logo upload --}}
        <div class="space-y-3">
            <h4 class="text-sm font-semibold dark:text-gray-100 flex items-center gap-2">
                <span class="text-slate-500">🖼</span> Logo
            </h4>

            <div class="grid grid-cols-[120px,minmax(0,1fr)] gap-4 items-start">
                <div
                    class="flex items-center justify-center border border-dashed border-slate-300 dark:border-neutral-500 rounded-md bg-slate-50 dark:bg-neutral-800 h-20">
                    @if ($logo)
                        <img src="{{ $logo->temporaryUrl() }}" class="max-h-16 max-w-full object-contain"
                            alt="Logo preview">
                    @elseif ($logo_path)
                        <img src="{{ Storage::disk('public')->url($logo_path) }}"
                            class="max-h-16 max-w-full object-contain" alt="Logo preview">
                    @else
                        <span class="text-[11px] text-slate-400 dark:text-neutral-400 text-center px-2">
                            No logo uploaded
                        </span>
                    @endif
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-medium text-slate-700 dark:text-neutral-100">
                        Choose Logo
                    </label>
                    <input type="file" wire:model="logo" accept="image/*"
                        class="block w-full text-xs text-slate-700 dark:text-neutral-100
                               file:mr-3 file:py-1.5 file:px-3
                               file:rounded-md file:border-0
                               file:text-xs file:font-medium
                               file:bg-slate-100 file:text-slate-700
                               hover:file:bg-slate-200">
                    @error('logo')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Header content --}}
        <div class="space-y-3">
            <h4 class="text-sm font-semibold dark:text-gray-100 flex items-center gap-2">
                <span class="text-slate-500">🧾</span> Header Content
            </h4>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="form-group">
                    <flux:input wire:model.live.debounce.300ms="main_title" label="Main Title (optional)"
                        placeholder="e.g. Order Placed" />
                </div>

                <div class="form-group">
                    <flux:input wire:model.live.debounce.300ms="header_title" label="Info Box Title (optional)"
                        placeholder="e.g. Order Info / Booking Info" />
                </div>
            </div>

            <div class="form-group">
                <flux:textarea wire:model.live.debounce.300ms="body" label="Mail Body (optional)"
                    placeholder="Write your email body here..." rows="6" />
                <p class="mt-1 text-[11px] text-slate-500 dark:text-neutral-300">
                    Supports basic HTML tags like &lt;p&gt;, &lt;strong&gt;, &lt;br&gt;, &lt;ul&gt;, &lt;li&gt;.
                </p>
            </div>
        </div>

        {{-- Button content --}}
        <div class="space-y-3">

            <div>
                <flux:input wire:model.live.debounce.300ms="button_text" label="Button Text (optional)"
                    placeholder="e.g. View Order / View Plan" />
                <p class="mt-1 text-[11px] text-slate-500 dark:text-neutral-300">
                    Link will be set dynamically in the email using the order / meal plan.
                </p>
            </div>
        </div>

        {{-- Footer + policies + socials toggles --}}
        <div class="space-y-4">
            <h4 class="text-sm font-semibold dark:text-gray-100 flex items-center gap-2">
                <span class="text-slate-500">🧩</span> Footer Content & Links
            </h4>

            <div class="form-group">
                <flux:textarea wire:model.live.debounce.300ms="footer_section" label="Footer Section Text (optional)"
                    placeholder="Any extra info or note." rows="3" />
            </div>

            {{-- Policy checkboxes --}}
            <div>
                <p class="text-xs font-semibold text-slate-600 dark:text-neutral-200 mb-2">
                    Policies to show
                </p>
                <div class="flex flex-wrap gap-3 text-xs">
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_privacy_policy"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Privacy Policy</span>
                    </label>
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_refund_policy"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Refund Policy</span>
                    </label>
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_cancellation_policy"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Cancellation Policy</span>
                    </label>
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_contact_us"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Contact Us</span>
                    </label>
                </div>
            </div>

            {{-- Social checkboxes --}}
            <div>
                <p class="text-xs font-semibold text-slate-600 dark:text-neutral-200 mb-2">
                    Social media to show (URLs from Company Info)
                </p>
                <div class="flex flex-wrap gap-3 text-xs">
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_facebook"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Facebook</span>
                    </label>
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_instagram"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Instagram</span>
                    </label>
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_twitter"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>Twitter / X</span>
                    </label>
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_tiktok"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>TikTok</span>
                    </label>
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_youtube"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>YouTube</span>
                    </label>
                    <label class="inline-flex items-center gap-1">
                        <input type="checkbox" wire:model.live="show_whatsapp"
                            class="rounded border-slate-300 text-rose-500 focus:ring-rose-500">
                        <span>WhatsApp</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <flux:input wire:model.live.debounce.300ms="copyright" label="Copyright Text (optional)"
                    placeholder="e.g. Copyright 2025 CloudBite. All rights reserved." />
            </div>
        </div>
    </section>
</div>
