<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Affecter : {{ $matiereRow->nom }}
            </h2>
            <a href="{{ route('matieres.manage') }}" class="px-3 py-2 rounded bg-gray-200 hover:bg-gray-300">Retour</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <form method="POST" action="{{ route('matieres.affecter.store', $matiereRow->id) }}">
                        @csrf

                        <div class="mb-4">
                            <p class="mb-2 text-gray-700">Choisis les classes :</p>

                            @foreach ($classes as $c)
                                <label class="block mb-2">
                                    <input type="checkbox" name="classes[]" value="{{ $c->id }}"
                                        {{ in_array($c->id, $classesAffectees ?? []) ? 'checked' : '' }}>
                                    <span class="ml-2">{{ $c->nom }}</span>
                                </label>
                            @endforeach
                        </div>

                        <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700" type="submit">
                            Enregistrer
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
