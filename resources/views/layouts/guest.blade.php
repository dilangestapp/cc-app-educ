<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>CC App Educ — Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSS AUTH DIRECT (PAS VITE) --}}
    <link rel="stylesheet" href="/css/auth.css?v={{ time() }}">
</head>
<body>

    <div class="auth-wrapper">
        {{ $slot }}
    </div>

</body>
</html>
