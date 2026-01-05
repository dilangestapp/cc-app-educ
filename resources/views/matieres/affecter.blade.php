<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Affecter une matière
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Matière : <span class="font-semibold text-gray-700">{{ $matiereRow->nom }}</span>
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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

                    <form method="POST" action="{{ route('matieres.affecter.store', $matiereRow->id) }}" class="space-y-4">
                        @csrf

                        <div>
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <div class="font-medium text-gray-800">Choisis les classes à associer</div>
                                <div class="text-sm text-gray-500">
                                    {{ count($classesAffectees ?? []) }} sélectionnée(s)
                                </div>
                            </div>

                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($classes as $c)
                                    <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-md hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox"
                                               name="classes[]"
                                               value="{{ $c->id }}"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-200"
                                               {{ in_array($c->id, $classesAffectees ?? []) ? 'checked' : '' }}>
                                        <span class="text-gray-800">{{ $c->nom }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                                Enregistrer
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
