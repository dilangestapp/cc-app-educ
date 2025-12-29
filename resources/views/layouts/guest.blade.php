<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
   <body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">CC App Educ</h1>
            <p class="text-sm text-gray-500">Plateforme sécurisée</p>
        </div>

        {{ $slot }}

    </div>

</body>

</html>
