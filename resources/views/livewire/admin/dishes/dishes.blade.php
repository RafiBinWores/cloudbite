<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Dishes') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all of the dishes') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Create modal Button --}}
    <flux:button :href="route('dishes.create')" wire:navigate class="cursor-pointer" icon="add-icon" variant="primary" color="rose">
            Create</flux:button>


    {{-- Delete Confirmation Modal --}}
    <livewire:common.delete-confirmation />




</div>
