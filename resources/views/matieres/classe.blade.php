<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Matières de la classe
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Classe : <span class="font-semibold text-gray-700">{{ $classeRow->nom ?? 'Classe' }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('classes.index') }}"
                   class="inline-flex items-center px-3 py-2 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Retour
                </a>

                <a href="{{ route('matieres.manage') }}"
                   class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                    Gestion matières
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (!empty($error))
                <div class="mb-4 rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-900">
                    {{ $error }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">

                    @if (!$matieres || count($matieres) === 0)
                        <div class="text-center py-8">
                            <div class="text-gray-700 font-medium">Aucune matière affectée.</div>
                            <div class="text-gray-500 text-sm mt-1">
                                Va dans “Gestion matières” puis clique sur “Affecter”.
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Matière
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($matieres as $m)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900">{{ $m->nom }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                                    <a href="{{ route('matieres.affecter', $m->id) }}"
                                                       class="inline-flex items-center px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">
                                                        Affecter
                                                    </a>

                                                    <a href="{{ route('matieres.edit', $m->id) }}"
                                                       class="inline-flex items-center px-3 py-2 rounded-md bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                                        Modifier
                                                    </a>
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
