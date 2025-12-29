<x-guest-layout>
    <div class="auth-card">

        <div class="auth-header">
            <h1 class="auth-title">CC App Educ</h1>
            <p class="auth-subtitle">
                Plateforme éducative sécurisée
            </p>
        </div>

        <x-auth-session-status :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="auth-form">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <div class="input-wrapper">
                    <span>📧</span>
                    <input
                        type="email"
                        name="email"
                        placeholder="Adresse email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <div class="input-wrapper">
                    <span>🔒</span>
                    <input
                        type="password"
                        name="password"
                        placeholder="Mot de passe"
                        required
                    >
                </div>
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Options -->
            <div class="auth-options">
                <label>
                    <input type="checkbox" name="remember">
                    Se souvenir de moi
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>

            <!-- Submit -->
            <div class="auth-submit">
                <button type="submit">→</button>
            </div>
        </form>

    </div>
</x-guest-layout>
