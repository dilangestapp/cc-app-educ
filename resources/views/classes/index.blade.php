<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Classes — CC APP EDUC
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-lg font-semibold mb-2">🏫 Gestion des classes</p>

                    @if ($classes->isEmpty())
                        <p class="text-gray-600">
                            Aucune classe n’a encore été créée.
                        </p>
                    @else
                        <ul class="list-disc list-inside text-gray-700">
                            @foreach ($classes as $classe)
                                <li>{{ $classe->nom }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <p class="text-sm text-gray-500 mt-4">
                        Cette section sera utilisée pour organiser les classes avant l’ajout des matières.
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
