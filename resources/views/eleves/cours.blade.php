<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mes cours</h2>
            <a href="{{ route('eleve.dashboard') }}" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour TB
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if($error)
                <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm">
                    {{ $error }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center">
                    <input
                        type="text"
                        name="q"
                        value="{{ $q }}"
                        placeholder="Rechercher un cours..."
                        class="w-full sm:max-w-md rounded-lg border-gray-300 focus:border-gray-400 focus:ring-gray-400"
                    />
                    <button class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Rechercher
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-5">
                @if($items->count() === 0)
                    <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center">
                        <div class="text-sm font-semibold text-gray-900">Aucun cours trouvé</div>
                        <div class="mt-1 text-sm text-gray-500">
                            Demande à l’admin d’affecter des cours à ta classe.
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($items as $c)
                            <div class="rounded-xl border border-gray-200 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">{{ $c->title }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        ID: {{ $c->id }}
                                        @if(!empty($c->created_at))
                                            • {{ \Illuminate\Support\Carbon::parse($c->created_at)->diffForHumans() }}
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('eleve.cours.show', $c->id) }}"
                                   class="inline-flex justify-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                                    Lire
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
