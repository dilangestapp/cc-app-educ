<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Mes quiz</h1>
                    <p class="text-sm text-gray-500">Les quiz apparaissent quand ils sont publiés pour ta classe.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('eleve.quiz.history') }}" class="px-4 py-2 rounded border bg-white">Historique</a>
                    <a href="{{ route('eleve.dashboard') }}" class="px-4 py-2 rounded border bg-white">← Retour TB</a>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$quizzes || count($quizzes) === 0)
                <div class="bg-white rounded shadow p-6 text-gray-600">
                    Aucun quiz pour l’instant.
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($quizzes as $q)
                        @php
                            $last = $lastScores[$q->id] ?? null;
                        @endphp
                        <div class="bg-white rounded shadow p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-xs text-gray-500">{{ $q->matiere_nom ?? 'Matière' }}</div>
                                    <div class="text-lg font-semibold">{{ $q->titre }}</div>
                                    @if($q->description)
                                        <div class="text-sm text-gray-600 mt-1">{{ $q->description }}</div>
                                    @endif
                                    <div class="text-xs text-gray-500 mt-2">
                                        @if($q->duree_minutes) Durée : {{ $q->duree_minutes }} min • @endif
                                        Tentatives : {{ (int)$q->max_attempts === 0 ? 'illimité' : $q->max_attempts }}
                                    </div>
                                </div>
                                <a href="{{ route('eleve.quiz.show', $q->id) }}" class="px-4 py-2 rounded bg-black text-white">
                                    Ouvrir
                                </a>
                            </div>

                            @if($last)
                                <div class="mt-3 text-sm">
                                    Dernier score : <span class="font-semibold">{{ $last->score }}/{{ $last->total }}</span>
                                    <span class="text-gray-500 text-xs">({{ \Illuminate\Support\Carbon::parse($last->finished_at)->diffForHumans() }})</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
