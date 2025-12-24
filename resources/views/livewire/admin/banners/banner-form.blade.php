<div>
    <flux:modal name="banner-modal" class="md:w-full">
        <form wire:submit="submit" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $isView ? 'Banner details' : ($bannerId ? 'Update' : 'Create') . ' Banner' }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ $isView ? 'View banner details information' : ($bannerId ? 'Update' : 'Add') . '  banner details information.' }}
                </flux:text>
            </div>

            {{-- Thumbnail --}}
            <section class="space-y-2">
                <h3 class="text-base font-semibold">Thumbnail</h3>

                <div x-data="{
                        isOver: false,
                        isUploading: false,
                        progress: 0,
                        pick() { $refs.file.click() }
                    }"
                    x-on:dragover.prevent="isOver = true"
                    x-on:dragleave.prevent="isOver = false"
                    x-on:drop.prevent="isOver = false"
                    x-on:livewire-upload-start="isUploading = true"
                    x-on:livewire-upload-finish="isUploading = false; progress = 0"
                    x-on:livewire-upload-error="isUploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    class="w-full">

                    <input x-ref="file" type="file" accept="image/*" class="hidden"
                        wire:model.live="image"
                        wire:key="image-input-{{ $bannerId ?? 'new' }}">

                    <div
                        class="relative w-full h-36 rounded-2xl border-2 border-dashed transition grid place-items-center cursor-pointer
                            {{ $errors->has('image') ? 'border-red-500' : 'border-slate-300' }}"
                        x-bind:class="isOver ? 'border-slate-400 bg-slate-50' : ''"
                        @click="pick()"
                    >
                        @if (!$image && !$existingImage)
                            <div class="flex flex-col items-center gap-2 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-cloud-upload-icon lucide-cloud-upload">
                                    <path d="M12 13v8" />
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
                                    <path d="m8 17 4-4 4 4" />
                                </svg>
                                <span class="text-sm text-slate-500">Upload Image</span>
                                <p class="text-xs text-slate-500">png/jpg/jpeg/svg/webp • up to 5MB</p>
                            </div>
                        @endif

                        <template x-if="isUploading">
                            <div class="absolute inset-0 rounded-2xl bg-white/60 grid place-items-end">
                                <div class="w-full h-1 bg-slate-200">
                                    <div class="h-1 bg-slate-500" :style="`width:${progress}%`"></div>
                                </div>
                            </div>
                        </template>

                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}"
                                class="absolute inset-0 w-full h-full object-cover rounded-2xl" alt="preview">
                            <button type="button" wire:click="clearImage" @click.stop
                                class="absolute top-2 right-2 rounded-full bg-white/90 hover:bg-white px-2 py-1 text-xs text-slate-700 shadow cursor-pointer">
                                Remove
                            </button>
                        @elseif ($existingImage)
                            <img src="{{ asset($existingImage) }}"
                                class="absolute inset-0 w-full h-full object-cover rounded-2xl" alt="current">
                            <button type="button" @click.stop="pick()"
                                class="absolute top-2 right-2 rounded-full bg-white/90 hover:bg-white px-2 py-1 text-xs text-slate-700 shadow">
                                Change
                            </button>
                        @endif
                    </div>

                    @error('image')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- Title --}}
            <div class="form-group">
                <x-input
                    label="Title*"
                    wire:model.live.debounce.300ms="title"
                    class="rounded-lg !bg-white/10 !py-[9px]
                        {{ $errors->has('title') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                    placeholder="e.g. Eid Special"
                />
            </div>

            {{-- Description --}}
            <div class="form-group">
                <x-textarea
                    label="Description"
                    rows="3"
                    wire:model.live.debounce.300ms="description"
                    class="rounded-lg !bg-white/10 !py-[9px]
                        {{ $errors->has('description') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                    placeholder="Short details for this banner..."
                />
            </div>

            {{-- Banner Type --}}
            <div class="form-group">
                <x-select
                    wire:model.live="is_slider"
                    label="Banner Type*"
                    :options="[
                        ['id' => 0, 'name' => 'Single Banner (Show one image)'],
                        ['id' => 1, 'name' => 'Banner Slider (3:1 image ratio)'],
                    ]"
                    class="rounded-lg !bg-white/10 !py-[9px]
                        {{ $errors->has('is_slider') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                    clearable="false"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Item Type --}}
                <div class="form-group">
                    <x-select
                        wire:model.live="item_type"
                        label="Item Type*"
                        :options="[
                            ['id' => 'category', 'name' => 'Category'],
                            ['id' => 'dish', 'name' => 'Dish'],
                        ]"
                        class="rounded-lg !bg-white/10 !py-[9px]
                            {{ $errors->has('item_type') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                    />
                </div>

                {{-- Item (FIXED with wire:key) --}}
                <div class="form-group" wire:key="banner-item-select-{{ $item_type }}">
                    @php
                        if ($item_type === 'category') {
                            $items = \App\Models\Category::where('status', 'Active')
                                ->orderBy('name', 'ASC')
                                ->get(['id', 'name', 'image'])
                                ->map(fn ($c) => [
                                    'id' => $c->id,
                                    'name' => $c->name,
                                    'avatar' => $c->image
                                        ? \Illuminate\Support\Facades\Storage::url($c->image)
                                        : asset('assets/images/placeholders/cat-placeholder.png'),
                                ])->values()->toArray();

                            $itemLabel = 'Category*';
                        } else {
                            $items = \App\Models\Dish::where('visibility', 'Yes')
                                ->orderBy('title', 'ASC')
                                ->get(['id', 'title', 'thumbnail'])
                                ->map(fn ($d) => [
                                    'id' => $d->id,
                                    'name' => $d->title,
                                    'avatar' => $d->thumbnail
                                        ? \Illuminate\Support\Facades\Storage::url($d->thumbnail)
                                        : asset('assets/images/placeholders/cat-placeholder.png'),
                                ])->values()->toArray();

                            $itemLabel = 'Dish*';
                        }
                    @endphp

                    <x-select
                        wire:model.live="item_id"
                        :label="$itemLabel"
                        :options="$items"
                        class="rounded-lg !bg-white/10 !py-[9px]
                            {{ $errors->has('item_id') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                        searchable
                    />
                </div>
            </div>

            {{-- Start/End date time --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <x-input
                        type="datetime-local"
                        label="Start Date & Time"
                        wire:model.live="start_at"
                        class="rounded-lg !bg-white/10 !py-[9px]
                            {{ $errors->has('start_at') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                    />
                </div>

                <div class="form-group">
                    <x-input
                        type="datetime-local"
                        label="End Date & Time"
                        wire:model.live="end_at"
                        class="rounded-lg !bg-white/10 !py-[9px]
                            {{ $errors->has('end_at') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                    />
                </div>
            </div>

            {{-- Status --}}
            <div class="form-group">
                <x-select
                    wire:model.live="status"
                    label="Status*"
                    :options="[
                        ['id' => 'active', 'name' => 'Active'],
                        ['id' => 'disable', 'name' => 'Disable'],
                    ]"
                    class="rounded-lg !bg-white/10 !py-[9px]
                        {{ $errors->has('status') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                    clearable="false"
                />
            </div>

            {{-- Submit & Cancel --}}
            <div class="flex">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button icon="cross-icon" class="cursor-pointer me-2">Cancel</flux:button>
                </flux:modal.close>

                @if (!$isView)
                    <flux:button type="submit" icon="fileAdd-icon" class="cursor-pointer" variant="primary"
                        color="rose" wire:loading.attr="disabled" wire:target="submit">
                        <span wire:loading.remove wire:target="submit">{{ $bannerId ? 'Update' : 'Create' }}</span>
                        <span wire:loading wire:target="submit">{{ $bannerId ? 'Updating...' : 'Creating...' }}</span>
                    </flux:button>
                @endif
            </div>

        </form>
    </flux:modal>
</div>
