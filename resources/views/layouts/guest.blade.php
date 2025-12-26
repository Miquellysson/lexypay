<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --ink: #0f172a;
                --accent: #f97316;
                --accent-2: #0f766e;
                --page: #f7f4ef;
                --card: rgba(255, 255, 255, 0.94);
            }

            body {
                font-family: "Space Grotesk", ui-sans-serif, system-ui, -apple-system, sans-serif;
                color: var(--ink);
                background: radial-gradient(1200px 500px at 10% -10%, #fdebd1 0%, transparent 60%),
                    radial-gradient(900px 400px at 90% -5%, #d7f2f1 0%, transparent 60%),
                    var(--page);
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen overflow-hidden">
            <div class="absolute -top-28 -left-28 h-64 w-64 rounded-full opacity-40 blur-3xl"
                style="background: radial-gradient(circle, #fbbf24 0%, transparent 70%);">
            </div>
            <div class="absolute -bottom-28 -right-28 h-72 w-72 rounded-full opacity-40 blur-3xl"
                style="background: radial-gradient(circle, #22d3ee 0%, transparent 70%);">
            </div>

            <div class="relative z-10 mx-auto flex min-h-screen max-w-6xl items-center px-6 py-12">
                <div class="grid w-full grid-cols-1 items-center gap-12 lg:grid-cols-2">
                    <div class="hidden lg:flex flex-col gap-6">
                        <a href="/" class="inline-flex items-center gap-3">
                            <x-application-logo class="h-12 w-12 fill-current text-gray-900" />
                            <span class="text-xl font-semibold tracking-tight">LEXI Gateway</span>
                        </a>
                        <div class="text-4xl font-semibold leading-tight">
                            Pagamentos USD com checkout direto e painel multi-merchant.
                        </div>
                        <p class="text-lg text-gray-600">
                            Fluxo enxuto para MVP: Stripe Checkout, webhooks idempotentes e
                            monitoramento em tempo real.
                        </p>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span class="rounded-full border border-gray-200 bg-white/70 px-3 py-1">Stripe</span>
                            <span class="rounded-full border border-gray-200 bg-white/70 px-3 py-1">Multi-tenant</span>
                            <span class="rounded-full border border-gray-200 bg-white/70 px-3 py-1">Filament</span>
                        </div>
                    </div>

                    <div class="w-full">
                        <div class="rounded-2xl border border-white/60 bg-white/80 p-8 shadow-xl backdrop-blur">
                            <div class="mb-6 flex items-center gap-3 lg:hidden">
                                <x-application-logo class="h-10 w-10 fill-current text-gray-900" />
                                <span class="text-lg font-semibold tracking-tight">LEXI Gateway</span>
                            </div>
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
