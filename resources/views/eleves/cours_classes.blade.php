<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Affecter cours aux classes</h2>
            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                ← Retour dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6">

                @if (session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-100">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="text-sm text-gray-500">Cours</div>
                <div class="mt-1 text-lg font-semibold text-gray-900">{{ $cours_title }}</div>
                <div class="mt-1 text-xs text-gray-500">ID: {{ $cours_id }}</div>

                <form method="POST" action="{{ route('admin.cours.classes.update', ['id' => $cours_id]) }}" class="mt-6">
                    @csrf

                    <div class="space-y-2">
                        @foreach($classes as $c)
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200">
                                <input type="checkbox" name="classes[]" value="{{ $c->id }}"
                                    @checked(in_array($c->id, $assigned, true))>
                                <span class="text-sm text-gray-800">{{ $c->label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <button class="mt-5 px-5 py-2.5 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        Enregistrer l’affectation
                    </button>
                </form>

                <div class="mt-6 text-xs text-gray-500">
                    Astuce : après avoir affecté, l’élève de cette classe verra le cours dans /eleve/cours.
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
