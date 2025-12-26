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
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="mx-auto mt-8 max-w-6xl px-6">
                    <div class="rounded-2xl border border-white/70 bg-white/80 px-6 py-5 shadow-lg backdrop-blur">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="mx-auto mt-8 max-w-6xl px-6 pb-12">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
