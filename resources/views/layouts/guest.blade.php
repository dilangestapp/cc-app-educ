<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CC-APP-EDUC') }}</title>

    @php
        $hasViteManifest = file_exists(public_path('build/manifest.json'));
    @endphp

    @if ($hasViteManifest)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            body { margin:0; font-family: Arial, sans-serif; background:#0b1220; color:#fff; }
            .auth-wrap { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
            .card { width:100%; max-width:420px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:18px; padding:18px; }
            .title { font-size:20px; font-weight:700; margin:0 0 6px; }
            .sub { font-size:13px; opacity:.75; margin:0 0 14px; }
            label { display:block; font-size:13px; opacity:.85; margin:10px 0 6px; }
            input, select { width:100%; padding:10px 12px; border-radius:12px; border:1px solid rgba(255,255,255,.18); background:rgba(255,255,255,.08); color:#fff; outline:none; }
            .row { display:flex; gap:10px; }
            .btn { width:100%; padding:11px 12px; border-radius:12px; border:0; background:#4f46e5; color:#fff; font-weight:700; cursor:pointer; margin-top:14px; }
            .link { display:inline-block; margin-top:12px; color:#c7d2fe; text-decoration:none; font-size:13px; }
            .err { background:rgba(239,68,68,.14); border:1px solid rgba(239,68,68,.25); padding:10px 12px; border-radius:12px; margin-bottom:12px; font-size:13px; }
            .ok  { background:rgba(34,197,94,.14); border:1px solid rgba(34,197,94,.25); padding:10px 12px; border-radius:12px; margin-bottom:12px; font-size:13px; }
        </style>
    @endif
</head>
<body>
    <div class="auth-wrap">
        {{ $slot }}
    </div>
</body>
</html>
