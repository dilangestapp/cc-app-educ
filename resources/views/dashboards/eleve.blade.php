<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tableau de bord Élève
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p>✅ Bienvenue Élève : {{ auth()->user()->pseudo ?? auth()->user()->email }}</p>
                <p>Type compte: <b>{{ auth()->user()->type_compte }}</b></p>

                <hr class="my-4">

                <p>📌 Prochaine étape : afficher cours, quiz, notes, etc.</p>
            </div>
        </div>
    </div>
</x-app-layout>
