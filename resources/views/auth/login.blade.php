<x-guest-layout>
    <div class="auth-card">

        <div class="auth-title">CC App Educ</div>
        <div class="auth-subtitle">Plateforme éducative sécurisée</div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="auth-field">
                <input type="email" name="email" placeholder="Adresse email" required autofocus>
            </div>

            <div class="auth-field">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>

            <button class="auth-btn">Connexion</button>

            <div class="auth-links">
                <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            </div>
        </form>

    </div>
</x-guest-layout>
