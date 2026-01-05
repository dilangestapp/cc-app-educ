<x-app-layout>
    <style>
        nav.bg-white { background: rgba(255,255,255,.78) !important; backdrop-filter: blur(10px); }
        header.bg-white { background: rgba(255,255,255,.78) !important; backdrop-filter: blur(10px); box-shadow: none !important; }
    </style>

    <div class="min-h-screen"
         style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        <div class="min-h-screen bg-slate-900/55">
            <x-slot name="header">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Matières de la classe</h2>
                        <p class="text-sm text-gray-700 mt-1">
                            Classe : <span class="font-semibold text-gray-900">{{ $classeRow->nom ?? 'Classe' }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('classes.index') }}"
                           class="inline-flex items-center px-3 py-2 rounded-lg bg-white/90 border border-white/60 text-gray-800 hover:bg-white shadow-sm">
                            Retour
                        </a>

                        <a href="{{ route('matieres.manage') }}"
                           class="inline-flex items-center px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
                            Gestion matières
                        </a>
                    </div>
                </div>
            </x-slot>

            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    @if (!empty($error))
                        <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-yellow-900">
                            {{ $error }}
                        </div>
                    @endif

                    <div class="bg-white/95 shadow-sm sm:rounded-2xl border border-white/50 overflow-hidden">
                        <div class="p-6">

                            @if (!$matieres || count($matieres) === 0)
                                <div class="text-center py-8">
                                    <div class="text-gray-900 font-semibold">Aucune matière affectée.</div>
                                    <div class="text-sm text-gray-600 mt-1">Va dans “Gestion matières” puis clique sur “Affecter”.</div>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="text-left text-xs uppercase tracking-wider text-gray-500">
                                                <th class="py-3 pr-4">Matière</th>
                                                <th class="py-3 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach ($matieres as $m)
                                                <tr class="hover:bg-gray-50/60">
                                                    <td class="py-4 pr-4">
                                                        <div class="font-semibold text-gray-900">{{ $m->nom }}</div>
                                                    </td>
                                                    <td class="py-4">
                                                        <div class="flex items-center justify-end gap-2 flex-wrap">
                                                            <a href="{{ route('matieres.affecter', $m->id) }}"
                                                               class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm text-sm">
                                                                Affecter
                                                            </a>

                                                            <a href="{{ route('matieres.edit', $m->id) }}"
                                                               class="inline-flex items-center px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 shadow-sm text-sm">
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
        </div>
    </div>
</x-app-layout>
