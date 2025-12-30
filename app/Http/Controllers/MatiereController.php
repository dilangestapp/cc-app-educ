<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MatiereController extends Controller
{
    /**
     * Page de gestion globale des matières (UNE matière existe une seule fois)
     * URL: /matieres
     */
    public function manage()
    {
        $matieres = DB::table('matieres')->orderBy('nom')->get();
        return view('matieres.manage', compact('matieres'));
    }

    /**
     * Matières d’une classe (parcours pédagogique)
     * URL: /classes/{classe}/matieres
     */
    public function indexByClasse($classe)
    {
        $classeRow = DB::table('classes')->where('id', $classe)->first();
        abort_if(!$classeRow, 404);

        $matieres = DB::table('matieres')
            ->join('classe_matiere', 'matieres.id', '=', 'classe_matiere.matiere_id')
            ->where('classe_matiere.classe_id', $classe)
            ->select('matieres.*')
            ->orderBy('matieres.nom')
            ->get();

        return view('matieres.classe', compact('classeRow', 'matieres'));
    }

    /**
     * Formulaire création matière
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

        $data = ['nom' => trim($request->nom)];

        // Safe timestamps (si colonnes existent)
        if (Schema::hasColumn('matieres', 'created_at')) $data['created_at'] = now();
        if (Schema::hasColumn('matieres', 'updated_at')) $data['updated_at'] = now();

        DB::table('matieres')->insert($data);

        return redirect()->route('matieres.manage')->with('success', 'Matière créée.');
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

        $data = ['nom' => trim($request->nom)];
        if (Schema::hasColumn('matieres', 'updated_at')) $data['updated_at'] = now();

        DB::table('matieres')->where('id', $matiere)->update($data);

        return redirect()->route('matieres.manage')->with('success', 'Matière mise à jour.');
    }

    /**
     * Supprimer matière (et ses affectations)
     */
    public function destroy($matiere)
    {
        DB::table('classe_matiere')->where('matiere_id', $matiere)->delete();
        DB::table('matieres')->where('id', $matiere)->delete();

        return redirect()->route('matieres.manage')->with('success', 'Matière supprimée.');
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

        return view('matieres.affecter', compact('matiereRow', 'classes', 'classesAffectees'));
    }

    /**
     * Enregistrer affectations
     */
    public function storeAffectation(Request $request, $matiere)
    {
        // reset
        DB::table('classe_matiere')->where('matiere_id', $matiere)->delete();

        $classes = $request->input('classes', []);
        if (is_array($classes) && count($classes)) {
            foreach ($classes as $classeId) {
                $row = [
                    'matiere_id' => (int)$matiere,
                    'classe_id'  => (int)$classeId,
                ];
                // Safe timestamps pivot
                if (Schema::hasColumn('classe_matiere', 'created_at')) $row['created_at'] = now();
                if (Schema::hasColumn('classe_matiere', 'updated_at')) $row['updated_at'] = now();

                DB::table('classe_matiere')->insert($row);
            }
        }

        return redirect()->route('matieres.manage')->with('success', 'Affectations mises à jour.');
    }
}
