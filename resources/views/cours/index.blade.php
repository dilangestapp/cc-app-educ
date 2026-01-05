<x-app-layout>
    <style>
        .min-h-screen.bg-gray-100 { background: transparent !important; }
        header, nav { background: rgba(8, 12, 16, .45) !important; backdrop-filter: blur(10px) !important; border-bottom: 1px solid rgba(255,255,255,.10) !important; }
        nav a, nav button, nav span, nav div { color: rgba(255,255,255,.88) !important; }

        body, main, table, th, td, p, h1, h2, h3, label, span, a { color: rgba(255,255,255,.90); }
        thead th { color: rgba(255,255,255,.60) !important; }

        .glass-card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.10); backdrop-filter: blur(14px); }
        .glass-input { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); color: rgba(255,255,255,.92); }
        .glass-input::placeholder { color: rgba(255,255,255,.55); }

        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.55rem 1rem; border-radius:.9rem; font-size:.875rem; font-weight:600; transition:.15s; border:1px solid transparent; }
        .btn-ghost { background: rgba(255,255,255,.10); border-color: rgba(255,255,255,.12); color:#fff; }
        .btn-ghost:hover { background: rgba(255,255,255,.16); }
        .btn-primary { background: rgb(79 70 229); color:#fff; }
        .btn-primary:hover { background: rgb(67 56 202); }
        .btn-danger { background: rgb(220 38 38); color:#fff; }
        .btn-danger:hover { background: rgb(185 28 28); }

        .chip { display:inline-flex; align-items:center; padding:.25rem .65rem; border-radius:9999px; font-size:.75rem; border:1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.07); color: rgba(255,255,255,.88) !important; }
        .tag-on { border:1px solid rgba(34,197,94,.25); background: rgba(34,197,94,.10); color: rgba(187,247,208,.95) !important; }
        .tag-off { border:1px solid rgba(244,63,94,.25); background: rgba(244,63,94,.10); color: rgba(254,205,211,.95) !important; }
    </style>

    <div class="min-h-screen"
         style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        <div class="min-h-screen bg-black/60">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                <div class="space-y-2 mb-4">
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

                @php $count = $cours ? count($cours) : 0; @endphp

                <div class="glass-card rounded-2xl overflow-hidden shadow-lg">
                    <div class="p-5 border-b border-white/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-xs text-white/60">
                                    <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                                    <span class="mx-1">/</span>
                                    <a href="{{ route('matieres.manage') }}" class="hover:text-white">Matières</a>
                                    <span class="mx-1">/</span>
                                    <span class="text-white font-semibold">Cours</span>
                                </div>
                                <div class="mt-1 flex items-center gap-3">
                                    <h1 class="text-xl sm:text-2xl font-bold text-white">Cours — {{ $matiereRow->nom }}</h1>
                                    <span class="chip">{{ $count }} cours</span>
                                </div>
                                <p class="mt-1 text-sm text-white/70">Le contenu de la matière se gère ici, par classe.</p>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('matieres.manage') }}" class="btn btn-ghost">← Retour</a>
                                <a href="{{ route('cours.create', $matiereRow->id) }}{{ ($classeFilter ?? 0) ? ('?classe=' . $classeFilter) : '' }}" class="btn btn-primary">
                                    ＋ Nouveau cours
                                </a>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <input id="coursSearch" class="glass-input w-full rounded-xl px-4 py-2 text-sm" placeholder="Rechercher un cours...">

                            <form method="GET" class="flex items-center gap-2">
                                <select name="classe" class="glass-input rounded-xl px-3 py-2 text-sm">
                                    <option value="0">Toutes les classes</option>
                                    @foreach(($classes ?? collect()) as $cl)
                                        <option value="{{ $cl->id }}" {{ (int)($classeFilter ?? 0) === (int)$cl->id ? 'selected' : '' }}>
                                            {{ $cl->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-ghost" type="submit">Filtrer</button>
                            </form>
                        </div>
                    </div>

                    <div class="p-5">
                        @if(!$cours || $count === 0)
                            <div class="text-center py-10">
                                <div class="mx-auto h-12 w-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-2xl">📄</div>
                                <h3 class="mt-4 text-lg font-semibold text-white">Aucun cours</h3>
                                <p class="mt-1 text-sm text-white/70">Crée un premier cours pour cette matière.</p>
                                <div class="mt-5">
                                    <a href="{{ route('cours.create', $matiereRow->id) }}" class="btn btn-primary">＋ Nouveau cours</a>
                                </div>
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-xl border border-white/10">
                                <table class="min-w-full">
                                    <thead class="bg-white/5">
                                        <tr class="text-left text-xs uppercase tracking-wider text-white/55">
                                            <th class="py-3 px-4">Titre</th>
                                            <th class="py-3 px-4">Classe</th>
                                            <th class="py-3 px-4">Statut</th>
                                            <th class="py-3 px-4 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="coursBody" class="divide-y divide-white/10">
                                        @foreach($cours as $c)
                                            <tr class="hover:bg-white/5 transition" data-name="{{ strtolower($c->titre ?? '') }}">
                                                <td class="py-4 px-4">
                                                    <div class="font-semibold text-white">{{ $c->titre }}</div>
                                                    <div class="text-[11px] text-white/55">ID: {{ $c->id }}</div>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <span class="chip">{{ $c->classe_nom ?? ('Classe #' . $c->classe_id) }}</span>
                                                </td>
                                                <td class="py-4 px-4">
                                                    @php $actif = (int)($c->actif ?? 1); @endphp
                                                    <span class="chip {{ $actif ? 'tag-on' : 'tag-off' }}">
                                                        {{ $actif ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </td>
                                                <td class="py-4 px-4">
                                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                                        <a href="{{ route('cours.edit', $c->id) }}" class="btn btn-ghost">Modifier</a>
                                                        <form method="POST" action="{{ route('cours.destroy', $c->id) }}"
                                                              onsubmit="return confirm('Supprimer le cours : {{ addslashes($c->titre) }} ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Supprimer</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div id="coursEmpty" class="hidden text-center py-8">
                                    <div class="text-white font-semibold">Aucun résultat</div>
                                    <div class="text-sm text-white/70 mt-1">Essaie un autre mot-clé.</div>
                                </div>
                            </div>

                            <script>
                                (function () {
                                    const input = document.getElementById('coursSearch');
                                    const body = document.getElementById('coursBody');
                                    const empty = document.getElementById('coursEmpty');
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
