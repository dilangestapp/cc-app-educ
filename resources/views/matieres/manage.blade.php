<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-xs text-gray-500">
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                    <span class="mx-1">/</span>
                    <span class="text-gray-700 font-medium">Matières</span>
                </div>

                <h2 class="mt-1 font-semibold text-2xl text-gray-900 leading-tight">
                    Gestion des matières
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Crée, organise et affecte les matières aux classes.
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 shadow-sm">
                    <span class="text-lg leading-none">←</span>
                    <span>Retour</span>
                </a>

                <a href="{{ route('matieres.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
                    <span class="text-lg leading-none">＋</span>
                    <span>Nouvelle matière</span>
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

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Card --}}
            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                {{-- Toolbar --}}
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-600">
                            @php $count = $matieres ? count($matieres) : 0; @endphp
                            <span class="font-semibold text-gray-900">{{ $count }}</span> matière(s)
                        </div>

                        {{-- Search (front-only) --}}
                        <div class="w-full sm:w-80">
                            <input id="matiereSearch"
                                   type="text"
                                   placeholder="Rechercher une matière..."
                                   class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-4 sm:p-6">
                    @if (!$matieres || count($matieres) === 0)
                        <div class="text-center py-12">
                            <div class="mx-auto h-12 w-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-700 text-2xl">
                                📚
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">Aucune matière</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                Commence par créer ta première matière.
                            </p>
                            <div class="mt-5">
                                <a href="{{ route('matieres.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
                                    ＋ Nouvelle matière
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="text-left text-xs uppercase tracking-wider text-gray-500">
                                        <th class="py-3 pr-4">Matière</th>
                                        <th class="py-3 pr-4">Statut</th>
                                        <th class="py-3 text-right">Actions</th>
                                    </tr>
                                </thead>

                                <tbody id="matiereTableBody" class="divide-y divide-gray-100">
                                    @foreach ($matieres as $m)
                                        <tr class="hover:bg-gray-50/60" data-name="{{ strtolower($m->nom) }}">
                                            <td class="py-4 pr-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-9 w-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700">
                                                        {{ strtoupper(mb_substr($m->nom, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ $m->nom }}</div>
                                                        <div class="text-xs text-gray-500">ID: {{ $m->id }}</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="py-4 pr-4">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-green-50 text-green-700 border border-green-200">
                                                    Active
                                                </span>
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

                                                    <form method="POST" action="{{ route('matieres.destroy', $m->id) }}"
                                                          onsubmit="return confirm('Supprimer la matière : {{ addslashes($m->nom) }} ?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="inline-flex items-center px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm text-sm">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div id="matiereEmptySearch" class="hidden text-center py-10">
                                <div class="text-gray-900 font-semibold">Aucun résultat</div>
                                <div class="text-sm text-gray-600 mt-1">Essaie un autre mot-clé.</div>
                            </div>
                        </div>

                        {{-- Simple search script (offline / no libs) --}}
                        <script>
                            (function () {
                                const input = document.getElementById('matiereSearch');
                                const body = document.getElementById('matiereTableBody');
                                const empty = document.getElementById('matiereEmptySearch');

                                if (!input || !body) return;

                                function filter() {
                                    const q = (input.value || '').trim().toLowerCase();
                                    const rows = body.querySelectorAll('tr');
                                    let visible = 0;

                                    rows.forEach(row => {
                                        const name = row.getAttribute('data-name') || '';
                                        const show = !q || name.includes(q);
                                        row.style.display = show ? '' : 'none';
                                        if (show) visible++;
                                    });

                                    if (empty) empty.classList.toggle('hidden', visible !== 0);
                                }

                                input.addEventListener('input', filter);
                            })();
                        </script>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
