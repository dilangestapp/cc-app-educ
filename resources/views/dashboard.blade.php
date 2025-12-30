<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard — CC APP EDUC
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Bienvenue -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6 text-gray-900 text-lg">
                    👋 Bienvenue <strong>{{ Auth::user()->pseudo ?? Auth::user()->name }}</strong>
                </div>
            </div>

            <!-- Cartes principales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Élèves (à venir) -->
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                    <h3 class="text-lg font-semibold">👨‍🎓 Élèves</h3>
                    <p class="text-gray-600 mt-2">Accès élèves (à venir)</p>
                </div>

                <!-- Classes (point d'entrée) -->
                <a href="{{ route('classes.index') }}"
                   class="bg-white p-6 rounded-lg shadow hover:shadow-md transition block">
                    <h3 class="text-lg font-semibold">🏫 Classes</h3>
                    <p class="text-gray-600 mt-2">
                        Accéder aux matières et cours par classe
                    </p>
                </a>

                <!-- Matières (via classes) -->
                <a href="{{ route('classes.index') }}"
                   class="bg-white p-6 rounded-lg shadow hover:shadow-md transition block">
                    <h3 class="text-lg font-semibold">📚 Matières</h3>
                    <p class="text-gray-600 mt-2">
                        Parcours : Classe → Matière
                    </p>
                </a>

                <!-- Notes / Quiz (à venir) -->
                <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                    <h3 class="text-lg font-semibold">📊 Notes</h3>
                    <p class="text-gray-600 mt-2">Évaluations (à venir)</p>
                </div>

            </div>

            <!-- Rappel pédagogique -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-blue-900">
                <p class="font-semibold mb-1">📌 Logique pédagogique</p>
                <p class="text-sm">
                    Classe → Matière → Cours → Quiz → Résultats
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
