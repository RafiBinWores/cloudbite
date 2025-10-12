<div>
    {{-- <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" /> --}}

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    {{-- <form method="POST" wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('Email address')" type="email" required autofocus autocomplete="email"
            placeholder="email@example.com" />

        <!-- Password -->
        <div class="relative">
            <flux:input wire:model="password" :label="__('Password')" type="password" required
                autocomplete="current-password" :placeholder="__('Password')" viewable />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm cursor-pointer" :href="route('password.request')"
                    wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Remember me')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full cursor-pointer">{{ __('Log in') }}
            </flux:button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link :href="route('register')" wire:navigate class="cursor-pointer">{{ __('Sign up') }}</flux:link>
        </div>
    @endif --}}



    <section class="min-h-screen bg-[#f6f7f9]">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <!-- Left: Image -->
                    <div class="relative">
                        <img src="{{ asset('assets/images/login.jpg') }}" alt="People dining"
                            class="w-full h-[260px] sm:h-[340px] lg:h-full object-cover" />
                        <!-- Logo on image -->
                        <img src="{{ asset('assets/images/logos/logo-light.png') }}" alt="FOODU"
                            class="absolute left-6 top-6 h-10 sm:h-12" />
                    </div>

                    <!-- Right: Form -->
                    <div class="px-6 sm:px-10 lg:px-14 py-10 sm:py-12">
                        <h1
                            class="font-oswald text-3xl sm:text-4xl font-bold mb-2 bg-gradient-to-r from-[#e80f3a] via-orange-400 to-yellow-400 bg-clip-text text-transparent uppercase">
                            WELCOME BACK
                        </h1>
                        <p class="text-slate-500 font-jost">
                            Enter your email and password to continue
                        </p>

                        <form class="mt-8 space-y-5" wire:submit="login" method="POST">
                            <!-- Email -->
                            <div>
                                <label for="email" class="sr-only">Email</label>
                                <input id="email" type="email" required placeholder="Email"
                                    wire:model="email"
                                    class="w-full h-12 rounded-md bg-[#eef0f5] px-4 outline-none border-none focus:ring-2 focus:ring-customRed-100 placeholder:text-slate-400"
                                    required />
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="sr-only">Password</label>
                                <input id="password" type="password" placeholder="Password" wire:model="password"
                                    class="w-full h-12 rounded-md bg-[#eef0f5] px-4 outline-none border-none focus:ring-2 focus:ring-customRed-100 placeholder:text-slate-400"
                                    required viewable />
                            </div>

                            <div class="flex items-center justify-between">
                                <!-- Remember Me -->
                                <label class="label">
                                    <input type="checkbox" wire:model="remember" checked="checked" class="checkbox checkbox-xs md:checkbox-sm checkbox-error" />
                                    <span class="text-slate-800 text-sm font-medium">Remember me</span>
                                </label>

                                @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" wire:navigate class="text-customRed-100 text-sm font-medium">Forgot your password?</a>
                                @endif
                            </div>

                            <!-- Login Button -->
                            <button type="submit"
                                class="relative w-full rounded-md px-8 md:px-10 py-3 overflow-hidden cursor-pointer bg-customRed-100 font-oswald text-white group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/60">
                                <!-- overlay expands + rotates -->
                                <span
                                    class="absolute inset-0 bg-slate-900 transform origin-center scale-0 rotate-45 transition-transform duration-500 ease-out group-hover:scale-1150"></span>

                                <span class="relative z-10 transition-colors duration-300 group-hover:text-white">
                                    LOGIN
                                </span>
                            </button>
                        </form>

                        <!-- Divider text -->
                        <div class="relative my-8">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="bg-white px-4 font-oswald text-lg font-medium">Or Login With</span>
                            </div>
                        </div>

                        <!-- Social buttons -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 font-oswald">
                            <a href=""
                                class="h-12 rounded-md border border-slate-200 bg-white hover:bg-slate-50 transition flex items-center justify-center gap-3">
                                <img src="{{ asset('assets/images/icons/google.svg') }}" alt="Google icon" class="size-8">
                                <span class="font-oswald font-medium">Google</span>
                            </a>
                            <a href=""
                                class="h-12 rounded-md border border-slate-200 bg-white hover:bg-slate-50 transition flex items-center justify-center gap-3">
                                <img src="{{ asset('assets/images/icons/facebook.svg') }}" alt="Google icon" class="size-9">
                                <span class="font-oswald font-medium">Facebook</span>
                            </a>
                        </div>

                        <!-- Register -->
                        @if (Route::has('register'))
                            <p class="mt-6 text-center text-slate-500 font-jost">
                                Don’t have any account?
                                <a href="{{ route('register') }}" wire:navigate
                                    class="text-orange-500 font-medium hover:underline font-oswald">Register Now</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
