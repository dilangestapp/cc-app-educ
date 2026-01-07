<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class EleveCoursController extends Controller
{
    private function coursTable(): string
    {
        if (Schema::hasTable('cours')) return 'cours';
        if (Schema::hasTable('courses')) return 'courses';
        return 'cours';
    }

    private function matieresTable(): string
    {
        if (Schema::hasTable('matieres')) return 'matieres';
        if (Schema::hasTable('subjects')) return 'subjects';
        return 'matieres';
    }

    /**
     * Détecte automatiquement la table pivot qui lie classe <-> matière
     * (selon ton projet, ça peut varier).
     */
    private function classeMatierePivot(): ?string
    {
        $candidates = [
            'classe_matiere',
            'classe_matieres',
            'classes_matieres',
            'classe_matiere_pivot',
            'classe_matiere_affectations',
        ];

        foreach ($candidates as $t) {
            if (!Schema::hasTable($t)) continue;
            if (Schema::hasColumn($t, 'classe_id') && Schema::hasColumn($t, 'matiere_id')) {
                return $t;
            }
        }

        return null;
    }

    private function emptyPaginator(Request $request): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            items: [],
            total: 0,
            perPage: 10,
            currentPage: 1,
            options: ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if (empty($user->classe_id)) {
            return redirect()->route('eleve.classe.edit');
        }

        $coursTable = $this->coursTable();
        $q = trim((string) $request->query('q', ''));

        if (!Schema::hasTable($coursTable)) {
            return view('eleves.cours', [
                'items' => $this->emptyPaginator($request),
                'q' => $q,
                'error' => "Table des cours introuvable (attendu: {$coursTable}).",
            ]);
        }

        // Colonnes possibles
        $titleCol = Schema::hasColumn($coursTable, 'titre') ? 'titre'
            : (Schema::hasColumn($coursTable, 'title') ? 'title'
            : (Schema::hasColumn($coursTable, 'nom') ? 'nom' : null));

        // 1) ✅ Si cours_classes contient des affectations -> on utilise ça
        $useCoursClasses = Schema::hasTable('cours_classes')
            && DB::table('cours_classes')->where('classe_id', (int)$user->classe_id)->exists();

        if ($useCoursClasses) {
            $query = DB::table($coursTable)
                ->join('cours_classes', 'cours_classes.cours_id', '=', $coursTable . '.id')
                ->where('cours_classes.classe_id', '=', (int) $user->classe_id)
                ->select([
                    $coursTable . '.id',
                    $titleCol ? ($coursTable . '.' . $titleCol . ' as title') : DB::raw("CONCAT('Cours #', {$coursTable}.id) as title"),
                    Schema::hasColumn($coursTable, 'created_at') ? $coursTable . '.created_at' : DB::raw('NULL as created_at'),
                ])
                ->orderByDesc($coursTable . '.id');

            if ($q !== '' && $titleCol) {
                $query->where($coursTable . '.' . $titleCol, 'like', "%{$q}%");
            }

            $items = $query->paginate(10)->withQueryString();

            return view('eleves.cours', [
                'items' => $items,
                'q' => $q,
                'error' => null,
            ]);
        }

        // 2) ✅ SINON: logique normale de ton projet -> cours via matières affectées à la classe
        $pivot = $this->classeMatierePivot();
        $matieresTable = $this->matieresTable();

        if (!$pivot || !Schema::hasTable($matieresTable)) {
            return view('eleves.cours', [
                'items' => $this->emptyPaginator($request),
                'q' => $q,
                'error' => "Impossible de trouver la table d'affectation classe↔matière (pivot).",
            ]);
        }

        // Vérifier que la table cours a bien matiere_id
        if (!Schema::hasColumn($coursTable, 'matiere_id')) {
            return view('eleves.cours', [
                'items' => $this->emptyPaginator($request),
                'q' => $q,
                'error' => "La colonne matiere_id est introuvable dans {$coursTable}.",
            ]);
        }

        // Matières de la classe
        $matiereIds = DB::table($pivot)
            ->where('classe_id', (int) $user->classe_id)
            ->pluck('matiere_id')
            ->toArray();

        if (count($matiereIds) === 0) {
            return view('eleves.cours', [
                'items' => $this->emptyPaginator($request),
                'q' => $q,
                'error' => "Aucune matière n'est affectée à ta classe. (Admin: affecte des matières à cette classe)",
            ]);
        }

        // Cours des matières de la classe
        $query = DB::table($coursTable)
            ->whereIn('matiere_id', $matiereIds)
            ->select([
                $coursTable . '.id',
                $titleCol ? ($coursTable . '.' . $titleCol . ' as title') : DB::raw("CONCAT('Cours #', {$coursTable}.id) as title"),
                Schema::hasColumn($coursTable, 'created_at') ? $coursTable . '.created_at' : DB::raw('NULL as created_at'),
            ])
            ->orderByDesc($coursTable . '.id');

        if ($q !== '' && $titleCol) {
            $query->where($coursTable . '.' . $titleCol, 'like', "%{$q}%");
        }

        $items = $query->paginate(10)->withQueryString();

        return view('eleves.cours', [
            'items' => $items,
            'q' => $q,
            'error' => null,
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (empty($user->classe_id)) {
            return redirect()->route('eleve.classe.edit');
        }

        $coursTable = $this->coursTable();
        if (!Schema::hasTable($coursTable)) abort(404);

        // 1) autorisation via cours_classes si existe et utilisé
        if (Schema::hasTable('cours_classes')) {
            $ok = DB::table('cours_classes')
                ->where('cours_id', (int)$id)
                ->where('classe_id', (int)$user->classe_id)
                ->exists();

            if ($ok) {
                return $this->renderCourse($coursTable, (int)$id);
            }
        }

        // 2) autorisation via matières affectées
        $pivot = $this->classeMatierePivot();
        if (!$pivot || !Schema::hasColumn($coursTable, 'matiere_id')) abort(403);

        $row = DB::table($coursTable)->where('id', (int)$id)->first();
        if (!$row) abort(404);

        $matiereId = (int) ($row->matiere_id ?? 0);
        if ($matiereId <= 0) abort(403);

        $ok = DB::table($pivot)
            ->where('classe_id', (int)$user->classe_id)
            ->where('matiere_id', $matiereId)
            ->exists();

        if (!$ok) abort(403);

        return $this->renderCourse($coursTable, (int)$id);
    }

    private function renderCourse(string $coursTable, int $id)
    {
        $titleCol = Schema::hasColumn($coursTable, 'titre') ? 'titre'
            : (Schema::hasColumn($coursTable, 'title') ? 'title'
            : (Schema::hasColumn($coursTable, 'nom') ? 'nom' : null));

        $contentCol = Schema::hasColumn($coursTable, 'contenu') ? 'contenu'
            : (Schema::hasColumn($coursTable, 'content') ? 'content'
            : (Schema::hasColumn($coursTable, 'body') ? 'body'
            : (Schema::hasColumn($coursTable, 'description') ? 'description' : null)));

        $row = DB::table($coursTable)->where('id', $id)->first();
        if (!$row) abort(404);

        $title = $titleCol ? ($row->{$titleCol} ?? ('Cours #'.$row->id)) : ('Cours #'.$row->id);
        $content = $contentCol ? ($row->{$contentCol} ?? '') : '';

        return view('eleves.cours_show', [
            'id' => (int)$row->id,
            'title' => $title,
            'content' => $content,
        ]);
    }
}
