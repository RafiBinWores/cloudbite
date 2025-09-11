<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

    <flux:header container
        class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 hidden lg:block">

        <flux:spacer />
        <flux:navbar class="me-4">

            {{-- Notification --}}
            <flux:navbar.item class="max-lg:hidden" icon="bell" href="" wire:navigate label="Help" />

            {{-- Theme Toggle --}}
            <flux:dropdown x-data align="end">
                <flux:button variant="subtle" square class="group" aria-label="Preferred color scheme">
                    <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini"
                        class="text-zinc-500 dark:text-white" />
                    <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini"
                        class="text-zinc-500 dark:text-white" />
                    <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" />
                    <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" />
                </flux:button>

                <flux:menu>
                    <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
                    <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
                    <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            {{-- Setting --}}
            {{-- <flux:navbar.item class="max-lg:hidden" icon="cog-6-tooth" href="{{ route('settings.profile') }}"
                wire:navigate label="Settings" /> --}}

        </flux:navbar>

        {{-- Topbar dropdown --}}
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        {{-- Main --}}
        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Main')" class="grid">

                {{-- Dashboard --}}
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        {{-- Product Management --}}
        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Product Management')" class="grid">

                {{-- Categories --}}
                <flux:navlist.item icon="cat-icon" :href="route('categories.index')"
                    :current="request()->routeIs('categories.index')" wire:navigate>
                    {{ __('Categories') }}</flux:navlist.item>


                {{-- Cuisine --}}
                <flux:navlist.item icon="cuisine-icon" :href="route('cuisines.index')"
                    :current="request()->routeIs('cuisines.index')" wire:navigate>
                    {{ __('Cuisines') }}</flux:navlist.item>

                {{-- product --}}
                <flux:navlist.item icon="dish-icon" :href="route('dishes.index')"
                    :current="request()->routeIs('dishes.index')" wire:navigate>
                    {{ __('Dishes') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>


        {{-- Dish Customization --}}
        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Dish Customization')" class="grid">

                {{-- Crusts --}}
                <flux:navlist.item icon="crust-icon" :href="route('crusts.index')"
                    :current="request()->routeIs('crusts.index')" wire:navigate>
                    {{ __('Crusts') }}</flux:navlist.item>

                {{-- Buns --}}
                <flux:navlist.item icon="bun-icon" :href="route('buns.index')"
                    :current="request()->routeIs('buns.index')" wire:navigate>
                    {{ __('Buns') }}</flux:navlist.item>

                {{-- Add ons --}}
                <flux:navlist.item icon="addOns-icon" :href="route('addOns.index')"
                    :current="request()->routeIs('addOns.index')" wire:navigate>
                    {{ __('Add Ons') }}</flux:navlist.item>

                {{-- Tags --}}
                <flux:navlist.item icon="tag" :href="route('tags.index')"
                    :current="request()->routeIs('tags.index')" wire:navigate>
                    {{ __('Tags') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <!-- Desktop User Menu -->
        {{-- <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown> --}}
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}</flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>


    {{ $slot }}


    <!-- TOASTS with icons, labels, and decreasing progress -->
    <div x-data="toastHub()" @toast.window="push($event.detail)"
        class="fixed top-4 right-4 z-50 space-y-2 w-[92vw] max-w-sm" aria-live="polite" aria-atomic="true">
        <template x-for="t in toasts" :key="t.id">
            <div x-init="start(t)" @mouseenter="pause(t)" @mouseleave="resume(t)" x-show="t.visible"
                x-transition.opacity.scale class="overflow-hidden rounded-xl shadow-lg text-white"
                :class="{
                    'bg-emerald-600': t.type === 'success',
                    'bg-red-600': t.type === 'error',
                    'bg-amber-600': t.type === 'warning',
                    'bg-slate-800': !['success', 'error', 'warning'].includes(t.type)
                }"
                role="status">
                <!-- Body -->
                <div class="px-4 py-3 flex items-start gap-3">
                    <!-- Icon -->
                    <div class="shrink-0 mt-0.5">
                        <template x-if="t.type === 'success'">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                        </template>
                        <template x-if="t.type === 'error'">
                            <i class="fa-solid fa-circle-xmark text-xl"></i>
                        </template>
                        <template x-if="t.type === 'warning'">
                            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                        </template>
                        <template x-if="!['success','error','warning'].includes(t.type)">
                            <i class="fa-solid fa-circle-info text-xl"></i>
                        </template>
                    </div>

                    <!-- Text -->
                    <div class="flex-1">
                        {{-- <div class="font-semibold capitalize text-sm" x-text="t.type || 'info'"></div> --}}
                        <div class="text-sm/6" x-text="t.message"></div>
                    </div>

                    <!-- Dismiss button -->
                    <button class="ml-2 shrink-0 px-1 rounded hover:bg-white/10 focus:outline-none"
                        @click="close(t.id)" aria-label="Dismiss notification">✕</button>
                </div>

                <!-- Progress bar (decreasing) -->
                <div class="h-1 bg-black/20">
                    <div class="h-full"
                        :class="{
                            'bg-emerald-300': t.type === 'success',
                            'bg-red-300': t.type === 'error',
                            'bg-amber-300': t.type === 'warning',
                            'bg-slate-300': !['success', 'error', 'warning'].includes(t.type)
                        }"
                        :style="`width:${progressPct(t)}%`">
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script src="./node_modules/preline/dist/preline.js"></script>
    <script>
        function toastHub() {
            return {
                toasts: [],
                push({
                    type = 'info',
                    message = '',
                    duration = 3000
                } = {}) {
                    const id = Date.now() + Math.random();
                    this.toasts.push({
                        id,
                        type,
                        message,
                        duration,
                        remaining: duration,
                        startedAt: null,
                        raf: null,
                        paused: false,
                        visible: true,
                    });
                },
                start(t) {
                    t.startedAt = performance.now();
                    const tick = (now) => {
                        if (t.paused) {
                            t.raf = requestAnimationFrame(tick);
                            return;
                        }
                        const elapsed = now - t.startedAt;
                        t.remaining -= elapsed;
                        t.startedAt = now;
                        if (t.remaining <= 0) {
                            this.close(t.id);
                            return;
                        }
                        t.raf = requestAnimationFrame(tick);
                    };
                    t.raf = requestAnimationFrame(tick);
                },
                pause(t) {
                    t.paused = true;
                },
                resume(t) {
                    t.paused = false;
                    t.startedAt = performance.now();
                },
                progressPct(t) {
                    return Math.max(0, Math.min(100, (t.remaining / t.duration) * 100));
                },
                close(id) {
                    const i = this.toasts.findIndex(x => x.id === id);
                    if (i !== -1) {
                        const t = this.toasts[i];
                        if (t.raf) cancelAnimationFrame(t.raf);
                        t.visible = false;
                        setTimeout(() => this.toasts.splice(i, 1), 180);
                    }
                },
            };
        }
    </script>

    @stack('scripts')

    @fluxScripts
</body>

</html>
