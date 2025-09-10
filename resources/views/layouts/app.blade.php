<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Sandbox</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="{{ asset('assetsw/css/slick.css') }}" />
        <link rel="stylesheet" href="{{ asset('assetsw/css/aos.css') }}" />
        <link rel="stylesheet" href="{{ asset('assetsw/css/output.css') }}" />
        <link rel="stylesheet" href="{{ asset('assetsw/css/style.css') }}" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>

        <script src="{{ asset('assetsw/js/jquery-3.6.0.min.js') }}"></script>
        <script src="{{ asset('assetsw/js/aos.js') }}"></script>
        <script src="{{ asset('assetsw/js/slick.min.js') }}"></script>
        <script src="{{ asset('assetsw/js/main.js') }}"></script>
        <script src="{{ asset('assetsw/js/chart.js') }}"></script>
        
        {{-- Theme Toggle Script --}}
        <script>
            // Set initial theme based on localStorage or system preference
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }

            var themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function() {
                    // Check the current theme and toggle it
                    if (document.documentElement.classList.contains('dark')) {
                        localStorage.theme = 'light';
                        document.documentElement.classList.remove('dark');
                    } else {
                        localStorage.theme = 'dark';
                        document.documentElement.classList.add('dark');
                    }
                });
            }
        </script>

        @stack('scripts')

    </body>
</html>