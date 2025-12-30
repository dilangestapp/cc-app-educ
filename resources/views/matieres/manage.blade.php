<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📚 Gestion des matières
            </h2>

            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:underline">
                    Retour
                </a>

                <a href="{{ route('matieres.create') }}"
                   class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                    + Nouvelle matière
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <p class="text-gray-600">
                    Gestion globale : une matière existe une seule fois, puis on l’affecte aux classes.
                </p>
                <div class="mt-4 p-3 border rounded bg-gray-50 text-sm">
                    ✅ Une matière existe une seule fois, mais peut être affectée à plusieurs classes.
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    @if($matieres->count() === 0)
                        <p class="text-gray-700">Aucune matière. Clique sur <strong>“Nouvelle matière”</strong>.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                <tr class="text-left border-b">
                                    <th class="py-3">Nom</th>
                                    <th class="py-3">Classes liées</th>
                                    <th class="py-3 text-right">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($matieres as $m)
                                    <tr class="border-b">
                                        <td class="py-3 font-medium text-gray-900">{{ $m->nom }}</td>
                                        <td class="py-3 text-gray-700">{{ $m->classes_count }}</td>
                                        <td class="py-3">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('matieres.affecter', $m->id) }}"
                                                   class="px-3 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                                                    Affecter
                                                </a>

                                                <a href="{{ route('matieres.edit', $m->id) }}"
                                                   class="px-3 py-2 rounded bg-gray-800 text-white hover:bg-gray-900">
                                                    Modifier
                                                </a>

                                                <form method="POST" action="{{ route('matieres.destroy', $m->id) }}"
                                                      onsubmit="return confirm('Supprimer cette matière ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-3 py-2 rounded bg-red-600 text-white hover:bg-red-700">
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
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
