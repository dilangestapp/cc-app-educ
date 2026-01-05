<x-app-layout>
    <style>
        .min-h-screen.bg-gray-100 { background: transparent !important; }
        header, nav { background: rgba(8, 12, 16, .45) !important; backdrop-filter: blur(10px) !important; border-bottom: 1px solid rgba(255,255,255,.10) !important; }
        nav a, nav button, nav span, nav div { color: rgba(255,255,255,.88) !important; }

        .glass-card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.10); backdrop-filter: blur(14px); }
        .glass-input { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); color: rgba(255,255,255,.92); }
        .glass-input::placeholder { color: rgba(255,255,255,.55); }

        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.55rem 1rem; border-radius:.9rem; font-size:.875rem; font-weight:600; transition:.15s; border:1px solid transparent; }
        .btn-ghost { background: rgba(255,255,255,.10); border-color: rgba(255,255,255,.12); color:#fff; }
        .btn-ghost:hover { background: rgba(255,255,255,.16); }
        .btn-primary { background: rgb(79 70 229); color:#fff; }
        .btn-primary:hover { background: rgb(67 56 202); }

        label { color: rgba(255,255,255,.78); font-size:.85rem; }
    </style>

    <div class="min-h-screen" style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        <div class="min-h-screen bg-black/60">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-400/25 bg-red-300/10 px-4 py-3 text-red-100">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <div class="glass-card rounded-2xl overflow-hidden shadow-lg">
                    <div class="p-5 border-b border-white/10">
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div>
                                <div class="text-xs text-white/60">
                                    <a href="{{ route('matieres.manage') }}" class="hover:text-white">Matières</a>
                                    <span class="mx-1">/</span>
                                    <a href="{{ route('cours.index', $matiereRow->id) }}" class="hover:text-white">Cours</a>
                                    <span class="mx-1">/</span>
                                    <span class="text-white font-semibold">Créer</span>
                                </div>
                                <h1 class="mt-1 text-xl font-bold text-white">Nouveau cours — {{ $matiereRow->nom }}</h1>
                                <p class="mt-1 text-sm text-white/70">Crée un cours rattaché à une classe.</p>
                            </div>
                            <a href="{{ route('cours.index', $matiereRow->id) }}" class="btn btn-ghost">← Retour</a>
                        </div>
                    </div>

                    <div class="p-5">
                        <form method="POST" action="{{ route('cours.store', $matiereRow->id) }}">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1">Classe</label>
                                    <select name="classe_id" class="glass-input w-full rounded-xl px-3 py-2 text-sm" required>
                                        <option value="">-- Choisir --</option>
                                        @foreach(($classes ?? collect()) as $cl)
                                            <option value="{{ $cl->id }}" {{ (int)old('classe_id', $classePrefill ?? 0) === (int)$cl->id ? 'selected' : '' }}>
                                                {{ $cl->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block mb-1">Statut</label>
                                    <label class="inline-flex items-center gap-2 mt-2 text-white/80">
                                        <input type="checkbox" name="actif" value="1" {{ old('actif', 1) ? 'checked' : '' }}>
                                        <span>Actif</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block mb-1">Titre</label>
                                <input name="titre" value="{{ old('titre') }}" class="glass-input w-full rounded-xl px-3 py-2 text-sm" required>
                            </div>

                            <div class="mt-4">
                                <label class="block mb-1">Contenu</label>
                                <textarea name="contenu" rows="8" class="glass-input w-full rounded-xl px-3 py-2 text-sm"
                                          placeholder="Écris le contenu du cours...">{{ old('contenu') }}</textarea>
                            </div>

                            <div class="mt-5 flex items-center gap-2 flex-wrap">
                                <button class="btn btn-primary" type="submit">Enregistrer</button>
                                <a class="btn btn-ghost" href="{{ route('cours.index', $matiereRow->id) }}">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
