<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CC-APP-EDUC') }}</title>

    {{-- ✅ Base CSS TOUJOURS (même si Vite/Tailwind ne charge pas) --}}
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background: #eef2f7;
            color: #0f172a;
        }
        a { color: inherit; }
    </style>

    @php $hasViteManifest = file_exists(public_path('build/manifest.json')); @endphp
    @if ($hasViteManifest)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    {{ $slot }}
</body>
</html>
