<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatiereController extends Controller
{
    public function index($classe)
    {
        $classeRow = DB::table('classes')->where('id', $classe)->first();
        abort_if(!$classeRow, 404);

        // TEMPORAIRE : la table matieres peut être vide/non créée selon l’état
        $matieres = DB::hasTable('matieres')
            ? DB::table('matieres')->where('classe_id', $classe)->orderBy('id')->get()
            : collect();

        return view('matieres.index', compact('classeRow', 'matieres'));
    }

    public function create($classe)
    {
        $classeRow = DB::table('classes')->where('id', $classe)->first();
        abort_if(!$classeRow, 404);

        return view('matieres.create', compact('classeRow'));
    }

    public function store(Request $request, $classe)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        // Sécurité PHASE 4 : si la table n’existe pas encore, on bloque proprement
        abort_unless(DB::hasTable('matieres'), 400, 'Table matieres non disponible.');

        DB::table('matieres')->insert([
            'nom' => $request->nom,
            'classe_id' => $classe,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('matieres.index', $classe);
    }
}
