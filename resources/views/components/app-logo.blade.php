<div class="flex items-center justify-center">
    {{-- Light theme (white bg) uses dark logo --}}
    @if ($businessSetting?->logo_dark)
        <img src="{{ asset($businessSetting->logo_dark) }}" alt="Logo"
            class="h-12 w-auto block dark:hidden">
    @endif

    {{-- Dark theme (dark bg) uses light logo --}}
    @if ($businessSetting?->logo_light)
        <img src="{{ asset($businessSetting->logo_light) }}" alt="Logo"
            class="h-12 w-auto hidden dark:block">
    @endif


</div>
