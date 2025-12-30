<x-guest-layout>
    <div class="auth-card">

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
