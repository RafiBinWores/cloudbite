<div>

    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-3" level="1">{{ __('Add New Dish') }}</flux:heading>
        <flux:breadcrumbs class="mb-6">
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" separator="slash" wire:navigate />
            <flux:breadcrumbs.item href="{{ route('dishes.index') }}" separator="slash" wire:navigate>
                Dishes
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Add New Dish</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="submit" class="mt-6 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT COLUMN -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Name & Short Description -->
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Name and Description</h3>

                    <div class="space-y-6">
                        {{-- Title --}}
                        <div class="form-group">
                            <x-input label="Dish Title*" wire:model.live="title"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('title') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                                placeholder="eg. Chicken Fry" />
                        </div>

                        {{-- Short description only --}}
                        <div class="from-group md:col-span-2">
                            <x-textarea label="Short Description*" rows="3" wire:model.live="short_description"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('short_description') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                                placeholder="Mention about what your dish include or what item u will provide for the menu" />
                        </div>
                    </div>
                </section>

                <!-- Category -->
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Product Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Categories --}}
                        <div class="form-group">

                            @php
                                use Illuminate\Support\Facades\Storage;

                                $cat = App\Models\Category::where('status', 'active')->orderBy('name', 'ASC')->get()->map(
                                    fn($c) => [
                                        'id' => $c->id,
                                        'name' => $c->name,
                                        'avatar' => $c->image
                                            ? Storage::url($c->image)
                                            : asset('assets/images/placeholders/cat-placeholder.png'),
                                    ],
                                );
                            @endphp

                            <x-select wire:model.live="category_id" label="Category*" :options="$cat"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('category') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable />

                        </div>

                        {{-- Cuisines --}}
                        <div class="form-group">

                            @php
                                $cuisines = App\Models\Cuisine::where('status', 'active')->orderBy('name', 'ASC')->get()->map(
                                    fn($c) => [
                                        'id' => $c->id,
                                        'name' => $c->name,
                                        'image' => $c->image
                                            ? Storage::url($c->image)
                                            : asset('assets/images/placeholders/cat-placeholder.png'),
                                    ],
                                )
                            @endphp

                            <x-select wire:model.live="cuisine_id" label="Cuisine*" :options="$cuisines"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('cuisine') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable />

                        </div>

                        {{-- Tags --}}
                        <div class="form-group md:col-span-2">

                            @php
                                $tags = \App\Models\Tag::where('status', 'active')->pluck('name')->toArray();
                            @endphp

                            <x-select wire:model.live="tags" label="Tags" :options="$tags"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('tags') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable multiple />

                        </div>

                        {{-- Related Dish --}}
                        <div class="form-group md:col-span-2">

                            @php
                                $dishes = App\Models\Dish::where('visibility', 'Yes')->get(['id', 'title', 'thumbnail'])->map(
                                    fn($c) => [
                                        'id' => $c->id,
                                        'name' => $c->title,
                                        'avatar' => $c->thumbnail
                                            ? Storage::url($c->thumbnail)
                                            : asset('assets/images/placeholders/cat-placeholder.png'),
                                    ],
                                );
                            @endphp

                            <x-select type="number" wire:model.live="related_dishes" label="Related Dish" :options="$dishes"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('related_dishes') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable multiple />

                        </div>
                    </div>
                </section>

                <!-- Dish Pricing -->
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Dish Pricing</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Price --}}
                        <div class="form-group md:col-span-2">
                            <x-input type="number" min="0" step="any" label="Base Price (Tk)*" wire:model.live="price"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('price') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                placeholder="Price" />
                            <small class="text-xs text-neutral-500 dark:text-neutral-300">
                                This is default price if no variation is selected.
                            </small>
                        </div>

                        {{-- discount type --}}
                        <div class="from-group">
                            <x-select wire:model.live="discount_type" label="Discount type" :options="['percent', 'amount']"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('discount_type') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}" />
                        </div>

                        {{-- Discount --}}
                        <div class="form-group">
                            <x-input type="number" min="0" step="any" label="Discount" wire:model.live="discount"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('discount') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                placeholder="Discount" />
                        </div>

                        {{-- Vat --}}
                        <div class="form-group md:col-span-2">
                            <x-input type="number" min="0" label="Vat (%)" wire:model.live="vat"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('vat') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                placeholder="Vat" />
                        </div>

                    </div>
                </section>

                <!-- Variations Section -->
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold dark:text-gray-100">Variations</h3>

                        <flux:button type="button" variant="filled" color="rose" icon="plus-icon"
                            wire:click="addVariationGroup" class="cursor-pointer">
                            Add Variation
                        </flux:button>
                    </div>

                    <div class="space-y-4">
                        @forelse($variations as $vIndex => $variation)
                            <div class="border border-gray-200 dark:border-neutral-600 rounded-xl p-4 bg-neutral-50 dark:bg-neutral-600">
                                <div class="flex items-start gap-3">
                                    <div class="flex-1">
                                        <x-input
                                            label="Variation Name* (ex: Size, Crust, Drink)"
                                            wire:model.live="variations.{{ $vIndex }}.name"
                                            placeholder="Size"
                                            class="rounded-lg !bg-white/10 !py-[9px]" />
                                    </div>

                                    <flux:button
                                        type="button"
                                        icon="trash-icon"
                                        variant="filled"
                                        class="mt-6 cursor-pointer"
                                        wire:click="removeVariationGroup({{ $vIndex }})">
                                        Remove
                                    </flux:button>
                                </div>

                                {{-- Options under variation --}}
                                <div class="mt-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <p class="font-medium text-sm dark:text-gray-100">Options</p>

                                        <button type="button"
                                            wire:click="addVariationOption({{ $vIndex }})"
                                            class="text-sm text-rose-600 hover:underline cursor-pointer">
                                            + Add Option
                                        </button>
                                    </div>

                                    @foreach($variation['options'] ?? [] as $oIndex => $option)
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                            <div class="md:col-span-7">
                                                <x-input
                                                    label="Option Name (ex: Small, Medium)"
                                                    wire:model.live="variations.{{ $vIndex }}.options.{{ $oIndex }}.label"
                                                    placeholder="Small"
                                                    class="rounded-lg !bg-white/10 !py-[9px]" />
                                            </div>

                                            <div class="md:col-span-4">
                                                <x-input type="number" min="0" step="any"
                                                    label="Price (Tk)"
                                                    wire:model.live="variations.{{ $vIndex }}.options.{{ $oIndex }}.price"
                                                    placeholder="0"
                                                    class="rounded-lg !bg-white/10 !py-[9px]" />
                                            </div>

                                            <div class="md:col-span-1">
                                                <button type="button"
                                                    wire:click="removeVariationOption({{ $vIndex }}, {{ $oIndex }})"
                                                    class="h-10 w-full rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-neutral-700 dark:hover:bg-neutral-800 cursor-pointer flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-x-icon lucide-circle-x"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach

                                    @error("variations.$vIndex.options")
                                        <p class="text-red-500 text-sm">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-neutral-500 dark:text-neutral-300">
                                No variations added yet. Click “Add Variation” to create size/price options.
                            </p>
                        @endforelse
                    </div>
                </section>

                {{-- Meta info --}}
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <div class="flex items-center gap-1 mb-4">
                        <h3 class="text-lg font-semibold dark:text-gray-100">Meta Information</h3>
                        <small>(Optional)</small>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Meta Title --}}
                        <div class="from-group md:col-span-2">
                            <x-input wire:model.live="meta_title" label="Meta Title"
                                class="rounded-lg !border-neutral-300 dark:!border-neutral-500 !bg-white/10 !py-[9px] focus:!ring-red-500"
                                placeholder="Meta Title" />
                        </div>

                        {{-- Meta keyword --}}
                        <div class="from-group md:col-span-2">
                            <x-input wire:model.live="meta_keyword" label="Meta Keywords"
                                class="rounded-lg !border-neutral-300 dark:!border-neutral-500 !bg-white/10 !py-[9px] focus:!ring-red-500"
                                placeholder="Meta Keywords" />
                        </div>

                        {{-- Meta description --}}
                        <div class="from-group md:col-span-2">
                            <x-textarea label="Description" rows="3" wire:model.live="meta_description"
                                class="rounded-lg !border-neutral-300 dark:!border-neutral-500 !bg-white/10 !py-[9px] focus:!ring-red-500"
                                placeholder="Meta Description" />
                        </div>
                    </div>
                </section>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Dish Customization -->
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Dish Customization</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Crust --}}
                        <div class="form-group">

                            @php
                                $crusts = App\Models\Crust::where('status', 'active')->orderBy('name', 'ASC')->get();
                            @endphp

                            <x-select wire:model.live="crusts" label="Crusts" :options="$crusts"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('crusts') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable multiple />

                        </div>

                        {{-- Buns --}}
                        <div class="form-group">

                            @php
                                $bun = App\Models\Bun::where('status', 'active')->orderBy('name', 'ASC')->get();
                            @endphp

                            <x-select wire:model.live="buns" label="Buns" :options="$bun"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('buns') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable multiple />

                        </div>

                        {{-- Add ons --}}
                        <div class="form-group md:col-span-2">

                            @php
                                $addOn = App\Models\AddOn::where('status', 'active')->orderBy('name', 'ASC')->get();
                            @endphp

                            <x-select wire:model.live="addOns" label="Add Ons" :options="$addOn"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('addOns') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable multiple />

                        </div>
                    </div>
                </section>

                <!-- Manage stock -->
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Manage Stock</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- SKU --}}
                        <div class="from-group">
                            <x-input wire:model.live="sku" label="Stock Keeping Unit (SKU)"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('sku') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                placeholder="SKU-BB-66-A6" />
                        </div>

                        {{-- Track Stock --}}
                        <div class="from-group">
                            <x-select wire:model.live="track_stock" label="Track Stock" :options="['Yes', 'No']"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('track_stock') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}" />
                        </div>

                        {{-- Daily Stock --}}
                        <div class="from-group md:col-span-2">
                            <x-input type="number" min="0" wire:model.live="daily_stock" label="Daily Stock"
                                hint="Only required when Track Stock is set to Yes"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('daily_stock') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                placeholder="Daily Stock" />
                        </div>
                    </div>
                </section>

                {{-- Availability --}}
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Availability</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Available From --}}
                        <div class="from-group">
                            <x-input type="time" wire:model.live="available_from" label="Available From*"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('available_from') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}" />
                        </div>

                        {{-- Available Till --}}
                        <div class="from-group">
                            <x-input type="time" wire:model.live="available_till" label="Available Till*"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('available_from') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}" />
                        </div>
                    </div>
                </section>

                <!-- Product Image -->
                <section x-data="dishImages()" x-init="init()"
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">

                    <div class="flex items-center gap-2 mb-4">
                        <h3 class="text-lg font-semibold dark:text-gray-100">
                            Display Image <span class="text-red-500 font-normal text-base">*</span>
                        </h3>
                    </div>

                    <!-- Thumbnail (Big Preview) -->
                    <div class="relative aspect-square w-full overflow-hidden rounded-xl border 
                            {{ $errors->has('thumbnail') ? 'border-red-500' : 'border-gray-200 dark:border-neutral-600' }} 
                            bg-neutral-50 dark:bg-neutral-600 cursor-pointer"
                        @click="!thumbnailSrc && $refs.thumbnailInput.click()">

                        <template x-if="thumbnailSrc">
                            <img :src="thumbnailSrc" class="h-full w-full object-cover object-center" alt="">
                        </template>

                        <template x-if="!thumbnailSrc">
                            <div class="h-full w-full grid place-items-center text-center text-neutral-400 dark:text-neutral-300">
                                <p>
                                    Click to add Thumbnail <br>
                                    <small class="text-sm">Recommended image size for upload is 552x538 px. <br>
                                    Support png/jpg/jpeg/svg/webp • up to 5MB</small>
                                </p>
                            </div>
                        </template>

                        <!-- Remove thumbnail button -->
                        <button x-show="thumbnailSrc" @click.stop="removeThumbnail()" type="button"
                            class="absolute m-2 right-0 top-0 inline-flex h-6 w-6 items-center justify-center rounded-full bg-black/70 text-white text-sm">×</button>
                    </div>

                    <!-- Validation error -->
                    @error('thumbnail')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Hidden inputs -->
                    <input x-ref="thumbnailInput" type="file" accept="image/*" class="hidden"
                        wire:model.live="thumbnail">
                    <input x-ref="galleryInput" type="file" accept="image/*" multiple class="hidden"
                        wire:model.live="gallery">

                    <!-- Gallery row -->
                    <p class="mt-3">Images</p>
                    <div class="flex items-center gap-2 overflow-x-auto pt-1.5">
                        <!-- Gallery items -->
                        <template x-for="(g, i) in gallerySrcs" :key="g">
                            <div class="relative h-16 w-16 shrink-0 rounded-lg overflow-hidden border border-gray-200 dark:border-neutral-600 bg-neutral-100 dark:bg-neutral-600 cursor-pointer"
                                :class="displaySrc === g ? 'ring-2 ring-rose-400' : ''" @click="setDisplay(g)">
                                <img :src="g" class="h-full w-full object-cover" alt="">
                                <button @click.stop="removeGallery(i)" type="button"
                                    class="absolute -right-1 -top-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-black/70 text-white text-xs">×</button>
                            </div>
                        </template>

                        <!-- Add gallery button -->
                        <button type="button" @click="canAddMore() && $refs.galleryInput.click()"
                            :disabled="!canAddMore()"
                            :class="canAddMore() ? 'cursor-pointer' : 'opacity-40 cursor-not-allowed'"
                            class="h-16 w-16 shrink-0 rounded-lg border border-dashed border-gray-300 dark:border-neutral-500 text-neutral-400 dark:text-neutral-300 grid place-items-center"
                            title="Add gallery images">+</button>
                    </div>

                    <!-- Uploading state -->
                    <div x-cloak x-show="$wire.__instance.uploadsInProgress > 0"
                        class="text-xs text-neutral-500 dark:text-neutral-300 mt-2">
                        Uploading... please wait.
                    </div>
                </section>

                {{-- Visibility --}}
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Visibility</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="form-group md:col-span-2">
                            <x-select wire:model.live="visibility"
                                label="Turning visibility off will not show this dish in the website*"
                                :options="['Yes', 'No']"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('visibility') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}" />
                        </div>
                    </div>
                </section>

                {{-- Submit & Cancel button --}}
                <div class="flex">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button icon="cross-icon" variant="filled" class="cursor-pointer me-2">
                            Cancel
                        </flux:button>
                    </flux:modal.close>

                    <flux:button type="submit" icon="fileAdd-icon" class="cursor-pointer" variant="primary"
                        color="rose" wire:loading.attr="disabled" wire:target="submit">
                        <span wire:loading.remove wire:target="submit">Add Dish</span>
                        <span wire:loading wire:target="submit">Creating...</span>
                    </flux:button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            function dishImages() {
                return {
                    displaySrc: null,
                    thumbnailSrc: null,
                    gallerySrcs: [],
                    maxGallery: 4,

                    init() {
                        this.$watch('$wire.thumbnail', () => this.sync());
                        this.$watch('$wire.gallery', () => this.sync());
                        this.sync();
                    },

                    async sync() {
                        const urls = await this.$wire.previewUrls();
                        this.thumbnailSrc = urls.thumbnail || null;
                        this.gallerySrcs = urls.gallery || [];
                        if (!this.displaySrc) this.displaySrc = this.thumbnailSrc || this.gallerySrcs[0] || null;
                    },

                    setDisplay(src) {
                        if (src) this.displaySrc = src;
                    },
                    removeThumbnail() {
                        this.$wire.clearThumbnail();
                        this.thumbnailSrc = null;
                    },
                    removeGallery(i) {
                        this.$wire.removeFromGallery(i);
                        this.gallerySrcs.splice(i, 1);
                    },
                    canAddMore() {
                        return this.gallerySrcs.length < this.maxGallery;
                    },
                }
            }
        </script>
    @endpush

</div>
