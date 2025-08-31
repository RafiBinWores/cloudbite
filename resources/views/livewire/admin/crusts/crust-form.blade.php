    <flux:modal name="crust-modal" class="md:w-[32rem]">
        <form wire:submit="submit" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $isView ? 'Crust details' : ($crustId ? 'Update' : 'Create') . ' Crust' }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ $isView ? 'View crust details information' : ($crustId ? 'Update' : 'Add') . '  crust details information.' }}
                </flux:text>
            </div>

            {{-- Name --}}
            <div class="form-group">
                <flux:input :disabled="$isView" wire:model="name" label="Name" placeholder="Crust" />
            </div>

            {{-- Price --}}
            <div class="form-group">
                <flux:input :disabled="$isView" wire:model="price" label="Price" type="number" min="0" placeholder="Price" />
            </div>

            {{-- Status select --}}
            <div class="form-group">
                <flux:select :disabled="$isView" wire:model="status" label="Status" placeholder="Choose status...">
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="disable">Disable</flux:select.option>
                </flux:select>
            </div>

            {{-- Submit & Cancel button --}}
            <div class="flex">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost" icon="cross-icon" class="cursor-pointer me-2">Cancel</flux:button>
                </flux:modal.close>

                @if (!$isView)
                    <flux:button type="submit" icon="fileAdd-icon" class="cursor-pointer" color="rose"
                        wire:loading.attr="disabled" wire:target="submit">
                        <span wire:loading.remove wire:target="submit">{{ $crustId ? 'Update' : 'Create' }}</span>
                        <span wire:loading wire:target="submit">{{ $crustId ? 'Updating...' : 'Creating...' }}</span>
                    </flux:button>
                @endif
            </div>
        </form>
    </flux:modal>
