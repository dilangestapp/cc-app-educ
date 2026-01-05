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
                        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">Affecter une matière</h2>
                        <p class="text-sm text-gray-700 mt-1">
                            Matière : <span class="font-semibold text-gray-900">{{ $matiereRow->nom }}</span>
                        </p>
                    </div>

                    <a href="{{ route('matieres.manage') }}"
                       class="inline-flex items-center px-3 py-2 rounded-lg bg-white/90 border border-white/60 text-gray-800 hover:bg-white shadow-sm">
                        Retour
                    </a>
                </div>
            </x-slot>

            <div class="py-6">
                <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

                    <div class="bg-white/95 shadow-sm sm:rounded-2xl border border-white/50 overflow-hidden">
                        <div class="p-6">

                            <form method="POST" action="{{ route('matieres.affecter.store', $matiereRow->id) }}" class="space-y-4">
                                @csrf

                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="font-medium text-gray-800">Choisis les classes</div>
                                    <div class="text-sm text-gray-500">{{ count($classesAffectees ?? []) }} sélectionnée(s)</div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach ($classes as $c)
                                        <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox"
                                                   name="classes[]"
                                                   value="{{ $c->id }}"
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-200"
                                                   {{ in_array($c->id, $classesAffectees ?? []) ? 'checked' : '' }}>
                                            <span class="text-gray-800">{{ $c->nom }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm">
                                        Enregistrer
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
