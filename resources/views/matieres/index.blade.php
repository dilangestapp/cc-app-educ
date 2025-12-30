<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📚 Matières — {{ $classeRow->nom }}
            </h2>
            <a href="{{ route('classes.index') }}" class="text-sm text-gray-600 hover:underline">Retour</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm rounded-lg p-6">
                @if($matieres->count() === 0)
                    <p class="text-gray-700">Aucune matière affectée à cette classe.</p>
                    <p class="text-gray-600 mt-2">Va dans <strong>Gestion des matières</strong> pour affecter.</p>
                    <a href="{{ route('matieres.manage') }}"
                       class="inline-flex mt-4 px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                        Aller à Gestion des matières
                    </a>
                @else
                    <ul class="space-y-2">
                        @foreach($matieres as $m)
                            <li class="p-3 border rounded flex items-center justify-between">
                                <span class="font-medium text-gray-900">{{ $m->nom }}</span>
                                <span class="text-sm text-gray-500">Cours (bientôt)</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
