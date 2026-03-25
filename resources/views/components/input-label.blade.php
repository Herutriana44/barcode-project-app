@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-egg-800']) }}>
    {{ $value ?? $slot }}
</label>
