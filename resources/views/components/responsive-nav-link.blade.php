@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-egg-500 text-start text-base font-medium text-egg-900 bg-egg-100 focus:outline-none focus:text-egg-900 focus:bg-egg-200 focus:border-egg-600 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-egg-700 hover:text-egg-900 hover:bg-egg-50 hover:border-egg-200 focus:outline-none focus:text-egg-900 focus:bg-egg-50 focus:border-egg-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
