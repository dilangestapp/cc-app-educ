<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>CC App Educ — Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/logo.png">

    <!-- CSS AUTH (LOCAL, VERSIONNÉ) -->
    <link rel="stylesheet" href="/css/auth.css?v={{ time() }}">
</head>
<body>

    <div class="auth-wrapper">

        <!-- BRAND GLOBAL (toutes pages accueil) -->
        <div class="brand">
            <img src="/images/logo.png" alt="CC App Educ Logo">
            <div class="brand-name">
                CC <span>APP EDUC</span>
            </div>
            <div class="brand-tagline">
                Plateforme éducative sécurisée
            </div>
        </div>

        <!-- CONTENU PAGE (login, reset, etc.) -->
        {{ $slot }}

    </div>

</body>
</html>
