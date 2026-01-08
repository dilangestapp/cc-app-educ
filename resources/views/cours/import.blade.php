<x-app-layout>
    <style>
        .min-h-screen.bg-gray-100 { background: transparent !important; }
        header, nav {
            background: rgba(8, 12, 16, .45) !important;
            backdrop-filter: blur(10px) !important;
            border-bottom: 1px solid rgba(255,255,255,.10) !important;
        }
        nav a, nav button, nav span, nav div { color: rgba(255,255,255,.88) !important; }
        body, main, .min-h-screen, p, h1, h2, h3, label, span, a, small, strong { color: rgba(255,255,255,.90); }
        .glass-card { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.10); backdrop-filter: blur(14px); }
        .glass-input { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); color: rgba(255,255,255,.92); }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.55rem 1rem; border-radius:.9rem; font-size:.875rem; font-weight:600; transition:.15s; white-space:nowrap; }
        .btn-ghost { background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.10); color:#fff; }
        .btn-primary { background: rgb(79 70 229); color:#fff; }
        .hint { color: rgba(255,255,255,.70); font-size: .9rem; }
    </style>

    <div class="min-h-screen"
         style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        <div class="min-h-screen bg-black/60">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

                <div class="glass-card rounded-2xl overflow-hidden shadow-lg">
                    <div class="p-5 border-b border-white/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-xs text-white/60">
                                    <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                                    <span class="mx-1">/</span>
                                    <a href="{{ route('matieres.manage') }}" class="hover:text-white">Matières</a>
                                    <span class="mx-1">/</span>
                                    <a href="{{ route('cours.index', $matiereRow->id) }}" class="hover:text-white">Cours</a>
                                    <span class="mx-1">/</span>
                                    <span class="text-white font-semibold">Import</span>
                                </div>
                                <h1 class="mt-1 text-xl sm:text-2xl font-bold text-white">
                                    Importer un cours ({{ $matiereRow->nom }})
                                </h1>
                                <p class="mt-1 hint">
                                    DOCX recommandé : titres + paragraphe + images seront formatés automatiquement.
                                </p>
                            </div>

                            <div class="flex gap-2 flex-wrap">
                                <a href="{{ route('cours.create', $matiereRow->id) }}" class="btn btn-ghost">← Retour</a>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        @if (session('error'))
                            <div class="mb-4 rounded-xl border border-red-400/25 bg-red-300/10 px-4 py-3 text-red-100">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="mb-4 rounded-xl border border-green-400/25 bg-green-300/10 px-4 py-3 text-green-100">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-4 rounded-xl border border-red-400/25 bg-red-300/10 px-4 py-3 text-red-100">
                                <ul class="list-disc ml-5">
                                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('cours.import.store', $matiereRow->id) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block mb-2 text-white/80 font-semibold">Fichier (.docx ou .pdf)</label>
                                <input type="file" name="fichier" accept=".docx,.pdf"
                                       class="glass-input w-full rounded-xl px-4 py-3 text-sm" required>
                                <div class="mt-2 hint">
                                    PDF : texte seulement (si pdftotext dispo). DOCX : texte + images + mise en forme.
                                </div>
                            </div>

                            <div class="flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">Importer & pré-remplir</button>
                                <a href="{{ route('cours.create', $matiereRow->id) }}" class="btn btn-ghost">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
