@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-full border border-orange-200 bg-orange-50 px-3 py-1.5 text-sm font-semibold text-orange-700 transition'
            : 'inline-flex items-center rounded-full border border-transparent px-3 py-1.5 text-sm font-semibold text-gray-500 transition hover:border-gray-200 hover:text-gray-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
