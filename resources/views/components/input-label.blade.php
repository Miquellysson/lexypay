@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase tracking-widest text-gray-500']) }}>
    {{ $value ?? $slot }}
</label>
