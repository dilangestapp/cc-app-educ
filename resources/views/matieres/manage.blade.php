<x-app-layout>
    <style>
        /* Neutraliser le fond par défaut Breeze sur CETTE page */
        .min-h-screen.bg-gray-100 { background: transparent !important; }

        /* Navbar Breeze => glass dark (pour se fondre dans l'image) */
        nav.bg-white, header.bg-white {
            background: rgba(8, 12, 16, .55) !important;
            backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid rgba(255,255,255,.10) !important;
        }

        /* Couleurs du texte dans la navbar (sinon illisible) */
        nav a, nav button, nav span, nav div {
            color: rgba(255,255,255,.88) !important;
        }
        nav a:hover, nav button:hover {
            color: #fff !important;
        }

        /* Dropdown menu (si Breeze met du blanc) */
        .dropdown-content, [role="menu"] {
            background: rgba(12, 16, 20, .92) !important;
            border: 1px solid rgba(255,255,255,.10) !important;
            backdrop-filter: blur(10px) !important;
        }

        .glass-card {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.10);
            backdrop-filter: blur(14px);
        }
        .glass-input {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
            color: rgba(255,255,255,.92);
        }
        .glass-input::placeholder { color: rgba(255,255,255,.55); }
        .glass-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99,102,241,.28);
            border-color: rgba(99,102,241,.55);
        }
    </style>

    {{-- Background --}}
    <div class="min-h-screen"
         style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        {{-- Overlay lisible --}}
        <div class="min-h-screen bg-black/60">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

                {{-- Header interne pro --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-xs text-white/60">
                            <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                            <span class="mx-1">/</span>
                            <span class="text-white font-semibold">Matières</span>
                        </div>
                        <h1 class="mt-1 text-2xl sm:text-3xl font-bold text-white tracking-tight">
                            Gestion des matières
                        </h1>
                        <p class="mt-1 text-sm text-white/70">
                            Crée, organise et affecte les matières aux classes.
                        </p>
                    </div>

                    {{-- Actions top --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 border border-white/10 text-white hover:bg-white/15 transition">
                            <span>←</span> Retour
                        </a>

                        <a href="{{ route('matieres.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition shadow-sm">
                            <span class="text-lg leading-none">＋</span> Nouvelle matière
                        </a>
                    </div>
                </div>

                {{-- Alerts --}}
                <div class="mt-4 space-y-2">
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

                @php $count = $matieres ? count($matieres) : 0; @endphp

                {{-- Card principale --}}
                <div class="mt-5 glass-card rounded-2xl overflow-hidden shadow-lg">

                    {{-- Toolbar --}}
                    <div class="p-4 sm:p-5 border-b border-white/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-sm text-white/75">
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

                    {{-- Contenu --}}
                    <div class="p-4 sm:p-5">
                        @if (!$matieres || $count === 0)
                            <div class="text-center py-10">
                                <div class="mx-auto h-12 w-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-2xl">
                                    📚
                                </div>
                                <h3 class="mt-4 text-lg font-semibold text-white">Aucune matière</h3>
                                <p class="mt-1 text-sm text-white/70">Crée ta première matière pour commencer.</p>
                                <div class="mt-5">
                                    <a href="{{ route('matieres.create') }}"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                        ＋ Nouvelle matière
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-xl border border-white/10">
                                <table class="min-w-full">
                                    <thead class="bg-white/5">
                                        <tr class="text-left text-xs uppercase tracking-wider text-white/55">
                                            <th class="py-3 px-4">Matière</th>
                                            <th class="py-3 px-4">Statut</th>
                                            <th class="py-3 px-4 text-right">Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody id="matiereTableBody" class="divide-y divide-white/10">
                                        @foreach ($matieres as $m)
                                            <tr class="hover:bg-white/5 transition" data-name="{{ strtolower($m->nom) }}">
                                                {{-- Matière --}}
                                                <td class="py-4 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="h-10 w-10 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center text-white font-semibold">
                                                            {{ strtoupper(substr($m->nom, 0, 1)) }}
                                                        </div>
                                                        <div class="leading-tight">
                                                            <div class="font-semibold text-white">{{ $m->nom }}</div>
                                                            <div class="text-[11px] text-white/55">ID: {{ $m->id }}</div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Statut --}}
                                                <td class="py-4 px-4">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-green-300/10 text-green-200 border border-green-300/20">
                                                        Active
                                                    </span>
                                                </td>

                                                {{-- Actions --}}
                                                <td class="py-4 px-4">
                                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                                        <a href="{{ route('matieres.affecter', $m->id) }}"
                                                           class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition text-sm">
                                                            Affecter
                                                        </a>

                                                        <a href="{{ route('matieres.edit', $m->id) }}"
                                                           class="px-4 py-2 rounded-xl bg-white/10 border border-white/10 text-white hover:bg-white/15 transition text-sm">
                                                            Modifier
                                                        </a>

                                                        <form method="POST" action="{{ route('matieres.destroy', $m->id) }}"
                                                              onsubmit="return confirm('Supprimer la matière : {{ addslashes($m->nom) }} ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 transition text-sm">
                                                                Supprimer
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div id="matiereEmptySearch" class="hidden text-center py-8">
                                    <div class="text-white font-semibold">Aucun résultat</div>
                                    <div class="text-sm text-white/70 mt-1">Essaie un autre mot-clé.</div>
                                </div>
                            </div>

                            {{-- Search offline --}}
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
