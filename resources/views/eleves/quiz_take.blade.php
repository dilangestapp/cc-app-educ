<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4">

            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-xs text-gray-500">{{ $quizRow->matiere_nom ?? 'Matière' }}</div>
                    <h1 class="text-2xl font-bold">{{ $quizRow->titre }}</h1>
                    @if($quizRow->description)
                        <p class="text-sm text-gray-600 mt-1">{{ $quizRow->description }}</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('eleve.quiz.index') }}" class="px-4 py-2 rounded border bg-white">← Retour</a>
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

            <div class="bg-white rounded shadow p-5">
                <div class="text-sm text-gray-600">
                    @if($quizRow->duree_minutes)
                        Durée : <span class="font-semibold">{{ $quizRow->duree_minutes }} min</span> •
                    @endif
                    Tentatives : <span class="font-semibold">{{ (int)$quizRow->max_attempts === 0 ? 'illimité' : $quizRow->max_attempts }}</span>
                </div>

                @if(!$attempt)
                    <form method="POST" action="{{ route('eleve.quiz.start', $quizRow->id) }}" class="mt-4">
                        @csrf
                        <button class="px-5 py-2 rounded bg-black text-white">Commencer</button>
                    </form>

                    <div class="mt-6 text-sm text-gray-500">
                        Clique sur “Commencer” pour ouvrir les questions.
                    </div>
                @else
                    <form method="POST" action="{{ route('eleve.quiz.submit', $quizRow->id) }}" class="mt-5 space-y-6">
                        @csrf
                        <input type="hidden" name="attempt_id" value="{{ $attempt->id }}"/>

                        @foreach($questions as $i => $q)
                            <div class="border rounded p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="font-semibold">
                                        {{ $i+1 }}. {!! nl2br(e($q->enonce)) !!}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ (int)$q->points }} pt(s)
                                    </div>
                                </div>

                                <div class="mt-3 space-y-2">
                                    @foreach(($optionsByQuestion[$q->id] ?? []) as $opt)
                                        <label class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" name="q_{{ $q->id }}" value="{{ $opt->id }}">
                                            <span>{!! nl2br(e($opt->texte)) !!}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="flex gap-2">
                            <button class="px-5 py-2 rounded bg-black text-white">Terminer & Voir le score</button>
                            <a href="{{ route('eleve.quiz.index') }}" class="px-5 py-2 rounded border bg-white">Retour</a>
                        </div>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
