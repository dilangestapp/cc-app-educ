<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🏫 Affecter : {{ $matiereRow->nom }}
            </h2>

            <a href="{{ route('matieres.manage') }}"
               class="px-4 py-2 rounded bg-gray-200 text-gray-900 hover:bg-gray-300">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6 space-y-4">

                    <form method="POST" action="{{ route('matieres.affecter.store', $matiereRow->id) }}" class="space-y-4">
                        @csrf

                        <div class="space-y-2">
                            @foreach($classes as $c)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="classes[]" value="{{ $c->id }}"
                                           @if(in_array($c->id, $classesAffectees)) checked @endif>
                                    <span>{{ $c->nom }}</span>
                                </label>
                            @endforeach
                        </div>

                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                            Enregistrer
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
