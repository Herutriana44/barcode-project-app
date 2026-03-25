@props([
    'alt' => null,
])

<img
    src="{{ asset('icon.png') }}"
    alt="{{ $alt ?? config('app.name', 'Logo') }}"
    {{ $attributes->merge(['class' => 'shrink-0 object-contain']) }}
/>
