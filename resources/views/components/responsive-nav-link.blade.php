@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-orange-300 bg-orange-50 px-4 py-2 text-start text-base font-medium text-orange-700 transition'
            : 'block w-full border-l-4 border-transparent px-4 py-2 text-start text-base font-medium text-gray-600 transition hover:border-gray-300 hover:bg-white/70 hover:text-gray-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
