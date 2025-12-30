<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Classes — CC APP EDUC
            </h2>

            <a href="{{ route('classes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                ➕ Nouvelle classe
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <p class="text-lg font-semibold mb-4">🏫 Gestion des classes</p>

                    @if ($classes->isEmpty())
                        <p class="text-gray-600">
                            Aucune classe n’a encore été créée.
                        </p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($classes as $classe)
                                <div class="border rounded p-4 bg-gray-50 hover:bg-gray-100 transition">

                                    <p class="font-semibold text-lg mb-2">
                                        🏫 {{ $classe->nom }}
                                    </p>

                                    <a href="{{ route('matieres.index', $classe->id) }}"
                                       class="text-blue-600 hover:underline text-sm">
                                        📘 Voir les matières →
                                    </a>

                                </div>
                            @endforeach
                        </div>
                    @endif

                    <p class="text-sm text-gray-500 mt-6">
                        Chaque classe contient des matières, puis des cours.
                    </p>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
