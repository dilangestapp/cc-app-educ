<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Groupes</h2>
            <a href="{{ route('eleve.dashboard') }}" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour TB
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
                <div class="text-sm text-gray-500">Prévu</div>
                <div class="mt-2 text-lg font-semibold text-gray-900">Groupes de classe / matière</div>
                <p class="mt-2 text-sm text-gray-600">
                    Ici tu rejoindras des groupes, suivras des annonces et échangeras.
                </p>

                <div class="mt-5 rounded-xl border border-dashed border-gray-300 p-6 text-center">
                    <div class="text-sm font-semibold text-gray-900">Fonction en préparation</div>
                    <div class="mt-1 text-sm text-gray-500">On active dès qu’on crée la table “groups”.</div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('eleve.questions') }}" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Questions
                    </a>
                    <a href="{{ route('eleve.quiz') }}" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                        Quiz
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
