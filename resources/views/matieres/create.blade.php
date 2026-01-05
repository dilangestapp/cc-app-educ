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

        body, main, .min-h-screen, .glass-card, p, h1, h2, h3, label, span, a, small {
            color: rgba(255,255,255,.90);
        }
        .text-gray-900, .text-gray-800, .text-gray-700, .text-gray-600, .text-gray-500, .text-gray-400 {
            color: rgba(255,255,255,.85) !important;
        }

        .glass-card {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.10);
            backdrop-filter: blur(14px);
        }
        .glass-input {
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
            color: rgba(255,255,255,.92);
        }
        .glass-input::placeholder { color: rgba(255,255,255,.55); }
        .glass-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99,102,241,.28);
            border-color: rgba(99,102,241,.55);
        }

        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            gap: .5rem; padding: .55rem 1rem; border-radius: .9rem;
            font-size: .875rem; font-weight: 600; transition: .15s ease-in-out;
            white-space: nowrap;
        }
        .btn-ghost { background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.10); color: #fff; }
        .btn-ghost:hover { background: rgba(255,255,255,.16); }
        .btn-primary { background: rgb(79 70 229); color: #fff; }
        .btn-primary:hover { background: rgb(67 56 202); }
        .btn-danger { background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.10); color:#fff; }
        .btn-danger:hover { background: rgba(255,255,255,.16); }

        .vertical-label {
            position: absolute; right: 18px; top: 18px;
            writing-mode: vertical-rl; transform: rotate(180deg);
            letter-spacing: .25em; font-weight: 800; font-size: .75rem;
            color: rgba(255, 182, 193, .55) !important;
            user-select: none; pointer-events: none;
        }
    </style>

    <div class="min-h-screen"
         style="background-image:url('{{ asset('images/matieres-bg.png') }}');background-size:cover;background-position:center;background-repeat:no-repeat;">
        <div class="min-h-screen bg-black/60">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-400/30 bg-red-300/10 px-4 py-3 text-red-100">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <div class="glass-card rounded-2xl overflow-hidden shadow-lg relative">
                    <div class="vertical-label">MATIÈRES</div>

                    <div class="p-4 sm:p-5 border-b border-white/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-xs text-white/60">
                                    <a href="{{ route('dashboard') }}" class="hover:text-white">Dashboard</a>
                                    <span class="mx-1">/</span>
                                    <a href="{{ route('matieres.manage') }}" class="hover:text-white">Matières</a>
                                    <span class="mx-1">/</span>
                                    <span class="text-white font-semibold">Créer</span>
                                </div>
                                <h1 class="mt-1 text-xl sm:text-2xl font-bold text-white">Créer une matière</h1>
                                <p class="mt-1 text-sm text-white/70">Ajoute une matière unique.</p>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('matieres.manage') }}" class="btn btn-ghost">← Retour</a>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        <form method="POST" action="{{ route('matieres.store') }}" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-semibold text-white/80 mb-1">Nom</label>
                                <input name="nom" value="{{ old('nom') }}"
                                       placeholder="Ex: Informatique"
                                       class="glass-input w-full rounded-xl px-4 py-2 text-sm" required>
                                <div class="mt-1 text-xs text-white/55">Le nom doit être unique.</div>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <button class="btn btn-primary" type="submit">Enregistrer</button>
                                <a href="{{ route('matieres.manage') }}" class="btn btn-danger">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
