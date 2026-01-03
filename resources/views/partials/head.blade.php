<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" href="{{ asset($businessSetting->favicon) }}" sizes="any">

{{-- ✅ put this BEFORE @vite so app.js can read it --}}
<script>
    window.Laravel = {
        userId: @json(auth()->id()),
    };
</script>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Oswald:wght@200..700&display=swap"
      rel="stylesheet" />

<link rel="stylesheet" href="{{ asset('assets/css/all.css') }}">

@stack('styles')

@vite(['resources/css/app.css', 'resources/js/app.js'])

@livewireStyles
@fluxAppearance
