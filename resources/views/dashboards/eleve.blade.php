<x-app-layout>
    @php
        // ======================================================
        // CC-APP-EDUC — Dashboard Élève (V1 COMPLET + RESPONSIVE)
        // -> UI prêt / données placeholders (sans DB pour l’instant)
        // ======================================================

        $user = auth()->user();

        // Infos (placeholders)
        $classeLabel = 'Non définie';
        $niveauLabel = '—';

        // KPIs (placeholders)
        $kpi = [
            'cours_total' => 0,
            'quiz_todo'   => 0,
            'quiz_done'   => 0,
            'score_avg'   => null, // null => affichage "—"
        ];

        // Contenu (placeholders) — Mets [] si tu veux voir l’empty state
        $coursesRecent = [
            // Exemple (garde 1-2 items pour voir le rendu)
            // ['title' => 'Introduction — Bien démarrer', 'matiere' => 'Général', 'updated' => 'Aujourd’hui', 'status' => 'Nouveau'],
        ];

        $quizTodo = [
            // ['title' => 'Quiz découverte', 'matiere' => 'Général', 'duree' => '5 min', 'niveau' => 'Facile'],
        ];

        $quizDone = [
            // ['title' => 'Quiz 1', 'matiere' => 'Math', 'score' => '14/20', 'date' => 'Hier'],
        ];

        $notifications = [
            ['text' => 'Bienvenue sur CC-APP-EDUC ! Ton tableau de bord élève est prêt.', 'time' => 'Maintenant'],
            ['text' => 'Bientôt : cours, quiz, questions anonymes, groupes, progression…', 'time' => '—'],
            ['text' => 'Astuce : va dans “Paramètres” pour gérer ton profil.', 'time' => '—'],
        ];

        // Raccourcis : certains sont prêts, d’autres “bientôt”
        $shortcuts = [
            ['label' => 'Cours', 'icon' => 'book', 'href' => '#', 'hint' => 'Bientôt', 'disabled' => true],
            ['label' => 'Quiz', 'icon' => 'quiz', 'href' => '#', 'hint' => 'Bientôt', 'disabled' => true],
            ['label' => 'Questions anonymes', 'icon' => 'chat', 'href' => '#', 'hint' => 'Prévu', 'disabled' => true],
            ['label' => 'Groupes', 'icon' => 'group', 'href' => '#', 'hint' => 'Prévu', 'disabled' => true],
            ['label' => 'Paramètres', 'icon' => 'settings', 'href' => route('profile.edit'), 'hint' => 'Profil / sécurité', 'disabled' => false],
        ];

        // Progression placeholder
        $progressPercent = 0;
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight truncate">
                    Tableau de bord Élève
                </h2>

                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                    <span>
                        Bienvenue <span class="font-semibold text-gray-800">{{ $user->pseudo ?? $user->name ?? $user->email }}</span>
                    </span>
                    <span class="text-gray-300">•</span>
                    <span>Classe : <span class="font-medium text-gray-800">{{ $classeLabel }}</span></span>
                    <span class="text-gray-300">•</span>
                    <span>Niveau : <span class="font-medium text-gray-800">{{ $niveauLabel }}</span></span>

                    <span class="text-gray-300">•</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-700">
                        Type : <span class="ml-1 font-semibold">{{ $user->type_compte ?? 'eleve' }}</span>
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 text-sm">
                    ← Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-5 sm:py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ✅ KPIs (mobile: 2 colonnes / desktop: 4) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-4">
                    <div class="text-xs sm:text-sm text-gray-500">Cours disponibles</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $kpi['cours_total'] }}</div>
                    <div class="mt-2 text-xs text-gray-400">Cours publiés pour toi</div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-4">
                    <div class="text-xs sm:text-sm text-gray-500">Quiz à faire</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $kpi['quiz_todo'] }}</div>
                    <div class="mt-2 text-xs text-gray-400">À compléter</div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-4">
                    <div class="text-xs sm:text-sm text-gray-500">Quiz terminés</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $kpi['quiz_done'] }}</div>
                    <div class="mt-2 text-xs text-gray-400">Historique</div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-4">
                    <div class="text-xs sm:text-sm text-gray-500">Score moyen</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ $kpi['score_avg'] === null ? '—' : $kpi['score_avg'] }}
                    </div>
                    <div class="mt-2 text-xs text-gray-400">Bientôt calculé</div>
                </div>
            </div>

            {{-- ✅ Actions rapides (super important mobile) --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Accès rapides</h3>
                    <div class="text-xs text-gray-400">Mobile friendly</div>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                    @foreach($shortcuts as $s)
                        @php
                            $base = "w-full inline-flex items-center justify-between gap-2 px-4 py-3 rounded-xl border text-sm";
                            $enabled = "border-gray-200 bg-white hover:bg-gray-50 text-gray-800";
                            $disabled = "border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed";
                        @endphp

                        @if(!$s['disabled'])
                            <a href="{{ $s['href'] }}" class="{{ $base }} {{ $enabled }}">
                                <span class="font-medium">{{ $s['label'] }}</span>
                                <span class="text-xs text-gray-400">{{ $s['hint'] }}</span>
                            </a>
                        @else
                            <div class="{{ $base }} {{ $disabled }}">
                                <span class="font-medium">{{ $s['label'] }}</span>
                                <span class="text-xs text-gray-400">{{ $s['hint'] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- ✅ Continuer + Progression (responsive) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Continuer</h3>
                        <span class="text-xs text-gray-400">Reprends vite</span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="text-sm font-semibold text-gray-900">Dernier cours</div>
                            <div class="mt-1 text-sm text-gray-500">Aucun cours commencé.</div>
                            <button disabled class="mt-3 w-full px-4 py-2 rounded-lg bg-gray-200 text-gray-500 text-sm cursor-not-allowed">
                                Découvrir les cours
                            </button>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="text-sm font-semibold text-gray-900">Dernier quiz</div>
                            <div class="mt-1 text-sm text-gray-500">Aucun quiz en cours.</div>
                            <button disabled class="mt-3 w-full px-4 py-2 rounded-lg bg-gray-200 text-gray-500 text-sm cursor-not-allowed">
                                Voir les quiz
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl border border-gray-200 p-4 bg-gray-50">
                        <div class="text-sm font-semibold text-gray-900">Recommandé</div>
                        <div class="mt-1 text-sm text-gray-600">Commence un cours “Introduction” pour démarrer.</div>
                        <button disabled class="mt-3 px-4 py-2 rounded-lg bg-gray-200 text-gray-500 text-sm cursor-not-allowed">
                            Commencer
                        </button>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Progression</h3>
                        <span class="text-xs text-gray-400">Bientôt</span>
                    </div>

                    <div class="mt-3 text-sm text-gray-600">
                        Progression par matière, badges, points, classement…
                    </div>

                    <div class="mt-4">
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-2 bg-gray-400" style="width: {{ max(0, min(100, (int)$progressPercent)) }}%"></div>
                        </div>
                        <div class="mt-2 text-xs text-gray-400">{{ $progressPercent }}% — en attente de données</div>
                    </div>

                    <div class="mt-4 rounded-xl border border-gray-200 p-4">
                        <div class="text-sm font-semibold text-gray-900">Objectif du jour</div>
                        <div class="mt-1 text-sm text-gray-600">Lire 1 cours + faire 1 mini-quiz.</div>
                    </div>
                </div>
            </div>

            {{-- ✅ Cours récents + Quiz (mobile: liste / desktop: split) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Cours récents --}}
                <div class="lg:col-span-2 bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Cours récents</h3>
                        <span class="text-xs text-gray-400">Dernières mises à jour</span>
                    </div>

                    @if(count($coursesRecent) === 0)
                        <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-6 text-center">
                            <div class="text-sm font-semibold text-gray-900">Aucun cours pour l’instant</div>
                            <div class="mt-1 text-sm text-gray-500">
                                Dès qu’un enseignant publie un cours, il apparaîtra ici.
                            </div>
                        </div>
                    @else
                        {{-- Mobile list --}}
                        <div class="mt-4 space-y-3 lg:hidden">
                            @foreach($coursesRecent as $c)
                                <div class="rounded-xl border border-gray-200 p-4">
                                    <div class="font-semibold text-gray-900">{{ $c['title'] }}</div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        {{ $c['matiere'] }} • {{ $c['updated'] }}
                                    </div>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-700 text-xs">
                                            {{ $c['status'] ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Desktop table --}}
                        <div class="mt-4 hidden lg:block overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b">
                                        <th class="py-2 pr-4 font-medium">Titre</th>
                                        <th class="py-2 pr-4 font-medium">Matière</th>
                                        <th class="py-2 pr-4 font-medium">Dernière maj</th>
                                        <th class="py-2 pr-0 font-medium">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($coursesRecent as $c)
                                        <tr class="text-gray-700">
                                            <td class="py-3 pr-4">
                                                <div class="font-semibold text-gray-900">{{ $c['title'] }}</div>
                                            </td>
                                            <td class="py-3 pr-4">{{ $c['matiere'] }}</td>
                                            <td class="py-3 pr-4">{{ $c['updated'] }}</td>
                                            <td class="py-3 pr-0">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-700 text-xs">
                                                    {{ $c['status'] ?? '—' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-xs text-gray-400">Bientôt : recherche / filtres / matières</span>
                        <button disabled class="px-4 py-2 rounded-lg bg-gray-200 text-gray-500 text-sm cursor-not-allowed">
                            Voir tous les cours
                        </button>
                    </div>
                </div>

                {{-- Quiz --}}
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Quiz</h3>
                        <span class="text-xs text-gray-400">À faire & terminés</span>
                    </div>

                    <div class="mt-4">
                        <div class="text-sm font-semibold text-gray-800">À faire</div>

                        @if(count($quizTodo) === 0)
                            <div class="mt-2 rounded-xl border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                                Aucun quiz à faire pour l’instant.
                            </div>
                        @else
                            <div class="mt-2 space-y-2">
                                @foreach($quizTodo as $q)
                                    <div class="rounded-xl border border-gray-200 p-3">
                                        <div class="font-semibold text-gray-900">{{ $q['title'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $q['matiere'] }} • {{ $q['duree'] }} • {{ $q['niveau'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mt-5">
                        <div class="text-sm font-semibold text-gray-800">Terminés</div>

                        @if(count($quizDone) === 0)
                            <div class="mt-2 rounded-xl border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                                Aucun quiz terminé.
                            </div>
                        @else
                            <div class="mt-2 space-y-2">
                                @foreach($quizDone as $q)
                                    <div class="rounded-xl border border-gray-200 p-3">
                                        <div class="font-semibold text-gray-900">{{ $q['title'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $q['matiere'] }} • Score : {{ $q['score'] }} • {{ $q['date'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <button disabled class="w-full px-4 py-2 rounded-lg bg-gray-200 text-gray-500 text-sm cursor-not-allowed">
                            Voir tous les quiz
                        </button>
                    </div>
                </div>
            </div>

            {{-- ✅ Notifications + Aide (bien pour mobile) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Notifications</h3>
                        <span class="text-xs text-gray-400">Infos & nouveautés</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach($notifications as $n)
                            <div class="flex items-start justify-between gap-3 rounded-xl border border-gray-200 p-4">
                                <div class="text-sm text-gray-700">
                                    {{ $n['text'] }}
                                </div>
                                <div class="text-xs text-gray-400 whitespace-nowrap">
                                    {{ $n['time'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 text-xs text-gray-400">
                        Bientôt : annonces du prof, nouveaux cours, nouveaux quiz, rappels.
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Aide rapide</h3>

                    <div class="mt-3 space-y-3 text-sm text-gray-600">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="font-semibold text-gray-900">1) Commence ici</div>
                            <div class="mt-1">Quand les cours seront activés, tu pourras apprendre puis faire des quiz.</div>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="font-semibold text-gray-900">2) Tes paramètres</div>
                            <div class="mt-1">Va dans “Paramètres” pour gérer ton profil et ton mot de passe.</div>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="font-semibold text-gray-900">3) Questions anonymes</div>
                            <div class="mt-1">Prévu : poser une question sans montrer ton identité.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-xs text-gray-400 text-center pb-2">
                CC-APP-EDUC • Dashboard Élève V1 • {{ $user->email }}
            </div>

        </div>
    </div>
</x-app-layout>
