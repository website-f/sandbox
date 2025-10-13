<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Sandbox</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 via-white to-indigo-50">
            <div class="w-full max-w-6xl">
                <div class="bg-white shadow-2xl overflow-hidden sm:rounded-3xl">
                    <div class="px-6 py-8 sm:px-10 sm:py-12 lg:px-16 lg:py-16">
                        {{ $slot }}
                    </div>
                </div>
                
                <!-- Footer (Optional) -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        © {{ date('Y') }} Sandbox. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>