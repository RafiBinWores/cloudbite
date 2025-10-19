<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

    <x-toast />

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

        {{-- Promotion Management --}}
        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Promotion Management')" class="grid">

                {{-- Banners --}}
                <flux:navlist.item icon="Photo" :href="route('banners.index')"
                    :current="request()->routeIs('banners.index')" wire:navigate>
                    {{ __('Banners') }}</flux:navlist.item>

                {{-- Coupons --}}
                <flux:navlist.item icon="gift" :href="route('coupons.index')"
                    :current="request()->routeIs('coupons.index')" wire:navigate>
                    {{ __('Coupons') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        {{-- Report And Analytics --}}
        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Report And Analytics')" class="grid">

                {{-- Earning Report --}}
                <flux:navlist.item icon="presentation-chart-line" :href="route('banners.index')"
                    :current="request()->routeIs('banners.index')" wire:navigate>
                    {{ __('Earning Report') }}</flux:navlist.item>

                {{-- Order Report --}}
                <flux:navlist.item icon="chart-pie" :href="route('coupons.index')"
                    :current="request()->routeIs('coupons.index')" wire:navigate>
                    {{ __('Order Report') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        {{-- User Management --}}
        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('User Management')" class="grid">

                {{-- Customers --}}
                <flux:navlist.item icon="user-group" :href="route('banners.index')"
                    :current="request()->routeIs('banners.index')" wire:navigate>
                    {{ __('Customers') }}</flux:navlist.item>

                {{-- Deliveryman --}}
                <flux:navlist.item icon="delivery-icon" :href="route('delivery.index')"
                    :current="request()->routeIs('delivery.index')" wire:navigate>
                    {{ __('Deliveryman') }}</flux:navlist.item>

                {{-- Employees --}}
                <flux:navlist.item icon="employee-icon" :href="route('coupons.index')"
                    :current="request()->routeIs('coupons.index')" wire:navigate>
                    {{ __('Employees') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        {{-- System Setting --}}
        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('System Setting')" class="grid">

                {{-- Business Setup --}}
                <flux:navlist.item icon="building-storefront" :href="route('business_setup.index')"
                    :current="request()->routeIs('business_setup.index')" wire:navigate>
                    {{ __('Business Setup') }}</flux:navlist.item>

                {{-- Pages --}}
                <flux:navlist.item icon="window" :href="route('delivery.index')"
                    :current="request()->routeIs('delivery.index')" wire:navigate>
                    {{ __('Pages') }}</flux:navlist.item>
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
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>


    {{ $slot }}

    @stack('scripts')

    @fluxScripts
</body>

</html>
