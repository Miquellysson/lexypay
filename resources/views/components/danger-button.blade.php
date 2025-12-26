<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full bg-rose-600 px-5 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm ring-1 ring-rose-500/30 transition hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
