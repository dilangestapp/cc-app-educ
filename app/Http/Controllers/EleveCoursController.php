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

        // Si pas de classe -> renvoi vers choix classe (filet sécurité)
        if (empty($user->classe_id)) {
            return redirect()->route('eleve.classe.edit');
        }

        $coursTable = $this->coursTable();
        $q = trim((string) $request->query('q', ''));

        // Si tables manquantes, on renvoie une page sans crash
        if (!Schema::hasTable($coursTable)) {
            return view('eleves.cours', [
                'items' => $this->emptyPaginator($request),
                'q' => $q,
                'error' => "Table des cours introuvable (attendu: {$coursTable}).",
            ]);
        }

        if (!Schema::hasTable('cours_classes')) {
            return view('eleves.cours', [
                'items' => $this->emptyPaginator($request),
                'q' => $q,
                'error' => "Pivot cours_classes introuvable. Lance les migrations sur Railway.",
            ]);
        }

        // colonnes possibles
        $titleCol = Schema::hasColumn($coursTable, 'titre') ? 'titre'
            : (Schema::hasColumn($coursTable, 'title') ? 'title'
            : (Schema::hasColumn($coursTable, 'nom') ? 'nom' : null));

        $query = DB::table($coursTable)
            ->join('cours_classes', 'cours_classes.cours_id', '=', $coursTable.'.id')
            ->where('cours_classes.classe_id', '=', (int) $user->classe_id)
            ->select([
                $coursTable.'.id',
                $titleCol ? ($coursTable.'.'.$titleCol.' as title') : DB::raw("CONCAT('Cours #', {$coursTable}.id) as title"),
                Schema::hasColumn($coursTable, 'created_at') ? $coursTable.'.created_at' : DB::raw('NULL as created_at'),
            ])
            ->orderByDesc($coursTable.'.id');

        if ($q !== '' && $titleCol) {
            $query->where($coursTable.'.'.$titleCol, 'like', "%{$q}%");
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

        if (!Schema::hasTable($coursTable) || !Schema::hasTable('cours_classes')) {
            abort(404);
        }

        // Vérifier autorisation (cours affecté à la classe)
        $allowed = DB::table('cours_classes')
            ->where('cours_id', (int) $id)
            ->where('classe_id', (int) $user->classe_id)
            ->exists();

        if (!$allowed) abort(403);

        $titleCol = Schema::hasColumn($coursTable, 'titre') ? 'titre'
            : (Schema::hasColumn($coursTable, 'title') ? 'title'
            : (Schema::hasColumn($coursTable, 'nom') ? 'nom' : null));

        $contentCol = Schema::hasColumn($coursTable, 'contenu') ? 'contenu'
            : (Schema::hasColumn($coursTable, 'content') ? 'content'
            : (Schema::hasColumn($coursTable, 'body') ? 'body'
            : (Schema::hasColumn($coursTable, 'description') ? 'description' : null)));

        $row = DB::table($coursTable)->where('id', (int) $id)->first();
        if (!$row) abort(404);

        $title = $titleCol ? ($row->{$titleCol} ?? ('Cours #'.$row->id)) : ('Cours #'.$row->id);
        $content = $contentCol ? ($row->{$contentCol} ?? '') : '';

        return view('eleves.cours_show', [
            'id' => (int) $row->id,
            'title' => $title,
            'content' => $content,
        ]);
    }
}
