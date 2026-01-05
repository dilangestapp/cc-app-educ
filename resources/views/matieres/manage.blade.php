<x-app-layout>
    <style>
        /* Neutraliser le fond Breeze sur CETTE page */
        .min-h-screen.bg-gray-100 { background: transparent !important; }

        /* Navbar Breeze => glass dark (peu visible) */
        header, nav {
            background: rgba(8, 12, 16, .45) !important;
            backdrop-filter: blur(10px) !important;
            border-bottom: 1px solid rgba(255,255,255,.10) !important;
        }
        nav a, nav button, nav span, nav div { color: rgba(255,255,255,.88) !important; }
        nav a:hover, nav button:hover { color: #fff !important; }

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

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .55rem 1rem;
            border-radius: 0.9rem;
            font-size: .875rem;
            font-weight: 600;
            transition: .15s ease-in-out;
            white-space: nowrap;
        }
        .btn-ghost { background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.10); color: #fff; }
        .btn-ghost:hover { background: rgba(255,255,255,.16); }

        .btn-primary { background: rgb(79 70 229); color: #fff; }
        .btn-primary:hover { background: rgb(67 56 202); }

        .btn-blue { background: rgb(37 99 235); color: #fff; }
        .btn-blue:hover { background: rgb(29 78 216); }

        .btn-danger { background: rgb(220 38 38); color: #fff; }
        .btn-danger:hover { background: rgb(185 28 28); }

        .tag {
            display:inline-flex;
            align-items:center;
            padding:.25rem .6rem;
            border-radius:9999px;
            font-size:.75rem;
            border:1px solid rgba(34,197,94,.25);
            background: rgba(34,197,94,.10);
            color: rgba(187,247,208,.95);
        }

        /* Petit badge vertical décoratif (style magazine) */
        .vertical-label {
            position: absolute;
            right: 18px;
            top: 18px;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            letter-spacing: .25em;
            font-weight: 800;
            font-size: .75rem;
            color: rgba(255, 182, 193, .55);
            user-select: none;
            pointer-events: none;
        }
    </style>

    <div class="min-h-screen"
         style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        <div class="min-h-screen bg-black/60">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                {{-- Alerts --}}
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

                @php $count = $matieres ? count($matieres) : 0; @endphp

                {{-- Carte principale (tout est dedans => plus pro) --}}
                <div class="glass-card rounded-2xl overflow-hidden shadow-lg relative">
                    <div class="vertical-label">MATIÈRES</div>

                    {{-- Toolbar compacte --}}
                    <div class="p-4 sm:p-5 border-b border-white/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-xs text-white/60">
                                    <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                                    <span class="mx-1">/</span>
                                    <span class="text-white font-semibold">Matières</span>
                                </div>
                                <div class="mt-1 flex items-center gap-3">
                                    <h1 class="text-xl sm:text-2xl font-bold text-white">Gestion des matières</h1>
                                    <span class="text-xs px-2.5 py-1 rounded-full bg-white/10 border border-white/10 text-white/80">
                                        {{ $count }} matière(s)
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-white/70">
                                    Crée, organise et affecte les matières aux classes.
                                </p>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('dashboard') }}" class="btn btn-ghost">← Retour</a>
                                <a href="{{ route('matieres.create') }}" class="btn btn-primary">＋ Nouvelle matière</a>
                            </div>
                        </div>

                        {{-- Recherche --}}
                        <div class="mt-4">
                            <input id="matiereSearch"
                                   type="text"
                                   placeholder="Rechercher une matière..."
                                   class="glass-input w-full rounded-xl px-4 py-2 text-sm">
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
                                    <a href="{{ route('matieres.create') }}" class="btn btn-primary">＋ Nouvelle matière</a>
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
                                                <td class="py-4 px-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="h-10 w-10 rounded-xl bg-white/10 border border-white/10 flex items-center justify-center text-white font-bold">
                                                            {{ strtoupper(substr($m->nom, 0, 1)) }}
                                                        </div>
                                                        <div class="leading-tight">
                                                            <div class="font-semibold text-white">{{ $m->nom }}</div>
                                                            <div class="text-[11px] text-white/55">ID: {{ $m->id }}</div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="py-4 px-4">
                                                    <span class="tag">Active</span>
                                                </td>

                                                <td class="py-4 px-4">
                                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                                        <a href="{{ route('matieres.affecter', $m->id) }}" class="btn btn-blue">
                                                            Affecter
                                                        </a>

                                                        <a href="{{ route('matieres.edit', $m->id) }}" class="btn btn-ghost">
                                                            Modifier
                                                        </a>

                                                        <form method="POST" action="{{ route('matieres.destroy', $m->id) }}"
                                                              onsubmit="return confirm('Supprimer la matière : {{ addslashes($m->nom) }} ?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
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
