@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white/80 px-3 py-2 text-sm shadow-sm placeholder:text-gray-400 focus:border-orange-300 focus:ring-orange-200']) }}>
