<x-app-layout>
    @php
        $user = auth()->user();

        $classeLabel = 'Non définie';
        $niveauLabel = '—';

        $kpi = [
            'cours_total' => 0,
            'quiz_todo'   => 0,
            'quiz_done'   => 0,
            'score_avg'   => null,
        ];

        $coursesRecent = []; // plus tard: vrais cours élève
        $quizTodo = [];
        $quizDone = [];

        $notifications = [
            ['text' => 'Bienvenue sur CC-APP-EDUC ! Ton tableau de bord élève est prêt.', 'time' => 'Maintenant'],
            ['text' => 'Bientôt : cours, quiz, questions anonymes, groupes, progression…', 'time' => '—'],
            ['text' => 'Astuce : va dans “Paramètres” pour gérer ton profil.', 'time' => '—'],
        ];
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

            {{-- KPIs --}}
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

            {{-- Accès rapides (TOUS CLIQUABLES) --}}
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Accès rapides</h3>
                    <div class="text-xs text-gray-400">Tout est cliquable</div>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                    <a href="{{ route('eleve.cours') }}" class="w-full inline-flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                        <span class="font-medium">📚 Cours</span>
                        <span class="text-xs text-gray-400">Ouvrir</span>
                    </a>

                    <a href="{{ route('eleve.quiz') }}" class="w-full inline-flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                        <span class="font-medium">📝 Quiz</span>
                        <span class="text-xs text-gray-400">Ouvrir</span>
                    </a>

                    <a href="{{ route('eleve.questions') }}" class="w-full inline-flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                        <span class="font-medium">💬 Questions</span>
                        <span class="text-xs text-gray-400">Ouvrir</span>
                    </a>

                    <a href="{{ route('eleve.groupes') }}" class="w-full inline-flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                        <span class="font-medium">👥 Groupes</span>
                        <span class="text-xs text-gray-400">Ouvrir</span>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="w-full inline-flex items-center justify-between gap-2 px-4 py-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm text-gray-800">
                        <span class="font-medium">⚙️ Paramètres</span>
                        <span class="text-xs text-gray-400">Profil</span>
                    </a>
                </div>
            </div>

            {{-- Continuer (TOUS CLIQUABLES) --}}
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
                            <a href="{{ route('eleve.cours') }}" class="mt-3 inline-flex w-full justify-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                                Découvrir les cours
                            </a>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="text-sm font-semibold text-gray-900">Dernier quiz</div>
                            <div class="mt-1 text-sm text-gray-500">Aucun quiz en cours.</div>
                            <a href="{{ route('eleve.quiz') }}" class="mt-3 inline-flex w-full justify-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                                Voir les quiz
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl border border-gray-200 p-4 bg-gray-50">
                        <div class="text-sm font-semibold text-gray-900">Recommandé</div>
                        <div class="mt-1 text-sm text-gray-600">Commence par un cours d’introduction.</div>
                        <a href="{{ route('eleve.cours') }}" class="mt-3 inline-flex px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 text-sm hover:bg-gray-50">
                            Commencer
                        </a>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Progression</h3>
                        <a href="{{ route('eleve.progression') }}" class="text-sm text-gray-700 hover:underline">
                            Voir →
                        </a>
                    </div>

                    <div class="mt-3 text-sm text-gray-600">
                        Progression par matière, badges, points, classement…
                    </div>

                    <div class="mt-4">
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-2 bg-gray-400" style="width: 0%"></div>
                        </div>
                        <div class="mt-2 text-xs text-gray-400">0% — en attente de données</div>
                    </div>
                </div>
            </div>

            {{-- Cours récents + Quiz --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Cours récents</h3>
                        <a href="{{ route('eleve.cours') }}" class="text-sm text-gray-700 hover:underline">
                            Voir tout →
                        </a>
                    </div>

                    <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-6 text-center">
                        <div class="text-sm font-semibold text-gray-900">Aucun cours pour l’instant</div>
                        <div class="mt-1 text-sm text-gray-500">
                            Dès qu’un enseignant publie un cours, il apparaîtra ici.
                        </div>
                        <a href="{{ route('eleve.cours') }}" class="mt-4 inline-flex px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                            Aller aux cours
                        </a>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Quiz</h3>
                        <a href="{{ route('eleve.quiz') }}" class="text-sm text-gray-700 hover:underline">
                            Voir tout →
                        </a>
                    </div>

                    <div class="mt-4 rounded-xl border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                        Aucun quiz à faire pour l’instant.
                    </div>

                    <a href="{{ route('eleve.quiz') }}" class="mt-4 inline-flex w-full justify-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Voir tous les quiz
                    </a>
                </div>
            </div>

            {{-- Notifications + Aide --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Notifications</h3>
                        <span class="text-xs text-gray-400">Infos & nouveautés</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach($notifications as $n)
                            <div class="flex items-start justify-between gap-3 rounded-xl border border-gray-200 p-4">
                                <div class="text-sm text-gray-700">{{ $n['text'] }}</div>
                                <div class="text-xs text-gray-400 whitespace-nowrap">{{ $n['time'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Aide rapide</h3>

                    <div class="mt-3 space-y-3 text-sm text-gray-600">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="font-semibold text-gray-900">Cours</div>
                            <div class="mt-1">Accède à tous tes cours disponibles.</div>
                            <a href="{{ route('eleve.cours') }}" class="mt-2 inline-flex text-sm text-gray-700 hover:underline">Ouvrir →</a>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="font-semibold text-gray-900">Quiz</div>
                            <div class="mt-1">Teste-toi après les cours.</div>
                            <a href="{{ route('eleve.quiz') }}" class="mt-2 inline-flex text-sm text-gray-700 hover:underline">Ouvrir →</a>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="font-semibold text-gray-900">Questions anonymes</div>
                            <div class="mt-1">Prévu : poser une question sans montrer ton identité.</div>
                            <a href="{{ route('eleve.questions') }}" class="mt-2 inline-flex text-sm text-gray-700 hover:underline">Ouvrir →</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-xs text-gray-400 text-center pb-2">
                CC-APP-EDUC • Élève • {{ $user->email }}
            </div>

        </div>
    </div>
</x-app-layout>
