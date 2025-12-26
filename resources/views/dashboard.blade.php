<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="text-xs font-semibold uppercase tracking-widest text-gray-500">Overview</div>
            <h2 class="text-2xl font-semibold text-gray-900">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl border border-white/70 bg-white/80 p-6 shadow-lg backdrop-blur">
            <div class="text-sm font-semibold text-gray-700">Status</div>
            <div class="mt-2 text-lg text-gray-900">{{ __("You're logged in!") }}</div>
            <p class="mt-2 text-sm text-gray-600">
                Acesse o painel de pagamentos para acompanhar o volume e os eventos em tempo real.
            </p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-sm font-semibold text-gray-700">Painel Filament</div>
            <p class="mt-2 text-sm text-gray-600">
                Use o painel para visualizar pagamentos, filtros e timeline.
            </p>
            <a href="/admin" class="mt-4 inline-flex items-center text-sm font-semibold text-orange-600 hover:text-orange-500">
                Abrir painel
            </a>
        </div>
    </div>
</x-app-layout>
