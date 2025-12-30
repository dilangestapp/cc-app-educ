<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Créer une classe — CC APP EDUC
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('classes.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Nom de la classe
                            </label>
                            <input type="text" name="nom"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                   placeholder="Ex: 6e A, Terminale C"
                                   required>
                        </div>

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('classes.index') }}"
                               class="px-4 py-2 bg-gray-300 rounded">
                                Annuler
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Enregistrer
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
