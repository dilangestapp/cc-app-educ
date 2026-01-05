<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des matières</h2>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded bg-gray-200 hover:bg-gray-300">Retour</a>
                <a href="{{ route('matieres.create') }}" class="px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">+ Nouvelle matière</a>
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

            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (!$matieres || count($matieres) === 0)
                        <p>Aucune matière pour le moment.</p>
                    @else
                        <table class="min-w-full border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-3 py-2 text-left">Matière</th>
                                    <th class="border px-3 py-2 text-left" style="width:320px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($matieres as $m)
                                    <tr>
                                        <td class="border px-3 py-2">{{ $m->nom }}</td>
                                        <td class="border px-3 py-2" style="display:flex;gap:8px;flex-wrap:wrap;">
                                            <a class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300" href="{{ route('matieres.edit', $m->id) }}">Modifier</a>
                                            <a class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700" href="{{ route('matieres.affecter', $m->id) }}">Affecter</a>

                                            <form method="POST" action="{{ route('matieres.destroy', $m->id) }}" onsubmit="return confirm('Supprimer cette matière ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700" type="submit">Supprimer</button>
                                            </form>
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
