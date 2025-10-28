<div
    x-data="{ open: @entangle('open').live, tempId: @entangle('tempId').live }"
    x-on:address-modal:open.window="open = true; tempId = ($event.detail?.selectedId ?? tempId)"
    x-cloak
>
    @if($open)
        <div class="fixed inset-0 z-[90] flex items-center justify-center" aria-modal="true" role="dialog">
            <div class="absolute inset-0 bg-black/50" @click="$wire.closeModal()"></div>

            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl p-5 md:p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xl font-semibold">Select Address</h3>
                    <button type="button" class="p-2 rounded-lg hover:bg-slate-100" @click="$wire.closeModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- (disabled) use location --}}
                <button type="button" disabled class="flex items-center gap-2 text-red-500 font-medium mb-4 opacity-40 cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 2 4.23 8.46L24 12l-7.77 1.54L12 22l-4.23-8.46L0 12l7.77-1.54z"/></svg>
                    Use my current location
                </button>

                <div class="space-y-2 max-h-[52vh] overflow-y-auto pr-1">
                    @forelse($addresses as $addr)
                        @php $id = (int) $addr->id; @endphp
                        <label
                            class="block rounded-xl border p-4 transition cursor-pointer"
                            :class="Number(tempId) === {{ $id }} ? 'border-red-300 bg-red-50/40 ring-1 ring-red-200' : 'border-slate-200/70 hover:border-red-300'"
                            @click="tempId = {{ $id }}"
                        >
                            <div class="flex items-start gap-3">
                                <input type="radio"
                                       name="address_option"
                                       class="mt-1.5 size-4 shrink-0 accent-red-500"
                                       x-model="tempId"
                                       value="{{ $id }}" />

                                <div class="flex-1 text-sm">
                                    <div class="flex items-center gap-2">
                                        <p class="font-medium">{{ ucfirst($addr->label) }}</p>
                                        @if($addr->is_default)
                                            <span class="text-[11px] px-1.5 py-0.5 rounded border border-green-200 bg-green-50 text-green-700">Default</span>
                                        @endif
                                    </div>
                                    <p class="opacity-80">{{ $addr->address }}</p>
                                    <p class="opacity-70">{{ $addr->city }}{{ $addr->postal_code ? ' - '.$addr->postal_code : '' }}</p>

                                    @if($addr->contact_name || $addr->contact_phone)
                                        <p class="opacity-70 mt-0.5">{{ $addr->contact_name }}{{ $addr->contact_phone ? ' • '.$addr->contact_phone : '' }}</p>
                                    @endif

                                    @if(!empty($addr->note))
                                        <p class="text-xs mt-1 opacity-70">Saved note: {{ $addr->note }}</p>
                                    @endif
                                </div>

                                <div class="shrink-0">
                                    <a href="{{ route('address.create', $addr->id) }}"
                                       class="text-xs text-blue-600 hover:text-blue-700 underline underline-offset-2">Edit</a>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="text-sm opacity-70">No saved addresses yet.</div>
                    @endforelse
                </div>

                <div class="mt-5 flex items-center justify-between">
                    <a href="{{ route('address.create') }}" class="inline-flex items-center gap-2 text-red-500 hover:text-red-600 underline underline-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                        Add New Address
                    </a>

                    <button type="button"
                            class="px-6 py-2 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600 disabled:opacity-60 disabled:cursor-not-allowed"
                            :disabled="!tempId"
                            @click="$wire.select()">
                        Select
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
