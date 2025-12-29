<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center
                bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-500">

        <div class="w-full max-w-md bg-white/90 backdrop-blur
                    rounded-2xl shadow-2xl px-8 py-10">

            <!-- Branding -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-gray-800">
                    CC App Educ
                </h1>
                <p class="text-sm text-gray-500 mt-2">
                    Plateforme sécurisée de gestion éducative
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Adresse email" />
                    <x-text-input
                        id="email"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        class="mt-1 w-full rounded-xl"
                        placeholder="exemple@email.com"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" value="Mot de passe" />
                    <x-text-input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="mt-1 w-full rounded-xl"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember + Forgot -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center text-gray-600">
                        <input type="checkbox" name="remember"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2">Se souvenir de moi</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-indigo-600 hover:underline">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full py-3 rounded-xl
                               bg-indigo-600 hover:bg-indigo-700
                               text-white font-semibold
                               transition duration-200 shadow-lg">
                    Connexion
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
