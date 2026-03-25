@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-egg-500 text-sm font-medium leading-5 text-egg-900 focus:outline-none focus:border-egg-700 transition duration-150 ease-in-out lg:text-base'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-egg-600 hover:text-egg-900 hover:border-egg-300 focus:outline-none focus:text-egg-900 focus:border-egg-300 transition duration-150 ease-in-out lg:text-base';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
