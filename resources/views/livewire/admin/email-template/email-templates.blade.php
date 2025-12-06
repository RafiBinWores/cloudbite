@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1">
            <img class="w-8" src="{{ asset('assets/images/icons/mail.png') }}" alt="Email Icon">
            {{ __('Email Templates') }}
        </flux:heading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Tabs (custom, Livewire-controlled) --}}
    <div class="mb-6 inline-flex border-b border-slate-200 bg-white dark:border-neutral-700 dark:bg-neutral-800 w-full">
        <button
            type="button"
            wire:click="setTab('dish_order')"
            class="pb-3 text-sm transition font-semibold me-6 cursor-pointer
                {{ $activeTab === 'dish_order'
                    ? 'border-b border-rose-500 text-accent'
                    : 'text-slate-600 dark:text-neutral-300 hover:border-slate-100 dark:hover:border-neutral-700' }}"
        >
            Dish Order Email
        </button>

        <button
            type="button"
            wire:click="setTab('meal_plan_order')"
            class="pb-3 text-sm transition font-medium cursor-pointer
                {{ $activeTab === 'meal_plan_order'
                    ? 'border-b border-rose-500 text-accent'
                    : 'text-slate-600 dark:text-neutral-300 hover:border-b hover:border-slate-100 dark:hover:border-neutral-600' }}"
        >
            Meal Plan Order Email
        </button>
    </div>

    {{-- Shared editor / preview --}}
    @include('livewire.admin.email-template._editor')
</div>
