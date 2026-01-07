<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Choisir ma classe</h2>
            <a href="{{ route('eleve.dashboard') }}" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour TB
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">

                @if (session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-100">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">
                        {{ $errors->first() }}
                    </div>
                @endif

                <p class="text-sm text-gray-600">
                    Pour voir tes cours, tu dois d’abord choisir ta classe.
                </p>

                <form method="POST" action="{{ route('eleve.classe.update') }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Classe</label>
                        <select name="classe_id" class="mt-1 w-full rounded-lg border-gray-300 focus:border-gray-400 focus:ring-gray-400">
                            <option value="">— Choisir —</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" @selected(old('classe_id', auth()->user()->classe_id) == $c->id)>
                                    {{ $c->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
