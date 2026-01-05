<x-app-layout>
    <style>
        nav.bg-white { background: rgba(255,255,255,.78) !important; backdrop-filter: blur(10px); }
        header.bg-white { background: rgba(255,255,255,.78) !important; backdrop-filter: blur(10px); box-shadow: none !important; }
    </style>

    <div class="min-h-screen"
         style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        <div class="min-h-screen bg-slate-900/55">
            <x-slot name="header">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Modifier une matière</h2>
                        <p class="text-sm text-gray-700 mt-1">Mets à jour le nom de la matière.</p>
                    </div>

                    <a href="{{ route('matieres.manage') }}"
                       class="inline-flex items-center px-3 py-2 rounded-lg bg-white/90 border border-white/60 text-gray-800 hover:bg-white shadow-sm">
                        Retour
                    </a>
                </div>
            </x-slot>

            <div class="py-6">
                <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                            <div class="font-semibold mb-1">Erreur</div>
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="bg-white/95 shadow-sm sm:rounded-2xl border border-white/50 overflow-hidden">
                        <div class="p-6">
                            <form method="POST" action="{{ route('matieres.update', $matiereRow->id) }}" class="space-y-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                    <input name="nom"
                                           value="{{ old('nom', $matiereRow->nom) }}"
                                           class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                           required>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
                                        Mettre à jour
                                    </button>

                                    <a href="{{ route('matieres.manage') }}"
                                       class="inline-flex items-center px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 shadow-sm">
                                        Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
