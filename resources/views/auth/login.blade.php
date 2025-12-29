<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-100 px-4">

        <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">

            <!-- Logo / Title -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-indigo-700">
                    CC App Educ
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Plateforme sécurisée de gestion éducative
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Adresse email')" />
                    <x-text-input
                        id="email"
                        class="block mt-1 w-full rounded-lg"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Mot de passe')" />
                    <x-text-input
                        id="password"
                        class="block mt-1 w-full rounded-lg"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between text-sm">
                    <label for="remember_me" class="inline-flex items-center">
                        <input
                            id="remember_me"
                            type="checkbox"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            name="remember"
                        >
                        <span class="ml-2 text-gray-600">
                            Se souvenir de moi
                        </span>
                    </label>

                    @if (Route::has('password.request'))
                        <a
                            class="text-indigo-600 hover:underline"
                            href="{{ route('password.request') }}"
                        >
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <div>
                    <x-primary-button class="w-full justify-center py-2 text-base">
                        Connexion
                    </x-primary-button>
                </div>
            </form>

        </div>

    </div>
</x-guest-layout>
