<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}" />
        <title>ARTURO MOTORS</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>

    <body class="font-sans antialiased bg-gray-50">
        <x-banner />

        <div class="min-h-screen">
            @livewire('custom-nav-menu')

            <main class="pt-16">
                <div class="px-8 py-8 sm:px-10 sm:py-10 md:px-12 md:py-10 lg:px-16 lg:py-10 max-w-[1400px] mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>

        @stack('modals')
        @livewireScripts

        <footer class="border-t border-gray-200 mt-8">
            <div class="px-8 py-4 text-xs text-slate-400 text-right max-w-[1400px] mx-auto">
                Powered by GHFDEV ®
            </div>
        </footer>
    </body>
</html>
