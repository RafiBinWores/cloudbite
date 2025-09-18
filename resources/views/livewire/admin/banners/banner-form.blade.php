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



            {{-- CREATE/EDIT MODE: show uploader block always --}}
            <section class="space-y-2">
                <h3 class="text-base font-semibold">Thumbnail</h3>

                <div x-data="{
                    isOver: false,
                    isUploading: false,
                    progress: 0,
                    pick() { $refs.file.click() }
                }" x-on:dragover.prevent="isOver = true" x-on:dragleave.prevent="isOver = false"
                    x-on:drop.prevent="isOver = false" x-on:livewire-upload-start="isUploading = true"
                    x-on:livewire-upload-finish="isUploading = false; progress = 0"
                    x-on:livewire-upload-error="isUploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress" class="w-full">
                    {{-- Hidden file input --}}
                    <input x-ref="file" type="file" accept="image/*" class="hidden" wire:model.live="image"
                        wire:key="image-input-{{ $bannerId ?? 'new' }}">

                    {{-- TILE --}}
                    <div class="relative w-full h-36 rounded-2xl border-2 border-dashed transition
                   grid place-items-center cursor-pointer"
                        :class="isOver ? 'border-slate-400 bg-slate-50' : 'border-slate-300'" @click="pick()">
                        {{-- EMPTY STATE (like screenshot) --}}
                        @if (!$image && !$existingImage)
                            <div class="flex flex-col items-center gap-2 pointer-events-none">
                                {{-- Cloud upload icon (inline SVG, subtle grey) --}}
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

                        {{-- UPLOADING BAR --}}
                        <template x-if="isUploading">
                            <div class="absolute inset-0 rounded-2xl bg-white/60 grid place-items-end">
                                <div class="w-full h-1 bg-slate-200">
                                    <div class="h-1 bg-slate-500" :style="`width:${progress}%`"></div>
                                </div>
                            </div>
                        </template>

                        {{-- PREVIEW (newly selected) --}}
                        @if ($image)
                            <img src="{{ $image->temporaryUrl() }}"
                                class="absolute inset-0 w-full h-full object-cover rounded-2xl" alt="preview">
                            <button type="button" wire:click="clearImage" @click.stop
                                class="absolute top-2 right-2 rounded-full bg-white/90 hover:bg-white px-2 py-1 text-xs text-slate-700 shadow cursor-pointer">
                                Remove
                            </button>
                        @elseif ($existingImage)
                            {{-- EXISTING --}}
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
                <flux:input :disabled="$isView" wire:model="title" label="Title" placeholder="e.g. Eid Special" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                {{-- Item Type --}}
                <div class="form-group">
                    <flux:select :disabled="$isView" wire:model.live="item_type" label="Item Type"
                        placeholder="Choose item type...">
                        <flux:select.option value="category">Category</flux:select.option>
                        <flux:select.option value="dish">Dish</flux:select.option>
                    </flux:select>
                </div>

                {{-- Item --}}
                <div class="form-group">
                    <flux:select :disabled="$isView" wire:model.live="item_id"
                        :label="$item_type === 'category' ? 'Category' : 'Dish'"
                        :placeholder="$item_type === 'category' ? 'Choose a category' : 'Choose a dish'">

                        @if ($item_type === 'category')
                            @foreach (\App\Models\Category::where('status', 'Active')->get() as $cat)
                                <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                            @endforeach
                        @elseif ($item_type === 'dish')
                            @foreach (\App\Models\Dish::where('visibility', 'Yes')->get() as $dish)
                                <flux:select.option value="{{ $dish->id }}">{{ $dish->title }}</flux:select.option>
                            @endforeach
                        @endif
                    </flux:select>
                </div>
            </div>


            {{-- Status --}}
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
                    <flux:button icon="cross-icon" class="cursor-pointer me-2">Cancel</flux:button>
                </flux:modal.close>

                @if (!$isView)
                    <flux:button type="submit" icon="fileAdd-icon" class="cursor-pointer" variant="primary"
                        color="rose" wire:loading.attr="disabled" wire:target="submit">
                        <span wire:loading.remove wire:target="submit">{{ $bannerId ? 'Update' : 'Create' }}</span>
                        <span wire:loading
                            wire:target="submit">{{ $bannerId ? 'Updating...' : 'Creating...' }}</span>
                    </flux:button>
                @endif
            </div>
        </form>
    </flux:modal>
