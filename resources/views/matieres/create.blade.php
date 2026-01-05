<x-app-layout>
    <x-slot name="header">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Créer une matière</h2>
            <a href="{{ route('matieres.manage') }}" class="px-3 py-2 rounded bg-gray-200 hover:bg-gray-300">Retour</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('matieres.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block mb-1">Nom</label>
                            <input name="nom" value="{{ old('nom') }}" class="w-full border rounded px-3 py-2" required>
                        </div>

                        <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700" type="submit">
                            Enregistrer
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
