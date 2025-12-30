<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>CC App Educ — Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/logo.png">

    <!-- CSS AUTH LOCAL -->
    <link rel="stylesheet" href="/css/auth.css?v={{ time() }}">
</head>
<body>

    <div class="auth-wrapper">

        <!-- BRANDING UNIQUE -->
        <div class="brand">
            <img src="/images/logo.png" alt="CC App Educ Logo" class="brand-logo">

            <h1 class="brand-name">
                CC <span>APP EDUC</span>
            </h1>

            <p class="brand-tagline">
                Plateforme éducative sécurisée
            </p>
        </div>

        <!-- CONTENU (login, reset, etc.) -->
        {{ $slot }}

    </div>

</body>
</html>
