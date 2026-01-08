<x-app-layout>
    <style>
        .min-h-screen.bg-gray-100 { background: transparent !important; }
        header, nav {
            background: rgba(8, 12, 16, .45) !important;
            backdrop-filter: blur(10px) !important;
            border-bottom: 1px solid rgba(255,255,255,.10) !important;
        }
        nav a, nav button, nav span, nav div { color: rgba(255,255,255,.88) !important; }
        nav a:hover, nav button:hover { color: #fff !important; }

        body, main, .min-h-screen, p, h1, h2, h3, label, span, a, small, strong {
            color: rgba(255,255,255,.90);
        }
        .glass-card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.10); backdrop-filter: blur(14px); }
        .glass-input { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); color: rgba(255,255,255,.92); }
        .glass-input::placeholder { color: rgba(255,255,255,.55); }

        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.60rem 1rem; border-radius: .9rem; font-size:.875rem; font-weight:700; transition:.15s; white-space:nowrap; }
        .btn-ghost { background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.10); color:#fff; }
        .btn-ghost:hover { background: rgba(255,255,255,.16); }
        .btn-primary { background: rgb(99 102 241); color:#fff; }
        .btn-primary:hover { background: rgb(79 70 229); }
        .btn-dark { background: rgba(0,0,0,.45); border: 1px solid rgba(255,255,255,.12); color:#fff; }
        .btn-dark:hover { background: rgba(0,0,0,.60); }

        .hint { color: rgba(255,255,255,.72); font-size: .9rem; }
        .errorbox { border:1px solid rgba(239,68,68,.25); background: rgba(239,68,68,.10); color: #ffd6d6; }
    </style>

    <div class="min-h-screen"
         style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        <div class="min-h-screen bg-black/60">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

                <div class="glass-card rounded-2xl overflow-hidden shadow-lg">
                    {{-- HEADER --}}
                    <div class="p-5 border-b border-white/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="text-xs text-white/60">
                                    <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                                    <span class="mx-1">/</span>
                                    <a href="{{ route('matieres.manage') }}" class="hover:text-white">Matières</a>
                                    <span class="mx-1">/</span>
                                    <a href="{{ route('cours.index', $matiereRow->id) }}" class="hover:text-white">Cours</a>
                                    <span class="mx-1">/</span>
                                    <span class="text-white font-semibold">Créer</span>
                                </div>

                                <h1 class="mt-1 text-xl sm:text-2xl font-bold text-white truncate">
                                    Nouveau cours — {{ $matiereRow->nom ?? 'Matière' }}
                                </h1>

                                <p class="mt-1 hint">
                                    Crée un cours rattaché à une classe. (DOCX recommandé pour texte + images + mise en forme)
                                </p>
                            </div>

                            <div class="flex gap-2 flex-wrap">
                                {{-- ✅ IMPORTANT: ce bouton NE DOIT PAS soumettre le form --}}
                                <a href="{{ route('cours.import', $matiereRow->id) }}" class="btn btn-primary">
                                    ⬆️ Importer Word/PDF
                                </a>

                                <a href="{{ route('cours.index', $matiereRow->id) }}" class="btn btn-ghost">
                                    ← Retour
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- MESSAGES --}}
                    <div class="p-5 space-y-4">

                        @if (session('success'))
                            <div class="rounded-xl border border-emerald-400/20 bg-emerald-300/10 px-4 py-3 text-emerald-100">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="rounded-xl errorbox px-4 py-3">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="rounded-xl errorbox px-4 py-3">
                                <ul class="list-disc ml-5 text-sm">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- FORM --}}
                        <form method="POST" action="{{ route('cours.store', $matiereRow->id) }}" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-white/80 font-semibold">Classe</label>

                                    {{-- ✅ name="classe_id" obligatoire --}}
                                    <select name="classe_id" class="glass-input w-full rounded-xl px-4 py-3 text-sm" required>
                                        <option value="">-- Choisir une classe --</option>
                                        @foreach(($classes ?? []) as $cl)
                                            <option value="{{ $cl->id }}"
                                                {{ (string)old('classe_id') === (string)$cl->id ? 'selected' : '' }}>
                                                {{ $cl->nom }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="mt-2 hint">Le cours sera visible pour les élèves de cette classe.</div>
                                </div>

                                <div>
                                    <label class="block mb-2 text-white/80 font-semibold">Statut</label>
                                    <label class="inline-flex items-center gap-2 glass-input rounded-xl px-4 py-3 w-full">
                                        <input type="checkbox" name="actif" value="1"
                                               {{ old('actif', 1) ? 'checked' : '' }}>
                                        <span class="font-semibold">Actif</span>
                                    </label>
                                    <div class="mt-2 hint">Décoche si tu veux préparer le cours sans le publier.</div>
                                </div>
                            </div>

                            <div>
                                <label class="block mb-2 text-white/80 font-semibold">Titre</label>

                                {{-- ✅ name="titre" obligatoire --}}
                                <input type="text" name="titre" value="{{ old('titre') }}"
                                       class="glass-input w-full rounded-xl px-4 py-3 text-sm"
                                       placeholder="Ex: Chapitre 1 — Les bases..." required>
                            </div>

                            <div>
                                <label class="block mb-2 text-white/80 font-semibold">Contenu</label>
                                <textarea name="contenu" rows="12"
                                          class="glass-input w-full rounded-xl px-4 py-3 text-sm"
                                          placeholder="Écris le contenu du cours...">{{ old('contenu') }}</textarea>

                                <div class="mt-2 hint">
                                    Astuce : si tu importes un DOCX, le contenu sera pré-rempli automatiquement ici.
                                </div>
                            </div>

                            <div class="flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">✅ Enregistrer</button>
                                <a href="{{ route('cours.index', $matiereRow->id) }}" class="btn btn-dark">Annuler</a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
