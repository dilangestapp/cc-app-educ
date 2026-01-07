<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ma classe</h2>
            <a href="{{ route('eleve.dashboard') }}" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour TB
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
                <div class="text-sm text-gray-500">Plateforme payante</div>
                <div class="mt-2 text-lg font-semibold text-gray-900">Ta classe est déjà définie</div>
                <p class="mt-2 text-sm text-gray-600">
                    Pour des raisons de sécurité et de facturation, l’élève ne peut pas modifier sa classe.
                    Si tu dois changer de classe, contacte l’administration.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
