<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CC-APP-EDUC') }}</title>

    {{-- ✅ Base CSS de secours, NE FORCE PAS le fond (ton login/register garde ses couleurs) --}}
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            /* IMPORTANT: on ne force pas de background ici */
            color: #0f172a;
        }
        a { color: inherit; }
        .logo-wrap{
            width: 100%;
            display:flex;
            justify-content:center;
            align-items:center;
            margin: 18px 0 10px;
        }
        .logo-wrap img{
            width: 92px;
            height: auto;
            display:block;
        }
    </style>

    @php $hasViteManifest = file_exists(public_path('build/manifest.json')); @endphp
    @if ($hasViteManifest)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="logo-wrap">
        <img src="{{ asset('images/logo.png') }}" alt="CC-APP-EDUC">
    </div>

    {{ $slot }}
</body>
</html>
