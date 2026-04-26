@php
    $e = $employee;
@endphp
<div>
    <label class="block text-base font-medium text-egg-800">Nama *</label>
    <input type="text" name="name" value="{{ old('name', $e->name ?? '') }}" required maxlength="255"
        class="mt-1 block w-full rounded-lg border-egg-300 text-base py-2.5 shadow-sm focus:border-egg-500 focus:ring-egg-500" />
    @error('name')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-base font-medium text-egg-800">NIP *</label>
    <input type="text" name="nip" value="{{ old('nip', $e->nip ?? '') }}" required maxlength="64"
        pattern="[A-Za-z0-9._\-]+"
        title="Huruf, angka, titik, garis bawah, atau tanda hubung"
        class="mt-1 block w-full rounded-lg border-egg-300 text-base py-2.5 shadow-sm focus:border-egg-500 focus:ring-egg-500" />
    <p class="text-xs text-egg-600 mt-0.5">Digunakan di URL profil dan barcode ID card.</p>
    @error('nip')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-base font-medium text-egg-800">Jabatan</label>
    <input type="text" name="jabatan" value="{{ old('jabatan', $e->jabatan ?? '') }}" maxlength="255"
        class="mt-1 block w-full rounded-lg border-egg-300 text-base py-2.5 shadow-sm focus:border-egg-500 focus:ring-egg-500" />
    @error('jabatan')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-base font-medium text-egg-800">Departemen</label>
    <input type="text" name="departemen" value="{{ old('departemen', $e->departemen ?? '') }}" maxlength="255"
        class="mt-1 block w-full rounded-lg border-egg-300 text-base py-2.5 shadow-sm focus:border-egg-500 focus:ring-egg-500" />
    @error('departemen')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-base font-medium text-egg-800">Foto (opsional)</label>
    <input type="file" name="photo" accept="image/*"
        class="mt-1 block w-full text-base text-egg-800 file:mr-3 file:rounded-lg file:border-0 file:bg-egg-100 file:px-3 file:py-2 file:text-egg-900" />
    @error('photo')<p class="text-red-600 text-xs mt-0.5">{{ $message }}</p>@enderror
    @if (! empty($e?->photo_path))
        <p class="text-xs text-egg-600 mt-1">Foto saat ini tersimpan. Unggah berkas baru untuk mengganti.</p>
    @endif
</div>
