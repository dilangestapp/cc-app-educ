<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Progression</h2>
            <a href="{{ route('eleve.dashboard') }}" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour TB
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
                <div class="text-sm text-gray-500">Bientôt</div>
                <div class="mt-2 text-lg font-semibold text-gray-900">Suivi de ta progression</div>
                <p class="mt-2 text-sm text-gray-600">
                    Ici tu verras ta progression par matière, tes badges, tes points et ton classement.
                </p>

                <div class="mt-5 rounded-xl border border-dashed border-gray-300 p-6 text-center">
                    <div class="text-sm font-semibold text-gray-900">Aucune donnée pour l’instant</div>
                    <div class="mt-1 text-sm text-gray-500">Quand tu feras des cours/quiz, ta progression s’affichera.</div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('eleve.cours') }}" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Aller aux cours
                    </a>
                    <a href="{{ route('eleve.quiz') }}" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                        Aller aux quiz
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
