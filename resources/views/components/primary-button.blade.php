<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full bg-orange-500 px-5 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm ring-1 ring-orange-500/30 transition hover:bg-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
