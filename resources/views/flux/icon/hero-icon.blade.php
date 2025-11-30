{{-- Credit: Lucide (https://lucide.dev) --}}

@props([
    'variant' => 'outline',
])

@php
    if ($variant === 'solid') {
        throw new \Exception('The "solid" variant is not supported in Lucide.');
    }

    $classes = Flux::classes('shrink-0')->add(
        match ($variant) {
            'outline' => '[:where(&)]:size-6',
            'solid' => '[:where(&)]:size-6',
            'mini' => '[:where(&)]:size-5',
            'micro' => '[:where(&)]:size-4',
        },
    );

    $strokeWidth = match ($variant) {
        'outline' => 2,
        'mini' => 2.25,
        'micro' => 2.5,
    };
@endphp

<svg {{ $attributes->class($classes) }} data-flux-icon viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="{{ $strokeWidth }}" stroke-linecap="round" stroke-linejoin="round"
    class="lucide lucide-gallery-horizontal-end-icon lucide-gallery-horizontal-end">
    <path d="M2 7v10" />
    <path d="M6 5v14" />
    <rect width="12" height="18" x="10" y="3" rx="2" />
</svg>
