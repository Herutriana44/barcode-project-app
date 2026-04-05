@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-2 pt-1 pb-3 border-b-[3px] border-white text-base font-semibold leading-5 text-white focus:outline-none focus:border-egg-100 transition duration-150 ease-in-out lg:text-lg'
            : 'inline-flex items-center px-2 pt-1 pb-3 border-b-[3px] border-transparent text-base font-medium leading-5 text-white/85 hover:text-white hover:border-white/40 focus:outline-none focus:text-white focus:border-white/30 transition duration-150 ease-in-out lg:text-lg';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
