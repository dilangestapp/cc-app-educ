<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes quiz</h2>
            <a href="{{ route('eleve.dashboard') }}" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour TB
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
                <div class="text-sm text-gray-500">Module élève</div>
                <div class="mt-2 text-lg font-semibold text-gray-900">Quiz (bientôt)</div>
                <p class="mt-2 text-sm text-gray-600">
                    Ici tu feras des quiz, verras tes scores, et l’historique.
                </p>

                <div class="mt-5 rounded-xl border border-dashed border-gray-300 p-6 text-center">
                    <div class="text-sm font-semibold text-gray-900">Aucun quiz pour l’instant</div>
                    <div class="mt-1 text-sm text-gray-500">Les quiz apparaîtront quand ils seront publiés.</div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('eleve.cours') }}" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Aller aux cours
                    </a>
                    <a href="{{ route('eleve.progression') }}" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                        Voir progression
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
