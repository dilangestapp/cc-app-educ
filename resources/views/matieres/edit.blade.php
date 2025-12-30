<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ✏️ Modifier la matière
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST" action="{{ route('matieres.update', $matiereRow->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">
                            Nom de la matière
                        </label>
                        <input type="text"
                               name="nom"
                               value="{{ $matiereRow->nom }}"
                               required
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('classes.index') }}"
                           class="px-4 py-2 bg-gray-300 rounded">
                            Annuler
                        </a>
                        <button
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Enregistrer
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>
