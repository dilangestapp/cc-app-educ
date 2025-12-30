<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatiereController extends Controller
{
    /**
     * Liste des matières affectées à une classe
     */
    public function index($classe)
    {
        $classeRow = DB::table('classes')->where('id', $classe)->first();
        abort_if(!$classeRow, 404);

        $matieres = DB::table('matieres')
            ->join('classe_matiere', 'matieres.id', '=', 'classe_matiere.matiere_id')
            ->where('classe_matiere.classe_id', $classe)
            ->select('matieres.*')
            ->orderBy('matieres.nom')
            ->get();

        return view('matieres.index', compact('classeRow', 'matieres'));
    }

    /**
     * Formulaire création matière (globale)
     */
    public function create()
    {
        return view('matieres.create');
    }

    /**
     * Enregistrer une matière (UNE SEULE FOIS)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom',
        ]);

        DB::table('matieres')->insert([
            'nom' => $request->nom,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Matière créée.');
    }

    /**
     * Formulaire édition matière
     */
    public function edit($matiere)
    {
        $matiereRow = DB::table('matieres')->where('id', $matiere)->first();
        abort_if(!$matiereRow, 404);

        return view('matieres.edit', compact('matiereRow'));
    }

    /**
     * Mise à jour matière
     */
    public function update(Request $request, $matiere)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom,' . $matiere,
        ]);

        DB::table('matieres')
            ->where('id', $matiere)
            ->update([
                'nom' => $request->nom,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Matière mise à jour.');
    }

    /**
     * Supprimer matière (et ses affectations)
     */
    public function destroy($matiere)
    {
        DB::table('matieres')->where('id', $matiere)->delete();
        return redirect()->back()->with('success', 'Matière supprimée.');
    }

    /**
     * Page d’affectation matière → classes
     */
    public function affecter($matiere)
    {
        $matiereRow = DB::table('matieres')->where('id', $matiere)->first();
        abort_if(!$matiereRow, 404);

        $classes = DB::table('classes')->orderBy('nom')->get();

        $classesAffectees = DB::table('classe_matiere')
            ->where('matiere_id', $matiere)
            ->pluck('classe_id')
            ->toArray();

        return view(
            'matieres.affecter',
            compact('matiereRow', 'classes', 'classesAffectees')
        );
    }

    /**
     * Enregistrer affectations
     */
    public function storeAffectation(Request $request, $matiere)
    {
        DB::table('classe_matiere')->where('matiere_id', $matiere)->delete();

        if ($request->has('classes')) {
            foreach ($request->classes as $classeId) {
                DB::table('classe_matiere')->insert([
                    'matiere_id' => $matiere,
                    'classe_id' => $classeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Affectations mises à jour.');
    }
}
