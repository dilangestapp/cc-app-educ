<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard — CC APP EDUC
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Message de bienvenue -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    👋 Bienvenue <strong>{{ Auth::user()->name }}</strong> sur <strong>CC APP EDUC</strong>.
                </div>
            </div>

            <!-- Cartes principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">👨‍🎓 Élèves</h3>
                    <p class="text-gray-600 mt-2">Gestion des élèves</p>
                </div>

                <a href="{{ route('classes.index') }}" class="block hover:bg-gray-50 transition">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">🏫 Classes</h3>
                        <p class="text-gray-600 mt-2">Organisation des classes</p>
                    </div>
                </a>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">👨‍🏫 Enseignants</h3>
                    <p class="text-gray-600 mt-2">Gestion du personnel enseignant</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">📊 Notes</h3>
                    <p class="text-gray-600 mt-2">Résultats et évaluations</p>
                </div>

            </div>

            <!-- Actions rapides -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">⚡ Actions rapides</h3>
                    <ul class="list-disc list-inside text-gray-700">
                        <li>Ajouter un élève</li>
                        <li>Créer une classe</li>
                        <li>Saisir des notes</li>
                        <li>Consulter les bulletins</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
