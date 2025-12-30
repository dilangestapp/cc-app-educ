<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MatiereController extends Controller
{
    /**
     * Détecte le bon nom de colonne (nom ou name) dans matieres
     */
    private function matiereNameColumn(): string
    {
        if (Schema::hasColumn('matieres', 'nom')) return 'nom';
        if (Schema::hasColumn('matieres', 'name')) return 'name';
        return 'nom'; // fallback
    }

    /**
     * ✅ GESTION GLOBALE : /matieres
     */
    public function manage()
    {
        $col = $this->matiereNameColumn();

        $matieres = DB::table('matieres')
            ->leftJoin('classe_matiere', 'matieres.id', '=', 'classe_matiere.matiere_id')
            ->select(
                'matieres.id',
                DB::raw("matieres.$col as nom"),
                DB::raw('COUNT(classe_matiere.classe_id) as classes_count')
            )
            ->groupBy('matieres.id', "matieres.$col")
            ->orderBy("matieres.$col")
            ->get();

        return view('matieres.manage', compact('matieres'));
    }

    /**
     * ✅ MATIERES D’UNE CLASSE : /classes/{classe}/matieres
     */
    public function indexByClasse($classe)
    {
        $col = $this->matiereNameColumn();

        $classeRow = DB::table('classes')->where('id', $classe)->first();
        abort_if(!$classeRow, 404);

        $matieres = DB::table('matieres')
            ->join('classe_matiere', 'matieres.id', '=', 'classe_matiere.matiere_id')
            ->where('classe_matiere.classe_id', $classe)
            ->select('matieres.id', DB::raw("matieres.$col as nom"))
            ->orderBy("matieres.$col")
            ->get();

        return view('matieres.index', compact('classeRow', 'matieres'));
    }

    public function create()
    {
        return view('matieres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $col = $this->matiereNameColumn();

        // ✅ Unique (sans dépendre du nom de colonne)
        $exists = DB::table('matieres')->where($col, $request->nom)->exists();
        if ($exists) {
            return back()->withErrors(['nom' => 'Cette matière existe déjà.'])->withInput();
        }

        DB::table('matieres')->insert([
            $col => $request->nom,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('matieres.manage')->with('success', 'Matière créée.');
    }

    public function edit($matiere)
    {
        $col = $this->matiereNameColumn();

        $matiereRow = DB::table('matieres')
            ->select('id', DB::raw("$col as nom"))
            ->where('id', $matiere)
            ->first();

        abort_if(!$matiereRow, 404);

        return view('matieres.edit', compact('matiereRow'));
    }

    public function update(Request $request, $matiere)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $col = $this->matiereNameColumn();

        $exists = DB::table('matieres')
            ->where($col, $request->nom)
            ->where('id', '!=', $matiere)
            ->exists();

        if ($exists) {
            return back()->withErrors(['nom' => 'Cette matière existe déjà.'])->withInput();
        }

        DB::table('matieres')
            ->where('id', $matiere)
            ->update([
                $col => $request->nom,
                'updated_at' => now(),
            ]);

        return redirect()->route('matieres.manage')->with('success', 'Matière mise à jour.');
    }

    public function destroy($matiere)
    {
        DB::table('classe_matiere')->where('matiere_id', $matiere)->delete();
        DB::table('matieres')->where('id', $matiere)->delete();

        return redirect()->route('matieres.manage')->with('success', 'Matière supprimée.');
    }

    public function affecter($matiere)
    {
        $col = $this->matiereNameColumn();

        $matiereRow = DB::table('matieres')
            ->select('id', DB::raw("$col as nom"))
            ->where('id', $matiere)
            ->first();

        abort_if(!$matiereRow, 404);

        $classes = DB::table('classes')->orderBy('nom')->get();

        $classesAffectees = DB::table('classe_matiere')
            ->where('matiere_id', $matiere)
            ->pluck('classe_id')
            ->toArray();

        return view('matieres.affecter', compact('matiereRow', 'classes', 'classesAffectees'));
    }

    public function storeAffectation(Request $request, $matiere)
    {
        DB::table('classe_matiere')->where('matiere_id', $matiere)->delete();

        $classes = $request->input('classes', []);
        foreach ($classes as $classeId) {
            DB::table('classe_matiere')->insert([
                'matiere_id' => $matiere,
                'classe_id'  => $classeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('matieres.manage')->with('success', 'Affectations mises à jour.');
    }
}
