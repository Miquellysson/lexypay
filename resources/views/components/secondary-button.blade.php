<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-full border border-gray-300 bg-white px-5 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition hover:border-gray-400 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-300 focus:ring-offset-2 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
