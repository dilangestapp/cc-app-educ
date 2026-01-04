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
        $classeRow = DB::table('classes')->where('id', (int)$classe)->first();
        abort_if(!$classeRow, 404);

        $matieres = DB::table('matieres')
            ->join('classe_matiere', 'matieres.id', '=', 'classe_matiere.matiere_id')
            ->where('classe_matiere.classe_id', (int)$classe)
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

        $data = [
            'nom' => trim((string) $request->nom),
        ];

        // ✅ Compat legacy: si une colonne classe_id existe dans matieres (ancien schéma),
        // on la renseigne pour éviter un crash NOT NULL.
        if (Schema::hasColumn('matieres', 'classe_id')) {
            $data['classe_id'] = $this->isNullableColumn('matieres', 'classe_id') ? null : 0;
        }

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
        $matiereRow = DB::table('matieres')->where('id', (int)$matiere)->first();
        abort_if(!$matiereRow, 404);

        return view('matieres.edit', compact('matiereRow'));
    }

    /**
     * Mise à jour matière
     */
    public function update(Request $request, $matiere)
    {
        $matiere = (int)$matiere;

        $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom,' . $matiere,
        ]);

        $data = ['nom' => trim((string) $request->nom)];
        if (Schema::hasColumn('matieres', 'updated_at')) $data['updated_at'] = now();

        DB::table('matieres')->where('id', $matiere)->update($data);

        return redirect()->route('matieres.manage')->with('success', 'Matière mise à jour.');
    }

    /**
     * Supprimer matière (et ses affectations)
     */
    public function destroy($matiere)
    {
        $matiere = (int)$matiere;

        DB::transaction(function () use ($matiere) {
            DB::table('classe_matiere')->where('matiere_id', $matiere)->delete();
            DB::table('matieres')->where('id', $matiere)->delete();
        });

        return redirect()->route('matieres.manage')->with('success', 'Matière supprimée.');
    }

    /**
     * Page d’affectation matière → classes
     */
    public function affecter($matiere)
    {
        $matiere = (int)$matiere;

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
        $matiere = (int)$matiere;

        $classes = $request->input('classes', []);
        if (!is_array($classes)) $classes = [];

        // ✅ Validation basique des IDs
        $classes = array_values(array_unique(array_map('intval', $classes)));
        $classes = array_filter($classes, fn($id) => $id > 0);

        DB::transaction(function () use ($matiere, $classes) {

            // reset
            DB::table('classe_matiere')->where('matiere_id', $matiere)->delete();

            if (!count($classes)) {
                return;
            }

            $rows = [];
            $hasCreated = Schema::hasColumn('classe_matiere', 'created_at');
            $hasUpdated = Schema::hasColumn('classe_matiere', 'updated_at');
            $now = now();

            foreach ($classes as $classeId) {
                $row = [
                    'matiere_id' => $matiere,
                    'classe_id'  => (int)$classeId,
                ];
                if ($hasCreated) $row['created_at'] = $now;
                if ($hasUpdated) $row['updated_at'] = $now;
                $rows[] = $row;
            }

            DB::table('classe_matiere')->insert($rows);
        });

        return redirect()->route('matieres.manage')->with('success', 'Affectations mises à jour.');
    }

    /**
     * Détecter si une colonne est nullable (MySQL / PostgreSQL)
     */
    private function isNullableColumn(string $table, string $column): bool
    {
        try {
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                $dbName = DB::getDatabaseName();
                $row = DB::selectOne(
                    "SELECT IS_NULLABLE FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
                     LIMIT 1",
                    [$dbName, $table, $column]
                );
                return isset($row->IS_NULLABLE) && strtoupper((string)$row->IS_NULLABLE) === 'YES';
            }

            if ($driver === 'pgsql') {
                $row = DB::selectOne(
                    "SELECT is_nullable FROM information_schema.columns
                     WHERE table_schema = 'public' AND table_name = ? AND column_name = ?
                     LIMIT 1",
                    [$table, $column]
                );
                return isset($row->is_nullable) && strtoupper((string)$row->is_nullable) === 'YES';
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return false;
    }
}
