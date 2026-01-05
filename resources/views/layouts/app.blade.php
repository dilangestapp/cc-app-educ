<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- ⚠️ (Optionnel) fonts externes: dépend d'internet --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            html, body { width: 100%; max-width: 100%; overflow-x: auto; }
            main { width: 100%; max-width: 100%; }

            /* ✅ FIX GLOBAL: tableaux toujours visibles sur mobile */
            @media (max-width: 640px) {

                /* le tableau devient scrollable horizontalement */
                table {
                    display: block !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    overflow-x: auto !important;
                    -webkit-overflow-scrolling: touch;
                    border-collapse: separate;
                }

                /* empêche les colonnes d’être compressées/coupées */
                thead, tbody, tr {
                    display: table;
                    width: max-content;
                    table-layout: auto;
                }

                th, td {
                    white-space: nowrap !important;
                }

                /* si une section parent bloque le scroll, on force */
                .overflow-x-auto { overflow-x: auto !important; }
            }
        </style>
    </head>

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
