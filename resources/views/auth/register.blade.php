<x-guest-layout>
    <div class="card">
        <h1 class="title">Créer un compte</h1>
        <p class="sub">Utilise un pseudo (pas de nom réel). Choisis ton type de compte.</p>

        @if ($errors->any())
            <div class="err">
                <b>Erreurs :</b>
                <ul style="margin:6px 0 0 18px;">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label for="pseudo">Pseudo</label>
            <input id="pseudo" name="pseudo" type="text" value="{{ old('pseudo') }}" required autofocus>

            <label for="type_compte">Type de compte</label>
            <select id="type_compte" name="type_compte" required>
                <option value="">-- Choisir --</option>
                <option value="eleve" {{ old('type_compte')==='eleve'?'selected':'' }}>Élève</option>
                <option value="enseignant" {{ old('type_compte')==='enseignant'?'selected':'' }}>Enseignant</option>
                <option value="parent" {{ old('type_compte')==='parent'?'selected':'' }}>Parent</option>
                <option value="admin" {{ old('type_compte')==='admin'?'selected':'' }}>Admin</option>
            </select>

            <label for="email">Email (optionnel si tu gardes la connexion par email)</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username">

            <label for="password">Mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">

            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

            <button class="btn" type="submit">Créer le compte</button>

            <a class="link" href="{{ route('login') }}">Déjà inscrit ? Se connecter</a>
        </form>
    </div>
</x-guest-layout>
