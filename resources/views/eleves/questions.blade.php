<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Questions anonymes</h2>
            <a href="{{ route('eleve.dashboard') }}" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour TB
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
                <div class="text-sm text-gray-500">Prévu</div>
                <div class="mt-2 text-lg font-semibold text-gray-900">Poser une question (anonyme)</div>
                <p class="mt-2 text-sm text-gray-600">
                    Ici tu pourras poser une question sans montrer ton identité (modération prof).
                </p>

                <div class="mt-5 rounded-xl border border-dashed border-gray-300 p-6 text-center">
                    <div class="text-sm font-semibold text-gray-900">Fonction en préparation</div>
                    <div class="mt-1 text-sm text-gray-500">On l’activera dès que la base “questions” est prête.</div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('eleve.cours') }}" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Aller aux cours
                    </a>
                    <a href="{{ route('eleve.groupes') }}" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                        Groupes
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
