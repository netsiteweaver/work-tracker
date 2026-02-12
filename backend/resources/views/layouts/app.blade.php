<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicons -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-icon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
        <meta name="msapplication-config" content="{{ asset('favicon/browserconfig.xml') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased {{ ($darkMode ?? false) ? 'dark' : '' }}" id="app-body">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="w-full py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
        
        <!-- Dark Mode Toggle Script -->
        @auth
        <script>
            // Initialize dark mode on page load
            document.addEventListener('DOMContentLoaded', function() {
                const darkMode = {{ $darkMode ? 'true' : 'false' }};
                if (darkMode) {
                    document.body.classList.add('dark');
                    document.documentElement.classList.add('dark');
                } else {
                    document.body.classList.remove('dark');
                    document.documentElement.classList.remove('dark');
                }
            });

            async function toggleDarkMode() {
                const body = document.body;
                const html = document.documentElement;
                const isDark = body.classList.contains('dark');
                
                // Toggle immediately for better UX
                if (isDark) {
                    body.classList.remove('dark');
                    html.classList.remove('dark');
                } else {
                    body.classList.add('dark');
                    html.classList.add('dark');
                }

                // Save preference to server
                try {
                    const response = await fetch('{{ route("admin.settings.update") }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            dark_mode: !isDark
                        })
                    });

                    if (!response.ok) {
                        // Revert on error
                        if (isDark) {
                            body.classList.add('dark');
                            html.classList.add('dark');
                        } else {
                            body.classList.remove('dark');
                            html.classList.remove('dark');
                        }
                        console.error('Failed to save dark mode preference');
                    }
                } catch (error) {
                    // Revert on error
                    if (isDark) {
                        body.classList.add('dark');
                        html.classList.add('dark');
                    } else {
                        body.classList.remove('dark');
                        html.classList.remove('dark');
                    }
                    console.error('Error saving dark mode preference:', error);
                }
            }
        </script>
        @endauth
    </body>
</html>
