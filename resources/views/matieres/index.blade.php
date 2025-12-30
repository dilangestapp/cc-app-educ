<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Matières — {{ $classeRow->nom }}
            </h2>

            <a href="{{ route('matieres.create', $classeRow->id) }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                ➕ Nouvelle matière
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($matieres->isEmpty())
                        <p class="text-gray-600">
                            Aucune matière n’a encore été créée pour cette classe.
                        </p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($matieres as $matiere)
                                <div class="border rounded p-4 bg-gray-50">
                                    <p class="font-semibold text-lg">📘 {{ $matiere->nom }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <p class="text-sm text-gray-500 mt-6">
                        Les matières sont rattachées à une classe.
                    </p>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
