<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">{{ $title }}</h2>
            <a href="{{ route('eleve.cours') }}" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour cours
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">
                <div class="text-xs text-gray-500 mb-4">Cours #{{ $id }}</div>

                <div class="prose max-w-none">
                    {{-- Affichage simple : si ton contenu contient du HTML, il s’affichera en HTML --}}
                    {!! nl2br(e($content)) !!}
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('eleve.quiz') }}" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Aller aux quiz
                    </a>
                    <a href="{{ route('eleve.questions') }}" class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                        Poser une question
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
