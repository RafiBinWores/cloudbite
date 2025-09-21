<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1"><img class="w-8"
                src="{{ asset('assets/images/icons/delivery-man.png') }}" alt="Delivery man Icon">{{ __('Delivery Man') }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Create Button --}}
    <flux:button :href="route('delivery.create')" wire:navigate class="cursor-pointer" icon="add-icon" variant="primary"
        color="rose">
        Add New</flux:button>

    {{-- Delete Confirmation Modal --}}
    <livewire:common.delete-confirmation />

</div>
