<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MatiereController extends Controller
{
    /**
     * /matieres
     * Gestion globale (anti-500 : si table absente -> message clair)
     */
    public function manage()
    {
        if (!Schema::hasTable('matieres')) {
            return view('matieres.manage', [
                'matieres' => collect(),
                'error' => "La table 'matieres' n'existe pas encore en base. Lance les migrations en production (Railway) : php artisan migrate --force",
            ]);
        }

        $matieres = DB::table('matieres')->orderBy('nom')->get();

        return view('matieres.manage', [
            'matieres' => $matieres,
            'error' => null,
        ]);
    }

    /**
     * /classes/{classe}/matieres
     */
    public function indexByClasse($classe)
    {
        $classeId = (int) $classe;

        if (!Schema::hasTable('classes')) {
            abort(500, "La table 'classes' n'existe pas.");
        }

        $classeRow = DB::table('classes')->where('id', $classeId)->first();
        abort_if(!$classeRow, 404);

        // Si pivot absent, on ne plante pas : on montre vide
        if (!Schema::hasTable('classe_matiere') || !Schema::hasTable('matieres')) {
            return view('matieres.classe', [
                'classeRow' => $classeRow,
                'matieres'  => collect(),
                'error'     => "Tables manquantes (matieres / classe_matiere). Lance les migrations en production.",
            ]);
        }

        $matieres = DB::table('matieres')
            ->join('classe_matiere', 'matieres.id', '=', 'classe_matiere.matiere_id')
            ->where('classe_matiere.classe_id', $classeId)
            ->select('matieres.*')
            ->orderBy('matieres.nom')
            ->get();

        return view('matieres.classe', [
            'classeRow' => $classeRow,
            'matieres'  => $matieres,
            'error'     => null,
        ]);
    }

    public function create()
    {
        return view('matieres.create');
    }

    public function store(Request $request)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante. Lance les migrations.");

        $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom',
        ]);

        $data = ['nom' => trim((string)$request->nom)];

        // Compat legacy : si colonne classe_id existe et NOT NULL, on met 0.
        if (Schema::hasColumn('matieres', 'classe_id')) {
            $data['classe_id'] = $this->isNullableColumn('matieres', 'classe_id') ? null : 0;
        }

        if (Schema::hasColumn('matieres', 'created_at')) $data['created_at'] = now();
        if (Schema::hasColumn('matieres', 'updated_at')) $data['updated_at'] = now();

        DB::table('matieres')->insert($data);

        return redirect()->route('matieres.manage')->with('success', 'Matière créée.');
    }

    public function edit($matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante. Lance les migrations.");

        $matiereRow = DB::table('matieres')->where('id', (int)$matiere)->first();
        abort_if(!$matiereRow, 404);

        return view('matieres.edit', compact('matiereRow'));
    }

    public function update(Request $request, $matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante. Lance les migrations.");

        $matiereId = (int)$matiere;

        $request->validate([
            'nom' => 'required|string|max:255|unique:matieres,nom,' . $matiereId,
        ]);

        $data = ['nom' => trim((string)$request->nom)];
        if (Schema::hasColumn('matieres', 'updated_at')) $data['updated_at'] = now();

        DB::table('matieres')->where('id', $matiereId)->update($data);

        return redirect()->route('matieres.manage')->with('success', 'Matière mise à jour.');
    }

    public function destroy($matiere)
    {
        $matiereId = (int)$matiere;

        DB::transaction(function () use ($matiereId) {
            if (Schema::hasTable('classe_matiere')) {
                DB::table('classe_matiere')->where('matiere_id', $matiereId)->delete();
            }
            if (Schema::hasTable('matieres')) {
                DB::table('matieres')->where('id', $matiereId)->delete();
            }
        });

        return redirect()->route('matieres.manage')->with('success', 'Matière supprimée.');
    }

    public function affecter($matiere)
    {
        $matiereId = (int)$matiere;

        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante. Lance les migrations.");
        abort_if(!Schema::hasTable('classes'), 500, "Table 'classes' manquante.");
        abort_if(!Schema::hasTable('classe_matiere'), 500, "Table 'classe_matiere' manquante. Lance les migrations.");

        $matiereRow = DB::table('matieres')->where('id', $matiereId)->first();
        abort_if(!$matiereRow, 404);

        $classes = DB::table('classes')->orderBy('nom')->get();

        $classesAffectees = DB::table('classe_matiere')
            ->where('matiere_id', $matiereId)
            ->pluck('classe_id')
            ->toArray();

        return view('matieres.affecter', compact('matiereRow', 'classes', 'classesAffectees'));
    }

    public function storeAffectation(Request $request, $matiere)
    {
        $matiereId = (int)$matiere;

        abort_if(!Schema::hasTable('classe_matiere'), 500, "Table 'classe_matiere' manquante. Lance les migrations.");

        $classes = $request->input('classes', []);
        if (!is_array($classes)) $classes = [];

        $classes = array_values(array_unique(array_map('intval', $classes)));
        $classes = array_filter($classes, fn($id) => $id > 0);

        DB::transaction(function () use ($matiereId, $classes) {
            DB::table('classe_matiere')->where('matiere_id', $matiereId)->delete();

            if (!count($classes)) return;

            $hasCreated = Schema::hasColumn('classe_matiere', 'created_at');
            $hasUpdated = Schema::hasColumn('classe_matiere', 'updated_at');
            $now = now();

            $rows = [];
            foreach ($classes as $classeId) {
                $row = [
                    'matiere_id' => $matiereId,
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
        } catch (\Throwable $e) {}

        return false;
    }
}
