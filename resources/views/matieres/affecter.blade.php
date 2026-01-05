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

        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            gap: .5rem; padding: .55rem 1rem; border-radius: .9rem;
            font-size: .875rem; font-weight: 600; transition: .15s ease-in-out;
            white-space: nowrap;
        }
        .btn-ghost { background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.10); color: #fff; }
        .btn-ghost:hover { background: rgba(255,255,255,.16); }
        .btn-primary { background: rgb(37 99 235); color: #fff; }
        .btn-primary:hover { background: rgb(29 78 216); }
        .btn-danger { background: rgba(255,255,255,.10); border: 1px solid rgba(255,255,255,.10); color:#fff; }
        .btn-danger:hover { background: rgba(255,255,255,.16); }

        .checkbox-item{
            display:flex; align-items:center; gap:.65rem;
            padding:.6rem .75rem;
            border-radius: .9rem;
            background: rgba(255,255,255,.05);
            border:1px solid rgba(255,255,255,.10);
        }
        .checkbox-item:hover { background: rgba(255,255,255,.08); }
        .checkbox-item input { width:18px; height:18px; }

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
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

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
                                    <span class="text-white font-semibold">Affecter</span>
                                </div>
                                <h1 class="mt-1 text-xl sm:text-2xl font-bold text-white">Affecter une matière</h1>
                                <p class="mt-1 text-sm text-white/70">Matière : <span class="text-white font-semibold">{{ $matiereRow->nom }}</span></p>
                            </div>

                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('matieres.manage') }}" class="btn btn-ghost">← Retour</a>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        <form method="POST" action="{{ route('matieres.affecter.store', $matiereRow->id) }}">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach ($classes as $c)
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="classes[]" value="{{ $c->id }}"
                                            {{ in_array($c->id, $classesAffectees ?? []) ? 'checked' : '' }}>
                                        <div>
                                            <div class="font-semibold text-white">{{ $c->nom }}</div>
                                            <div class="text-xs text-white/55">ID: {{ $c->id }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mt-5 flex items-center gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="{{ route('matieres.manage') }}" class="btn btn-danger">Annuler</a>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
