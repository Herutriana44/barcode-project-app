@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-3 border-l-4 border-white text-start text-lg font-semibold text-white bg-white/15 focus:outline-none focus:bg-white/20 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-3 border-l-4 border-transparent text-start text-lg font-medium text-white/90 hover:text-white hover:bg-white/10 hover:border-white/30 focus:outline-none focus:bg-white/10 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
