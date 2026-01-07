<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="pseudo" value="Pseudonyme" />
            <x-text-input id="pseudo" class="block mt-1 w-full" type="text" name="pseudo"
                          :value="old('pseudo')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('pseudo')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       name="remember">
                <span class="ms-2 text-sm text-gray-600">Se souvenir de moi</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900"
               href="{{ route('register') }}">
                Créer un compte
            </a>

            <x-primary-button class="ms-3">
                Connexion
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
