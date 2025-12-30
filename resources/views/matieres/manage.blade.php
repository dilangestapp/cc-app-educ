<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">📚 Gestion des matières</h2>

            <div class="flex gap-2">
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-sm">
                    Retour
                </a>

                <a href="{{ route('matieres.create') }}"
                   class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white text-sm">
                    + Nouvelle matière
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 rounded bg-green-50 border border-green-200 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="p-6">

                    @if($matieres->count() === 0)
                        <p class="text-gray-600">Aucune matière. Clique sur “Nouvelle matière”.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b">
                                        <th class="py-2">Nom</th>
                                        <th class="py-2">Classes affectées</th>
                                        <th class="py-2 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($matieres as $m)
                                        <tr class="border-b">
                                            <td class="py-2 font-medium">{{ $m->nom }}</td>
                                            <td class="py-2">{{ $m->classes_count }}</td>
                                            <td class="py-2">
                                                <div class="flex gap-2 justify-end">
                                                    <a href="{{ route('matieres.affecter', $m->id) }}"
                                                       class="px-3 py-1 rounded bg-indigo-600 hover:bg-indigo-700 text-white text-xs">
                                                        Affecter
                                                    </a>

                                                    <a href="{{ route('matieres.edit', $m->id) }}"
                                                       class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-xs">
                                                        Modifier
                                                    </a>

                                                    <form action="{{ route('matieres.destroy', $m->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Supprimer cette matière ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-xs">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-6 text-sm text-blue-900 bg-blue-50 border border-blue-200 rounded p-4">
                        ✅ Rappel : Une matière existe une seule fois, mais peut être affectée à plusieurs classes.
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
