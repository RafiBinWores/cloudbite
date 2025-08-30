<flux:modal name="{{ $modalName }}" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="xl">{{ $heading }}</flux:heading>

            <flux:text class="mt-2">
                <p class="loading-loose pe-8">{!! $message !!}</p>
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>

            <flux:button wire:click="delete" type="submit" variant="danger" class="cursor-pointer">Delete</flux:button>
        </div>
    </div>
</flux:modal>
