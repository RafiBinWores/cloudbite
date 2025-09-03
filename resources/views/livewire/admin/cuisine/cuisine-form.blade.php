    <flux:modal name="cuisine-modal" class="md:w-[32rem]">
        <form wire:submit="submit" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $isView ? 'Cuisine details' : ($cuisineId ? 'Update' : 'Create') . ' Cuisine' }}
                </flux:heading>
                <flux:text class="mt-2">
                    {{ $isView ? 'View cuisine details information' : ($cuisineId ? 'Update' : 'Add') . '  cuisine details information.' }}
                </flux:text>
            </div>

            {{-- CREATE/EDIT MODE: show uploader block always --}}
            <section class="space-y-2">
                <h3 class="text-base font-semibold">Thumbnail</h3>

                <div x-data="{ isOver: false, isUploading: false, progress: 0, pick() { $refs.file.click() } }" x-on:dragover.prevent="isOver = true" x-on:dragleave.prevent="isOver = false"
                    x-on:drop.prevent="isOver = false" x-on:livewire-upload-start="isUploading = true"
                    x-on:livewire-upload-finish="isUploading = false; progress = 0"
                    x-on:livewire-upload-error="isUploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress" class="flex gap-3">
                    {{-- Hidden file input --}}
                    <input x-ref="file" type="file" accept="image/*" class="hidden" wire:model.live="image"
                        wire:key="image-input-{{ $cuisineId ?? 'new' }}">

                    @if ($image)
                        {{-- 1) TEMPORARY PREVIEW (new file selected) --}}
                        <div class="relative h-[84px] w-[84px] rounded-xl border overflow-hidden group bg-slate-100">
                            <img src="{{ $image->temporaryUrl() }}" class="h-full w-full object-cover" alt="preview">
                            <button type="button" wire:click="clearImage"
                                class="absolute inset-0 hidden group-hover:flex items-center justify-center bg-black/40"
                                aria-label="Remove" title="Remove">
                                <span
                                    class="inline-flex items-center justify-center size-8 rounded-full bg-white shadow">🗑️</span>
                            </button>
                        </div>
                    @elseif ($existingImage)
                        {{-- 2) EXISTING IMAGE (no new file chosen yet) --}}
                        <div class="relative h-[84px] w-[84px] rounded-xl border overflow-hidden group bg-slate-100">
                            <img src="{{ asset($existingImage) }}" class="h-full w-full object-cover"
                                alt="current image">
                        </div>
                    @endif

                    {{-- 3) Uploading tile (shown only while uploading) --}}
                    @if (!$isView)
                        <template x-if="isUploading">
                            <div
                                class="relative h-[84px] w-[84px] rounded-xl border overflow-hidden bg-slate-800 text-white grid place-items-center">
                                <div class="text-xs">Upload</div>
                                <div class="absolute bottom-0 left-0 h-1 bg-white/80" :style="`width:${progress}%`">
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="pick()"
                            class="h-[84px] w-[84px] rounded-xl border-2 border-dashed grid place-items-center hover:border-slate-900"
                            :class="isOver ? 'border-slate-900 bg-slate-50' : 'border-slate-300'"
                            title="{{ $existingImage && !$image ? 'Change image' : 'Add image' }}"
                            x-show="!isUploading && {{ $image ? 'false' : 'true' }}">
                            <span class="text-2xl leading-none">+</span>
                        </button>
                    @endif
                </div>

                @if (!$isView)
                    @error('image')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-500">png/jpg/jpeg/svg/webp • up to 2MB</p>
                @endif
            </section>

            {{-- Name --}}
            <div class="form-group">
                <flux:input :disabled="$isView" wire:model="name" label="Name" placeholder="Category name" />
            </div>

            {{-- Meta title --}}
            <div class="form-group">
                <flux:input :disabled="$isView" wire:model="meta_title" label="Meta Title" placeholder="SEO title" />
            </div>

            {{-- Meta description --}}
            <div class="form-group">
                <flux:textarea :disabled="$isView" wire:model="meta_description" label="Meta Description"
                    placeholder="Up to 500 chars" />
            </div>

            {{-- Meta keywords --}}
            <div class="form-group">
                <flux:input :disabled="$isView" wire:model="meta_keywords" label="Meta Keywords"
                    placeholder="comma,separated,keywords" />
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
                    <flux:button icon="cross-icon" class="cursor-pointer me-2">Cancel</flux:button>
                </flux:modal.close>

                @if (!$isView)
                    <flux:button type="submit" icon="fileAdd-icon" class="cursor-pointer" variant="primary" color="rose"
                        wire:loading.attr="disabled" wire:target="submit">
                        <span wire:loading.remove wire:target="submit">{{ $cuisineId ? 'Update' : 'Create' }}</span>
                        <span wire:loading wire:target="submit">{{ $cuisineId ? 'Updating...' : 'Creating...' }}</span>
                    </flux:button>
                @endif
            </div>
        </form>
    </flux:modal>
