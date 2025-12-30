<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <h2 style="font-weight:700; font-size:20px; line-height:1.2; color:#111827;">
                📚 Gestion des matières
            </h2>

            <a href="{{ route('dashboard') }}"
               style="font-size:14px; color:#2563eb; text-decoration:underline;">
                Retour
            </a>
        </div>
    </x-slot>

    <div style="padding:32px 0;">
        <div style="max-width:1100px; margin:0 auto; padding:0 16px;">

            {{-- ✅ Barre d’actions (toujours visible, style inline) --}}
            <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px;">
                <div style="font-size:14px; color:#4b5563;">
                    Gestion globale : une matière existe une seule fois, puis on l’affecte aux classes.
                </div>

                <a href="{{ route('matieres.create') }}"
                   style="display:inline-block; padding:10px 14px; border-radius:8px;
                          background:#2563eb; color:#ffffff; font-size:14px; font-weight:700;
                          text-decoration:none;">
                    + Nouvelle matière
                </a>
            </div>

            @if(session('success'))
                <div style="margin-bottom:14px; padding:10px 12px; border-radius:8px; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46;">
                    {{ session('success') }}
                </div>
            @endif

            <div style="background:#ffffff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden;">
                <div style="padding:18px;">

                    @if($matieres->count() === 0)
                        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                            <p style="margin:0; color:#374151;">
                                Aucune matière. Clique sur “Nouvelle matière”.
                            </p>

                            {{-- ✅ Deuxième bouton (dans le bloc vide) --}}
                            <a href="{{ route('matieres.create') }}"
                               style="display:inline-block; padding:10px 14px; border-radius:8px;
                                      background:#111827; color:#ffffff; font-size:14px; font-weight:700;
                                      text-decoration:none;">
                                + Nouvelle matière
                            </a>
                        </div>
                    @else
                        <div style="overflow-x:auto;">
                            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                                <thead>
                                    <tr style="text-align:left; border-bottom:1px solid #e5e7eb;">
                                        <th style="padding:10px 8px;">Nom</th>
                                        <th style="padding:10px 8px;">Classes affectées</th>
                                        <th style="padding:10px 8px; text-align:right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($matieres as $m)
                                        <tr style="border-bottom:1px solid #f3f4f6;">
                                            <td style="padding:10px 8px; font-weight:600; color:#111827;">
                                                {{ $m->nom }}
                                            </td>

                                            <td style="padding:10px 8px; color:#374151;">
                                                {{ $m->classes_count }}
                                            </td>

                                            <td style="padding:10px 8px;">
                                                <div style="display:flex; gap:8px; justify-content:flex-end; flex-wrap:wrap;">
                                                    <a href="{{ route('matieres.affecter', $m->id) }}"
                                                       style="display:inline-block; padding:7px 10px; border-radius:8px;
                                                              background:#4f46e5; color:#ffffff; text-decoration:none; font-size:12px; font-weight:700;">
                                                        Affecter
                                                    </a>

                                                    <a href="{{ route('matieres.edit', $m->id) }}"
                                                       style="display:inline-block; padding:7px 10px; border-radius:8px;
                                                              background:#e5e7eb; color:#111827; text-decoration:none; font-size:12px; font-weight:700;">
                                                        Modifier
                                                    </a>

                                                    <form action="{{ route('matieres.destroy', $m->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Supprimer cette matière ?');"
                                                          style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            style="padding:7px 10px; border-radius:8px;
                                                                   background:#dc2626; color:#ffffff; border:none; cursor:pointer;
                                                                   font-size:12px; font-weight:700;">
                                                            Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div style="margin-top:16px; font-size:13px; color:#1e3a8a; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px;">
                        ✅ Une matière existe une seule fois, mais peut être affectée à plusieurs classes.
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
