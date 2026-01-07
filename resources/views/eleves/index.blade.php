<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4">
            <div class="bg-white shadow rounded p-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-lg font-bold">Liste des élèves</h1>
                    <a href="{{ route('dashboard') }}" class="text-sm underline">Retour</a>
                </div>

                <p class="text-sm text-gray-500 mt-2">
                    Les élèves se créent uniquement via l’inscription (pseudonymes).
                </p>

                @if(!$eleves || count($eleves) === 0)
                    <div class="mt-6 text-gray-600">Aucun élève pour le moment.</div>
                @else
                    <div class="mt-4 space-y-2">
                        @foreach($eleves as $e)
                            @php $pseudo = $e->pseudo ?: ($e->name ?: 'user'); @endphp
                            <div class="border rounded p-3 flex items-center justify-between">
                                <div>
                                    <div class="font-semibold">👨‍🎓 {{ $pseudo }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $e->id }}</div>
                                </div>
                                <span class="text-xs bg-gray-100 border rounded px-2 py-1">Élève</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
