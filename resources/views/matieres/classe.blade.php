<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Matières de la classe : {{ $classeRow->nom ?? 'Classe' }}
            </h2>

            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('classes.index') }}" class="px-3 py-2 rounded bg-gray-200 hover:bg-gray-300">
                    Retour aux classes
                </a>
                <a href="{{ route('matieres.manage') }}" class="px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                    Gestion globale des matières
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (!$matieres || count($matieres) === 0)
                        <p>Aucune matière n’est affectée à cette classe pour le moment.</p>
                    @else
                        <table class="min-w-full border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-3 py-2 text-left">Matière</th>
                                    <th class="border px-3 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($matieres as $m)
                                    <tr>
                                        <td class="border px-3 py-2">{{ $m->nom }}</td>
                                        <td class="border px-3 py-2" style="display:flex;gap:8px;flex-wrap:wrap;">
                                            <a class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300"
                                               href="{{ route('matieres.edit', $m->id) }}">
                                                Modifier
                                            </a>

                                            <a class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700"
                                               href="{{ route('matieres.affecter', $m->id) }}">
                                                Affecter
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
