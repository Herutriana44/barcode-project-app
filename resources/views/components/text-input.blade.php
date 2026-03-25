@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-egg-300 focus:border-egg-500 focus:ring-egg-500 rounded-md shadow-sm']) !!}>
