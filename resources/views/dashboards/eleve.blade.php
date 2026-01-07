<x-app-layout>
    @php
        // ================================
        // CC-APP-EDUC — Dashboard Élève (V1)
        // Placeholders UI (sans DB pour l’instant)
        // ================================

        $user = auth()->user();

        // Exemple : classe (placeholder). Plus tard on remplacera par $user->classe->nom etc.
        $classeLabel = 'Non définie';

        // KPIs (placeholders)
        $kpis = [
            ['label' => 'Cours disponibles', 'value' => 0, 'hint' => 'Nouveaux cours publiés'],
            ['label' => 'Quiz à faire', 'value' => 0, 'hint' => 'À compléter cette semaine'],
            ['label' => 'Quiz terminés', 'value' => 0, 'hint' => 'Historique de tes quiz'],
            ['label' => 'Score moyen', 'value' => '—', 'hint' => 'Moyenne générale (bientôt)'],
        ];

        // Continuer (placeholders)
        $continue = [
            'last_course' => [
                'title' => 'Aucun cours commencé',
                'meta'  => 'Commence un cours pour le retrouver ici',
                'action_label' => 'Découvrir les cours',
                'action_href'  => '#',
                'disabled'     => true,
            ],
            'last_quiz' => [
                'title' => 'Aucun quiz en cours',
                'meta'  => 'Quand tu lances un quiz, tu peux reprendre ici',
                'action_label' => 'Voir les quiz',
                'action_href'  => '#',
                'disabled'     => true,
            ],
            'recommended' => [
                'title' => 'Cours recommandé',
                'meta'  => 'Conseil : commence par un cours “Introduction”',
                'action_label' => 'Commencer',
                'action_href'  => '#',
                'disabled'     => true,
            ],
        ];

        // Mes cours (placeholders)
        $myCourses = [
            ['title' => 'Introduction — Bien démarrer', 'matiere' => 'Général', 'date' => '—', 'status' => 'Nouveau'],
            ['title' => 'Aucun autre cours', 'matiere' => '—', 'date' => '—', 'status' => '—'],
            ['title' => '—', 'matiere' => '—', 'date' => '—', 'status' => '—'],
            ['title' => '—', 'matiere' => '—', 'date' => '—', 'status' => '—'],
            ['title' => '—', 'matiere' => '—', 'date' => '—', 'status' => '—'],
        ];

        // Mes quiz (placeholders)
        $quizTodo = [
            ['title' => 'Quiz découverte', 'matiere' => 'Général', 'duree' => '5 min', 'niveau' => 'Facile'],
            ['title' => '—', 'matiere' => '—', 'duree' => '—', 'niveau' => '—'],
            ['title' => '—', 'matiere' => '—', 'duree' => '—', 'niveau' => '—'],
        ];

        $quizDone = [
            ['title' => 'Aucun quiz terminé', 'matiere' => '—', 'score' => '—', 'date' => '—'],
            ['title' => '—', 'matiere' => '—', 'score' => '—', 'date' => '—'],
            ['title' => '—', 'matiere' => '—', 'score' => '—', 'date' => '—'],
        ];

        // Notifications (placeholders)
        $notifications = [
            ['text' => 'Bienvenue sur CC-APP-EDUC ! Ton tableau de bord élève est prêt.', 'time' => 'Maintenant'],
            ['text' => 'Bientôt : cours, quiz, questions anonymes, groupes, progression…', 'time' => '—'],
            ['text' => 'Astuce : complète ton profil dès que la section sera activée.', 'time' => '—'],
        ];

        // Raccourcis (boutons)
        $shortcuts = [
            ['label' => '📚 Cours', 'href' => '#', 'hint' => 'Bientôt disponible', 'disabled' => true],
            ['label' => '📝 Quiz', 'href' => '#', 'hint' => 'Bientôt disponible', 'disabled' => true],
            ['label' => '💬 Questions anonymes', 'href' => '#', 'hint' => 'Prévu', 'disabled' => true],
            ['label' => '👥 Groupes', 'href' => '#', 'hint' => 'Prévu', 'disabled' => true],
            ['label' => '⚙️ Paramètres', 'href' => route('profile.edit'), 'hint' => 'Profil / sécurité', 'disabled' => false],
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Tableau de bord Élève
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Bienvenue <span class="font-medium text-gray-800">{{ $user->pseudo ?? $user->name ?? $user->email }}</span>
                    <span class="mx-2 text-gray-300">•</span>
                    Classe : <span class="font-medium text-gray-800">{{ $classeLabel }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 text-sm">
                    ← Retour
                </a>

                <span class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">
                    Type : <span class="ml-1 font-semibold">{{ $user->type_compte ?? 'eleve' }}</span>
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- KPIs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($kpis as $k)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border border-gray-100">
                        <div class="text-sm text-gray-500">{{ $k['label'] }}</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $k['value'] }}</div>
                        <div class="mt-2 text-xs text-gray-400">{{ $k['hint'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Continuer --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Continuer</h3>
                        <span class="text-xs text-gray-400">Reprends là où tu t’es arrêté</span>
                    </div>

                    <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                        @foreach (['last_course' => 'Dernier cours', 'last_quiz' => 'Dernier quiz', 'recommended' => 'Recommandé'] as $key => $title)
                            @php $c = $continue[$key]; @endphp
                            <div class="rounded-xl border border-gray-200 p-5">
                                <div class="text-sm text-gray-500">{{ $title }}</div>
                                <div class="mt-2 font-semibold text-gray-900">{{ $c['title'] }}</div>
                                <div class="mt-1 text-sm text-gray-500">{{ $c['meta'] }}</div>

                                <div class="mt-4">
                                    @if(!$c['disabled'])
                                        <a href="{{ $c['action_href'] }}"
                                           class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                                            {{ $c['action_label'] }}
                                        </a>
                                    @else
                                        <button disabled
                                            class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-200 text-gray-500 text-sm cursor-not-allowed">
                                            {{ $c['action_label'] }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Mes cours + Mes quiz --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Mes cours --}}
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Mes cours</h3>
                            <span class="text-sm text-gray-500">Derniers éléments</span>
                        </div>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b">
                                        <th class="py-2 pr-4 font-medium">Titre</th>
                                        <th class="py-2 pr-4 font-medium">Matière</th>
                                        <th class="py-2 pr-4 font-medium">Date</th>
                                        <th class="py-2 pr-0 font-medium">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($myCourses as $course)
                                        <tr class="text-gray-700">
                                            <td class="py-3 pr-4">
                                                <div class="font-medium text-gray-900">{{ $course['title'] }}</div>
                                            </td>
                                            <td class="py-3 pr-4">{{ $course['matiere'] }}</td>
                                            <td class="py-3 pr-4">{{ $course['date'] }}</td>
                                            <td class="py-3 pr-0">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-gray-700 text-xs">
                                                    {{ $course['status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xs text-gray-400">Bientôt : filtre par matière / niveau</span>
                            <button disabled
                                class="px-4 py-2 rounded-lg bg-gray-200 text-gray-500 text-sm cursor-not-allowed">
                                Voir tous les cours
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Mes quiz --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Mes quiz</h3>

                        <div class="mt-4">
                            <div class="text-sm font-semibold text-gray-800">À faire</div>
                            <div class="mt-2 space-y-2">
                                @foreach($quizTodo as $q)
                                    <div class="rounded-lg border border-gray-200 p-3">
                                        <div class="font-medium text-gray-900">{{ $q['title'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $q['matiere'] }} • {{ $q['duree'] }} • {{ $q['niveau'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="text-sm font-semibold text-gray-800">Terminés</div>
                            <div class="mt-2 space-y-2">
                                @foreach($quizDone as $q)
                                    <div class="rounded-lg border border-gray-200 p-3">
                                        <div class="font-medium text-gray-900">{{ $q['title'] }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $q['matiere'] }} • Score : {{ $q['score'] }} • {{ $q['date'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4">
                            <button disabled
                                class="w-full px-4 py-2 rounded-lg bg-gray-200 text-gray-500 text-sm cursor-not-allowed">
                                Voir tous les quiz
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notifications + Raccourcis --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Notifications --}}
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                            <span class="text-xs text-gray-400">Infos & nouveautés</span>
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach($notifications as $n)
                                <div class="flex items-start justify-between gap-3 rounded-lg border border-gray-200 p-4">
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
                </div>

                {{-- Raccourcis --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900">Accès rapides</h3>
                        <p class="text-sm text-gray-500 mt-1">Tout ce dont tu as besoin</p>

                        <div class="mt-4 grid grid-cols-1 gap-2">
                            @foreach($shortcuts as $s)
                                @if(!$s['disabled'])
                                    <a href="{{ $s['href'] }}"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800 flex items-center justify-between">
                                        <span>{{ $s['label'] }}</span>
                                        <span class="text-xs text-gray-400">{{ $s['hint'] }}</span>
                                    </a>
                                @else
                                    <div class="w-full px-4 py-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-500 flex items-center justify-between">
                                        <span>{{ $s['label'] }}</span>
                                        <span class="text-xs text-gray-400">{{ $s['hint'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">
                                <div class="text-sm font-semibold text-gray-800">Progression (bientôt)</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    Progression par matière, badges, points, classement… seront ajoutés ensuite.
                                </div>
                                <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-2 bg-gray-400 w-1/12"></div>
                                </div>
                                <div class="mt-2 text-xs text-gray-400">0% — en attente de données</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer info debug (tu peux supprimer plus tard) --}}
            <div class="text-xs text-gray-400 text-center pb-2">
                CC-APP-EDUC • Dashboard Élève V1 • Connecté en tant que : <span class="font-medium">{{ $user->email }}</span>
            </div>

        </div>
    </div>
</x-app-layout>
