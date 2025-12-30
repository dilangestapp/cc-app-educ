<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔗 Affecter la matière — {{ $matiereRow->nom }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST"
                      action="{{ route('matieres.affecter.store', $matiereRow->id) }}">
                    @csrf

                    <p class="font-medium mb-4">Sélectionner les classes :</p>

                    @foreach ($classes as $classe)
                        <label class="flex items-center gap-2 mb-2">
                            <input type="checkbox"
                                   name="classes[]"
                                   value="{{ $classe->id }}"
                                   {{ in_array($classe->id, $classesAffectees) ? 'checked' : '' }}>
                            {{ $classe->nom }}
                        </label>
                    @endforeach

                    <div class="flex justify-end gap-2 mt-6">
                        <a href="{{ route('classes.index') }}"
                           class="px-4 py-2 bg-gray-300 rounded">
                            Retour
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
