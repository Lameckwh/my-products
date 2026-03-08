<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Red Hat Products') – {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen font-sans antialiased">

        <nav class="bg-red-700 shadow-md">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-14">
                    <a href="{{ route('products.index') }}" class="flex items-center gap-2 text-white font-semibold text-lg tracking-tight">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/>
                        </svg>
                        Red Hat Products
                    </a>
                    <a href="{{ route('products.create') }}"
                       class="inline-flex items-center gap-1 px-4 py-1.5 bg-white text-red-700 rounded text-sm font-medium hover:bg-red-50 transition-colors">
                        + New Product
                    </a>
                </div>
            </div>
        </nav>

        <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            @if (session('success'))
                <div class="mb-6 px-4 py-3 bg-green-100 border border-green-300 text-green-800 rounded text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')

        </main>

    </body>
</html>
