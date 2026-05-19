@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-2 border-b-2 border-white text-base font-semibold text-white transition duration-150 ease-in-out'
            : 'inline-flex items-center px-4 py-2 border-b-2 border-transparent text-base font-medium text-white/85 hover:text-white hover:border-white/40 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
