<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Modifier une matière
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Modifie le nom de la matière sélectionnée.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('matieres.manage') }}"
                   class="inline-flex items-center px-3 py-2 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                    <div class="font-semibold mb-1">Erreur</div>
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    <form method="POST" action="{{ route('matieres.update', $matiereRow->id) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la matière</label>
                            <input name="nom"
                                   value="{{ old('nom', $matiereRow->nom) }}"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                   required>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                                Mettre à jour
                            </button>

                            <a href="{{ route('matieres.manage') }}"
                               class="inline-flex items-center px-4 py-2 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Annuler
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
