<x-app-layout>
    <style>
        /* 1) On neutralise le fond gris du layout Breeze sur CETTE page */
        .min-h-screen.bg-gray-100 { background: transparent !important; }

        /* 2) On garde la navbar mais on lui donne un style "glass" propre */
        nav.bg-white {
            background: rgba(10, 14, 18, .55) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,.08) !important;
        }
        nav .text-gray-500, nav .text-gray-700, nav .text-gray-900 { color: rgba(255,255,255,.85) !important; }
        nav a:hover { color: white !important; }

        /* 3) Inputs : meilleure lisibilité sur fond sombre */
        .glass-input {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            color: rgba(255,255,255,.92);
        }
        .glass-input::placeholder { color: rgba(255,255,255,.55); }
        .glass-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,.25); }
    </style>

    {{-- Background full page --}}
    <div class="min-h-screen"
         style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">

        {{-- Overlay to make everything readable --}}
        <div class="min-h-screen bg-black/55">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                {{-- Internal Header (PRO) --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="text-xs text-white/60">
                            <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                            <span class="mx-1">/</span>
                            <span class="text-white font-semibold">Matières</span>
                        </div>

                        <h1 class="mt-2 text-3xl font-bold text-white tracking-tight">
                            Gestion des matières
                        </h1>

                        <p class="mt-1 text-sm text-white/70">
                            Crée, modifie, supprime et affecte les matières aux classes.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 border border-white/10 text-white hover:bg-white/15">
                            <span>←</span> Retour
                        </a>

                        <a href="{{ route('matieres.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
                            <span class="text-lg leading-none">＋</span> Nouvelle matière
                        </a>
                    </div>
                </div>

                {{-- Alerts --}}
                <div class="mt-6 space-y-3">
                    @if (!empty($error))
                        <div class="rounded-xl border border-yellow-400/30 bg-yellow-300/10 px-4 py-3 text-yellow-100">
                            {{ $error }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="rounded-xl border border-green-400/25 bg-green-300/10 px-4 py-3 text-green-100">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>

                {{-- Main Card --}}
                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 backdrop-blur-xl shadow-lg overflow-hidden">

                    {{-- Toolbar --}}
                    <div class="p-4 sm:p-6 border-b border-white/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm text-white/70">
                                @php $count = $matieres ? count($matieres) : 0; @endphp
                                <span class="font-semibold text-white">{{ $count }}</span> matière(s)
                            </div>

                            <div class="w-full sm:w-96">
                                <input id="matiereSearch"
                                       type="text"
                                       placeholder="Rechercher une matière..."
                                       class="glass-input w-full rounded-xl px-4 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-4 sm:p-6">
                        @if (!$matieres || count($matieres) === 0)
                            <div class="text-center py-12">
                                <div class="mx-auto h-12 w-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-2xl">
                                    📚
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-white">Aucune matière</h3>
                                <p class="mt-1 text-sm text-white/70">Crée ta première matière pour commencer.</p>
                                <div class="mt-5">
                                    <a href="{{ route('matieres.create') }}"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
                                        ＋ Nouvelle matière
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="text-left text-xs uppercase tracking-wider text-white/55">
                                            <th class="py-3 pr-4">Matière</th>
                                            <th class="py-3 pr-4">Statut</th>
                                            <th class="py-3 text-right">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="matiereTableBody" class="divide-y divide-white/10">
                                        @foreach ($matieres as $m)
                                            <tr class="hover:bg-white/5 transition" data-name="{{ strtolower($m->nom) }}">
                                                <td class="py-4 pr-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="h-10 w-10 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center text-white font-semibold">
                                                            {{ strtoupper(mb_substr($m->nom, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="font-semibold text-white">{{ $m->nom }}</div>
                                                            <div class="text-xs text-white/55">ID: {{ $m->id }}</div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="py-4 pr-4">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-green-300/10 text-green-200 border border-green-300/20">
                                                        Active
                                                    </span>
                                                </td>

                                                <td class="py-4">
                                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                                        <a href="{{ route('matieres.affecter', $m->id) }}"
                                                           class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                                            Affecter
                                                        </a>

                                                        <a href="{{ route('matieres.edit', $m->id) }}"
                                                           class="inline-flex items-center px-4 py-2 rounded-xl bg-white/10 border border-white/10 text-white hover:bg-white/15 text-sm">
                                                            Modifier
                                                        </a>

                                                        <form method="POST" action="{{ route('matieres.destroy', $m->id) }}"
                                                              onsubmit="return confirm('Supprimer la matière : {{ addslashes($m->nom) }} ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="inline-flex items-center px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 text-sm">
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
                                    <div class="text-white font-semibold">Aucun résultat</div>
                                    <div class="text-sm text-white/70 mt-1">Essaie un autre mot-clé.</div>
                                </div>
                            </div>

                            {{-- Search script (offline) --}}
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
    </div>
</x-app-layout>
