<x-app-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-xs text-gray-500">{{ $attemptRow->matiere_nom ?? 'Matière' }}</div>
                    <h1 class="text-2xl font-bold">Résultat — {{ $attemptRow->quiz_titre }}</h1>
                </div>
                <a href="{{ route('eleve.quiz.index') }}" class="px-4 py-2 rounded border bg-white">← Retour</a>
            </div>

            <div class="bg-white rounded shadow p-6">
                <div class="text-lg">
                    Score : <span class="font-bold">{{ $attemptRow->score }}/{{ $attemptRow->total }}</span>
                </div>

                <div class="text-sm text-gray-500 mt-2">
                    Tentative #{{ $attemptRow->attempt_no }} • Terminé le {{ \Illuminate\Support\Carbon::parse($attemptRow->finished_at)->format('d/m/Y H:i') }}
                </div>

                <div class="mt-5 flex gap-2">
                    <a href="{{ route('eleve.quiz.history') }}" class="px-4 py-2 rounded bg-black text-white">Voir historique</a>
                    <a href="{{ route('eleve.quiz.show', $attemptRow->quiz_id) }}" class="px-4 py-2 rounded border bg-white">Revoir le quiz</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
