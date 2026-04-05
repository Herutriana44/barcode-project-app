@php
    $e = $employee;
@endphp
<div>
    <label class="block text-xs font-medium text-egg-800">Nama *</label>
    <input type="text" name="name" value="{{ old('name', $e->name ?? '') }}" required maxlength="255"
        class="mt-0.5 block w-full rounded border-egg-300 text-sm py-1 shadow-sm focus:border-egg-500 focus:ring-egg-500" />
    @error('name')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-xs font-medium text-egg-800">NIP</label>
    <input type="text" name="nip" value="{{ old('nip', $e->nip ?? '') }}" maxlength="64"
        class="mt-0.5 block w-full rounded border-egg-300 text-sm py-1 shadow-sm focus:border-egg-500 focus:ring-egg-500" />
    @error('nip')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-xs font-medium text-egg-800">Telepon</label>
    <input type="text" name="phone" value="{{ old('phone', $e->phone ?? '') }}" maxlength="32"
        class="mt-0.5 block w-full rounded border-egg-300 text-sm py-1 shadow-sm focus:border-egg-500 focus:ring-egg-500" />
    @error('phone')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
</div>
