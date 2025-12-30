<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ➕ Nouvelle matière
            </h2>
            <a href="{{ route('matieres.manage') }}" class="text-sm text-gray-600 hover:underline">Retour</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('matieres.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom de la matière</label>
                        <input name="nom" value="{{ old('nom') }}" required
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Ex: Mathématiques">
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                                class="px-4 py-2 rounded-md bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                            Créer
                        </button>
                        <a href="{{ route('matieres.manage') }}"
                           class="px-4 py-2 rounded-md bg-gray-100 text-gray-800 hover:bg-gray-200">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
