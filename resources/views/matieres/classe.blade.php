<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Matières de la classe : {{ $classeRow->nom ?? 'Classe' }}
            </h2>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('classes.index') }}" class="px-3 py-2 rounded bg-gray-200 hover:bg-gray-300">Retour</a>
                <a href="{{ route('matieres.manage') }}" class="px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">Gestion matières</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (!empty($error))
                <div class="mb-4 p-3 rounded bg-yellow-100 text-yellow-900">
                    {{ $error }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (!$matieres || count($matieres) === 0)
                        <p>Aucune matière affectée.</p>
                    @else
                        <ul class="list-disc ml-5">
                            @foreach ($matieres as $m)
                                <li>{{ $m->nom }}</li>
                            @endforeach
                        </ul>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
