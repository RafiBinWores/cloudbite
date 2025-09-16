    <flux:modal name="coupon-modal" class="md:w-[800px]">
        <form wire:submit="submit" class="space-y-6">
                        <div>
                <flux:heading size="lg">
                    {{ $isView ? 'Coupon details' : ($couponId ? 'Update' : 'Create') . ' Coupon' }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ $isView ? 'View coupon details information' : ($couponId ? 'Update' : 'Add') . '  coupon details information.' }}
                </flux:text>
            </div>


            <div class="grid md:grid-cols-2 grid-cols-1 gap-3">
                {{-- Type --}}
                <div class="form-group">
                    <flux:select :disabled="$isView" wire:model="coupon_type" label="Coupon Type" placeholder="Choose coupon type...">
                        <flux:select.option value="default">Default</flux:select.option>
                        <flux:select.option value="first_order">First Order</flux:select.option>
                    </flux:select>
                </div>

                {{-- Title --}}
                <div class="form-group">
                    <flux:input :disabled="$isView" wire:model="title" label="Title" placeholder="e.g. Eid Special" />
                </div>
            </div>

            <div class="grid md:grid-cols-2 grid-cols-1 gap-3">
                {{-- Coupon code --}}
                <div class="form-group">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-gray-800 dark:text-white">
                            Coupon Code
                        </label>

                        <button type="button" wire:click="generateCouponCode"
                            class="text-accent text-sm cursor-pointer">
                            Generate code
                        </button>
                    </div>

                    <flux:input :disabled="$isView" :disabled="$isView" wire:model="coupon_code" label="" placeholder="e.g. Aw78cUeA" />
                </div>

                {{-- for single user --}}
                <div class="form-group">
                    <flux:input :disabled="$isView" :disabled="$isView" type="number" min="0" wire:model="same_user_limit" label="Limit For Same User"
                        placeholder="e.g. 10" />
                </div>
            </div>

            <div class="grid md:grid-cols-2 grid-cols-1 gap-3">
                {{-- Discount Type --}}
                <div class="form-group">
                    <flux:select :disabled="$isView" wire:model.live="discount_type" label="Discount Type" placeholder="Choose one...">
                        <flux:select.option value="percent">Percent</flux:select.option>
                        <flux:select.option value="amount">Amount</flux:select.option>
                    </flux:select>
                </div>

                {{-- Discount --}}
                <div class="form-group">
                    <flux:input :disabled="$isView" :disabled="$isView" type="number" min="0" wire:model.live="discount"
                        :label="$discount_type === 'amount' ? 'Discount Amount' : 'Discount Percent'"
                        placeholder="e.g. 10" />
                </div>
            </div>

            <div class="grid md:grid-cols-2 grid-cols-1 gap-3">
                {{-- Discount Type --}}
                <div class="form-group">
                    <flux:input :disabled="$isView" type="date" min="0" wire:model="start_date" label="Start Date" min="{{ now()->toDateString() }}"
                        placeholder="e.g. 10" />
                </div>

                {{-- Discount --}}
                <div class="form-group">
                    <flux:input :disabled="$isView" type="date" min="0" wire:model="expire_date" label="Expire Date" min="{{ now()->toDateString() }}"
                        placeholder="e.g. 10" />
                </div>
            </div>

            <div class="grid md:grid-cols-2 grid-cols-1 gap-3">
                {{-- Minimum Purchase --}}
                <div class="form-group">
                    <flux:input :disabled="$isView" type="number" min="0" wire:model="minimum_purchase" label="Minimum Purchase"
                        placeholder="e.g. 500" />
                </div>

                   {{-- Status --}}
                <div class="form-group">
                    <flux:select :disabled="$isView" wire:model="status" label="Status" placeholder="Choose status...">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="disable">Disable</flux:select.option>
                    </flux:select>
                </div>

            </div>

            {{-- Submit & Cancel button --}}
            <div class="flex">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button icon="cross-icon" class="cursor-pointer me-2">Cancel</flux:button>
                </flux:modal.close>

                @if (!$isView)
                <flux:button type="submit" icon="fileAdd-icon" class="cursor-pointer" variant="primary" color="rose"
                    wire:loading.attr="disabled" wire:target="submit">
                    <span wire:loading.remove wire:target="submit">{{ $couponId ? 'Update' : 'Create' }}</span>
                    <span wire:loading wire:target="submit">{{ $couponId ? 'Updating...' : 'Creating...' }}</span>
                </flux:button>
                @endif
            </div>
        </form>
    </flux:modal>
