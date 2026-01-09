<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-bold">Historique des quiz</h1>
                    <p class="text-sm text-gray-500">Tes tentatives terminées.</p>
                </div>
                <a href="{{ route('eleve.quiz.index') }}" class="px-4 py-2 rounded border bg-white">← Retour</a>
            </div>

            <div class="bg-white rounded shadow p-4">
                @if(!$attempts || count($attempts) === 0)
                    <div class="text-gray-600 p-3">Aucun historique pour le moment.</div>
                @else
                    <div class="overflow-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2">Date</th>
                                    <th class="py-2">Matière</th>
                                    <th class="py-2">Quiz</th>
                                    <th class="py-2">Score</th>
                                    <th class="py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempts as $a)
                                    <tr class="border-b">
                                        <td class="py-2">{{ \Illuminate\Support\Carbon::parse($a->finished_at)->format('d/m/Y H:i') }}</td>
                                        <td class="py-2">{{ $a->matiere_nom ?? '—' }}</td>
                                        <td class="py-2">{{ $a->quiz_titre }}</td>
                                        <td class="py-2 font-semibold">{{ $a->score }}/{{ $a->total }}</td>
                                        <td class="py-2">
                                            <a class="underline" href="{{ route('eleve.quiz.result', $a->id) }}">Résultat</a>
                                            <span class="mx-2 text-gray-300">|</span>
                                            <a class="underline" href="{{ route('eleve.quiz.show', $a->quiz_id) }}">Ouvrir quiz</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
