<x-app-layout>
    <style>
        .min-h-screen.bg-gray-100 { background: transparent !important; }
        header, nav { background: rgba(8, 12, 16, .45) !important; backdrop-filter: blur(10px) !important; border-bottom: 1px solid rgba(255,255,255,.10) !important; }
        nav a, nav button, nav span, nav div { color: rgba(255,255,255,.88) !important; }

        .glass-card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.10); backdrop-filter: blur(14px); }
        .glass-input { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); color: rgba(255,255,255,.92); }
        .glass-input::placeholder { color: rgba(255,255,255,.55); }

        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.55rem 1rem; border-radius:.9rem; font-size:.875rem; font-weight:600; transition:.15s; border:1px solid transparent; }
        .btn-ghost { background: rgba(255,255,255,.10); border-color: rgba(255,255,255,.12); color:#fff; }
        .btn-ghost:hover { background: rgba(255,255,255,.16); }
        .btn-primary { background: rgb(79 70 229); color:#fff; }
        .btn-primary:hover { background: rgb(67 56 202); }

        .chip { display:inline-flex; align-items:center; padding:.25rem .65rem; border-radius:9999px; font-size:.75rem; border:1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.07); color: rgba(255,255,255,.88) !important; }
        .tag { border:1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.06); }

        .cards { display:grid; grid-template-columns: 1fr; gap: 12px; }
        @media (min-width: 700px) { .cards { grid-template-columns: 1fr 1fr; } }
        @media (min-width: 1024px) { .cards { grid-template-columns: 1fr 1fr 1fr; } }
    </style>

    @php
        $count = isset($classes) ? (is_countable($classes) ? count($classes) : 0) : 0;

        // ✅ Route matières par classe (anti-500 si nom différent)
        $matieresRoute = null;
        if (\Illuminate\Support\Facades\Route::has('classes.matieres')) $matieresRoute = 'classes.matieres';
        elseif (\Illuminate\Support\Facades\Route::has('matieres.index')) $matieresRoute = 'matieres.index';
        elseif (\Illuminate\Support\Facades\Route::has('matieres.classe')) $matieresRoute = 'matieres.classe';
        elseif (\Illuminate\Support\Facades\Route::has('matieres.byClasse')) $matieresRoute = 'matieres.byClasse';
    @endphp

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

                <div class="glass-card rounded-2xl overflow-hidden shadow-lg">
                    <div class="p-5 border-b border-white/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-xs text-white/60">
                                    <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                                    <span class="mx-1">/</span>
                                    <span class="text-white font-semibold">Classes</span>
                                </div>

                                <div class="mt-1 flex items-center gap-3 flex-wrap">
                                    <h1 class="text-xl sm:text-2xl font-bold text-white">Gestion des classes</h1>
                                    <span class="chip">{{ $count }} classe(s)</span>
                                </div>

                                <p class="mt-1 text-sm text-white/70">
                                    Chaque classe contient des matières, puis des cours.
                                </p>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('dashboard') }}" class="btn btn-ghost">← Retour</a>
                                <a href="{{ route('classes.create') }}" class="btn btn-primary">＋ Nouvelle classe</a>
                            </div>
                        </div>

                        <div class="mt-4">
                            <input id="classeSearch" class="glass-input w-full rounded-xl px-4 py-2 text-sm"
                                   placeholder="Rechercher une classe...">
                        </div>
                    </div>

                    <div class="p-5">
                        @if (!empty($error))
                            {{-- si table absente, on s'arrête là --}}
                        @else
                            @if (!$classes || (method_exists($classes, 'isEmpty') && $classes->isEmpty()) || (is_array($classes) && !count($classes)))
                                <div class="text-center py-10">
                                    <div class="mx-auto h-12 w-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-2xl">🏫</div>
                                    <h3 class="mt-4 text-lg font-semibold text-white">Aucune classe</h3>
                                    <p class="mt-1 text-sm text-white/70">Crée une première classe pour commencer.</p>
                                    <div class="mt-5">
                                        <a href="{{ route('classes.create') }}" class="btn btn-primary">＋ Nouvelle classe</a>
                                    </div>
                                </div>
                            @else
                                <div id="classeCards" class="cards">
                                    @foreach ($classes as $classe)
                                        @php
                                            $nom = $classe->nom ?? 'Classe';
                                            $matCount = (int)($classe->matieres_count ?? 0);
                                            $coursCount = (int)($classe->cours_count ?? 0);
                                        @endphp

                                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 hover:bg-white/7 transition"
                                             data-name="{{ strtolower($nom) }}">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="font-semibold text-white text-base break-words">🏫 {{ $nom }}</div>
                                                    <div class="mt-1 text-xs text-white/55">ID: {{ $classe->id }}</div>
                                                </div>

                                                <div class="flex items-center gap-2 flex-wrap justify-end">
                                                    <span class="chip tag">Matières: {{ $matCount }}</span>
                                                    <span class="chip tag">Cours: {{ $coursCount }}</span>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                @if ($matieresRoute)
                                                    <a href="{{ route($matieresRoute, $classe->id) }}"
                                                       class="btn btn-ghost w-full">
                                                        📘 Voir les matières →
                                                    </a>
                                                @else
                                                    <div class="text-xs text-yellow-100/80">
                                                        Route “matières par classe” non trouvée (à corriger dans routes/web.php).
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div id="classeEmpty" class="hidden text-center py-8">
                                    <div class="text-white font-semibold">Aucun résultat</div>
                                    <div class="text-sm text-white/70 mt-1">Essaie un autre mot-clé.</div>
                                </div>

                                <script>
                                    (function () {
                                        const input = document.getElementById('classeSearch');
                                        const cardsWrap = document.getElementById('classeCards');
                                        const empty = document.getElementById('classeEmpty');
                                        if (!input || !cardsWrap) return;

                                        function filter() {
                                            const q = (input.value || '').trim().toLowerCase();
                                            const items = cardsWrap.querySelectorAll('[data-name]');
                                            let visible = 0;

                                            items.forEach(el => {
                                                const name = (el.getAttribute('data-name') || '');
                                                const show = !q || name.includes(q);
                                                el.style.display = show ? '' : 'none';
                                                if (show) visible++;
                                            });

                                            if (empty) empty.classList.toggle('hidden', visible !== 0);
                                        }

                                        input.addEventListener('input', filter);
                                    })();
                                </script>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
