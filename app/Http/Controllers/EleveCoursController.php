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

    private function matieresTable(): ?string
    {
        if (Schema::hasTable('matieres')) return 'matieres';
        if (Schema::hasTable('subjects')) return 'subjects';
        return null;
    }

    private function matiereLabelCol(?string $matieresTable): ?string
    {
        if (!$matieresTable) return null;

        foreach (['nom', 'name', 'libelle', 'titre', 'label'] as $col) {
            if (Schema::hasColumn($matieresTable, $col)) return $col;
        }
        return null;
    }

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

    private function isProbablyHtml(string $content): bool
    {
        return (bool) preg_match('/<\s*(p|h1|h2|h3|h4|img|table|ul|ol|li|div|span|br|strong|em|a|blockquote|pre)\b/i', $content);
    }

    private function normalizeCourseHtml(string $html): string
    {
        $html = preg_replace('~<script\b[^>]*>.*?</script>~is', '', $html) ?? $html;
        $html = preg_replace('/\son\w+\s*=\s*"[^"]*"/i', '', $html) ?? $html;
        $html = preg_replace("/\son\w+\s*=\s*'[^']*'/i", '', $html) ?? $html;

        // Fix src images
        $html = preg_replace_callback('~<img\b[^>]*\bsrc\s*=\s*([\'"])(.*?)\1~is', function ($m) {
            $src = trim((string) $m[2]);
            $src = str_replace('\\', '/', $src);

            // Bloque file:// ou C:\...
            if (preg_match('~^(file:|[a-zA-Z]:/)~', $src)) {
                return str_replace($m[0], '<img src=""', $m[0]);
            }

            if (preg_match('~^data:image/~i', $src)) {
                return str_replace($m[2], $src, $m[0]);
            }

            // public/xxx => /xxx
            if (str_starts_with($src, 'public/')) {
                $src = substr($src, 7);
            }

            // storage/xxx => /storage/xxx
            if (str_starts_with($src, 'storage/')) {
                $src = '/' . $src;
            }

            if ($src !== '' && !str_starts_with($src, '/') && !preg_match('~^https?://~i', $src)) {
                $src = '/' . $src;
            }

            return str_replace($m[2], $src, $m[0]);
        }, $html) ?? $html;

        return $html;
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

        $titleCol = Schema::hasColumn($coursTable, 'titre') ? 'titre'
            : (Schema::hasColumn($coursTable, 'title') ? 'title'
            : (Schema::hasColumn($coursTable, 'nom') ? 'nom' : null));

        $matieresTable = $this->matieresTable();
        $matiereLabelCol = $this->matiereLabelCol($matieresTable);
        $canJoinMatiere = $matieresTable && $matiereLabelCol && Schema::hasColumn($coursTable, 'matiere_id');

        // 1) Priorité: cours_classes
        $useCoursClasses = Schema::hasTable('cours_classes')
            && DB::table('cours_classes')->where('classe_id', (int)$user->classe_id)->exists();

        if ($useCoursClasses) {
            $query = DB::table($coursTable)
                ->join('cours_classes', 'cours_classes.cours_id', '=', $coursTable . '.id')
                ->where('cours_classes.classe_id', '=', (int) $user->classe_id);

            if ($canJoinMatiere) {
                $query->leftJoin($matieresTable, $matieresTable . '.id', '=', $coursTable . '.matiere_id');
            }

            $select = [
                $coursTable . '.id',
                $titleCol ? ($coursTable . '.' . $titleCol . ' as title') : DB::raw("CONCAT('Cours #', {$coursTable}.id) as title"),
                Schema::hasColumn($coursTable, 'created_at') ? $coursTable . '.created_at' : DB::raw('NULL as created_at'),
                $canJoinMatiere ? DB::raw($matieresTable . '.' . $matiereLabelCol . ' as matiere') : DB::raw("NULL as matiere"),
            ];

            $query->select($select)->orderByDesc($coursTable . '.id');

            if ($q !== '') {
                $query->where(function ($qq) use ($q, $coursTable, $titleCol, $canJoinMatiere, $matieresTable, $matiereLabelCol) {
                    if ($titleCol) $qq->where($coursTable . '.' . $titleCol, 'like', "%{$q}%");
                    if ($canJoinMatiere) $qq->orWhere($matieresTable . '.' . $matiereLabelCol, 'like', "%{$q}%");
                });
            }

            $items = $query->paginate(10)->withQueryString();

            return view('eleves.cours', ['items' => $items, 'q' => $q, 'error' => null]);
        }

        // 2) Fallback: via pivot classe<->matiere
        $pivot = $this->classeMatierePivot();
        if (!$pivot || !Schema::hasTable($pivot)) {
            return view('eleves.cours', [
                'items' => $this->emptyPaginator($request),
                'q' => $q,
                'error' => "Impossible de trouver la table d'affectation classe↔matière (pivot).",
            ]);
        }

        if (!Schema::hasColumn($coursTable, 'matiere_id')) {
            return view('eleves.cours', [
                'items' => $this->emptyPaginator($request),
                'q' => $q,
                'error' => "La colonne matiere_id est introuvable dans {$coursTable}.",
            ]);
        }

        $matiereIds = DB::table($pivot)
            ->where('classe_id', (int) $user->classe_id)
            ->pluck('matiere_id')
            ->toArray();

        if (count($matiereIds) === 0) {
            return view('eleves.cours', [
                'items' => $this->emptyPaginator($request),
                'q' => $q,
                'error' => "Aucune matière n'est affectée à ta classe.",
            ]);
        }

        $query = DB::table($coursTable)->whereIn($coursTable . '.matiere_id', $matiereIds);

        if ($canJoinMatiere) {
            $query->leftJoin($matieresTable, $matieresTable . '.id', '=', $coursTable . '.matiere_id');
        }

        $query->select([
            $coursTable . '.id',
            $titleCol ? ($coursTable . '.' . $titleCol . ' as title') : DB::raw("CONCAT('Cours #', {$coursTable}.id) as title"),
            Schema::hasColumn($coursTable, 'created_at') ? $coursTable . '.created_at' : DB::raw('NULL as created_at'),
            $canJoinMatiere ? DB::raw($matieresTable . '.' . $matiereLabelCol . ' as matiere') : DB::raw("NULL as matiere"),
        ])->orderByDesc($coursTable . '.id');

        if ($q !== '') {
            $query->where(function ($qq) use ($q, $coursTable, $titleCol, $canJoinMatiere, $matieresTable, $matiereLabelCol) {
                if ($titleCol) $qq->where($coursTable . '.' . $titleCol, 'like', "%{$q}%");
                if ($canJoinMatiere) $qq->orWhere($matieresTable . '.' . $matiereLabelCol, 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(10)->withQueryString();

        return view('eleves.cours', ['items' => $items, 'q' => $q, 'error' => null]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (empty($user->classe_id)) {
            return redirect()->route('eleve.classe.edit');
        }

        $coursTable = $this->coursTable();
        if (!Schema::hasTable($coursTable)) abort(404);

        if (Schema::hasTable('cours_classes')) {
            $ok = DB::table('cours_classes')
                ->where('cours_id', (int)$id)
                ->where('classe_id', (int)$user->classe_id)
                ->exists();
            if ($ok) return $this->renderCourse($coursTable, (int)$id);
        }

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
        $raw = $contentCol ? (string)($row->{$contentCol} ?? '') : '';

        $isHtml = $this->isProbablyHtml($raw);
        $content = $isHtml ? $this->normalizeCourseHtml($raw) : $raw;

        return view('eleves.cours_show', [
            'id' => (int)$row->id,
            'title' => $title,
            'content' => $content,
            'isHtml' => $isHtml,
        ]);
    }
}
