<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CoursController extends Controller
{
    /**
     * /matieres/{matiere}/cours
     */
    public function index(Request $request, $matiere)
    {
        if (!Schema::hasTable('matieres')) {
            abort(500, "La table 'matieres' n'existe pas.");
        }

        $matiereId = (int)$matiere;
        $matiereRow = DB::table('matieres')->where('id', $matiereId)->first();
        abort_if(!$matiereRow, 404);

        if (!Schema::hasTable('cours')) {
            return view('cours.index', [
                'matiereRow' => $matiereRow,
                'cours' => collect(),
                'classes' => collect(),
                'classeFilter' => 0,
                'error' => "La table 'cours' n'existe pas encore. Lance les migrations en production : php artisan migrate --force",
            ]);
        }

        $classeFilter = (int)($request->query('classe', 0));
        $hasClasses = Schema::hasTable('classes');
        $classes = $hasClasses ? DB::table('classes')->orderBy('nom')->get() : collect();

        $q = DB::table('cours as c')
            ->where('c.matiere_id', $matiereId)
            ->orderBy('c.id', 'desc');

        if ($classeFilter > 0) {
            $q->where('c.classe_id', $classeFilter);
        }

        if ($hasClasses) {
            $q->leftJoin('classes as cl', 'cl.id', '=', 'c.classe_id')
              ->select('c.*', 'cl.nom as classe_nom');
        } else {
            $q->select('c.*');
        }

        $cours = $q->get();

        return view('cours.index', [
            'matiereRow' => $matiereRow,
            'cours' => $cours,
            'classes' => $classes,
            'classeFilter' => $classeFilter,
            'error' => null,
        ]);
    }

    /**
     * /matieres/{matiere}/cours/create
     */
    public function create(Request $request, $matiere)
    {
        abort_if(!Schema::hasTable('matieres'), 500, "Table 'matieres' manquante.");
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante. Lance les migrations.");

        $matiereId = (int)$matiere;
        $matiereRow = DB::table('matieres')->where('id', $matiereId)->first();
        abort_if(!$matiereRow, 404);

        $classes = Schema::hasTable('classes') ? DB::table('classes')->orderBy('nom')->get() : collect();
        $classePrefill = (int)$request->query('classe', 0);

        return view('cours.create', compact('matiereRow', 'classes', 'classePrefill'));
    }

    /**
     * POST /matieres/{matiere}/cours
     */
    public function store(Request $request, $matiere)
    {
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante. Lance les migrations.");

        $matiereId = (int)$matiere;

        $request->validate([
            'classe_id' => 'required|integer',
            'titre'     => 'required|string|max:255',
            'contenu'   => 'nullable|string',
        ]);

        $classeId = (int)$request->classe_id;

        if (Schema::hasTable('classes')) {
            $classe = DB::table('classes')->where('id', $classeId)->first();
            abort_if(!$classe, 404);
        }

        $data = [
            'matiere_id' => $matiereId,
            'classe_id'  => $classeId,
            'titre'      => trim((string)$request->titre),
            'contenu'    => $request->contenu ?? '',
        ];

        // actif (si colonne existe)
        if (Schema::hasColumn('cours', 'actif')) {
            $data['actif'] = $request->has('actif') ? 1 : 0;
        }

        if (Schema::hasColumn('cours', 'created_at')) $data['created_at'] = now();
        if (Schema::hasColumn('cours', 'updated_at')) $data['updated_at'] = now();

        DB::table('cours')->insert($data);

        return redirect()->route('cours.index', $matiereId)->with('success', 'Cours créé.');
    }

    /**
     * /cours/{cours}/edit
     */
    public function edit($cours)
    {
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante. Lance les migrations.");

        $coursId = (int)$cours;
        $coursRow = DB::table('cours')->where('id', $coursId)->first();
        abort_if(!$coursRow, 404);

        $matiereRow = Schema::hasTable('matieres')
            ? DB::table('matieres')->where('id', (int)$coursRow->matiere_id)->first()
            : null;

        $classes = Schema::hasTable('classes') ? DB::table('classes')->orderBy('nom')->get() : collect();

        return view('cours.edit', compact('coursRow', 'matiereRow', 'classes'));
    }

    /**
     * PUT /cours/{cours}
     */
    public function update(Request $request, $cours)
    {
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante. Lance les migrations.");

        $coursId = (int)$cours;
        $coursRow = DB::table('cours')->where('id', $coursId)->first();
        abort_if(!$coursRow, 404);

        $request->validate([
            'classe_id' => 'required|integer',
            'titre'     => 'required|string|max:255',
            'contenu'   => 'nullable|string',
        ]);

        $classeId = (int)$request->classe_id;

        if (Schema::hasTable('classes')) {
            $classe = DB::table('classes')->where('id', $classeId)->first();
            abort_if(!$classe, 404);
        }

        $data = [
            'classe_id' => $classeId,
            'titre'     => trim((string)$request->titre),
            'contenu'   => $request->contenu ?? '',
        ];

        if (Schema::hasColumn('cours', 'actif')) {
            $data['actif'] = $request->has('actif') ? 1 : 0;
        }

        if (Schema::hasColumn('cours', 'updated_at')) $data['updated_at'] = now();

        DB::table('cours')->where('id', $coursId)->update($data);

        return redirect()->route('cours.index', (int)$coursRow->matiere_id)->with('success', 'Cours mis à jour.');
    }

    /**
     * DELETE /cours/{cours}
     */
    public function destroy($cours)
    {
        abort_if(!Schema::hasTable('cours'), 500, "Table 'cours' manquante. Lance les migrations.");

        $coursId = (int)$cours;
        $coursRow = DB::table('cours')->where('id', $coursId)->first();
        abort_if(!$coursRow, 404);

        DB::table('cours')->where('id', $coursId)->delete();

        return redirect()->route('cours.index', (int)$coursRow->matiere_id)->with('success', 'Cours supprimé.');
    }
}
