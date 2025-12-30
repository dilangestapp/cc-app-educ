<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📚 Matières — {{ $classeRow->nom }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('matieres.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    ➕ Créer une matière
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg">
                <div class="p-6">

                    @if ($matieres->isEmpty())
                        <p class="text-gray-600">
                            Aucune matière n’est encore affectée à cette classe.
                        </p>
                    @else
                        <table class="w-full border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-4 py-2 text-left">Matière</th>
                                    <th class="border px-4 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($matieres as $matiere)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-4 py-2 font-medium">
                                            📘 {{ $matiere->nom }}
                                        </td>
                                        <td class="border px-4 py-2 text-center space-x-3">

                                            <a href="{{ route('matieres.affecter', $matiere->id) }}"
                                               class="text-indigo-600 hover:underline">
                                                🔗 Affecter
                                            </a>

                                            <a href="{{ route('matieres.edit', $matiere->id) }}"
                                               class="text-blue-600 hover:underline">
                                                ✏️ Modifier
                                            </a>

                                            <form action="{{ route('matieres.destroy', $matiere->id) }}"
                                                  method="POST"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    onclick="return confirm('Supprimer cette matière ?')"
                                                    class="text-red-600 hover:underline">
                                                    🗑️ Supprimer
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <p class="text-sm text-gray-500 mt-4">
                        Une matière est créée une seule fois et peut être affectée à plusieurs classes.
                    </p>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
