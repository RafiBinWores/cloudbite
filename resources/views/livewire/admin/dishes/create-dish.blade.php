<div>

    {{-- Styles --}}
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

        <style>
            /* Dark theme override for Quill toolbar */
            .dark .ql-toolbar {
                background-color: none;
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
            }

            .dark .ql-toolbar button,
            .dark .ql-toolbar .ql-picker {
                color: #828690;
                /* Tailwind gray-200 */
            }

            .dark .ql-toolbar button:hover,
            .dark .ql-toolbar .ql-picker-label:hover {
                color: #f87171;
                /* Tailwind rose-400 for hover */
            }

            .dark .ql-toolbar .ql-stroke {
                stroke: #828690 !important;
                /* make SVG icons visible */
            }

            .dark .ql-toolbar .ql-fill {
                fill: #828690 !important;
            }

            .dark .ql-toolbar .ql-picker-options {
                background-color: #111827;
                /* Tailwind neutral-900 */
                border-color: #374151;
            }

            .dark .ql-toolbar .ql-picker-options span {
                color: #828690;
            }

            .dark .ql-toolbar .ql-picker-options span:hover {
                background: #374151;
            }
        </style>
    @endpush

    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-3" level="1">{{ __('Add New Dish') }}</flux:heading>
        <flux:breadcrumbs class="mb-6">
            <flux:breadcrumbs.item href="{{ route('dashboard') }}" icon="home" separator="slash" wire:navigate />
            <flux:breadcrumbs.item href="{{ route('dishes.index') }}" separator="slash" wire:navigate>Dishes
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item separator="slash">Add New Dish</flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="submit" class="mt-6 space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT COLUMN -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Name & Description -->
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Name and Description</h3>

                    <div class="space-y-6">
                        {{-- Title --}}
                        <div class="form-group">
                            <x-input label="Dish Title" wire:model.live="title"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('title') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                                placeholder="eg. Chicken Fry" />
                        </div>

                        {{-- Description --}}
                        <div class="form-group dark">
                            <label for="editor">Description</label>

                            {{-- Only the editor is ignored --}}
                            <div wire:ignore>
                                <div id="editor" class="min-h-38 rounded-b-lg border border-gray-300"></div>
                            </div>

                            {{-- Hidden input bound to Livewire --}}
                            <input type="hidden" id="description" wire:model.live="description">

                            {{-- This will now update properly --}}
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
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

                                $cat = App\Models\Category::all(['id', 'name', 'image'])->map(
                                    fn($c) => [
                                        'id' => $c->id,
                                        'name' => $c->name,
                                        'avatar' => $c->image
                                            ? Storage::url($c->image)
                                            : asset('assets/images/placeholders/cat-placeholder.png'),
                                    ],
                                );
                            @endphp

                            <x-select wire:model.live="category" label="Category" :options="$cat"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('category') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable />

                        </div>

                        {{-- Tags --}}
                        <div class="form-group">

                            @php
                                $cat = App\Models\Category::all(['id', 'name']);
                            @endphp

                            <x-select wire:model.live="tags" label="Tags" :options="$cat"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('tags') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable multiple />

                        </div>

                        {{-- Related Dish --}}
                        <div class="form-group md:col-span-2">

                            {{-- @php
                                use Illuminate\Support\Facades\Storage;

                                $cat = App\Models\Category::all(['id', 'name', 'image'])->map(
                                    fn($c) => [
                                        'id' => $c->id,
                                        'name' => $c->name,
                                        'avatar' => $c->image
                                            ? Storage::url($c->image)
                                            : asset('assets/images/placeholders/cat-placeholder.png'),
                                    ],
                                );
                            @endphp --}}

                            <x-select wire:model.live="related_dish" label="Related Dish"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('related_dish') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable searchable />

                        </div>
                    </div>
                </section>

                <!-- Manage Stock -->
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
                            <x-select wire:model.live="track_stock" label="Track Stock" :options="['Yes', 'No',]" class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('track_stock') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}" clearable />
                        </div>

                        {{-- Daily Stock --}}
                        <div class="from-group md:col-span-2">
                            <x-input type="number" min="0" wire:model.live="daily_stock" label="Daily Stock" hint="Only required when track stock selected Yes"
                                class="rounded-lg !border-neutral-300 dark:!border-neutral-500 !bg-white/10 !py-[9px] focus:!ring-red-500"
                                placeholder="Daily Stock" />
                        </div>
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
                            <x-input type="number" min="0" wire:model.live="meta_keyword" label="Meta Keywords"
                                class="rounded-lg !border-neutral-300 dark:!border-neutral-500 !bg-white/10 !py-[9px] focus:!ring-red-500"
                                placeholder="Meta Keywords" />
                        </div>

                        {{-- Meta description --}}
                        <div class="from-group md:col-span-2">
                            <x-textarea label="Your bio" rows="3" wire:model.live="meta_description"
                                class="rounded-lg !border-neutral-300 dark:!border-neutral-500 !bg-white/10 !py-[9px] focus:!ring-red-500"
                                placeholder="Meta Description" />
                        </div>
                    </div>
                </section>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Product Details -->
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

                                $bun = App\Models\Bun::all(['id', 'name']);
                            @endphp

                            <x-select wire:model.live="buns" label="Buns" :options="$bun"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('buns') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable multiple />

                        </div>

                        {{-- Add ons --}}
                        <div class="form-group md:col-span-2">

                            @php

                                $addOn = App\Models\AddOn::all(['id', 'name']);
                            @endphp

                            <x-select wire:model.live="addOns" label="Add Ons" :options="$addOn"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('addOns') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                clearable searchable multiple />

                        </div>
                    </div>
                </section>

                <!-- Product Pricing -->
                <section
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
                    <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Dish Pricing</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Price --}}
                        <div class="form-group">
                            <x-input type="number" min="0" label="Price (Tk)" wire:model.live="price"
                                class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('price') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}"
                                placeholder="Price" />
                        </div>
                        
                        {{-- Discount --}}
                        <div class="form-group">
                            <x-input type="number" min="0" label="Discount (%)" wire:model.live="discount"
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

                <!-- Product Image -->
                <section x-data="dishImages()" x-init="init()"
                    class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">

                    <div class="flex items-center gap-2 mb-4">
                        <h3 class="text-lg font-semibold dark:text-gray-100">Display Image</h3>
                    </div>

                    <!-- Thumbnail (Big Preview) -->
                    <div class="relative aspect-square w-full overflow-hidden rounded-xl border 
                {{ $errors->has('thumbnail') ? 'border-red-500' : 'border-gray-200 dark:border-neutral-600' }} 
                bg-neutral-50 dark:bg-neutral-600 cursor-pointer"
                        @click="!thumbnailSrc && $refs.thumbnailInput.click()">

                        <template x-if="thumbnailSrc">
                            <img :src="thumbnailSrc" class="h-full w-full object-cover object-center"
                                alt="">
                        </template>

                        <template x-if="!thumbnailSrc">
                            <div class="h-full w-full grid place-items-center text-center text-neutral-400 dark:text-neutral-300">
                                <p>
                                    Click to add Thumbnail <br>
                                <small class="text-sm">Recommended image size for upload is 552x538 px.</small>
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

                {{-- Submit & Cancel button --}}
                <div class="flex">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button icon="cross-icon" variant="filled" class="cursor-pointer me-2">Cancel
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
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
        <script>
            let quill;

            document.addEventListener('livewire:navigated', () => {
                // init once per navigation
                quill = new Quill('#editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote'],
                            ['link'],
                            [{
                                list: 'ordered'
                            }, {
                                list: 'bullet'
                            }, {
                                list: 'check'
                            }],
                            [{
                                script: 'sub'
                            }, {
                                script: 'super'
                            }],
                            [{
                                indent: '-1'
                            }, {
                                indent: '+1'
                            }],
                            [{
                                direction: 'rtl'
                            }],
                            [{
                                size: ['small', false, 'large', 'huge']
                            }],
                            [{
                                header: [1, 2, 3, 4, 5, 6, false]
                            }],
                            [{
                                color: []
                            }, {
                                background: []
                            }],
                            [{
                                font: []
                            }],
                            [{
                                align: []
                            }],
                            ['clean']
                        ]
                    }
                });

                // set initial value from Livewire (edit mode)
                const initial = @this.get('description') ?? '';
                if (initial) quill.root.innerHTML = initial;

                // keep Livewire in sync via hidden input
                const input = document.querySelector('#description');

                quill.on('text-change', () => {
                    const html = quill.root.innerHTML;
                    const plain = quill.getText().trim();
                    const input = document.getElementById('description');
                    input.value = plain.length ? html : ''; // normalize empty (<p><br></p>) to ''
                    input.dispatchEvent(new Event('input'));
                });
            });
        </script>

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
