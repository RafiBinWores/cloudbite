<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1">
            <img class="w-8" src="{{ asset('assets/images/icons/delivery-man.png') }}" alt="Delivery man Icon">
            {{ __('Edit Delivery Man') }}
        </flux:heading>
        <flux:separator variant="subtle" />
    </div>

    <form wire:submit="save" class="mt-6 space-y-6">
        {{-- Basic Info --}}
        <div class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl py-5">
            <h3 class="text-lg font-semibold mb-2 dark:text-gray-100 px-5">Deliveryman information</h3>
            <flux:separator class="mb-4" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5">

                <div class="space-y-6">
                    {{-- First name --}}
                    <x-input label="First Name *" wire:model.live="first_name"
                        class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('first_name') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                        placeholder="First Name" />

                    {{-- Last name --}}
                    <x-input label="Last Name" wire:model.live="last_name"
                        class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('last_name') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                        placeholder="Last Name" />

                    {{-- Phone number --}}
                    <x-input type="number" min="0" label="Phone Number *" wire:model.live="phone_number"
                        class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('phone_number') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                        placeholder="e.g. 016xxxxxxxx" />
                </div>

                {{-- Profile image --}}
                <div class="text-center">
                    <div class="mb-4">
                        <p class="font-medium">Profile Image</p>
                        <p class="text-xs">JPG, JPEG, PNG, WEBP Less Than 2MB <strong>(Ratio 1:1)</strong></p>
                    </div>

                    <div class="flex justify-center"
                        x-data="{ isOver:false, isUploading:false, progress:0, pick(){ $refs.file.click() } }"
                        x-on:dragover.prevent="isOver=true" x-on:dragleave.prevent="isOver=false"
                        x-on:drop.prevent="isOver=false"
                        x-on:livewire-upload-start="isUploading=true"
                        x-on:livewire-upload-finish="isUploading=false; progress=0"
                        x-on:livewire-upload-error="isUploading=false"
                        x-on:livewire-upload-progress="progress=$event.detail.progress"
                        class="w-full">

                        <input x-ref="file" type="file" accept="image/*" class="hidden" wire:model.live="image">

                        <div class="relative size-[180px] rounded-2xl border-2 border-dashed transition grid place-items-center cursor-pointer"
                            :class="isOver ? 'border-slate-400 bg-slate-50' : 'border-slate-300'"
                            @click="pick()">

                            {{-- Progress overlay --}}
                            <template x-if="isUploading">
                                <div class="absolute inset-0 rounded-2xl bg-white/60 grid place-items-end">
                                    <div class="w-full h-1 bg-slate-200">
                                        <div class="h-1 bg-slate-600" :style="`width:${progress}%`"></div>
                                    </div>
                                </div>
                            </template>

                            {{-- Preview priority: new -> existing --}}
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover rounded-2xl" alt="preview">
                                <button type="button" wire:click="clearImage" @click.stop
                                    class="absolute top-2 right-2 rounded-full bg-white/90 hover:bg-white px-2 py-1 text-xs text-slate-700 shadow">
                                    Remove
                                </button>
                            @elseif ($existingImage)
                                <img src="{{ asset('storage/' . ltrim($existingImage, '/')) }}" class="absolute inset-0 w-full h-full object-cover rounded-2xl" alt="current">
                                <button type="button" @click.stop="pick()"
                                    class="absolute top-2 right-2 rounded-full bg-white/90 hover:bg-white px-2 py-1 text-xs text-slate-700 shadow">
                                    Change
                                </button>
                            @else
                                <div class="flex flex-col items-center gap-2 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-cloud-upload">
                                        <path d="M12 13v8" />
                                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
                                        <path d="m8 17 4-4 4 4" />
                                    </svg>
                                    <span class="text-sm text-slate-500">Click To Upload Image</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @error('image')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Identity --}}
        <div class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl py-5">
            <h3 class="text-lg font-semibold mb-2 dark:text-gray-100 px-5">Identity information</h3>
            <flux:separator class="mb-4" />

            <div class="grid grid-cols-1 gap-6 p-5">
                <div class="space-y-6 grid grid-cols-1 md:grid-cols-2 md:gap-6">
                    <x-select wire:model.live="identity_type" label="Identity Type *"
                        :options="['nid' => 'NID', 'driving_license' => 'Driving License', 'passport' => 'Passport']"
                        class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('identity_type') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}" />

                    <x-input label="Identity Number *" wire:model.live="identity_number"
                        class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('identity_number') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                        placeholder="e.g. DH-12312" />
                </div>

                {{-- Identity dropzone --}}
                <div class="text-center">
                    <div class="mb-4">
                        <p class="font-medium">Identity Image</p>
                        <p class="text-xs">JPG, JPEG, PNG, WEBP Less Than 2MB <strong>(Ratio 1:1)</strong> • Max 2 images in total</p>
                    </div>

                    @php
                        $existingCount = count($existingIdentityImages ?? []);
                        $newCount = count($identity_images ?? []);
                        $totalCount = $existingCount + $newCount;
                        $maxed = $totalCount >= 2;
                        $remain = max(0, 2 - $existingCount - $newCount);
                    @endphp

                    <div class="flex justify-center"
                        x-data="{
                            isOver:false, isUploading:false, progress:0,
                            maxed: @js($maxed),
                            remaining: @js($remain),
                            pick(){ if(!this.maxed) $refs.identityfile.click(); },
                            dropFiles(ev){
                                if (this.maxed) return;
                                const all = Array.from(ev.dataTransfer?.files || []);
                                if (!all.length) return;
                                const files = all.slice(0, this.remaining);
                                $wire.uploadMultiple('identity_uploads', files, () => {}, () => {}, (p)=>{ this.progress = p; });
                            }
                        }"
                        class="w-full">

                        <input x-ref="identityfile" type="file" accept="image/*" class="hidden" multiple wire:model.live="identity_uploads">

                        <div class="relative w-full max-w-md rounded-2xl border-2 border-dashed transition grid place-items-center cursor-pointer p-6"
                            :class="[isOver ? 'border-slate-400 bg-slate-50' : 'border-slate-300', maxed ? 'opacity-75 cursor-not-allowed' : '']"
                            @dragover.stop.prevent="isOver=true" @dragenter.stop.prevent="isOver=true"
                            @dragleave.stop.prevent="isOver=false" @drop.stop.prevent="isOver=false; dropFiles($event)"
                            @click="pick()"
                            x-on:livewire-upload-start="isUploading=true"
                            x-on:livewire-upload-finish="isUploading=false; progress=0"
                            x-on:livewire-upload-error="isUploading=false"
                            x-on:livewire-upload-progress="progress=$event.detail.progress">

                            <div class="flex flex-col items-center gap-2 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cloud-upload">
                                    <path d="M12 13v8" />
                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242" />
                                    <path d="m8 17 4-4 4 4" />
                                </svg>
                                <span class="text-sm text-slate-600" x-show="!maxed">
                                    Click or drop images (up to {{ $remain }} more).
                                </span>
                                <span class="text-sm text-slate-500" x-show="maxed">
                                    Maximum 2 images selected/stored.
                                </span>
                            </div>

                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-0.5 text-xs rounded-full bg-slate-800 text-white">
                                    {{ $totalCount }}/2
                                </span>
                            </div>

                            <template x-if="isUploading">
                                <div class="absolute inset-0 rounded-2xl bg-white/60 grid place-items-end">
                                    <div class="w-full h-1 bg-slate-200">
                                        <div class="h-1 bg-slate-600" :style="`width:${progress}%`"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Errors --}}
                    @error('identity_images') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    @error('identity_images.*') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror

                    {{-- Previews: existing + new --}}
                    <div class="flex justify-center gap-3 mt-3 flex-wrap">
                        {{-- Existing (with remove) --}}
                        @foreach (($existingIdentityImages ?? []) as $idx => $path)
                            <div class="relative w-[180px] h-[130px] rounded-2xl overflow-hidden">
                                <img src="{{ asset('storage/' . ltrim($path, '/')) }}" class="w-full h-full object-cover" alt="identity existing">
                                <button type="button" wire:click="removeExistingIdentityImage({{ $idx }})"
                                    class="absolute top-2 right-2 rounded-full bg-white/90 hover:bg-white px-2 py-1 text-xs text-slate-700 shadow">
                                    Remove
                                </button>
                            </div>
                        @endforeach

                        {{-- New (with remove) --}}
                        @foreach (($identity_images ?? []) as $idx => $img)
                            <div class="relative w-[180px] h-[130px] rounded-2xl overflow-hidden">
                                <img src="{{ $img->temporaryUrl() }}" class="w-full h-full object-cover" alt="identity new">
                                <button type="button" wire:click="removeIdentityImage({{ $idx }})"
                                    class="absolute top-2 right-2 rounded-full bg-white/90 hover:bg-white px-2 py-1 text-xs text-slate-700 shadow">
                                    Remove
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Account --}}
        <div class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl pt-5">
            <h3 class="text-lg font-semibold mb-2 dark:text-gray-100 px-5">Deliveryman information</h3>
            <flux:separator class="mb-4" />

            <div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-6 p-5 space-y-6">
                <x-input type="email" label="Email *" wire:model="email"
                    class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('email') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                    placeholder="e.g. test@example.com" />

                <x-password label="Password (leave blank to keep old password)" wire:model="password"
                    class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('password') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-400' }}"
                    placeholder="8+ Characters" />
            </div>
        </div>

        {{-- Status --}}
        <section class="bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-2xl p-5">
            <h3 class="text-lg font-semibold mb-4 dark:text-gray-100">Status</h3>
            <div>
                <x-select wire:model="status"
                    label="Disabling the status will halt all activities for this delivery driver*"
                    :options="['active' => 'Active', 'disable' => 'Disable']"
                    class="rounded-lg !bg-white/10 !py-[9px] {{ $errors->has('status') ? '!border-red-500 focus:!ring-red-500' : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-red-500' }}" />
            </div>
        </section>

        {{-- Actions --}}
        <div class="flex">
            <flux:spacer />
            <flux:button type="submit" icon="fileAdd-icon" class="cursor-pointer" variant="primary" color="rose"
                wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Save Changes</span>
                <span wire:loading wire:target="save">Saving...</span>
            </flux:button>
        </div>
    </form>
</div>
